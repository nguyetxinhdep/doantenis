<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        return view('admin.home', [
            'title' => 'Welcome!',
        ]);

        // return view('layouts.app2', [
        //     'title' => 'Trang Quản Trị Admin',
        // ]);
    }
}
