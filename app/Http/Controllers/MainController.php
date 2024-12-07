<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    // public function index()
    // {
    //     $tongSoHopDong = HopDong::count();
    //     $tongSoNhanVien = NhanVien::count();
    //     $tongSoPhongBan = PhongBan::count();

    //     return view('home', [
    //         'title' => 'Trang Quản Trị Admin',
    //         'tong_so_hop_dong' => $tongSoHopDong,
    //         'tong_so_nhan_vien' => $tongSoNhanVien,
    //         'tong_so_phong_ban' => $tongSoPhongBan,
    //     ]);
    // }

    public function index()
    {
        // if (Auth()->user()->Role == '3') {
        //     $branchId = 26; // Giá trị branch_id cần lọc (có thể thay đổi thành tham số nếu cần)

        //     $result = DB::table('courts')
        //         ->leftJoin('bookings', function ($join) {
        //             $join->on('courts.Court_id', '=', 'bookings.court_id')
        //                 ->whereDate('bookings.Date_booking', '=', now()->toDateString()) // Ngày hiện tại
        //                 ->whereTime('bookings.Start_time', '>=', '18:00:00') // Thời gian bắt đầu >= 18:00
        //                 ->whereTime('bookings.End_time', '<=', '23:30:00'); // Thời gian kết thúc <= 23:30
        //         })
        //         ->select(
        //             'courts.branch_id',
        //             DB::raw('COUNT(DISTINCT CASE WHEN bookings.court_id IS NOT NULL THEN courts.Court_id END) AS courts_booked'),
        //             DB::raw('COUNT(DISTINCT CASE WHEN bookings.court_id IS NULL THEN courts.Court_id END) AS courts_available')
        //         )
        //         ->where('courts.branch_id', $branchId) // Lọc branch_id = 26
        //         ->groupBy('courts.branch_id')
        //         ->get();

        //     return view('admin.home', [
        //         'title' => 'Welcome!',
        //     ]);
        // }

        return view('admin.home', [
            'title' => 'Welcome!',
        ]);

        // return view('layouts.app2', [
        //     'title' => 'Trang Quản Trị Admin',
        // ]);
    }
}
