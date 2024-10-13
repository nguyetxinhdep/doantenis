<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function showForm()
    {
        return view('user.formRegister', [
            'title' => 'Đăng ký tài khoản'
        ]);
    }


    public function registerStore(Request $request)
    {
        // Kiểm tra hợp lệ của dữ liệu từ form
        $request->validate([
            'Name' => 'required|string|max:255',
            'Phone' => 'required|digits_between:10,11',
            'Email' => 'required|string|email|max:255|unique:users',
            'Password' => 'required|string|confirmed',
        ], [
            'Email.unique' => 'Email đã tồn tại', // thông báo lỗi khi email đã tồn tại
            'Password.confirmed' => 'Mật khẩu xác nhận không khớp',
        ]);

        // Sử dụng transaction để đảm bảo rollback nếu có lỗi xảy ra
        DB::beginTransaction();

        try {
            // Tạo người dùng mới
            $user = User::create([
                'Name' => $request->input('Name'),
                'Phone' => $request->input('Phone'),
                'Email' => $request->input('Email'),
                'password' => bcrypt($request->input('Password')), // Mã hóa mật khẩu
                'Role' => '5', // Gán role mặc định cho người dùng
            ]);

            // Tạo một bản ghi khách hàng
            Customer::create([
                'user_id' => $user->User_id, // Truy cập id của người dùng vừa tạo
                'customer_type_id' => 1, // Loại khách hàng mặc định (vãng lai)
            ]);

            // Nếu tất cả thành công, commit transaction
            DB::commit();

            // Đăng ký thành công, chuyển hướng hoặc trả về thông báo
            return response()->json(['message' => 'Đăng ký tài khoản thành công',], 201);
        } catch (\Exception $e) {
            // Nếu có lỗi, rollback transaction
            DB::rollBack();

            // Trả về thông báo lỗi
            return redirect()->back()->with('error', 'Đăng ký tài khoản thất bại. Vui lòng thử lại.');
        }
    }
}
