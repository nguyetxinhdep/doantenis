<?php

namespace App\Http\Controllers\booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Customer;
use App\Models\PriceList;
use App\Models\TimeSlot;
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
            dd(123);
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

        // -----
        $customerID = (Customer::where('user_id', Auth()->user()->User_id)->first())->Customer_id;
        $customer_type_id = (Customer::where('user_id', Auth()->user()->User_id)->first())->customer_type_id;

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
            $timeslots = TimeSlot::where('branch_id', $branchID)->where('Status', $time_slot_status)->get();

            // Lặp qua từng khoảng thời gian
            foreach ($times as $key => $time) {
                if ($time['timeStart']->lessThanOrEqualTo($mergedEnd)) {
                    // Nếu thời gian tiếp theo liên tục với thời gian trước đó
                    $mergedEnd = max($mergedEnd, $time['timeEnd']);
                } else {
                    // ---thao tác để ra timeslot mong muốn
                    // Lặp qua từng khung giờ để kiểm tra
                    foreach ($timeslots as $slot) {
                        // Chuyển đổi thời gian bắt đầu và kết thúc của khung giờ thành định dạng Carbon
                        $startTime = Carbon::createFromFormat('H:i:s', $slot->start_time); // assuming it's stored in 'H:i:s' format
                        $endTime = Carbon::createFromFormat('H:i:s', $slot->end_time);

                        // Kiểm tra nếu khoảng thời gian nhận được nằm trong khoảng từ start_time đến end_time
                        if ($mergedStart->greaterThanOrEqualTo($startTime) && $mergedEnd->lessThanOrEqualTo($endTime)) {
                            $timeslot_id = $slot->Time_slot_id; // Ghi lại khung giờ phù hợp
                            break; // Thoát khỏi vòng lặp nếu đã tìm thấy khung giờ phù hợp
                        }
                    }

                    // lấy ra bảng giá phù hợp
                    $pricelist = PriceList::where('time_slot_id', $timeslot_id)->where('customer_type_id', $customer_type_id)->first();


                    // Nếu không liên tục, lưu thông tin booking và khởi tạo thời gian mới
                    $bookings[] = [
                        'court_id' => $courtId,
                        'Date_booking' => $request->date,
                        'Start_time' => $mergedStart->format('H:i'),
                        'End_time' => $mergedEnd->format('H:i'),
                        'Status' => 0,
                        'time_slot_id' => $timeslot_id,
                        'customer_id' => $customerID,
                        'price_list_id' => $pricelist->Price_list_id,
                        'branch_id' => $branchID
                    ];
                    $mergedStart = $time['timeStart'];
                    $mergedEnd = $time['timeEnd'];
                }
            }
            // -----------end foreach

            foreach ($timeslots as $slot) {
                // Chuyển đổi thời gian bắt đầu và kết thúc của khung giờ thành định dạng Carbon
                $startTime = Carbon::createFromFormat('H:i:s', $slot->Start_time); // assuming it's stored in 'H:i:s' format
                $endTime = Carbon::createFromFormat('H:i:s', $slot->End_time);

                // Kiểm tra nếu khoảng thời gian nhận được nằm trong khoảng từ start_time đến end_time
                if ($mergedStart->greaterThanOrEqualTo($startTime) && $mergedEnd->lessThanOrEqualTo($endTime)) {
                    $timeslot_id = $slot->Time_slot_id; // Ghi lại khung giờ phù hợp
                    break; // Thoát khỏi vòng lặp nếu đã tìm thấy khung giờ phù hợp
                }
            }

            // lấy ra bảng giá phù hợp
            $pricelist = PriceList::where('time_slot_id', $timeslot_id)->where('customer_type_id', $customer_type_id)->first();

            // Thêm booking cuối cùng vào mảng
            $bookings[] = [
                'court_id' => $courtId,
                'Date_booking' => $request->date,
                'Start_time' => $mergedStart->format('H:i'),
                'End_time' => $mergedEnd->format('H:i'),
                'Status' => 0,
                'time_slot_id' => $timeslot_id,
                'customer_id' => $customerID,
                'price_list_id' => $pricelist->Price_list_id,
                'branch_id' => $branchID
            ];
        }

        $total = 0;

        // Bắt đầu transaction
        DB::beginTransaction();
        try {
            // Lưu các booking vào cơ sở dữ liệu
            foreach ($bookings as $booking) {
                Booking::create($booking); // Tạo bản ghi mới trong bảng bookings
                $price_list = PriceList::where('Price_list_id', $booking['price_list_id'])->first();
                // Tính tổng tiền
                $total += $this->calculatePrice($price_list->Price, $booking['Start_time'], $booking['End_time']);
            }

            // Commit transaction nếu mọi thứ thành công
            DB::commit();
            Log::debug($total);

            // Trả về phản hồi cho client
            return response()->json(['success' => true, 'total' => $total]);
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();
            Log::error('Error occurred while reserving: ' . $e->getMessage());

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
