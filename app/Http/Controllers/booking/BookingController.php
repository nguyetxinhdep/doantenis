<?php

namespace App\Http\Controllers\booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use Illuminate\Http\Request;

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
}
