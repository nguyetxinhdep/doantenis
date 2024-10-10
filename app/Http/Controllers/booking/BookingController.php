<?php

namespace App\Http\Controllers\booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\PriceList;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

    // Hàm xử lý yêu cầu đặt sân
    public function reserve(Request $request)
    {
        // Xác thực dữ liệu đầu vào: phải có 'selectedCells' và 'date'
        $this->validate($request, [
            'selectedCells' => 'required', // selectedCells phải là một mảng
            'date' => 'required|date', // date phải là một ngày hợp lệ
        ]);

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
            // Lặp qua từng khoảng thời gian
            foreach ($times as $key => $time) {
                if ($time['timeStart']->lessThanOrEqualTo($mergedEnd)) {
                    // Nếu thời gian tiếp theo liên tục với thời gian trước đó
                    $mergedEnd = max($mergedEnd, $time['timeEnd']);
                } else {
                    // Nếu không liên tục, lưu thông tin booking và khởi tạo thời gian mới
                    $bookings[] = [
                        'court_id' => $courtId,
                        'Date_booking' => $request->date,
                        'Start_time' => $mergedStart->format('H:i'),
                        'End_time' => $mergedEnd->format('H:i'),
                        'Status' => 0,
                        'time_slot_id' => 3,
                        'customer_id' => 1,
                        'price_list_id' => 4,
                        'branch_id' => 1
                    ];
                    $mergedStart = $time['timeStart'];
                    $mergedEnd = $time['timeEnd'];
                }
            }

            // Thêm booking cuối cùng vào mảng
            $bookings[] = [
                'court_id' => $courtId,
                'Date_booking' => $request->date,
                'Start_time' => $mergedStart->format('H:i'),
                'End_time' => $mergedEnd->format('H:i'),
                'Status' => 0,
                'time_slot_id' => 3,
                'customer_id' => 1,
                'price_list_id' => 4,
                'branch_id' => 1
            ];
        }
        Log::debug($bookings);

        // Lưu các booking vào cơ sở dữ liệu
        foreach ($bookings as $booking) {
            Booking::create($booking); // Tạo bản ghi mới trong bảng bookings
        }

        // Trả về phản hồi cho client
        return response()->json(['success' => true]);
    }



    //  phương thức để tính tiền
    private function calculatePrice($courtId, $startTime, $endTime)
    {
        // Logic tính tiền dựa trên sân, thời gian bắt đầu và kết thúc
        $pricePerHour = 100000; // Giá một giờ (giả sử)
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $duration = ($end - $start) / 3600; // Tính thời gian đặt bằng giờ
        return $pricePerHour * $duration; // Tính tổng tiền
    }
}
