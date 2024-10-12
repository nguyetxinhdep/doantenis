<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login', [
            'title' => 'Đăng nhập hệ thống'
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email:filter',
            'password' => 'required'
        ]);

        // dd($request);
        if (Auth::attempt([
            'email' => $request->input('email'),
            'password' =>  $request->input('password'),
        ], $request->has('remember'))) {
            // Kiểm tra thêm điều kiện Role = 3 là quản lý thì lấy id chi nhánh lưu vào session
            if (Auth::user()->Role == '3') {
                // Lấy chi nhánh đầu tiên mà người dùng có quyền
                $branches  = User::join('managers', function (JoinClause $join) {
                    $join->on('users.User_id', '=', 'managers.user_id');
                })
                    ->join('branches', function (JoinClause $join) {
                        $join->on('managers.Manager_id', '=', 'branches.manager_id');
                    })
                    ->where('users.User_id', (Auth::user()->User_id))
                    ->where('branches.Status', 3)
                    ->select(
                        'branches.*',
                        'users.User_id as User_id'
                    ) // Chọn các cột cần thiết
                    ->get();

                if ($branches) {
                    // Lưu ID chi nhánh vào session
                    session(['branch_active' => $branches->first()]);
                    session(['all_branch' => $branches]);

                    // Trả về phản hồi đăng nhập thành công
                    return redirect()->route('home')->with('success', 'Đăng nhập thành công');
                }
            }
            // Kiểm tra thêm điều kiện Role
            if (Auth::user()->Role == '5') {
                $customer = Customer::where('user_id', (Auth::user()->User_id))->first();
                session(['customer_id' => $customer->Customer_id]);
                // Nếu role khác 0 thì cho phép vào trang admin
                return redirect()->route('welcome')->with('success', 'Đăng nhập thành công');
            }

            // Kiểm tra thêm điều kiện Role
            if (Auth::user()->Role != '0' && Auth::user()->Role != '-1') {
                // Nếu role khác 0 thì cho phép vào trang admin
                return redirect()->route('home')->with('success', 'Đăng nhập thành công');
            }

            // Nếu role bằng 0, đăng xuất người dùng
            Auth::logout();
            Log::debug('error:Quyền hạn của bạn không đủ để đăng nhập!');
            return redirect()->back();
        }
        Log::debug('error:Tài khoản hoặc mật khẩu không đúng!');
        return redirect()->back();
    }

    public function logout()
    {
        // Xóa tất cả session
        session()->flush();
        Auth::logout();
        return redirect('admin/login'); // Redirect to the login page or any other page you prefer
    }

    public function forgotPass()
    {
        return view('auth.passwords.email', [
            'title' => 'Quên mật khẩu',
        ]);
    }

    public function sendMailCofirm(Request $req)
    {
        $req->validate([
            'email' => 'required|exists:users'
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email hợp lệ',
            'email.exists' => 'Email này không tồn tại trong hệ thống',
        ]);

        $mailkh = $req->input('email');

        $user = User::where('email', $mailkh)->first();

        $token = Str::random(40);
        $user->update(['token_change_pass' => $token]);

        Mail::send('auth.passwords.yeucaudoipass', compact('mailkh', 'user'), function ($email) use ($mailkh) {
            $email->subject('Yêu cầu đổi mật khẩu');
            $email->to($mailkh);
        });

        // Chuyển hướng đến route 'yeucauthanhcong' với thông báo thành công
        return redirect()->route('login')->with('message', 'Email yêu cầu đổi mật khẩu đã được gửi!');
    }

    public function accept(User $id, $token)
    {
        if ($id->token_change_pass === $token) {
            $title = 'Reset pass';
            return view('auth.passwords.confirm', compact('id', 'title', 'token'));
        } else {
            return abort(404);
        }
    }

    public function changPass(Request $req, User $id, $token)
    {
        if ($id->token_change_pass === $token) {
            $req->validate([
                'password' => 'required',
                'confirm-password' => 'required|same:password',
            ]);
            // dd($req->password);
            $pass_new = bcrypt($req->password);
            $id->update([
                'password' => $pass_new,
                'token_change_pass' => null,
            ]);
            return redirect()->route('login')->with('message', 'Đặt lại mật khẩu thành công');
        } else {
            return abort(404);
        }
    }
}
