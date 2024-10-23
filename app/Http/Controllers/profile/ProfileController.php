<?php

namespace App\Http\Controllers\profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Hiển thị form đổi mật khẩu
    public function changePassword()
    {
        $title = "Đổi mật khẩu";
        return view('profile.change-password', compact('title')); // Đường dẫn đến view change-password.blade.php
    }

    // Xử lý cập nhật mật khẩu
    public function updatePassword(Request $req)
    {
        // Validate dữ liệu
        $req->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed' => 'Mật khẩu nhập lại không khớp.'
        ]);

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($req->current_password, Auth()->user()->password)) {
            return redirect()->route('profile.changePassword')->with('danger', 'Mật khẩu hiện tại không đúng');
        }

        // Bắt đầu transaction
        DB::beginTransaction();

        try {
            // Cập nhật mật khẩu mới
            $user = User::where('User_id', Auth()->user()->User_id)->first();
            $user->password = bcrypt($req->new_password);
            $user->save();
            // Commit transaction
            DB::commit();

            // Chuyển hướng sau khi cập nhật thành công
            return redirect()->route('profile.changePassword')->with('success', 'Mật khẩu đã được thay đổi thành công!');
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();

            // Ghi lại lỗi và hiển thị thông báo lỗi
            return redirect()->route('profile.changePassword')->with('danger', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit()
    {
        // Lấy thông tin nhân viên theo ID
        $user = User::where('User_id', Auth()->user()->User_id)->first();
        $title = "Thông tin cá nhân";
        return view('profile.edit', compact('user', 'title'));
    }
    public function updateProfile(Request $req, $id)
    {
        // Validate dữ liệu
        $req->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',User_id',
            'phone' => 'required|string|max:15',
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

            // Commit transaction nếu không có lỗi
            DB::commit();

            // Chuyển hướng sau khi cập nhật thành công
            return redirect()->route('profile.edit')->with('success', 'Thông tin cá nhân đã được cập nhật thành công!');
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();

            // Ghi lại lỗi và hiển thị thông báo lỗi
            return redirect()->route('profile.edit')->with('danger', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
