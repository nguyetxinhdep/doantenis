<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Manager;
use App\Models\Staff;
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

    public function index(Request $request)
    {
        $title = "Welcome";

        // Kiểm tra vai trò người dùng
        if (Auth()->user()->Role == '3') {
            // Lấy các tham số từ form
            $date = $request->get('date', now()->toDateString());  // Giá trị mặc định là ngày hiện tại
            $startTime = $request->get('start_time', \Carbon\Carbon::now()->addHours(7)->format('H:i'));
            $endTime = $request->get('end_time', '23:30:00');     // Giá trị mặc định là 23:30
            $branchId = $request->get('branch_id'); // Lọc theo branch_id (chi nhánh)
            $manatemp = Manager::where('user_id', Auth()->user()->User_id)->first();
            $managerId = $manatemp->Manager_id; // Giả định manager_id là 16

            // Truy vấn dữ liệu với các điều kiện
            $result = DB::table('courts')
                ->join('branches', 'courts.branch_id', '=', 'branches.Branch_id')
                ->leftJoin('bookings', function ($join) use ($date, $startTime, $endTime) {
                    $join->on('courts.Court_id', '=', 'bookings.court_id')
                        ->whereDate('bookings.Date_booking', '=', $date)
                        ->whereTime('bookings.Start_time', '>=', $startTime)
                        ->whereTime('bookings.End_time', '<=', $endTime);
                })
                ->selectRaw('
                COUNT(DISTINCT CASE WHEN bookings.court_id IS NOT NULL THEN courts.Court_id END) AS courts_booked,
                COUNT(DISTINCT CASE WHEN bookings.court_id IS NULL THEN courts.Court_id END) AS courts_available
            ')
                ->where('branches.manager_id', $managerId);

            // Lọc thêm theo chi nhánh nếu có
            if ($branchId) {
                $result->where('branches.branch_id', $branchId);
            }

            // Lấy kết quả
            $data = $result->first();

            // Lấy thông tin manager và danh sách chi nhánh của họ
            $manager = Manager::where('user_id', Auth()->user()->User_id)->first();
            $branches = Branch::where('manager_id', $manager->Manager_id)->get();

            // Nhận năm và danh sách branch_id từ request (hoặc gán mặc định)
            $year = $request->input('year', date('Y'));
            $branchId = $request->input('branch_id');

            // Lấy dữ liệu doanh thu
            $query = DB::table('payments as p')
                ->join('bookings as bk', 'p.booking_id', '=', 'bk.Booking_id')
                ->join('courts as c', 'bk.court_id', '=', 'c.Court_id')
                ->join(
                    'branches as b',
                    'c.branch_id',
                    '=',
                    'b.Branch_id'
                )
                ->selectRaw('
                    MONTH(bk.Date_booking) as month,
                    YEAR(bk.Date_booking) as year,
                    SUM(p.Amount) as total_revenue
                ')
                ->whereYear('bk.Date_booking', $year)
                ->where('b.manager_id', $managerId)
                ->where('p.Status', 1);

            if ($branchId) {
                $query->where('b.Branch_id', $branchId);
            }

            $revenues = $query
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();

            // Chuẩn bị dữ liệu cho biểu đồ
            $chartData = [
                'labels' => $revenues->pluck('month')->map(function ($month) {
                    return 'Tháng ' . $month;
                })->toArray(),
                'data' => $revenues->pluck('total_revenue')->toArray()
            ];

            // Lấy danh sách địa điểm
            $branches = DB::table('branches')
                ->select('Branch_id', 'Name')
                ->where('manager_id', $manager->Manager_id)
                ->get();

            // dd($branches);


            return view('admin.home', compact('title', 'branches', 'data', 'date', 'startTime', 'endTime', 'branchId', 'chartData'));
        } elseif (Auth()->user()->Role == '4') {
            // Lấy các tham số từ form
            $date = $request->get('date', now()->toDateString());  // Giá trị mặc định là ngày hiện tại
            $startTime = $request->get('start_time', \Carbon\Carbon::now()->addHours(7)->format('H:i'));
            $endTime = $request->get('end_time', '23:30:00');     // Giá trị mặc định là 23:30
            $branchId = $request->get('branch_id'); // Lọc theo branch_id (chi nhánh)
            $staff = Staff::where('user_id', Auth()->user()->User_id)->first();
            $branch_id = $staff->branch_id; // Giả định manager_id là 16

            // Truy vấn dữ liệu với các điều kiện
            $result = DB::table('courts')
                ->join('branches', 'courts.branch_id', '=', 'branches.Branch_id')
                ->leftJoin('bookings', function ($join) use ($date, $startTime, $endTime) {
                    $join->on('courts.Court_id', '=', 'bookings.court_id')
                        ->whereDate('bookings.Date_booking', '=', $date)
                        ->whereTime('bookings.Start_time', '>=', $startTime)
                        ->whereTime('bookings.End_time', '<=', $endTime);
                })
                ->selectRaw('
                COUNT(DISTINCT CASE WHEN bookings.court_id IS NOT NULL THEN courts.Court_id END) AS courts_booked,
                COUNT(DISTINCT CASE WHEN bookings.court_id IS NULL THEN courts.Court_id END) AS courts_available
            ')
                ->where('branches.Branch_id', $branch_id);

            // Lọc thêm theo chi nhánh nếu có
            // if ($branchId) {
            //     $result->where('branches.branch_id', $branchId);
            // }

            // Lấy kết quả
            $data = $result->first();

            // Lấy thông tin manager và danh sách chi nhánh của họ
            // $manager = Manager::where('user_id', Auth()->user()->User_id)->first();
            $branches = Branch::where('Branch_id', $staff->branch_id)->get();

            // Nhận năm và danh sách branch_id từ request (hoặc gán mặc định)
            $year = $request->input('year', date('Y'));
            $branchId = $request->input('branch_id');

            // Lấy dữ liệu doanh thu
            $query = DB::table('payments as p')
                ->join('bookings as bk', 'p.booking_id', '=', 'bk.Booking_id')
                ->join('courts as c', 'bk.court_id', '=', 'c.Court_id')
                ->join(
                    'branches as b',
                    'c.branch_id',
                    '=',
                    'b.Branch_id'
                )
                ->selectRaw('
                    MONTH(bk.Date_booking) as month,
                    YEAR(bk.Date_booking) as year,
                    SUM(p.Amount) as total_revenue
                ')
                ->whereYear('bk.Date_booking', $year)
                ->where('b.Branch_id', $staff->branch_id)
                ->where('p.Status', 1);

            // if ($branchId) {
            //     $query->where('b.Branch_id', $branchId);
            // }

            $revenues = $query
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();

            // Chuẩn bị dữ liệu cho biểu đồ
            $chartData = [
                'labels' => $revenues->pluck('month')->map(function ($month) {
                    return 'Tháng ' . $month;
                })->toArray(),
                'data' => $revenues->pluck('total_revenue')->toArray()
            ];

            // Lấy danh sách địa điểm
            $branches = DB::table('branches')
                ->select('Branch_id', 'Name')
                ->where('Branch_id', $staff->branch_id)
                ->get();


            return view('admin.home', compact('title', 'branches', 'data', 'date', 'startTime', 'endTime', 'branchId', 'chartData'));
        }

        return view('admin.home', ['title' => 'Trang Quản Trị Admin']);
    }
}
