<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\auth\LoginController;
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
use Illuminate\Support\Facades\Session;

class BranchController extends Controller
{
    public function viewDeleteRequired()
    {
        $branches = Branch::join('managers', 'branches.manager_id', '=', 'managers.Manager_id')
            ->join('users', 'managers.user_id', '=', 'users.User_id')
            ->where('managers.user_id', Auth()->user()->User_id)
            ->where('branches.Status', 3)
            ->select('branches.*', 'users.User_id as User_id')
            ->get();

        $title = "Xóa địa điểm";
        return view('branch.viewDeleteRequired', compact('branches', 'title'));
    }

    public function postDeleteRequired(Request $req)
    {
        $dele = Branch::where('Branch_id', $req->Branch_id)->first();
        $dele->delete();
        $branch = $req->Branch_id;
        $isBranch = false;

        // nếu xóa trúng branch hienenj tại thì gửi cờ  để logout chủ sân
        if (session('branch_active')->Branch_id == $req->Branch_id) {
            $isBranch = true;
        }
        return response()->json([
            'message' => 'Xóa thành công',
            'branch' => $branch,
            'isBranch' => $isBranch
        ], 201); // 201 status code for successful resource creation
    }

    public function welcome()
    {
        $branches = Branch::where('Status', '3')->get();
        return view('welcome', compact('branches'));
    }

