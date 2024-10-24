<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    // Hiển thị form tạo nhân viên
    public function createStaff()
    {
        $branches = Branch::all(); // Lấy danh sách các chi nhánh để hiển thị trong form
        $title = "Tạo nhân viên";
        return view('staff.create', compact('branches', 'title'));
    }

    public function destroyStaff(Request $req)
    {
        if (!empty($req->input('user_id'))) {
            $user = User::find($req->input('user_id'));
            // Kiểm tra xem người dùng có tồn tại không
            if (!$user) {
                return response()->json([
                    'message' => 'Nhân viên không tồn tại',
                    'error' => 'Nhân viên không tồn tại'
                ], 500);
            }
            $user->delete();
            return response()->json([
                'message' => 'Xóa nhân viên thành công!',
                'branch_id' => $user->User_id
            ], 201);
        }
    }

    public function viewStaff()
    {
        $title = "Danh sách nhân viên";

        // Lấy danh sách nhân viên từ database
        $staffs = User::join('staff', 'users.User_id', 'staff.user_id')
            ->select(
                'users.*',
                'staff.*'
            )->where('staff.branch_id', '=', session('branch_active')->Branch_id)->paginate(10); // Giới hạn 10 bản ghi mỗi trang

        return view('staff.listStaff', compact('staffs', 'title'));
    }

    // Lưu nhân viên mới vào cơ sở dữ liệu
    public function storeStaff(Request $req)
    {
        // Validate dữ liệu
        $req->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'branch_id' => 'required|exists:branches,Branch_id',
        ]);

        // Bắt đầu transaction
        DB::beginTransaction();

        try {
            // Tạo user mới
            $usernew = User::create([
                'Name' => $req->name,
                'Email' => $req->email,
                'Phone' => $req->phone,
                'Address' => $req->address,
                'Role' => '4', // nhân viên chi nhánh
                'password' => bcrypt('123456'),
            ]);

            // tạo nhân viên mới
            Staff::create([
                'user_id' => $usernew->User_id,
                'branch_id' => $req->branch_id,
            ]);

            // Commit transaction nếu không có lỗi
            DB::commit();

            // Chuyển hướng sau khi tạo thành công
            return redirect()->route('manage-branches.createStaff')->with('success', 'Nhân viên đã được thêm thành công!');
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();

            // Ghi lại lỗi và hiển thị thông báo lỗi
            return redirect()->route('employees.create')->with('danger', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function editStaff($id)
    {
        // Lấy thông tin nhân viên theo ID
        $staff = Staff::where('user_id', $id)->first();
        // dd(113);
        $branches = Branch::all(); // Lấy danh sách chi nhánh để hiển thị trong dropdown
        $title = "Sửa nhân viên";
        return view('staff.edit', compact('staff', 'branches', 'title'));
    }

    public function updateStaff(Request $req, $id)
    {
        // Validate dữ liệu
        $req->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id . ',User_id',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'branch_id' => 'required|exists:branches,Branch_id',
        ]);

        // Bắt đầu transaction
        DB::beginTransaction();

        try {
            // Tìm user và cập nhật
            $user = User::where('User_id', $id)->first();
            $user->update([
                'Name' => $req->name,
                'Email' => $req->email,
                'Phone' => $req->phone,
                'Address' => $req->address,
            ]);

            // Cập nhật thông tin nhân viên
            $staff = Staff::where('user_id', $user->User_id)->first();
            $staff->update([
                'branch_id' => $req->branch_id,
            ]);

            // Commit transaction nếu không có lỗi
            DB::commit();

            // Chuyển hướng sau khi cập nhật thành công
            return redirect()->route('manage-branches.viewStaff')->with('success', 'Nhân viên đã được cập nhật thành công!');
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();

            // Ghi lại lỗi và hiển thị thông báo lỗi
            return redirect()->back()->with('danger', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
