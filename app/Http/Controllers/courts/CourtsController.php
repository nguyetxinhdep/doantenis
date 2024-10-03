<?php

namespace App\Http\Controllers\courts;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourtsController extends Controller
{
    public function index()
    {
        // Lấy danh sách sân từ cơ sở dữ liệu
        $courts = Court::where('branch_id', session('branch_active')->Branch_id)->paginate(5);
        // Log::debug(session('branch_active')->Branch_id);
        // Trả về view danh sách sân
        return view('courts.index', [
            'courts' => $courts,
            'title' => 'Danh sách Sân'
        ]);
    }

    public function show($id)
    {
        // Lấy sân theo ID
        $court = Court::findOrFail($id);

        // Lấy tất cả giờ đã đặt của sân
        $bookings = Booking::where('court_id', $id)
            ->orderby('created_at')
            ->paginate(20);

        // Trả về view chi tiết sân
        return view('courts.show', [
            'court' => $court,
            'title' => 'Chi tiết sân',
            'bookings' => $bookings
        ]);
    }

    public function viewCreate()
    {
        return view('courts.viewCreate', [
            'title' => 'Tạo sân cho chi nhánh'
        ]);
    }

    public function CourtCreate(Request $req)
    {
        if ($req->creationType == 'single') {
            // kiểm tra dữ liệu
            $validatedData = $req->validate(
                [
                    'Name' => 'required',
                ],
                [
                    'Name.required' => 'Tên sân không được trống'
                ]
            );
            // Tạo sân
            $court = new Court(); // tạo  đối tượng từ model Court
            $court->name = $req->Name;
            $court->availability = $req->Availability; // 1 = Hoạt động

            // mã chi nhánh
            $court->branch_id = $req->branch_id;

            $court->save();

            return response()->json(['message' => 'Sân đã được tạo thành công.']);
        } else if ($req->creationType == 'bulk') {
            // kiểm tra dữ liệu
            $validatedData = $req->validate(
                [
                    'minCourts' => 'required',
                    'maxCourts' => 'required',
                ],
                [
                    'minCourts.required' => 'Không được để trống "Từ số"',
                    'maxCourts.required' => 'Không được để trống "Đến số"',
                ]
            );

            // Xử lý tạo hàng loạt sân
            $minCourts = $req->minCourts;
            $maxCourts = $req->maxCourts;

            // Tạo sân từ minCourts đến maxCourts
            for ($i = $minCourts; $i <= $maxCourts; $i++) {
                // Tạo tên sân với định dạng "Sân $i"
                $courtName = "Sân " . $i;

                // Tạo đối tượng sân mới
                $court = new Court(); // tạo  đối tượng từ model Court
                $court->name = $courtName; // Tên sân
                $court->availability = 1; // Trạng thái hoạt động

                // mã chi nhánh
                $court->branch_id = $req->branch_id;

                $court->save(); // Lưu vào cơ sở dữ liệu
            }

            return response()->json(['message' => 'Đã tạo thành công ' . ($maxCourts - $minCourts + 1) . ' sân.']);
        }

        return response()->json(['message' => 'Đã xảy ra lỗi.']);
    }
}
