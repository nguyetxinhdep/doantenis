<?php

namespace App\Http\Controllers\booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PriceList;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function bookingCalendar($date)
    {
        // Lấy danh sách sân và giờ đã đặt(bảng booking)
        $courts = Court::where('branch_id', session('branch_active')->Branch_id)->get();
        $bookings = Booking::where('branch_id', session('branch_active')->Branch_id)
            ->where('Date_booking', $date)->get(); // Lấy tất cả đặt sân
        $title = "Lịch";

        return view('booking.calendar', compact('courts', 'bookings', 'title'));
    }

    public function bookingCalendarWelcome(Request $req)
    {
        // Tạo biến date với ngày hiện tại
        $date = date('Y-m-d'); // Định dạng ngày hiện tại là Y-m-d
        $branch_id = $req->input('branch_id');

        // Lấy danh sách sân và giờ đã đặt(bảng booking)
        $courts = Court::where('branch_id', $branch_id)->get();
        $bookings = Booking::where('branch_id', $branch_id)
            ->where('Date_booking', $date)->get(); // Lấy tất cả đặt sân
        $title = "Lịch";

        return view('booking.calendarWelcome', compact('courts', 'bookings', 'title'));
    }

    public function bookingCalendarSearch(Request $req)
    {
        if (isset($req->date)) {
            $dateSearch = $req->date;
            // Lấy danh sách sân và giờ đã đặt(bảng booking)
            $courts = Court::where('branch_id', session('branch_active')->Branch_id)->get();
            $bookings = Booking::where('branch_id', session('branch_active')->Branch_id)
                ->where('Date_booking', $dateSearch)->get(); // Lấy tất cả đặt sân
            $title = "Lịch";

            return view('booking.calendar', compact('courts', 'bookings', 'title'));
        } else {
            dd(123);
        }
    }

    public function customerSearchCalendar(Request $req)
    {
        if (isset($req->date)) {
            $dateSearch = $req->date;
            // Lấy danh sách sân và giờ đã đặt(bảng booking)
            $courts = Court::where('branch_id', $req->branch_id)->get();
            $bookings = Booking::where('branch_id', $req->branch_id)
                ->where('Date_booking', $dateSearch)->get(); // Lấy tất cả đặt sân
            $title = "Lịch";

            return view('booking.calendarWelcome', compact('courts', 'bookings', 'title'));
        } else {
            return abort(404);
        }
    }

    public function bookingHistory(Request $req)
    {
        $title = "Lịch sử đặt sân";

        $history = Booking::join('branches', 'bookings.branch_id', '=', 'branches.Branch_id')
            ->join('courts', 'bookings.court_id', '=', 'courts.Court_id')
            ->join('payments', 'bookings.Booking_id', '=', 'payments.booking_id')
            ->where("bookings.customer_id", session('customer_id'))
            // ->where("bookings.Status", 2)
            ->select(
                'bookings.*',
                'branches.name as branch_name',
                'courts.name as court_name',
                'payments.Debt as Debt',
                'payments.Payment_id as Payment_id',
            )
            ->orderBy('bookings.created_at', 'desc')
            ->get();

        return view('booking.bookingHistory', compact('history', 'title'));
    }

    public function xuLyThanhToanTC(Request $req, $Payment_id, $Booking_id, $pay)
    {
        DB::beginTransaction(); // Bắt đầu transaction

        try {
            // kiểm tra kết quả thanh toán từ momo
            $resultCode = $req->input('resultCode');

            // nếu thanh toán thành công
            if ($resultCode == 0) {
                $payment = Payment::where('Payment_id', $Payment_id)->first();

                if (!$payment) {
                    // Nếu không tìm thấy payment, ném ra ngoại lệ
                    throw new \Exception('Payment not found');
                }

                // Tính toán số tiền còn nợ
                $debt = $payment->Amount - ($payment->Paid + $pay);
                $status = ($debt == 0 ? 1 : 0); //trạng thái, 0 chưa thanh toán đủ, 1 thanh toán đủ
                // Cập nhật bản ghi thanh toán
                $payment->update([
                    'Payment_method' => 'Bank',
                    'Debt' => $debt, // tiền còn nợ
                    'Paid' => $payment->Paid + $pay, // Số tiền đã thanh toán
                    'Status' => $status, // Trạng thái thanh toán
                    'Payment_date' => now(),
                ]);

                //----------------------bảng booking
                $booking = Booking::where('Booking_id', $Booking_id)->first();

                if (!$booking) {
                    // Nếu không tìm thấy booking, ném ra ngoại lệ
                    throw new \Exception('Booking not found');
                }

                // Cập nhật trạng thái booking
                $booking->update([
                    'Status' => $status, // Trạng thái thanh toán
                ]);

                DB::commit(); // Cam kết transaction nếu mọi thứ thành công

                return redirect()->route('booking.history')->with('success', 'Thanh toán thành công.');
            } else {
                return redirect()->route('booking.history');
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction nếu có lỗi
            Log::error('Payment processing error: ' . $e->getMessage());

            return redirect()->route('booking.history')->with('error', 'Có lỗi xảy ra trong quá trình thanh toán: ' . $e->getMessage());
        }
    }

    // Hàm xử lý yêu cầu đặt sân
    public function reserve(Request $request)
    {
        // Xác thực dữ liệu đầu vào: phải có 'selectedCells' và 'date'
        $this->validate($request, [
            'selectedCells' => 'required', // selectedCells phải là một mảng
            'date' => 'required|date', // date phải là một ngày hợp lệ
        ]);
        // Bắt đầu transaction
        DB::beginTransaction();
        try {
            $paymentOption = $request->input('paymentOption'); // Nhận hình thức thanh toán
            // Log::debug($paymentOption);
            $timestamp = strtotime($request->date); // Chuyển đổi ngày thành timestamp

            if (date('N', $timestamp) >= 1 && date('N', $timestamp) <= 5) {
                $time_slot_status = 1; // Ngày từ thứ Hai đến thứ Sáu
            } else {
                $time_slot_status = 2; // Ngày thứ Bảy hoặc Chủ nhật
            }

            $reservations = []; // Mảng để lưu trữ thông tin đặt sân tạm thời
            // Log::debug($request->selectedCells);
            // Lặp qua các ô đã chọn từ client
            foreach ($request->selectedCells as $cell) {
                $courtId = $cell['courtId'];
                $timeStart = \Carbon\Carbon::createFromFormat('H:i', $cell['timeStart']); // Chuyển đổi thời gian bắt đầu
                $timeEnd = \Carbon\Carbon::createFromFormat('H:i', $cell['timeEnd']); // Chuyển đổi thời gian kết thúc

                // Kiểm tra xem đã có đặt sân cho sân này hay chưa
                if (!isset($reservations[$courtId])) {
                    $reservations[$courtId] = []; // Nếu chưa, khởi tạo một mảng trống cho sân này
                }

                // Thêm thông tin thời gian vào mảng đặt sân
                $reservations[$courtId][] = [
                    'timeStart' => $timeStart,
                    'timeEnd' => $timeEnd,
                ];
            }

            // -----kiểm tra ai là người tạo để lấy customerid và customer type id
            if (Auth()->user()->Role == 5) { //là khách hàng
                $customerID = (Customer::where('user_id', Auth()->user()->User_id)->first())->Customer_id;
                // $customer_type_id = (Customer::where('user_id', Auth()->user()->User_id)->first())->customer_type_id;
                $customer_type_id = 1;
            } else { // là chủ sân hoặc nhân viên
                $ten_kh = $request->input('name');
                $sdt_kh = $request->input('phone');
                $tien_da_tra = floatval($request->input('amount')); //tiền đã trả

                $user_new = User::create([
                    'Name' => $ten_kh,
                    'Phone' => $sdt_kh,
                    'Role' => '5',
                ]);

                $kh_new = Customer::create([
                    'user_id' => $user_new->User_id,
                    // 'customer_type_id' => 1,
                ]);

                $customerID = $kh_new->Customer_id;
                $customer_type_id = 1;
            }


            $bookings = []; // Mảng chứa thông tin booking đã gộp
            // Log::debug($reservations);
            // Lặp qua từng sân
            foreach ($reservations as $courtId => $times) {
                // Sắp xếp các thời gian theo thời gian bắt đầu
                usort($times, function ($a, $b) {
                    return $a['timeStart']->timestamp <=> $b['timeStart']->timestamp;
                });

                // Khởi tạo thời gian bắt đầu và kết thúc gộp
                $mergedStart = $times[0]['timeStart'];
                $mergedEnd = $times[0]['timeEnd'];

                // láy id chi nhánh
                $branchID = Court::where('Court_id', $courtId)->first()->branch_id;

                // lấy tất cả khung giờ theo id chi nhánh
                $timeslots = TimeSlot::where('branch_id', $branchID)
                    ->where('Status', $time_slot_status)
                    ->orderBy('Start_time', 'asc') // Sắp xếp theo Start_time tăng dần
                    ->get();

                // Lặp qua từng khoảng thời gian
                foreach ($times as $key => $time) {
                    if ($time['timeStart']->lessThanOrEqualTo($mergedEnd)) {
                        // Nếu thời gian tiếp theo liên tục với thời gian trước đó
                        $mergedEnd = max($mergedEnd, $time['timeEnd']);
                    }
                }
                // -----------end foreach

                // tạo mảng lưu giá trị có slot để xử lý nếu đặt ở 2 khung giờ khác nhau với giá khác nhau
                $arraytimeslot = [];

                foreach ($timeslots as $slot) {
                    $arraytimeslot[] = $slot;

                    // Chuyển đổi thời gian bắt đầu và kết thúc của khung giờ thành định dạng Carbon
                    $startTime = Carbon::createFromFormat('H:i:s', $slot->Start_time); // assuming it's stored in 'H:i:s' format
                    $endTime = Carbon::createFromFormat('H:i:s', $slot->End_time);

                    // Kiểm tra nếu khoảng thời gian nhận được nằm trong khoảng từ start_time đến end_time
                    // Log các giá trị debug
                    // Log::debug('merstart:', ['merstart' => $mergedStart]);
                    // Log::debug('merend', ['merend' => $mergedEnd]);
                    // Log::debug('start:', ['start' => $startTime]);
                    // Log::debug('end', ['end' => $endTime]);
                    if ($mergedStart->greaterThanOrEqualTo($startTime) && $mergedEnd->lessThanOrEqualTo($endTime)) {
                        $timeslot_id = $slot->Time_slot_id; // Ghi lại khung giờ phù hợp
                        break; // Thoát khỏi vòng lặp nếu đã tìm thấy khung giờ phù hợp
                    }
                }

                if (isset($timeslot_id)) {
                    // lấy ra bảng giá phù hợp
                    $pricelist = PriceList::where('time_slot_id', $timeslot_id)->where('customer_type_id', $customer_type_id)->first();
                    // lấy max booking_code để tạo bookingcode mới 
                    $maxBookingCode = Booking::max('booking_code') ?? 0;
                    $bookingcodeNew = $maxBookingCode + 1;

                    // Thêm booking cuối cùng vào mảng
                    $bookings[] = [
                        'court_id' => $courtId,
                        'Date_booking' => $request->date,
                        'Start_time' => $mergedStart->format('H:i'),
                        'End_time' => $mergedEnd->format('H:i'),
                        'Status' => 2,
                        'time_slot_id' => $timeslot_id,
                        'customer_id' => $customerID,
                        'price_list_id' => $pricelist->Price_list_id,
                        'branch_id' => $branchID,
                        'booking_code' => $bookingcodeNew
                    ];
                } else {
                    // nếu giờ đặt nằm ở 2 khung giờ thì tách ra 2 booking
                    for ($i = 0; $i < count($arraytimeslot); $i++) {
                        $slottemp = $arraytimeslot[$i];
                        if (
                            $mergedStart->greaterThanOrEqualTo($slottemp->Start_time)
                            && $mergedEnd->greaterThan($slottemp->End_time)
                            && $mergedEnd->lessThan($arraytimeslot[$i + 1]->End_time)
                        ) {
                            // lấy max booking_code để tạo bookingcode mới 
                            $maxBookingCode = Booking::max('booking_code') ?? 0;
                            $bookingcodeNew = $maxBookingCode + 1;
                            // Log::debug('code:', [$bookingcodeNew]);
                            // lấy ra bảng giá phù hợp
                            $pricelist1 = PriceList::where('time_slot_id', $arraytimeslot[$i]->Time_slot_id)->where('customer_type_id', $customer_type_id)->first();
                            $pricelist2 = PriceList::where('time_slot_id', $arraytimeslot[$i + 1]->Time_slot_id)->where('customer_type_id', $customer_type_id)->first();
                            $bookings[] = [
                                'court_id' => $courtId,
                                'Date_booking' => $request->date,
                                'Start_time' => $mergedStart->format('H:i'),
                                'End_time' => $arraytimeslot[$i]->End_time,
                                'Status' => 2,
                                'time_slot_id' => $arraytimeslot[$i]->Time_slot_id,
                                'customer_id' => $customerID,
                                'price_list_id' => $pricelist1->Price_list_id,
                                'branch_id' => $branchID,
                                'booking_code' => $bookingcodeNew
                            ];

                            $bookings[] = [
                                'court_id' => $courtId,
                                'Date_booking' => $request->date,
                                'Start_time' => $arraytimeslot[$i]->End_time,
                                'End_time' => $mergedEnd->format('H:i'),
                                'Status' => 2,
                                'time_slot_id' => $arraytimeslot[$i + 1]->Time_slot_id,
                                'customer_id' => $customerID,
                                'price_list_id' => $pricelist2->Price_list_id,
                                'branch_id' => $branchID,
                                'booking_code' => $bookingcodeNew
                            ];
                        }
                    }
                }
            }

            // -------------------
            // Lưu các booking vào cơ sở dữ liệu
            foreach ($bookings as $booking) {
                $bookingcreate = Booking::create($booking); // Tạo bản ghi mới trong bảng bookings
                $price_list = PriceList::where('Price_list_id', $booking['price_list_id'])->first();
                // Tính tổng tiền
                $total = $this->calculatePrice($price_list->Price, $booking['Start_time'], $booking['End_time']);

                // -----kiểm tra ai là người tạo để lấy customerid và customer type id
                if (Auth()->user()->Role == 5) { //là khách hàng
                    Payment::create([
                        'Amount' => $total,
                        'Payment_method' => 'Bank',
                        'Debt' => $total, //còn nợ
                        'Paid' => 0,
                        'Status' => 0,
                        'branch_id' => $booking['branch_id'],
                        'booking_id' => $bookingcreate->Booking_id
                    ]);
                } else { // là chủ sân hoặc nhân viên
                    $con_no = $total - $tien_da_tra;
                    if ($con_no == 0) { // trả đủ rồi
                        $status = 1;
                    } else {
                        $status = 0; //chưa trả đủ
                    }
                    Payment::create([
                        'Amount' => $total,
                        'Payment_method' => 'Bank',
                        'Debt' => $con_no,
                        'Paid' => $tien_da_tra,
                        'Status' => $status,
                        'branch_id' => $booking['branch_id'],
                        'booking_id' => $bookingcreate->Booking_id
                    ]);
                }
            }

            // Commit transaction nếu mọi thứ thành công
            DB::commit();
            // Log::debug($total);

            // Trả về phản hồi cho client
            return response()->json(['success' => true, 'total' => $total]);
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();
            Log::error('Error occurred while reserving: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Đặt sân thất bại.'], 500);
        }
    }


    // Hàm xử lý yêu cầu đặt sân cố định
    public function datCoDinh(Request $request)
    {
        $request->validate([
            'startDate' => 'required|date|after_or_equal:today', // Không cho phép ngày quá khứ
            'endDate' => 'required|date|after_or_equal:startDate',
            'schedules' => 'required|array',
            'schedules.*.day' => 'required|string',
            'schedules.*.courts' => 'required|array',
            'schedules.*.courts.*.court' => 'required|string',
            'schedules.*.courts.*.startTime' => 'required|date_format:H:i',
            'schedules.*.courts.*.endTime' => 'required|date_format:H:i|after:schedules.*.courts.*.startTime',
        ]);
        // Lấy dữ liệu từ request
        $startDate = Carbon::parse($request->input('startDate'));
        $endDate = Carbon::parse($request->input('endDate'));
        $schedules = $request->input('schedules');

        // Mảng ánh xạ từ giá trị số sang tên ngày trong tuần
        $dayMapping = [
            0 => 'Sunday',    // Chủ Nhật
            1 => 'Monday',    // Thứ Hai
            2 => 'Tuesday',   // Thứ Ba
            3 => 'Wednesday', // Thứ Tư
            4 => 'Thursday',  // Thứ Năm
            5 => 'Friday',    // Thứ Sáu
            6 => 'Saturday'   // Thứ Bảy
        ];

        // Bắt đầu transaction
        DB::beginTransaction();
        try {
            // $paymentOption = $request->input('paymentOption'); // Nhận hình thức thanh toán
            // Log::debug($paymentOption);
            // $timestamp = strtotime($request->date); // Chuyển đổi ngày thành timestamp
            // -----kiểm tra ai là người tạo để lấy customerid và customer type id
            if (Auth()->user()->Role == 5) { //là khách hàng
                $customerID = (Customer::where('user_id', Auth()->user()->User_id)->first())->Customer_id;
                // $customer_type_id = (Customer::where('user_id', Auth()->user()->User_id)->first())->customer_type_id;
                $customer_type_id = 2;
            } else { // là chủ sân hoặc nhân viên
                $ten_kh = $request->input('user_name');
                $sdt_kh = $request->input('user_phone');
                $user_id = $request->input('user_id');
                $tien_da_tra = 0; //tiền đã trả
                // Kiểm tra nếu user_id rỗng
                if (empty($user_id)) {
                    $user_new = User::create([
                        'Name' => $ten_kh,
                        'Phone' => $sdt_kh,
                        'Role' => '5',
                    ]);

                    $kh_new = Customer::create([
                        'user_id' => $user_new->User_id,
                        // 'customer_type_id' => 1,
                    ]);

                    $customerID = $kh_new->Customer_id;
                    $customer_type_id = 2; //cố định
                } else {

                    $customerID = (Customer::where('user_id', $user_id)->first())->Customer_id;
                    $customer_type_id = 2;
                }
            }


            $bookings = []; // Mảng chứa thông tin booking đã gộp
            // Log::debug($reservations);
            // Lặp qua từng sân

            // Lặp qua từng ngày trong khoảng từ startDate đến endDate
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dayNumber = $date->dayOfWeek; // Lấy chỉ số ngày trong tuần (0 - 6)
                $dayName = $dayMapping[$dayNumber]; // Lấy tên ngày tương ứng từ mảng ánh xạ

                foreach ($schedules as $schedule) {
                    // Kiểm tra xem chỉ số ngày có khớp với ngày từ dữ liệu đầu vào không
                    if ($schedule['day'] == $dayNumber) {
                        foreach ($schedule['courts'] as $court) {
                            // Sử dụng phương thức dayOfWeek của Carbon
                            if ($date->isWeekday()) {
                                $time_slot_status = 1; // Ngày từ thứ Hai đến thứ Sáu
                            } else {
                                $time_slot_status = 2; // Ngày thứ Bảy hoặc Chủ nhật
                            }
                            // Log::debug([
                            //     'start_date' => $startDate->toDateString(),
                            //     'end_date' => $endDate->toDateString(),
                            //     'day' => $dayName, // Lưu tên ngày
                            //     'court_id' => $court['court'],
                            //     'start_time' => $court['startTime'],
                            //     'end_time' => $court['endTime'],
                            //     'specific_date' => $date->toDateString() // Ngày thực tế
                            // ]);
                            // láy id chi nhánh
                            $branchID = Court::where('Court_id', $court['court'])->first()->branch_id;

                            // lấy tất cả khung giờ theo id chi nhánh
                            $timeslots = TimeSlot::where('branch_id', $branchID)
                                ->where('Status', $time_slot_status)
                                ->orderBy('Start_time', 'asc') // Sắp xếp theo Start_time tăng dần
                                ->get();

                            // -----------end foreach

                            // tạo mảng lưu giá trị có slot để xử lý nếu đặt ở 2 khung giờ khác nhau với giá khác nhau
                            $arraytimeslot = [];

                            foreach ($timeslots as $slot) {
                                $arraytimeslot[] = $slot;

                                // Chuyển đổi thời gian bắt đầu và kết thúc của khung giờ thành định dạng Carbon
                                $startTime = Carbon::createFromFormat('H:i:s', $slot->Start_time); // assuming it's stored in 'H:i:s' format
                                $endTime = Carbon::createFromFormat('H:i:s', $slot->End_time);
                                $courtStartTime = Carbon::createFromFormat('H:i', $court['startTime']);
                                $courtEndTime = Carbon::createFromFormat('H:i', $court['endTime']);

                                if ($courtStartTime->greaterThanOrEqualTo($startTime) && $courtEndTime->lessThanOrEqualTo($endTime)) {
                                    $timeslot_id = $slot->Time_slot_id; // Ghi lại khung giờ phù hợp
                                    break; // Thoát khỏi vòng lặp nếu đã tìm thấy khung giờ phù hợp
                                }
                            }

                            if (isset($timeslot_id)) {
                                // lấy ra bảng giá phù hợp
                                $pricelist = PriceList::where('time_slot_id', $timeslot_id)->where('customer_type_id', $customer_type_id)->first();
                                // lấy max booking_code để tạo bookingcode mới 
                                $maxBookingCode = Booking::max('booking_code') ?? 0;
                                $bookingcodeNew = $maxBookingCode + 1;

                                // Thêm booking cuối cùng vào mảng
                                $bookings[] = [
                                    'court_id' => $court['court'],
                                    'Date_booking' => $date->toDateString(),
                                    'Start_time' => $courtStartTime->format('H:i'),
                                    'End_time' => $courtEndTime->format('H:i'),
                                    'Status' => 2,
                                    'time_slot_id' => $timeslot_id,
                                    'customer_id' => $customerID,
                                    'price_list_id' => $pricelist->Price_list_id,
                                    'branch_id' => $branchID,
                                    'booking_code' => $bookingcodeNew
                                ];
                            } else {
                                // nếu giờ đặt nằm ở 2 khung giờ thì tách ra 2 booking
                                for ($i = 0; $i < count($arraytimeslot); $i++) {
                                    $slottemp = $arraytimeslot[$i];
                                    if (
                                        $courtStartTime->greaterThanOrEqualTo($slottemp->Start_time)
                                        && $courtEndTime->greaterThan($slottemp->End_time)
                                        && $courtEndTime->lessThan($arraytimeslot[$i + 1]->End_time)
                                    ) {
                                        // lấy max booking_code để tạo bookingcode mới 
                                        $maxBookingCode = Booking::max('booking_code') ?? 0;
                                        $bookingcodeNew = $maxBookingCode + 1;
                                        // Log::debug('code:', [$bookingcodeNew]);
                                        // lấy ra bảng giá phù hợp
                                        $pricelist1 = PriceList::where('time_slot_id', $arraytimeslot[$i]->Time_slot_id)->where('customer_type_id', $customer_type_id)->first();
                                        $pricelist2 = PriceList::where('time_slot_id', $arraytimeslot[$i + 1]->Time_slot_id)->where('customer_type_id', $customer_type_id)->first();
                                        $bookings[] = [
                                            'court_id' => $court['court'],
                                            'Date_booking' => $date->toDateString(),
                                            'Start_time' => $courtStartTime->format('H:i'),
                                            'End_time' => $arraytimeslot[$i]->End_time,
                                            'Status' => 2,
                                            'time_slot_id' => $arraytimeslot[$i]->Time_slot_id,
                                            'customer_id' => $customerID,
                                            'price_list_id' => $pricelist1->Price_list_id,
                                            'branch_id' => $branchID,
                                            'booking_code' => $bookingcodeNew
                                        ];

                                        $bookings[] = [
                                            'court_id' => $court['court'],
                                            'Date_booking' => $date->toDateString(),
                                            'Start_time' => $arraytimeslot[$i]->End_time,
                                            'End_time' => $courtEndTime->format('H:i'),
                                            'Status' => 2,
                                            'time_slot_id' => $arraytimeslot[$i + 1]->Time_slot_id,
                                            'customer_id' => $customerID,
                                            'price_list_id' => $pricelist2->Price_list_id,
                                            'branch_id' => $branchID,
                                            'booking_code' => $bookingcodeNew
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // -------------------
            // Lưu các booking vào cơ sở dữ liệu
            foreach ($bookings as $booking) {
                $bookingcreate = Booking::create($booking); // Tạo bản ghi mới trong bảng bookings
                $price_list = PriceList::where('Price_list_id', $booking['price_list_id'])->first();
                // Tính tổng tiền
                $total = $this->calculatePrice($price_list->Price, $booking['Start_time'], $booking['End_time']);

                // -----kiểm tra ai là người tạo để lấy customerid và customer type id
                if (Auth()->user()->Role == 5) { //là khách hàng
                    Payment::create([
                        'Amount' => $total,
                        'Payment_method' => 'Bank',
                        'Debt' => $total, //còn nợ
                        'Paid' => 0,
                        'Status' => 0,
                        'branch_id' => $booking['branch_id'],
                        'booking_id' => $bookingcreate->Booking_id
                    ]);
                } else { // là chủ sân hoặc nhân viên
                    $con_no = $total - $tien_da_tra;
                    if ($con_no == 0) { // trả đủ rồi
                        $status = 1;
                    } else {
                        $status = 0; //chưa trả đủ
                    }
                    Payment::create([
                        'Amount' => $total,
                        'Payment_method' => 'Bank',
                        'Debt' => $con_no,
                        'Paid' => $tien_da_tra,
                        'Status' => $status,
                        'branch_id' => $booking['branch_id'],
                        'booking_id' => $bookingcreate->Booking_id
                    ]);
                }
            }

            // Commit transaction nếu mọi thứ thành công
            DB::commit();
            // Log::debug($total);

            // Trả về phản hồi cho client
            // return response()->json(['success' => true, 'total' => $total]);
            // Lấy ngày hiện tại
            $date = now()->format('Y-m-d'); // Định dạng ngày là YYYY-MM-DD
            // Trả về thông tin thành công
            // Lưu trữ thông báo vào session
            session()->flash('success', 'Đặt sân cố định thành công');
            return response()->json([
                'success' => true,
                'redirect' => route('booking.calendar', ['date' => $date])
            ]);
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();
            Log::debug(456);
            Log::error('Error occurred while reserving: ' . $e->getMessage());
            Log::error('Booking Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e,
            ]);
            return response()->json(['success' => false, 'message' => 'Đặt sân thất bại.'], 500);
        }
    }



    //  phương thức để tính tiền
    private function calculatePrice($pricePerHour, $startTime, $endTime)
    {
        // Logic tính tiền dựa trên sân, thời gian bắt đầu và kết thúc
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $duration = ($end - $start) / 3600; // Tính thời gian đặt bằng giờ
        return $pricePerHour * $duration; // Tính tổng tiền
    }
}