    public function danhsachsan(Request $req)
    {
        $branches = Branch::where('Status', '3');

        if (isset($req->search)) {
            $branches->where('Name', 'like', '%' . $req->search . '%');
        }

        // Lấy kết quả phân trang và gán vào biến $branches
        $branches = $branches->get();

        return view('branch.danhsachsanKhachHang', compact('branches'));
    }


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
            'title' => 'Danh sách',
            'branches' => $branches,
        ]);
    }

    public function showForm()
    {
        return view('branch.formRegister', [
            'title' => 'Đăng ký kinh doanh'
        ]);
    }

    public function adminshowForm()
    {
        return view('branch.formAdminBranchRegister', [
            'title' => 'Đăng ký kinh doanh'
        ]);
    }

    public function showformEmaiExists()
    {
        return view('branch.formRegisterEmailExists', [
            'title' => 'Đăng ký kinh doanh'
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
                'Email' => 'required|email|',
            ]
            // ,
            // [
            //     'Email.unique' => 'Email đã tồn tại', // thông báo lỗi khi email đã tồn tại
            // ]
        );
        // Start a transaction
        DB::beginTransaction();
        try {
            // Lấy thông tin user hiện tại
            $user = User::where('User_id', Auth()->user()->User_id)->first();
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
            $branch->Status = 0;
            $branch->save();

            // Commit the transaction nếu không có lỗi
            DB::commit();

            // Return a JSON response
            return response()->json([
                'message' => 'Đăng ký thành công, Chờ duyệt',
                'branch' => $branch,
            ], 201); // 201 status code for successful resource creation

        } catch (\Exception $e) {
            // Rollback the transaction nếu có lỗi xảy ra
            DB::rollBack();
            Log::error('Đăng ký thất bại: ' . $e->getMessage());

            // Return a JSON response with error
            return response()->json([
                'message' => 'Đăng ký thất bại, vui lòng thử lại sau.',
                'error' => $e->getMessage(),
            ], 500); // 500 status code for server error
        }
    }
    public function adminregister(Request $request)
    {
        // dd($request);
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
            ],
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
            Mail::send('branch.mailCapTaiKhoan', compact('Email', 'user', 'admin'), function ($email) use ($Email) {
                $email->subject('Cấp tài khoản');
                $email->to($Email);
            });
            // Return a JSON response
            return response()->json([
                'message' => 'Tạo địa điểm kinh doanh mới và tài khoản chủ sân thành công',
                'branch' => $branch,
            ], 201); // 201 status code for successful resource creation

        } catch (\Exception $e) {
            // Rollback the transaction nếu có lỗi xảy ra
            DB::rollBack();
            Log::error('Tạo địa điểm kinh doanh mới và tài khoản chủ sân thất bại: ' . $e->getMessage());

            // Return a JSON response with error
            return response()->json([
                'message' => 'Tạo địa điểm kinh doanh mới và tài khoản chủ sân thất bại, vui lòng thử lại sau.',
                'error' => $e->getMessage(),
            ], 500); // 500 status code for server error
        }
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
            ]
            // ,
            // [
            //     'Email.unique' => 'Email đã tồn tại', // thông báo lỗi khi email đã tồn tại
            // ]
        );

        // Save branch information
        $branch = new Branch();
        $branch->Name = $request->Name;
        $branch->Location = $request->Location;
        $branch->Phone = $request->Phone;
        $branch->Email = $request->Email;
        $branch->manager_id = $request->manager_id; //mã manager của người tạo
        $branch->Status = 0; // chờ duyệt
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
                $query->where('branches.Status', '0'); // mã branch chờ duyệt
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
            'title' => 'Danh sách Chờ duyệt',
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
                $query->Where('branches.Status', '-1'); // chờ thỏa thuận
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
            'title' => 'Danh sách Chờ thỏa thuận hợp đồng',
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
            $soluongBranch = Branch::where('manager_id', $managerid)->where('Status', 3)->count();

            if ($soluongBranch > 1) {
                $branch = Branch::find($branchid);

                if ($branch->Status == 3) {
                    $branch->delete();
                    Mail::send('branch.mailTuChoi', compact('Email', 'user', 'soluongBranch'), function ($email) use ($Email) {
                        $email->subject('Xóa 1 chi Nhánh');
                        $email->to($Email);
                    });
                } else {
                    $branch->delete();
                    // Manager::find($managerid)->delete();
                    // User::find($userid)->delete();
                    // Gửi email
                    Mail::send('branch.mailTuChoi', compact('Email', 'user'), function ($email) use ($Email) {
                        $email->subject('Từ Chối Đăng Ký Chi Nhánh');
                        $email->to($Email);
                    });
                }

                // Commit giao dịch nếu không có lỗi
                DB::commit();


                $isBranch = false;

                // nếu xóa trúng branch hienenj tại thì gửi cờ  để logout chủ sân
                if (Auth()->user()->Role == '3') {
                    if (session('branch_active')->Branch_id == $req->Branch_id) {
                        $isBranch = true;
                    }
                }

                return response()->json([
                    'message' => 'Đã xóa 1 chi nhánh và gửi Email tới khách hàng',
                    'redirect' => route('pending.approval'), // Route chờ duyệt
                    'branch_id' => $branchid,
                    'isBranch' => $isBranch
                ], 201);
            } else {
                // Tìm chi nhánh theo ID
                $branch = Branch::find($branchid);
                if ($branch->Status == 3) {
                    // $user->Role = '5';
                    $user->delete();
                    $managertemp = Manager::find($managerid);
                    if ($managertemp) {
                        $managertemp->delete();
                    }
                    // Xóa tất cả các phiên của người dùng này
                    // Session::where('User_id', $userid)->delete();

                    $branch->delete();
                    Mail::send('branch.mailTuChoi', compact('Email', 'user', 'soluongBranch'), function ($email) use ($Email) {
                        $email->subject('Xóa 1 chi Nhánh');
                        $email->to($Email);
                    });
                } else {
                    $branch->delete();
                    $managertemp = Manager::find($managerid);
                    if ($managertemp) {
                        $managertemp->delete();
                    }
                    // User::find($userid)->delete();
                    // Gửi email
                    Mail::send('branch.mailTuChoi', compact('Email', 'user'), function ($email) use ($Email) {
                        $email->subject('Từ Chối Đăng Ký Chi Nhánh');
                        $email->to($Email);
                    });
                }

                // Tìm tất cả nhân viên thuộc chi nhánh này và xóa
                $staffDeleted = Staff::where('branch_id', $branchid)->delete();
                // Commit giao dịch nếu không có lỗi
                DB::commit();

                $isBranch = false;

                // nếu xóa trúng branch hienenj tại thì gửi cờ  để logout chủ sân
                if (Auth()->user()->Role == '3') {
                    if (session('branch_active')->Branch_id == $req->Branch_id) {
                        $isBranch = true;
                    }
                }

                return response()->json([
                    'message' => 'Đã xóa và gửi Email tới khách hàng',
                    'redirect' => route('pending.approval'), // Route chờ duyệt
                    'branch_id' => $branchid,
                    'isBranch' => $isBranch
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
            // // Cập nhật role user là -1 -> chờ ký hợp đồng
            // if ($user->Role == '0') {
            //     $user->Role = '-1';
            //     $user->save();
            // }

            $branch = Branch::find($branchid);
            if ($branch->Status == 0) {
                $branch->Status = -1;
                $branch->save();
            }

            $admin = false;
            // Gửi email
            Mail::send('branch.mailDongY', compact('Email', 'user', 'date', 'time', 'admin'), function ($email) use ($Email) {
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
            $branch = Branch::find($branchid);

            if ($branch->Status == -1 && $user->Role == '3') {
                $branch->Status = 3;
                $branch->save();

                Mail::send('branch.mailCamOn', compact('Email', 'user'), function ($email) use ($Email) {
                    $email->subject('Tiếp tục đồng hành');
                    $email->to($Email);
                });
            }


            if ($user->Role == '5') { // Cập nhật role user là 5 -> khách hàng với tài khoản đăng ký kinh doanh lần đầu
                $user->Role = '3';
                $user->save();
                // Xóa tất cả các phiên của người dùng này
                // Session::where('User_id', $userid)->delete();
                $admin = false;
                Mail::send('branch.mailCapTaiKhoan', compact('Email', 'user', 'admin'), function ($email) use ($Email) {
                    $email->subject('Cấp tài khoản');
                    $email->to($Email);
                });

                // cập nhật role chi nhánh sau khi gửi mail
                $branch->Status = 3;
                $branch->save();
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
            Log::error($e->getMessage());
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
            'title' => 'Thông tin địa điểm',
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
            'title' => 'Danh sách địa điểm',
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
                'Email.unique' => 'Email branches đã tồn tại', // thông báo lỗi khi email đã tồn tại
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
                // $originalName = $request->file('Image')->getClientOriginalName();
                $originalName = 'branchID_' . $branch->Branch_id . '_anhdaidien.png';
                $urlImage = "/images/khachhang/chinhanh/$originalName";
                if (file_exists(public_path('images/khachhang/chinhanh/') . $originalName)) {
                    unlink(public_path('images/khachhang/chinhanh/') . $originalName);
                }
                $path = $request->file('Image')->move(public_path('images/khachhang/chinhanh/'), $originalName);;
                $branch->Image = $urlImage;
            }

            if ($request->file('Cover_image')) {
                // $originalName = $request->file('Cover_image')->getClientOriginalName();
                $originalName = 'branchID_' . $branch->Branch_id . '_anhbia.png';
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
