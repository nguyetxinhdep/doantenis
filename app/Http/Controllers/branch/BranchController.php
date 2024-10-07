<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Manager;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BranchController extends Controller
{

    public function search(Request $request)
    {
        $query = $request->input('query');

        // Thực hiện truy vấn để lấy danh sách gợi ý (ví dụ từ database)
        $suggestions = Branch::where('Name', 'LIKE', "%$query%")->limit(6)->get();
        Log::debug($suggestions);
        return response()->json($suggestions);
    }

    public function viewAll()
    {
        $branches = Branch::join('managers', function (JoinClause $join) {
            $join->on('branches.manager_id', '=', 'managers.Manager_id');
        })
            ->join('users', function (JoinClause $join) {
                $join->on('managers.user_id', '=', 'users.User_id');
            })->where('branches.Status', 3)
            ->select(
                'branches.*',
                'branches.Name as branch_name',
                // 'staff.*',
                'users.Name as user_name',
                'users.Email as user_email',
                'users.User_id as user_id',
                'users.Phone as user_phone',
            ) // Chọn các cột cần thiết
            ->get();
        return view('branch.viewAll', [
            'title' => 'DS Chi nhánh',
            'branches' => $branches,
        ]);
    }

    public function showForm()
    {
        return view('branch.formRegister', [
            'title' => 'Đăng ký chi nhánh'
        ]);
    }

    public function showformEmaiExists()
    {
        return view('branch.formRegisterEmailExists', [
            'title' => 'Đăng ký chi nhánh'
        ]);
    }

    public function register(Request $request)
    {
        // dd($request);
        $this->validate(
            $request,
            [
                'Name' => 'required',
                'Location' => 'required',
                'Phone' => 'required',
                'Email' => 'required|email|unique:users,Email',
                'HoTen' => 'required',
                'Address' => 'required',
                'SDTCaNhan' => 'required',
            ],
            [
                'Email.unique' => 'Email đã tồn tại', // thông báo lỗi khi email đã tồn tại
            ]
        );
        // Log::debug($request);

        // tạo đối tượng user
        $user = new User();
        $user->Name = $request->HoTen;
        $user->Email = $request->Email;
        $user->Phone = $request->SDTCaNhan;
        $user->Address = $request->Address;
        $user->Role = '0';
        // Mã hóa mật khẩu bằng Bcrypt trước khi lưu
        $user->password = bcrypt('123456');
        $user->save();
        // Lấy ID của người dùng vừa tạo
        $userId = $user->User_id;

        //tạo đối tượng staff
        $manager = new Manager();
        $manager->Manager_code = 0;
        $manager->user_id = $userId;
        $manager->save();
        // Lấy ID của người dùng vừa tạo
        $managerID = $manager->Manager_id;


        // Save branch information
        $branch = new Branch();
        $branch->Name = $request->Name;
        $branch->Location = $request->Location;
        $branch->Phone = $request->Phone;
        $branch->Email = $request->Email;
        $branch->manager_id = $managerID; //mã manager vừa tạo vưa tạo
        $branch->Status = 0; //mã manager của người tạo
        $branch->save();

        // Return a JSON response
        return response()->json([
            'message' => 'Đăng ký thành công, Chờ duyệt',
            'branch' => $branch,
        ], 201); // 201 status code for successful resource creation
    }

    public function registerBranchEmaiExists(Request $request)
    {
        // dd($request);
        $this->validate(
            $request,
            [
                'Name' => 'required',
                'Location' => 'required',
                'Phone' => 'required',
            ],
            [
                'Email.unique' => 'Email đã tồn tại', // thông báo lỗi khi email đã tồn tại
            ]
        );

        // Save branch information
        $branch = new Branch();
        $branch->Name = $request->Name;
        $branch->Location = $request->Location;
        $branch->Phone = $request->Phone;
        $branch->Email = $request->Email;
        $branch->manager_id = $request->manager_id; //mã manager của người tạo
        $branch->Status = 0; //mã manager của người tạo
        $branch->save();

        // Return a JSON response
        return response()->json([
            'message' => 'Đăng ký thành công, Chờ duyệt',
            'branch' => $branch,
        ], 201); // 201 status code for successful resource creation
    }

    // hiển thị danh sách chờ duyệt
    public function showPending()
    {
        $test = Branch::join('managers', function (JoinClause $join) {
            $join->on('branches.manager_id', '=', 'managers.Manager_id');
        })
            ->join('users', function (JoinClause $join) {
                $join->on('managers.user_id', '=', 'users.User_id');
            })->where(function ($query) {
                $query->where('users.Role', '0')
                    ->orWhere('branches.Status', '0'); // Thêm điều kiện hoặc
            })
            ->select(
                'branches.*',
                'branches.Name as branch_name',
                // 'staff.*',
                'users.Name as user_name',
                'users.Email as user_email',
                'users.User_id as user_id',
                'users.Phone as user_phone',
            ) // Chọn các cột cần thiết
            ->get();
        // dd($test);


        return view('branch.showPending', [
            'title' => 'DS Chờ duyệt',
            'branches' => $test,
        ]);
    }

    // hiển thị danh sách thỏa thuận ký hợp đồng
    public function showPendingAgree()
    {
        $test = Branch::join('managers', function (JoinClause $join) {
            $join->on('branches.manager_id', '=', 'managers.Manager_id');
        })
            ->join('users', function (JoinClause $join) {
                $join->on('managers.user_id', '=', 'users.User_id');
            })->where(function ($query) {
                $query->where('users.Role', '-1')
                    ->orWhere('branches.Status', '-1'); // Thêm điều kiện hoặc
            })
            ->select(
                'branches.*',
                'branches.Name as branch_name',
                // 'staff.*',
                'users.Name as user_name',
                'users.Email as user_email',
                'users.User_id as user_id',
                'users.Phone as user_phone',
            ) // Chọn các cột cần thiết
            ->get();
        // dd($test);


        return view('branch.showPendingAgree', [
            'title' => 'DS Chờ thỏa thuận hợp đồng',
            'branches' => $test,
        ]);
    }

    // từ chối đăng ký chi nhánh
    public function rejectBranch(Request $req)
    {
        DB::beginTransaction();
        try {
            // Lấy ID từ request
            $branchid = $req->input('Branch_id');
            $managerid = $req->input('Manager_id');
            $userid = $req->input('User_id');
            $Email = $req->input('Email');

            $user = User::find($userid);

            // kiểm tra số lượng chi nhánh đã có
            $soluongBranch = Branch::where('manager_id', $managerid)->count();

            if ($soluongBranch > 1) {
                $branch = Branch::find($branchid)->delete();
                Mail::send('branch.mailTuChoi', compact('Email', 'user', 'soluongBranch'), function ($email) use ($Email) {
                    $email->subject('Xóa 1 chi Nhánh');
                    $email->to($Email);
                });

                // Commit giao dịch nếu không có lỗi
                DB::commit();

                return response()->json([
                    'message' => 'Đã xóa 1 chi nhánh và gửi Email tới khách hàng',
                    'redirect' => route('pending.approval'), // Route chờ duyệt
                    'branch_id' => $branchid
                ], 201);
            } else {
                // Tìm chi nhánh theo ID
                $branch = Branch::find($branchid)->delete();
                Manager::find($managerid)->delete();
                User::find($userid)->delete();
                // Gửi email
                Mail::send('branch.mailTuChoi', compact('Email', 'user'), function ($email) use ($Email) {
                    $email->subject('Từ Chối Đăng Ký Chi Nhánh');
                    $email->to($Email);
                });

                // Commit giao dịch nếu không có lỗi
                DB::commit();

                return response()->json([
                    'message' => 'Đã từ chối đăng ký và gửi Email tới khách hàng',
                    'redirect' => route('pending.approval'), // Route chờ duyệt
                    'branch_id' => $branchid
                ], 201);
            }
        } catch (\Exception $e) {
            // Rollback giao dịch nếu có lỗi
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Đã có lỗi xảy ra. Vui lòng thử lại!',
                'error' => $e->getMessage() // Thông báo lỗi (có thể bỏ đi nếu không muốn hiển thị)
            ], 500);
        }
    }

    public function selectTime(Request $req)
    {
        // Lấy ID từ request
        $branchid = $req->input('Branch_id');
        $managerid = $req->input('Manager_id');
        $userid = $req->input('User_id');
        $Email = $req->input('Email');

        // $user = User::find($userid);

        return view('branch.selectTime', [
            'title' => 'Hẹn giờ gặp mặt',
            'Email' => $Email,
            'Manager_id' => $managerid,
            'Branch_id' => $branchid,
            'User_id' => $userid,
        ]);
    }

    //đồng ý đăng ký và gửi mail để gặp mặt thỏa thuận

    public function approveBranch(Request $req)
    {
        // dd(123);
        // Bắt đầu giao dịch
        DB::beginTransaction();
        try {
            // Lấy ID từ request
            $branchid = $req->input('Branch_id');
            $userid = $req->input('User_id');
            $Email = $req->input('Email');
            // dd($Email);
            // Lấy ngày tháng năm gặp
            $date = $req->input('date');
            $time = $req->input('time');
            $user = User::find($userid);
            // Cập nhật role user là -1 -> chờ ký hợp đồng
            if ($user->Role == '0') {
                $user->Role = '-1';
                $user->save();
            }

            $branch = Branch::find($branchid);
            if ($branch->Status == 0) {
                $branch->Status = -1;
                $branch->save();
            }

            // Gửi email
            Mail::send('branch.mailDongY', compact('Email', 'user', 'date', 'time'), function ($email) use ($Email) {
                $email->subject('Xác Nhận Đăng Ký');
                $email->to($Email);
            });

            // Commit giao dịch nếu không có lỗi
            DB::commit();

            return response()->json([
                'message' => 'Email đã được gửi thành công.',
                'redirect' => route('pending.approval'), // Route chờ duyệt
                'branch_id' => $branchid
            ], 201);
        } catch (\Exception $e) {
            // Rollback giao dịch nếu có lỗi
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Đã có lỗi xảy ra. Vui lòng thử lại!',
                'error' => $e->getMessage() // Thông báo lỗi (có thể bỏ đi nếu không muốn hiển thị)
            ], 500);
        }
    }


    //thỏa thuận thành công gửi mail cấp tài khoản
    public function agreeBranch(Request $req)
    {
        // Bắt đầu giao dịch
        DB::beginTransaction();

        try {
            // Lấy ID từ request
            $branchid = $req->input('Branch_id');
            $userid = $req->input('User_id');
            $Email = $req->input('Email');

            $user = User::find($userid);
            // Cập nhật role user là -1 -> chờ ký hợp đồng
            if ($user->Role == '-1') {
                $user->Role = '3';
                $user->save();

                Mail::send('branch.mailCapTaiKhoan', compact('Email', 'user'), function ($email) use ($Email) {
                    $email->subject('Cấp tài khoản');
                    $email->to($Email);
                });
            }

            $branch = Branch::find($branchid);
            if ($branch->Status == -1) {
                $branch->Status = 3;
                $branch->save();

                Mail::send('branch.mailCamOn', compact('Email', 'user'), function ($email) use ($Email) {
                    $email->subject('Tiếp tục đồng hành');
                    $email->to($Email);
                });
            }

            // Nếu mọi thứ đều thành công, commit giao dịch
            DB::commit();

            return response()->json([
                'message' => 'Email đã được gửi thành công.',
                'branch_id' => $branchid
            ], 201);
        } catch (\Exception $e) {
            // Nếu có lỗi, rollback giao dịch
            DB::rollBack();

            return response()->json([
                'message' => 'Đã có lỗi xảy ra. Vui lòng thử lại!',
                'error' => $e->getMessage() // Thông báo lỗi (có thể bỏ đi nếu không muốn hiển thị)
            ], 500);
        }
    }

    public function setBranchActive($branch_id)
    {
        // Lưu ID chi nhánh vào session
        $branch = Branch::join('managers', 'branches.manager_id', '=', 'managers.Manager_id')
            ->join('users', 'managers.user_id', '=', 'users.User_id')
            ->where('branches.Branch_id', $branch_id)
            ->select('branches.*', 'users.User_id as User_id')
            ->first();

        session(['branch_active' => $branch]);
        // Trả về phản hồi
        return redirect()->back();
    }

    // admin quản lý chi nhánh 
    public function getBranchDtl($branch_id)
    {
        $branch = Branch::select(
            'branches.*',
            'branches.Name as branch_name',
            // 'staff.*',
            'users.Name as user_name',
            'users.Email as user_email',
            'users.User_id as user_id',
            'users.Phone as user_phone',
            'users.Address as user_address',
        )
            ->join('managers', function ($q) {
                $q->on('branches.manager_id', '=', 'managers.Manager_id');
            })
            ->join('users', function ($join) {
                $join->on('managers.user_id', '=', 'users.User_id');
            })->where('branches.Status', 3)
            ->where('Branch_id', $branch_id)
            ->first();
        return view('branch.viewDetail', [
            'title' => 'DS Chi nhánh',
            'data' => $branch,
        ]);
    }
    // quản lý cập nhật thông tin chi nhánh 
    public function managerGetBranchDtl()
    {
        $branch_id = session('branch_active')->Branch_id;
        $branch = Branch::select(
            'branches.*',
            'branches.Name as branch_name',
            // 'staff.*',
            'users.Name as user_name',
            'users.Email as user_email',
            'users.User_id as user_id',
            'users.Phone as user_phone',
            'users.Address as user_address',
        )
            ->join('managers', function ($q) {
                $q->on('branches.manager_id', '=', 'managers.Manager_id');
            })
            ->join('users', function ($join) {
                $join->on('managers.user_id', '=', 'users.User_id');
            })->where('branches.Status', 3)
            ->where('Branch_id', $branch_id)
            ->first();
        return view('branch.viewDetail', [
            'title' => 'DS Chi nhánh',
            'data' => $branch,
        ]);
    }

    public function reloadBranch()
    {
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
                return redirect()->back()->with('success', 'Cập nhật danh sách chi nhánh thành công');
            }

            return redirect()->back()->with('success', 'Cập nhật danh sách chi nhánh thành công');
        }
    }

    public function updateBranch(Request $request, $id) //biến id gửi trên url
    {
        if (session()->has('branch_active')) {
            $userIdManager = session('branch_active')->User_id;
            // dd(session('branch_active'));
            // dd($userIdManager);
            $branch_id = session('branch_active')->Branch_id;

            $userManager = User::find($userIdManager);
        } else {
            $branch_id = $id;

            $userManager = Branch::join('managers', 'branches.manager_id', '=', 'managers.Manager_id')
                ->join('users', 'managers.user_id', '=', 'users.User_id')
                ->where('branches.Branch_id', $branch_id)
                ->select('users.*', 'users.User_id as User_id')
                ->first();

            $userIdManager = $userManager->User_id;
        }

        $this->validate(
            $request,
            [
                'Email' => 'email|unique:users,Email,' . $userIdManager . ',User_id',
                'Image' => 'file|mimes:jpg,png,pdf|max:2048',
                'Cover_image' => 'file|mimes:jpg,png,pdf|max:2048',
            ],
            [
                'Email.unique' => 'Email đã tồn tại', // thông báo lỗi khi email đã tồn tại
                'Image.file' => 'Ảnh bìa không phải phải là file',
                'Image.mimes' => 'Ảnh bìa phải có phần mở rộng là jpg,png,pdf',
                'Cover_image.file' => 'Ảnh bìa không phải phải là file',
                'Cover_image.mimes' => 'Ảnh bìa phải có phần mở rộng là jpg,png,pdf',
            ]
        );
        // if ($request->file('Image')) {
        //     # code...
        // }

        // dd($request->all(), $userManager);

        try {
            DB::beginTransaction();

            if ($request->input('user_name')) {
                $userManager->Name = $request->input('user_name');
            }

            if ($request->input('user_email')) {
                $userManager->Email = $request->input('user_email');
            }

            if ($request->input('user_address')) {
                $userManager->Address = $request->input('user_address');
            }

            if ($request->input('user_phone')) {
                $userManager->Phone = $request->input('user_phone');
            }

            $userManager->save();
            // update branch

            $branch = Branch::find($branch_id);

            if ($request->file('Image')) {
                $originalName = $request->file('Image')->getClientOriginalName();
                $urlImage = "/images/khachhang/chinhanh/$originalName";
                if (file_exists(public_path('images/khachhang/chinhanh/') . $originalName)) {
                    unlink(public_path('images/khachhang/chinhanh/') . $originalName);
                }
                $path = $request->file('Image')->move(public_path('images/khachhang/chinhanh/'), $originalName);;
                $branch->Image = $urlImage;
            }

            if ($request->file('Cover_image')) {
                $originalName = $request->file('Cover_image')->getClientOriginalName();
                $urlCover_image = "/images/khachhang/chinhanh/$originalName";
                if (file_exists(public_path('images/khachhang/chinhanh/') . $originalName)) {
                    unlink(public_path('images/khachhang/chinhanh/') . $originalName);
                }
                $path = $request->file('Cover_image')->move(public_path('images/khachhang/chinhanh/'), $originalName);;
                $branch->Cover_image = $urlCover_image;
            }

            if ($request->input('Name')) {
                $branch->Name = $request->input('Name');
            }

            if ($request->input('Location')) {
                $branch->Location = $request->input('Location');
            }

            if ($request->input('Phone')) {
                $branch->Phone = $request->input('Phone');
            }

            if ($request->input('Email')) {
                $branch->Email = $request->input('Email');
            }

            if ($request->input('link_map')) {
                $branch->link_map = $request->input('link_map');
            }

            $branch->save();

            DB::commit();

            // gọi hàm cập nhật lại danh sách chi nhánh
            $this->reloadBranch();

            return redirect()->back()->with('success', "Cập nhật thành công");

            //code...
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
