<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\AdminSub;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Manager;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    // -----------khách hàng------------
    public function indexkhachhang()
    {
        $accounts = User::whereNotNull('email')->where('email', '!=', '')
            ->where('Role', '5')->get(); // Lấy thông tin tài khoản
        $title = "Danh sách khách hàng";
        return view('user.khachhang.index', compact('accounts', 'title'));
    }

    public function editkhachang($id)
    {
        // Retrieve all accounts
        $account = User::findOrFail($id);

        $title = "Thông tin khách hàng";
        return view('user.khachhang.edit', compact('account', 'title'));
    }

    public function updatekhachhang(Request $request, $id)
    {
        // Xác thực dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id . ',User_id', // Đảm bảo email là duy nhất, ngoại trừ người dùng hiện tại
            'phone' => 'required|max:15', // Giới hạn độ dài số điện thoại
        ]);

        // Tìm người dùng theo ID
        $account = User::findOrFail($id);

        // Cập nhật thông tin người dùng
        $account->Name = $request->name;
        $account->Email = $request->email;
        $account->Phone = $request->phone;

        // Lưu thay đổi
        $account->save();

        // Quay lại với thông báo thành công
        return redirect()->route('admin.account.khachang')->with('success', 'Thông tin tài khoản đã được cập nhật thành công.');
    }

    public function createkhachhang()
    {

        $title = "Tạo khách hàng";
        return view('user.khachhang.create', compact('title'));
    }

    public function storekhachhang(Request $request)
    {
        // Xác thực dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email', // Đảm bảo email là duy nhất
            'phone' => 'required|max:15', // Giới hạn độ dài số điện thoại
        ]);

        // Tạo người dùng mới
        $account = new User();
        $account->Name = $request->name;
        $account->Email = $request->email;
        $account->Phone = $request->phone;
        $account->Role = '5';

        // Lưu thông tin người dùng
        $account->save();

        // Quay lại với thông báo thành công
        return redirect()->route('admin.account.khachang')->with('success', 'Tài khoản đã được thêm thành công.');
    }


    // ------------end khách hàng-------------

    // -----------nhân viên------------
    public function indexnhanvien()
    {
        $accounts = User::whereNotNull('email')->where('email', '!=', '')
            ->where('Role', '4')->get(); // Lấy thông tin tài khoản
        $title = "Danh sách nhanvien";
        return view('user.nhanvien.index', compact('accounts', 'title'));
    }

    public function editnhanvien($id)
    {
        // Retrieve all accounts
        $account = User::findOrFail($id);
        // dd($account);
        $staff = Staff::where('user_id', $id)->first();

        $branches = Branch::where('Status', 3)
            ->join(
                'managers',
                'branches.manager_id',
                '=',
                'managers.Manager_id'
            ) // Join với bảng managers
            ->join('users', 'managers.user_id', '=', 'users.User_id') // Join với bảng users
            ->select(
                'branches.*',
                'users.User_id'
            ) // Chọn tất cả các trường từ bảng branches
            ->get();

        // dd($branches);
        $title = "Thông tin nhân viên";
        return view('user.nhanvien.edit', compact('account', 'branches', 'title', 'staff'));
    }

    public function updatenhanvien(Request $request, $id)
    {
        // Xác thực dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id . ',User_id', // Đảm bảo email là duy nhất, ngoại trừ người dùng hiện tại
            'phone' => 'required|max:15', // Giới hạn độ dài số điện thoại
            'branch_id' => 'required|exists:branches,Branch_id', // Kiểm tra xem chi nhánh có tồn tại không
        ]);

        // Tìm người dùng theo ID
        $account = User::findOrFail($id);

        // Cập nhật thông tin người dùng
        $account->Name = $request->name;
        $account->Email = $request->email;
        $account->Phone = $request->phone;
        // Lưu thay đổi
        $account->save();

        $staff = Staff::where('user_id', $id)->first();
        $staff->branch_id = $request->branch_id;
        $staff->save();

        // Quay lại với thông báo thành công
        return redirect()->route('admin.account.nhanvien')->with('success', 'Thông tin tài khoản đã được cập nhật thành công.');
    }

    public function createnhanvien()
    {
        $branches = Branch::where('Status', 3)->get();
        $title = "Tạo Nhân viên";
        return view('user.nhanvien.create', compact('title', 'branches'));
    }

    public function storenhanvien(Request $request)
    {
        // Xác thực dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email', // Đảm bảo email là duy nhất
            'phone' => 'required|max:15', // Giới hạn độ dài số điện thoại
            'branch_id' => 'required|exists:branches,Branch_id', // Kiểm tra xem chi nhánh có tồn tại không
        ]);

        // Tạo người dùng mới
        $account = new User();
        $account->Name = $request->name;
        $account->Email = $request->email;
        $account->Phone = $request->phone;
        $account->Role = '4';

        // Lưu thông tin người dùng
        $account->save();

        $staff = new Staff();
        $staff->branch_id = $request->branch_id;
        $staff->user_id = $account->User_id;
        $staff->save();

        // Quay lại với thông báo thành công
        return redirect()->route('admin.account.nhanvien')->with('success', 'Tài khoản đã được thêm thành công.');
    }

    // ------------end nhân viên-------------
    // -----------nhân viên hệ thống------------
    public function indexnhanvienhetong()
    {
        $accounts = User::whereNotNull('email')->where('email', '!=', '')
            ->where('Role', '2')->get(); // Lấy thông tin tài khoản
        $title = "Danh sách nhân viên hệ thống";
        return view('user.subadmin.index', compact('accounts', 'title'));
    }

    public function editnhanvienhetong($id)
    {
        // Retrieve all accounts
        $account = User::findOrFail($id);
        // dd($account);
        $staff = Staff::where('user_id', $id)->first();

        $branches = Branch::where('Status', 3)
            ->join(
                'managers',
                'branches.manager_id',
                '=',
                'managers.Manager_id'
            ) // Join với bảng managers
            ->join('users', 'managers.user_id', '=', 'users.User_id') // Join với bảng users
            ->select(
                'branches.*',
                'users.User_id'
            ) // Chọn tất cả các trường từ bảng branches
            ->get();

        // dd($branches);
        $title = "Thông tin nhân viên hệ thống";
        return view('user.subadmin.edit', compact('account', 'branches', 'title', 'staff'));
    }

    public function updatenhanvienhetong(Request $request, $id)
    {
        // Xác thực dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id . ',User_id', // Đảm bảo email là duy nhất, ngoại trừ người dùng hiện tại
            'phone' => 'required|max:15', // Giới hạn độ dài số điện thoại
            // 'branch_id' => 'required|exists:branches,Branch_id', // Kiểm tra xem chi nhánh có tồn tại không
        ]);

        // Tìm người dùng theo ID
        $account = User::findOrFail($id);

        // Cập nhật thông tin người dùng
        $account->Name = $request->name;
        $account->Email = $request->email;
        $account->Phone = $request->phone;
        // Lưu thay đổi
        $account->save();

        // Quay lại với thông báo thành công
        return redirect()->route('admin.account.nhanvienhetong')->with('success', 'Thông tin tài khoản đã được cập nhật thành công.');
    }

    public function createnhanvienhetong()
    {
        $branches = Branch::where('Status', 3)->get();
        $title = "Tạo Nhân viên hệ thống";
        return view('user.subadmin.create', compact('title', 'branches'));
    }

    public function storenhanvienhetong(Request $request)
    {
        // Xác thực dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email', // Đảm bảo email là duy nhất
            'phone' => 'required|max:15', // Giới hạn độ dài số điện thoại
            // 'branch_id' => 'required|exists:branches,Branch_id', // Kiểm tra xem chi nhánh có tồn tại không
        ]);

        // Tạo người dùng mới
        $account = new User();
        $account->Name = $request->name;
        $account->Email = $request->email;
        $account->Phone = $request->phone;
        $account->password = bcrypt("Tennis@123");
        $account->Role = '2';

        // Lưu thông tin người dùng
        $account->save();

        $staff = new AdminSub();
        // $staff->branch_id = $request->branch_id;
        $staff->user_id = $account->User_id;
        $staff->save();

        // Quay lại với thông báo thành công
        return redirect()->route('admin.account.nhanvienhetong')->with('success', 'Tài khoản đã được thêm thành công.');
    }

    // ------------end nhân viên hệ thống-------------
    // -----------chủ sân------------
    public function indexchusan()
    {
        $accounts = User::whereNotNull('email')->where('email', '!=', '')
            ->where('Role', '3')->get(); // Lấy thông tin tài khoản
        $title = "Danh sách chủ sân";
        return view('user.chusan.index', compact('accounts', 'title'));
    }

    public function editchusan($id)
    {
        // Retrieve all accounts
        $account = User::findOrFail($id);

        // $branches = Branch::where('Status', 3)
        //     ->join(
        //         'managers',
        //         'branches.manager_id',
        //         '=',
        //         'managers.Manager_id'
        //     ) // Join với bảng managers
        //     ->join('users', 'managers.user_id', '=', 'users.User_id') // Join với bảng users
        //     ->select(
        //         'branches.*',
        //         'users.User_id'
        //     ) // Chọn tất cả các trường từ bảng branches
        //     ->get();

        // dd($branches);
        $title = "Thông tin chủ sân";
        return view('user.chusan.edit', compact('account', 'title'));
    }

    public function updatechusan(Request $request, $id)
    {
        // Xác thực dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id . ',User_id', // Đảm bảo email là duy nhất, ngoại trừ người dùng hiện tại
            'phone' => 'required|max:15', // Giới hạn độ dài số điện thoại
        ]);

        // Tìm người dùng theo ID
        $account = User::findOrFail($id);

        // Cập nhật thông tin người dùng
        $account->Name = $request->name;
        $account->Email = $request->email;
        $account->Phone = $request->phone;
        // Lưu thay đổi
        $account->save();

        // Quay lại với thông báo thành công
        return redirect()->route('admin.account.chusan')->with('success', 'Thông tin tài khoản đã được cập nhật thành công.');
    }

    public function createchusan()
    {
        $branches = Branch::where('Status', 3)->get();
        $title = "Tạo Nhân viên";
        return view('user.chusan.create', compact('title', 'branches'));
    }

    public function storechusan(Request $request)
    {
        $this->validate(
            $request,
            [
                'username' => 'required',
                'userphone' => 'required|numeric',
                'useremail' => 'required|email|unique:users,Email',
                'Name' => 'required',
                'Location' => 'required',
                'Phone' => 'required|numeric',
                'Email' => 'required|email|',
            ]
            ,
            [
                'useremail.unique' => 'Email đã tồn tại', // thông báo lỗi khi email đã tồn tại
            ]
        );
        // Start a transaction
        DB::beginTransaction();
        try {
            // Lấy thông tin user hiện tại
            $user = new User();
            $user->Name = $request->username;
            $user->Phone = $request->userphone;
            $user->Email = $request->useremail;
            $user->Role = '3';
            $user->password = bcrypt('Tennis@123');
            $user->save();

            $userId = $user->User_id;
            // $user->Role = '3';
            // $user->save();

            // Tạo đối tượng manager
            $manager = new Manager();
            $manager->Manager_code = 0;
            $manager->user_id = $userId;
            $manager->save();

            // Lấy ID của manager vừa tạo
            $managerID = $manager->Manager_id;

            // Tạo đối tượng branch
            $branch = new Branch();
            $branch->Name = $request->Name;
            $branch->Location = $request->Location;
            $branch->Phone = $request->Phone;
            $branch->Email = $request->Email;
            $branch->manager_id = $managerID;
            $branch->Status = 3;
            $branch->save();

            $admin = true;
            // Commit the transaction nếu không có lỗi
            DB::commit();
            $Email = $request->useremail;
            Mail::send('branch.mailCapTaiKhoan', compact('Email', 'user','admin'), function ($email) use ($Email) {
                $email->subject('Cấp tài khoản');
                $email->to($Email);
            });
            // Return a JSON response
            return response()->json([
                'message' => 'Tạo chủ sân và địa điểm kinh doanh mới thành công',
                'branch' => $branch,
            ], 201); // 201 status code for successful resource creation

        } catch (\Exception $e) {
            // Rollback the transaction nếu có lỗi xảy ra
            DB::rollBack();
            Log::error('Tạo chủ sân và địa điểm kinh doanh mới thất bại: ' . $e->getMessage());

            // Return a JSON response with error
            return response()->json([
                'message' => 'Tạo chủ sân và địa điểm kinh doanh mới thất bại, vui lòng thử lại sau.',
                'error' => $e->getMessage(),
            ], 500); // 500 status code for server error
        }
    }

    // ------------end chủ sân-------------

    public function showChangePasswordForm($id)
    {
        $account = User::findOrFail($id); // Lấy thông tin tài khoản
        $title = "Đổi mật khẩu";
        return view('user.viewchangpass', compact('account', 'title'));
    }

    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => [
                'required',
                'string',
                'min:8', // Mật khẩu mới phải có ít nhất 8 ký tự
                'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>])(?=.*\d).+$/', // Mật khẩu phải có ít nhất 1 chữ in hoa, 1 ký tự đặc biệt và 1 số
            ],
            'password_confirmation' => 'required|string|same:new_password', // Xác nhận mật khẩu phải có độ dài tối thiểu
        ], [
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password.regex' => 'Mật khẩu mới phải bao gồm ít nhất 1 chữ in hoa, 1 ký tự đặc biệt và 1 số.',
            'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu mới.',
            'password_confirmation.same' => 'Mật khẩu xác nhận không khớp.',
        ]);

        // Kiểm tra nếu người dùng không tồn tại
        $user = User::findOrFail($id);

        // Mã hóa mật khẩu mới và lưu vào cơ sở dữ liệu
        $user->password = bcrypt($request->new_password);
        $user->save();

        return redirect()->route('manage-account.viewAll')->with('success', 'Mật khẩu đã được đổi thành công.');
    }


    public function adminshowFormkhachang()
    {
        $title = "Thêm tài khoản khách hàng";
        return view('user.khachhang.index', compact('title')); // Tạo view cho việc thêm tài khoản
    }

    public function showForm()
    {
        return view('user.formRegister', [
            'title' => 'Đăng ký tài khoản'
        ]);
    }

    public function viewAll(Request $request)
    {
        // Retrieve all accounts
        $accounts = User::whereNotNull('email')->where('email', '!=', '');
        if ($request->filled('phone')) {
            $phone = ltrim($request->phone, '0');
            $accounts->where('Phone', 'like', '%' . $phone . '%');
        }

        // Tìm kiếm theo tên
        if ($request->filled('name')) {
            $accounts->where('Name', 'like', '%' . $request->name . '%');
        }

        // Tìm kiếm theo email
        if ($request->filled('email')) {
            $accounts->where('Email', 'like', '%' . $request->email . '%');
        }

        // Tìm kiếm theo role
        if ($request->filled('role')) {
            $accounts->where('Role', 'like', '%' . $request->role . '%');
        }
        $accounts = $accounts->get();
        // dd($accounts);
        $title = "Danh sách";
        // Return view with accounts data
        return view('user.viewAll', compact('accounts', 'title'));
    }

    public function updateaccount(Request $request, $id)
    {
        // Bắt đầu giao dịch
        DB::beginTransaction();

        try {
            // Xác thực dữ liệu đầu vào
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:15',
                'role' => 'required|integer|in:1,2,3,4,5',
            ]);

            // Tìm tài khoản người dùng theo ID
            $account = User::findOrFail($id);

            // Cập nhật thông tin tài khoản
            $account->Name = $validatedData['name'];
            $account->Email = $validatedData['email'];
            $account->Phone = $validatedData['phone'];
            $account->Role = $validatedData['role'];

            // Cập nhật branch_id chỉ khi người dùng có vai trò là "Chủ sân" 
            if (in_array($validatedData['role'], ['3'])) {
                if ($request->has('branch_ids')) {
                    // Tìm manager tương ứng với branch_id
                    $manager = Manager::where('user_id', $id)->first();

                    // Nếu manager không tồn tại, tạo mới một manager
                    if (!$manager) {
                        $manager = new Manager();
                        $manager->user_id = $account->User_id; // Gán user_id cho manager
                        $manager->Manager_code = 0; //
                        $manager->save(); // Lưu manager vào cơ sở dữ liệu
                    }

                    // Gán manager cho user
                    // $branch = Branch::where('Branch_id', $validatedData['branch_id'])->first();
                    // $branch->manager_id = $manager_id; // Lưu branch_id
                    // $branch->save();
                    // Xóa liên kết với các branch cũ
                    // Branch::where('manager_id', $manager->Manager_id)->update(['manager_id' => null]);

                    // Lấy danh sách các chi nhánh được chọn

                    Branch::whereIn('Branch_id', $request->branch_ids)->update(['manager_id' => $manager->Manager_id]);

                    // Kiểm tra các manager không còn quản lý chi nhánh nào
                    $managersWithoutBranches = Manager::doesntHave('branches')->pluck('Manager_id');

                    // Xuất thông báo cho các manager không còn quản lý chi nhánh nào
                    foreach ($managersWithoutBranches as $managerId) {
                        $managernew = Manager::where('Manager_id', $managerId)->first();
                        $usernew = User::where('User_id', $managernew->user_id)->first();
                        $usernew->Role = '5';
                        $usernew->save();
                        $managernew->delete();
                    }
                }
            } elseif (in_array($validatedData['role'], ['4'])) { //hoặc "Nhân viên"
                $staff = Staff::where('user_id', $id)->first();

                // Nếu manager không tồn tại, tạo mới một manager
                if (!$staff) {
                    $staff = new Staff();
                    $staff->user_id = $account->User_id; // Gán user_id cho manager
                    $staff->branch_id = $request->branch_id; //
                }

                $staff->branch_id = $request->branch_id; //
                $staff->save(); // Lưu manager vào cơ sở dữ liệu
            } elseif (in_array($validatedData['role'], ['5'])) { //hoặc "Nhân viên"
                $cus = Customer::where('user_id', $id)->first();

                // Nếu manager không tồn tại, tạo mới một manager
                if (!$cus) {
                    $cus = new Customer();
                    $cus->user_id = $account->User_id; // Gán user_id cho manager
                }

                $cus->user_id = $account->User_id; // Gán user_id cho manager
                $cus->save(); // Lưu manager vào cơ sở dữ liệu
            } elseif (in_array($validatedData['role'], ['2'])) { //hoặc "Nhân viên"
                $admin_sub = AdminSub::where('user_id', $id)->first();

                // Nếu manager không tồn tại, tạo mới một manager
                if (!$admin_sub) {
                    $admin_sub = new AdminSub();
                    $admin_sub->user_id = $account->User_id; // Gán user_id cho manager
                }

                $admin_sub->user_id = $account->User_id; // Gán user_id cho manager
                $admin_sub->save(); // Lưu manager vào cơ sở dữ liệu
            }

            // Lưu thay đổi vào cơ sở dữ liệu
            $account->save();

            // Cam kết giao dịch
            DB::commit();

            // Chuyển hướng về trang danh sách tài khoản với thông báo thành công
            return redirect()->route('manage-account.viewAll')->with('success', 'Cập nhật tài khoản thành công');
        } catch (\Exception $e) {
            // Nếu có lỗi xảy ra, rollback giao dịch
            DB::rollback();

            // Chuyển hướng về trang danh sách tài khoản với thông báo lỗi
            return redirect()->route('manage-account.viewAll')->with('danger', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function getaccountDtl($id)
    {
        // Retrieve all accounts
        $account = User::findOrFail($id);
        $staff = Staff::where('user_id', $id)->first();

        // Lấy danh sách chi nhánh với trạng thái là 3
        $branches = Branch::where('Status', 3)
            ->join(
                'managers',
                'branches.manager_id',
                '=',
                'managers.Manager_id'
            ) // Join với bảng managers
            ->join('users', 'managers.user_id', '=', 'users.User_id') // Join với bảng users
            ->select(
                'branches.*',
                'users.User_id as userid'
            ) // Chọn tất cả các trường từ bảng branches
            ->get();
        $manager = Manager::where('user_id', $id)->first();

        $title = "Detail";
        // Return view with accounts data
        return view('user.detail', compact('account', 'branches', 'title', 'manager', 'staff'));
    }

    // Delete account
    public function destroyStaff(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:users,User_id'
        ]);

        $account = User::findOrFail($request->account_id);
        $account->delete();

        return redirect()->back()->with('success', 'Xóa tài khoản thành công');
    }

    public function searchUsers(Request $request)
    {
        $nameuser = $request->input('name');

        // Lấy ra danh sách người dùng với số điện thoại và tên
        $users = User::select('User_id', 'Name', 'Phone')
            ->where('Role', '5') // Giả sử vai trò khách hàng có ID là 5
            ->where('Name', 'LIKE', "%{$nameuser}%")
            ->get();

        // Lọc dữ liệu để chỉ lấy những bản ghi không bị trùng lặp
        $uniqueUsers = $users->groupBy(function ($user) {
            return $user->Name . '|' . $user->Phone; // Kết hợp tên và số điện thoại để lọc
        })->map(function ($group) {
            return $group->first(); // Giữ lại bản ghi đầu tiên trong mỗi nhóm
        })->values(); // Reset keys

        return response()->json($uniqueUsers); // Trả về dữ liệu đã lọc
    }


    public function registerStore(Request $request)
    {
        // Kiểm tra hợp lệ của dữ liệu từ form
        $request->validate([
            'Name' => 'required|string|max:255',
            'Phone' => 'required|digits_between:10,11',
            'Email' => 'required|string|email|max:255|unique:users',
            'Password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>])(?=.*\d).+$/'
            ],
        ], [
            'Email.unique' => 'Email đã tồn tại', // thông báo lỗi khi email đã tồn tại
            'Password.confirmed' => 'Mật khẩu xác nhận không khớp',
            'Password.min' => 'Mật khẩu phải chứa ít nhất 8 ký tự',
            'Password.regex' => 'Mật khẩu phải chứa ít nhất 1 chữ in hoa, 1 ký tự đặc biệt và 1 số',
            

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
                // 'customer_type_id' => 1, // Loại khách hàng mặc định (vãng lai)
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
