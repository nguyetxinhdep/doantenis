<?php

namespace App\Http\Controllers\booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Branch;
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
    public function layBangGia($branch_id)
    {
        // Lấy danh sách sân từ cơ sở dữ liệu và nhóm theo khung giờ
        $list = DB::table('price_list')
            ->join('time_slots', 'price_list.time_slot_id', '=', 'time_slots.Time_slot_id')
            ->where('time_slots.branch_id', $branch_id)
            ->select(
                'time_slots.Start_time',
                'time_slots.End_time',
                'price_list.Price',
                'price_list.customer_type_id',
                'price_list.Price_list_id',
                'price_list.time_slot_id',
                'time_slots.Status'
            )
            ->get();

        // Nhóm các bản ghi theo khung giờ
        $groupedList = $list->groupBy(function ($item) {
            return $item->Start_time . ' - ' . $item->End_time; // Nhóm theo khung giờ
        });

        return $groupedList;
    }

    public function bookingCalendar($date)
    {
        // Lấy danh sách sân và giờ đã đặt(bảng booking)
        $courts = Court::where('branch_id', session('branch_active')->Branch_id)->where('Availability',  1)->get();
        $bookings = Booking::where('branch_id', session('branch_active')->Branch_id)
            ->where('Date_booking', $date) // Lấy tất cả đặt sân
            ->where('Status', '!=', 3)->get();
        $title = "Lịch";

        return view('booking.calendar', compact('courts', 'bookings', 'title'));
    }

    public function lichTheoNgay($date)
    {
        // Lấy danh sách sân và giờ đã đặt(bảng booking)
        $courts = Court::where('branch_id', session('branch_active')->Branch_id)->get();
        $bookings = Booking::where('branch_id', session('branch_active')->Branch_id)
            ->where('Date_booking', $date) // Lấy tất cả đặt sân
            ->where('Status', '!=', 3)->get();
        $title = "Lịch";

        return view('booking.lichTheoNgay', compact('courts', 'bookings', 'title'));
    }



    // controller xử lý hiển thị lịch cho khách hàng xem
    public function bookingCalendarWelcome(Request $req)
    {
        // Tạo biến date với ngày hiện tại
        $date = date('Y-m-d'); // Định dạng ngày hiện tại là Y-m-d
        $branch_id = $req->input('branch_id');

        // Lấy danh sách sân và giờ đã đặt(bảng booking)
        $courts = Court::where('branch_id', $branch_id)->where('Availability',  1)->get();
        $bookings = Booking::where('branch_id', $branch_id)
            ->where('Date_booking', $date) // Lấy tất cả đặt sân
            ->where('Status', '!=', 3)->get();
            

        // giát tiền
        $groupedList = $this->layBangGia($branch_id);
        // sân
        $branch = Branch::where('branch_id', $req->branch_id)->first();

        $title = "Lịch";

        return view('booking.calendarWelcome', compact('courts', 'bookings', 'title', 'groupedList', 'branch'));
    }

    public function bookingCalendarSearch(Request $req)
    {
        if (isset($req->date)) {
            $dateSearch = $req->date;
            // Lấy danh sách sân và giờ đã đặt(bảng booking)
            $courts = Court::where('branch_id', session('branch_active')->Branch_id)->where('Availability',  1)->get();
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
            $courts = Court::where('branch_id', $req->branch_id)->where('Availability',  1)->get();
            $bookings = Booking::where('branch_id', $req->branch_id)
                ->where('Date_booking', $dateSearch)->get(); // Lấy tất cả đặt sân
            $title = "Lịch";

            // giát tiền
            $groupedList = $this->layBangGia($req->branch_id);

            // sân
            $branch = Branch::where('branch_id', $req->branch_id)->first();

            return view('booking.calendarWelcome', compact('courts', 'bookings', 'title', 'branch', 'groupedList'));
        } else {
            return abort(404);
        }
    }

    public function bookingHistory(Request $req)
    {
        $title = "Lịch sử đặt sân";

        // Tạo đối tượng query ban đầu
        $history = Booking::join('customers', 'bookings.customer_id', '=', 'customers.Customer_id')
            ->join('users', 'users.User_id', '=', 'customers.user_id')
            ->join('courts', 'bookings.court_id', '=', 'courts.Court_id')
            ->join('branches', 'courts.branch_id', '=', 'branches.Branch_id') // Join bảng branches
            ->join('payments', 'bookings.Booking_id', '=', 'payments.booking_id')
            ->where('users.User_id', Auth()->user()->User_id)
            ->select(
                'bookings.booking_code',
                // 'bookings.Booking_id',
                DB::raw('GROUP_CONCAT(bookings.Date_booking SEPARATOR ", ") as Date_booking'),
                DB::raw('GROUP_CONCAT(bookings.Start_time SEPARATOR ", ") as Start_time'),
                DB::raw('GROUP_CONCAT(bookings.End_time SEPARATOR ", ") as End_time'),
                DB::raw('MAX(bookings.Status) as Status'),
                DB::raw('MAX(bookings.branch_id) as branch_id'),
                DB::raw('MAX(users.Name) as user_name'),
                DB::raw('MAX(users.Phone) as user_phone'),
                DB::raw('GROUP_CONCAT(courts.name SEPARATOR ", ") as court_name'),
                DB::raw('MAX(branches.Name) as branch_address'), // Lấy địa chỉ của chi nhánh
                DB::raw('SUM(payments.Amount) as Amount'),
                DB::raw('SUM(payments.Paid) as Paid'),
                DB::raw('SUM(payments.Debt) as Debt'),
                DB::raw('GROUP_CONCAT(payments.Payment_id SEPARATOR ", ") as Payment_id'),
                DB::raw('MAX(bookings.created_at) as created_at') // Thêm trường created_at để sắp xếp
            )
            ->groupBy('bookings.booking_code', 'bookings.Date_booking') // Nhóm theo booking_code
            ->orderBy('created_at', 'desc'); // Sắp xếp theo thời gian tạo mới nhất


        // lấy branch_id trong booking theo customer id
        $bookingtemp = Booking::where('customer_id', session('customer_id'))
            ->select('branch_id')
            ->distinct()
            ->get();

        // Trích xuất mảng các branch_id từ $bookingtemp
        $branchIds = $bookingtemp->pluck('branch_id');

        // Lấy danh sách các Branch tương ứng với các branch_id duy nhất
        $branches = Branch::whereIn('Branch_id', $branchIds)->get();

        // dd($branchIds);
        // In ra câu truy vấn SQL
        // dd($history->toSql(), $history->getBindings());
        // Tìm kiếm theo số điện thoại (loại bỏ số 0 đầu tiên)
        if ($req->filled('phone')) {
            $phone = ltrim($req->phone, '0');
            $history->where('users.Phone', 'like', '%' . $phone . '%');
        }

        // Tìm kiếm theo tên
        if ($req->filled('name')) {
            $history->where(
                'users.Name',
                'like',
                '%' . $req->name . '%'
            );
        }

        // Tìm kiếm theo ngày đặt
        if ($req->filled('date')) {
            $history->whereDate(
                'bookings.Date_booking',
                $req->date
            );
        }

        // Tìm kiếm theo trạng thái
        if ($req->filled('status')) {
            $history->where('bookings.Status', $req->status);
        }

        // Thêm điều kiện tìm kiếm theo tên nếu có
        if ($req->filled('branch')) {
            $history->where('branches.Name', 'LIKE', '%' . $req->branch . '%');
        }

        // Thực hiện phân trang với 10 bản ghi trên mỗi trang
        $history = $history->paginate(10);

        return view('booking.bookingHistory', compact('history', 'title', 'branches'));
    }

    public function xuLyThanhToanTC(Request $req)
    {
        DB::beginTransaction(); // Bắt đầu transaction

        try {
            // kiểm tra kết quả thanh toán từ momo
            // $resultCode = $req->input('resultCode');
            // xử lý thanh toán từ vnpay
            $transactionStatus = $req->input('vnp_TransactionStatus');
            //$resultCode == 0 nếu thanh toán thành công
            if ($transactionStatus == '00') {
                // Lấy thông tin từ request
                $Payment_id = explode(',', $req->input('Payment_id')); // Tách các Payment_id thành mảng

                foreach ($Payment_id as $id) {
                    // Tìm payment record theo từng Payment_id
                    $payment = Payment::find($id);

                    if (!$payment) {
                        // Trả về thông báo lỗi nếu không tìm thấy payment record nào trong danh sách
                        return redirect()->back()->with('danger', 'Không tìm thấy thông tin thanh toán');
                    }

                    if ($req->pay == 'half') {
                        // Tính toán lại số tiền còn nợ và số tiền đã trả
                        $newDebt = $payment->Debt - ($payment->Debt) / 2;
                        $newPaid = $payment->Paid + ($payment->Debt) / 2; // Số tiền đã trả
                    } elseif ($req->pay == 'full') {

                        // Tính toán lại số tiền còn nợ và số tiền đã trả
                        $newDebt = $payment->Debt - $payment->Debt;
                        $newPaid = $payment->Paid + $payment->Debt; // Số tiền đã trả
                    }

                    // Cập nhật lại số tiền nợ và số tiền đã trả trong bảng payments
                    $payment->Debt = $newDebt; // Debt không thể nhỏ hơn 0
                    $payment->Paid = $newPaid; // Cập nhật số tiền đã trả

                    if ($newDebt <= 0) {
                        $payment->Status = 1; // Đánh dấu hết nợ nếu đã thanh toán đủ
                    }
                    $payment->Payment_date = now();
                    $payment->save(); // Lưu lại từng bản ghi payment


                    // tìm booking 
                    $booking = Booking::find($payment->booking_id);

                    if ($newDebt <= 0) { // Nếu số tiền đã trả hết, cập nhật trạng thái đã trả đủ
                        if ($booking) {
                            $booking->Status = 1; // Cập nhật trạng thái đã thanh toán đủ
                            $booking->save();
                        }
                    } else {
                        if ($booking) {
                            $booking->Status = 0; // Cập nhật trạng thái chưa thu đủ
                            $booking->save();
                        }
                    }
                }

                DB::commit(); // Commit transaction
                return redirect()->route('booking.history')->with('success', 'Thanh toán thành công.');
            } else {
                return redirect()->route('booking.history')->with('danger', 'Thanh toán thất bại.');
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction nếu có lỗi
            Log::error('Payment processing error: ' . $e->getMessage());

            return redirect()->route('booking.history')->with('error', 'Có lỗi xảy ra trong quá trình thanh toán: ' . $e->getMessage());
        }
    }

    // Hàm xử lý yêu cầu đặt sân bên trang chủ
    public function reserve(Request $request)
    {
        // Xác thực dữ liệu đầu vào: phải có 'selectedCells' và 'date'
        $this->validate($request, [
            'selectedCells' => 'required', // selectedCells phải là một mảng
            'date' => 'required|date', // date phải là một ngày hợp lệ
        ]);
        if (!auth()->check()) {
            return response()->json(['error' => 'Bạn cần đăng nhập để đặt sân.'], 401);
        }
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
            $customerID = (Customer::where('user_id', Auth()->user()->User_id)->first())->Customer_id;
            // $customer_type_id = (Customer::where('user_id', Auth()->user()->User_id)->first())->customer_type_id;
            $customer_type_id = 1; //là vãng lai

            // -----kiểm tra ai là người tạo để lấy customerid và customer type id
            // if (Auth()->user()->Role == 5) { //là khách hàng
            //     $customerID = (Customer::where('user_id', Auth()->user()->User_id)->first())->Customer_id;
            //     // $customer_type_id = (Customer::where('user_id', Auth()->user()->User_id)->first())->customer_type_id;
            //     $customer_type_id = 1;
            // } else { // là chủ sân hoặc nhân viên
            //     $ten_kh = $request->input('name');
            //     $sdt_kh = $request->input('phone');
            //     $tien_da_tra = floatval($request->input('amount')); //tiền đã trả

            //     $user_new = User::create([
            //         'Name' => $ten_kh,
            //         'Phone' => $sdt_kh,
            //         'Role' => '5',
            //     ]);

            //     $kh_new = Customer::create([
            //         'user_id' => $user_new->User_id,
            //         // 'customer_type_id' => 1,
            //     ]);

            //     $customerID = $kh_new->Customer_id;
            //     $customer_type_id = 1;
            // }


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
                Payment::create([
                    'Amount' => $total,
                    'Payment_method' => 'Bank',
                    'Debt' => $total, //còn nợ
                    'Paid' => 0,
                    'Status' => 0,
                    'branch_id' => $booking['branch_id'],
                    'booking_id' => $bookingcreate->Booking_id
                ]);

                // -----kiểm tra ai là người tạo để lấy customerid và customer type id
                // if (Auth()->user()->Role == 5) { //là khách hàng
                //     Payment::create([
                //         'Amount' => $total,
                //         'Payment_method' => 'Bank',
                //         'Debt' => $total, //còn nợ
                //         'Paid' => 0,
                //         'Status' => 0,
                //         'branch_id' => $booking['branch_id'],
                //         'booking_id' => $bookingcreate->Booking_id
                //     ]);
                // } else { // là chủ sân hoặc nhân viên
                //     $con_no = $total - $tien_da_tra;
                //     if ($con_no == 0) { // trả đủ rồi
                //         $status = 1;
                //     } else {
                //         $status = 0; //chưa trả đủ
                //     }
                //     Payment::create([
                //         'Amount' => $total,
                //         'Payment_method' => 'Bank',
                //         'Debt' => $con_no,
                //         'Paid' => $tien_da_tra,
                //         'Status' => $status,
                //         'branch_id' => $booking['branch_id'],
                //         'booking_id' => $bookingcreate->Booking_id
                //     ]);
                // }
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

    // Hàm xử lý yêu cầu đặt sân bên trang quản lý
    public function managerreserve(Request $request)
    {
        // Xác thực dữ liệu đầu vào: phải có 'selectedCells' và 'date'
        $this->validate($request, [
            'selectedCells' => 'required', // selectedCells phải là một mảng
            'date' => 'required|date', // date phải là một ngày hợp lệ
        ]);
        if (!auth()->check()) {
            return response()->json(['error' => 'Bạn cần đăng nhập để đặt sân.'], 401);
        }
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
                // Log::debug($customerID);
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
                // Kiểm tra sự tồn tại của booking
                $existingBooking = Booking::where('court_id', $booking['court_id'])
                    ->where('Date_booking', $booking['Date_booking'])
                    ->where('status', '!=', 3) // Thêm điều kiện status khác 3(3 là sân đã hủy)
                    ->where(function ($query) use ($booking) {
                        $query->whereBetween('Start_time', [$booking['Start_time'], $booking['End_time']])
                            ->orWhereBetween('End_time', [$booking['Start_time'], $booking['End_time']])
                            ->orWhere(function ($query) use ($booking) {
                                $query->where('Start_time', '<=', $booking['Start_time'])
                                    ->where('End_time', '>=', $booking['End_time']);
                            });
                    })
                    ->first();

                if ($existingBooking) {
                    // Sân đã được đặt, thông báo cho người dùng
                    return response()->json(['available' => false, 'message' => ' Đặt sân thất bại. Sân vào ngày ' . $booking['Date_booking'] . ' đã được người khác đặt.'], 409);
                }

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
            // if (Auth()->user()->Role == 5) {
            if ($request->input('khachhang') == 1) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('booking.history')
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'redirect' => route('booking.calendar', ['date' => $date])
                ]);
            }
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();
            // Log::debug(456);
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
