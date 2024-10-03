<?php

namespace App\Http\Controllers\price_list;

use App\Http\Controllers\Controller;
use App\Models\PriceList;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceListController extends Controller
{
    public function index()
    {
        // Lấy danh sách sân từ cơ sở dữ liệu và nhóm theo khung giờ
        $list = DB::table('price_list')
            ->join('time_slots', 'price_list.time_slot_id', '=', 'time_slots.Time_slot_id')
            ->where('time_slots.branch_id', session('branch_active')->Branch_id)
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

        // Trả về view danh sách sân
        return view('price_list.index', [
            'list' => $groupedList,
            'title' => 'Danh sách Sân'
        ]);
    }

    // Hiển thị form thêm dữ liệu
    public function create()
    {
        // Lấy tất cả các loại khách hàng và khung giờ để hiển thị trong form
        $customerTypes = \App\Models\CustomerType::all();
        $timeSlots = TimeSlot::all();
        $title = "Thêm bảng giá";

        return view('price_list.create', compact('customerTypes', 'timeSlots', 'title'));
    }

    // Xử lý lưu dữ liệu thêm
    public function store(Request $request)
    {
        // Xác thực dữ liệu
        $validatedData = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:1,2', // Chọn loại ngày
            'fixed_price' => 'required|numeric|min:0',
            'walk_in_price' => 'required|numeric|min:0',
        ]);

        // Tạo biến nhận model timeslot để lưu dữ liệu
        $timeslot = new TimeSlot();
        // Gán dữ liệu cho biến
        $timeslot->Start_time = $request->start_time;
        $timeslot->End_time = $request->end_time;
        $timeslot->Status = $request->status; // Lấy status từ request
        $timeslot->branch_id = session('branch_active')->Branch_id;
        $timeslot->save();

        // Lấy id của timeslot vừa tạo
        $id_timeslot = $timeslot->Time_slot_id;

        // Tạo biến nhận model price_list cho giá cố định
        $fixedPriceList = new PriceList();
        $fixedPriceList->Price = $request->fixed_price;
        $fixedPriceList->time_slot_id = $id_timeslot;
        $fixedPriceList->customer_type_id = 2; // Giá cố định
        $fixedPriceList->save();

        // Tạo biến nhận model price_list cho giá vãng lai
        $walkInPriceList = new PriceList();
        $walkInPriceList->Price = $request->walk_in_price;
        $walkInPriceList->time_slot_id = $id_timeslot;
        $walkInPriceList->customer_type_id = 1; // Giá vãng lai
        $walkInPriceList->save();

        // Redirect hoặc trả về thông báo thành công
        return response()->json(["message" => "Tạo bảng giá thành công!"]);
    }

    public function edit($id)
    {
        // Tìm time slot từ ID price_list
        $priceListVangLai = PriceList::where('time_slot_id', $id)
            ->where('customer_type_id', 1)
            ->firstOrFail(); // Giá vãng lai

        $priceListCoDinh = PriceList::where('time_slot_id', $id)
            ->where('customer_type_id', 2)
            ->firstOrFail(); // Giá cố định

        // Lấy time slot tương ứng
        $timeSlot = TimeSlot::findOrFail($priceListVangLai->time_slot_id);

        // Trả về view edit với dữ liệu cần thiết
        return view('price_list.edit', [
            'priceListVangLai' => $priceListVangLai,
            'priceListCoDinh' => $priceListCoDinh,
            'timeSlot' => $timeSlot,
            'title' => 'Sửa'
        ]);
    }


    public function update(Request $request, $id)
    {
        // Xác thực dữ liệu đầu vào
        $validatedData = $request->validate([
            'status' => 'required|in:1,2',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'vang_lai_price' => 'required|numeric|min:0',
            'co_dinh_price' => 'required|numeric|min:0',
        ]);
        // Cập nhật status cho time slot
        $timeSlot = TimeSlot::findOrFail($id);
        $timeSlot->Status = $request->status;
        $timeSlot->Start_time = $request->start_time; // Cập nhật thời gian bắt đầu
        $timeSlot->End_time = $request->end_time; // Cập nhật thời gian kết thúc
        $timeSlot->save();

        // Cập nhật giá vãng lai (customer_type_id = 1)
        $priceListVangLai = PriceList::where('time_slot_id', $id)
            ->where('customer_type_id', 1)
            ->firstOrFail();
        $priceListVangLai->Price = $request->vang_lai_price;
        $priceListVangLai->save();

        // Cập nhật giá cố định (customer_type_id = 2)
        $priceListCoDinh = PriceList::where('time_slot_id', $id)
            ->where('customer_type_id', 2)
            ->firstOrFail();
        $priceListCoDinh->Price = $request->co_dinh_price;
        $priceListCoDinh->save();

        // Chuyển hướng và hiển thị thông báo thành công
        return redirect()->route('price_list.index')->with('success', 'Cập nhật bảng giá thành công!');
    }


    public function destroy($id)
    {
        $Timeslot = Timeslot::findOrFail($id);
        $Timeslot->delete();

        return redirect()->route('price_list.index')->with('success', 'Xóa bảng giá thành công');
    }
}
