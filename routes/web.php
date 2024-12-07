<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\booking\BookingController;
use App\Http\Controllers\branch\BranchController;
use App\Http\Controllers\courts\CourtsController;
use App\Http\Controllers\custemer_type\CustomerTypeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\price_list\PriceListController;
use App\Http\Controllers\profile\ProfileController;
use App\Http\Controllers\staff\StaffController;
use App\Http\Controllers\thanhtoan\PaymentController;
use App\Http\Controllers\user\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// //trang khách hàng vào thấy
// Route::get('/', function () {
//     return view('welcome');
// })->name('welcome');
Route::get('/', [BranchController::class, 'welcome'])->name('welcome');
Route::get('/danhsachsan', [BranchController::class, 'danhsachsan'])->name('danhsachsan');

// Route xem lịch đặt sân trang welcome
Route::get('welcome-booking-calendar/', [BookingController::class, 'bookingCalendarWelcome'])->name('welcome.booking.calendar');
// khách hàng tìm lịch
Route::get('cus-calendar-search', [BookingController::class, 'customerSearchCalendar'])->name('customer.calendar.search');
// Route::post('dat-booking-calendar/', [BookingController::class, 'reserve'])->name('booking.reserve');

// search sân tenis
// routes/web.php
Route::get('/search', [BranchController::class, 'search'])->name('search');

Route::middleware('guest')->group(function () { //chưa đăng nhập mới vào được những route này
    Route::get('admin/login', [LoginController::class, 'index'])->name('login'); //đặt tên route là login cho thuận tiện
    Route::post('admin/login/store', [LoginController::class, 'store'])->name('login.store');
    Route::get('/user/register', [UserController::class, 'showForm'])->name('user.register');
    Route::post('/user/register-store', [UserController::class, 'registerStore'])->name('user.register.store');
});

//--------------------------------------------------------------------------------------------------

//route đổi mật khẩu
Route::get('/forgot-password', [LoginController::class, 'forgotPass'])->name('password.request');
Route::post('/forgot-password', [LoginController::class, 'sendMailCofirm'])->name('send.email.change.pass');
Route::get('xacnhan/{id}/{token}', [LoginController::class, 'accept'])->name('xacnhan');
Route::post('doimatkhau/{id}/{token}', [LoginController::class, 'changPass'])->name('doimatkhau');

//--------------------------------------------------------------------------------------------------

//đăng nhập rồi mới vào những route bên dưới được
Route::middleware(['auth'])->group(function () {
    Route::get('/search-users', [UserController::class, 'searchUsers'])->name('search.user');

    // thanh toán
    // Route::post('/vnpay_payment', [PaymentController::class, 'vnpay_payment']);
    // Route::post('/momo_payment', [PaymentController::class, 'momo_payment']);
    Route::post('/momo_paymentQR', [PaymentController::class, 'vnpay_payment']);
    Route::get('/xulythanhtoanthanhcong/', [BookingController::class, 'xuLyThanhToanTC'])->name('xulythanhtoanthanhcong');

    // -------------------
    Route::get('admin/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/home', [MainController::class, 'index'])->name('home');

    //chờ duyệt
    Route::get('/pending-approval', [BranchController::class, 'showPending'])->name('pending.approval');
    //---------chọn giờ gặp mặt
    Route::post('/approveBranch/selecttime', [BranchController::class, 'selectTime'])->name('approveBranch.selecttime');
    Route::post('/approveBranch', [BranchController::class, 'approveBranch'])->name('approveBranch');
    Route::post('/rejectBranch', [BranchController::class, 'rejectBranch'])->name('rejectBranch');

    // chờ thỏa thuận kí hợp đồng
    Route::get('/pending-agree', [BranchController::class, 'showPendingAgree'])->name('pending.agree');
    Route::post('/agree-success', [BranchController::class, 'agreeBranch'])->name('agree.success');

    // set branch active
    Route::get('/set-branch-active/{branch_id}', [BranchController::class, 'setBranchActive'])->name('setBranchActive');

    //đăng ký chi nhánh
    Route::get('/branch/register', [BranchController::class, 'showForm'])->name('register');
    Route::post('/branch/register', [BranchController::class, 'register'])->name('branch.store');
    // đăng kí chi nhánh với email đã tồn tại
    Route::get('/branch/register-emails-exists', [BranchController::class, 'showformEmaiExists'])->name('branch.email.exists');
    Route::post('/branch/register-emails-exists', [BranchController::class, 'registerBranchEmaiExists'])->name('branch.email.exists.post');
    // view xóa địa điểm
    Route::get('/branch/view/deleteRequired', [BranchController::class, 'viewDeleteRequired'])->name('branch.delete.reuired');
    Route::post('/branch/post/deleteRequired', [BranchController::class, 'postDeleteRequired'])->name('branch.delete.reuired.post');

    // ---------------------------------------------------------------------------------------
    // // Nhóm quản lý chi nhánh
    // Route::prefix('manage-branches')->group(function () {
    //     Route::get('viewAll', [BranchController::class, 'viewAll'])->name('manage-branches.viewAll');
    // });

    // Nhóm quản lý chi nhánh chủ sân
    Route::prefix('manage-branches')->group(function () {
        Route::get('viewAll', [BranchController::class, 'viewAll'])->name('manage-branches.viewAll');
        Route::get('reload-branches', [BranchController::class, 'reloadBranch'])->name('manage-branches.reload');
        // admin quản lí chi nhánh
        Route::get('detail/{id}', [BranchController::class, 'getBranchDtl'])->name('admin.manage-branches.detail');
        Route::post('detail/{id}/update', [BranchController::class, 'updateBranch'])->name('manage-branches.update');
        // manager cập nhật thông tin chi nhánh
        Route::get('branch/detail', [BranchController::class, 'managerGetBranchDtl'])->name('manage-branches.detail');

        // ------------staff-----------
        // view tạo nhân viên cho chi nhánh
        Route::get('create-staff', [StaffController::class, 'createStaff'])->name('manage-branches.createStaff');
        // Route::post('send-mail-create', [StaffController::class, 'sendmailcreate'])->name('manage-branches.sendmail.createStaff');

        Route::get('/staff/confirm/{token}/{branch_id}', [StaffController::class, 'confirmStaff'])->name('staff.confirm');
        Route::get('/staff/reject/{token}', [StaffController::class, 'rejectStaff'])->name('staff.reject');

        Route::post('store-staff', [StaffController::class, 'storeStaff'])->name('manage-branches.storeStaff');
        Route::get('view-staff', [StaffController::class, 'viewStaff'])->name('manage-branches.viewStaff');
        Route::get('view-editStaff/{id}', [StaffController::class, 'editStaff'])->name('manage-branches.editStaff');
        Route::post('updateStaff/{id}', [StaffController::class, 'updateStaff'])->name('manage-branches.updateStaff');
        Route::post('destroyStaff', [StaffController::class, 'destroyStaff'])->name('manage-branches.destroy');

        // -------------admin-----------
        //đăng ký chi nhánh
        Route::get('/branch/register', [BranchController::class, 'adminshowForm'])->name('admin.branch.register');
        Route::post('/branch/register', [BranchController::class, 'adminregister'])->name('admin.branch.store');
    });

    // Nhóm quản lý tài khoản
    Route::prefix('manage-account')->group(function () {
        // quản lý tài khoản
        Route::get('viewAll', [UserController::class, 'viewAll'])->name('manage-account.viewAll');
        // admin quản lí chi nhánh
        Route::get('detail/{id}', [UserController::class, 'getaccountDtl'])->name('admin.manage-account.detail');
        Route::post('detail/{id}/update', [UserController::class, 'updateaccount'])->name('manage-account.update');
        // routes/web.php
        Route::get('/manage-account/{id}/change-password', [UserController::class, 'showChangePasswordForm'])->name('manage-account.changePasswordForm');
        Route::post('/manage-account/{id}/change-password', [UserController::class, 'changePassword'])->name('manage-account.changePassword');
        Route::post('destroyStaff', [UserController::class, 'destroyStaff'])->name('manage-account.destroy');

        // -------------admin-----------
        //quản lý tài khoản khách hàng
        Route::get('/khachang', [UserController::class, 'indexkhachhang'])->name('admin.account.khachang');
        Route::get('/account/khachang/edit/{id}', [UserController::class, 'editkhachang'])->name('admin.account.edit.khachang');
        Route::post('/account/khachang/update/{id}', [UserController::class, 'updatekhachhang'])->name('admin.account.update.khachang');
        Route::get('/account/khachang', [UserController::class, 'createkhachhang'])->name('admin.account.create.khachang');
        Route::post('/account/khachang/addaccount', [UserController::class, 'storekhachhang'])->name('admin.account.store.khachang');

        //quản lý tài khoản nhân viên
        Route::get('/nhanvien', [UserController::class, 'indexnhanvien'])->name('admin.account.nhanvien');
        Route::get('/account/nhanvien/edit/{id}', [UserController::class, 'editnhanvien'])->name('admin.account.edit.nhanvien');
        Route::post('/account/nhanvien/update/{id}', [UserController::class, 'updatenhanvien'])->name('admin.account.update.nhanvien');
        Route::get('/account/nhanvien', [UserController::class, 'createnhanvien'])->name('admin.account.create.nhanvien');
        Route::post('/account/nhanvien/addaccount', [UserController::class, 'storenhanvien'])->name('admin.account.store.nhanvien');

        //quản lý tài khoản chủ sân
        Route::get('/chusan', [UserController::class, 'indexchusan'])->name('admin.account.chusan');
        Route::get('/account/chusan/edit/{id}', [UserController::class, 'editchusan'])->name('admin.account.edit.chusan');
        Route::post('/account/chusan/update/{id}', [UserController::class, 'updatechusan'])->name('admin.account.update.chusan');
        Route::get('/account/chusan', [UserController::class, 'createchusan'])->name('admin.account.create.chusan');
        Route::post('/account/chusan/addaccount', [UserController::class, 'storechusan'])->name('admin.account.store.chusan');

        // quản lý tài khoản subadmin
        Route::get('/nhanvienhetong', [UserController::class, 'indexnhanvienhetong'])->name('admin.account.nhanvienhetong');
        Route::get('/account/nhanvienhetong/edit/{id}', [UserController::class, 'editnhanvienhetong'])->name('admin.account.edit.nhanvienhetong');
        Route::post('/account/nhanvienhetong/update/{id}', [UserController::class, 'updatenhanvienhetong'])->name('admin.account.update.nhanvienhetong');
        Route::get('/account/nhanvienhetong', [UserController::class, 'createnhanvienhetong'])->name('admin.account.create.nhanvienhetong');
        Route::post('/account/nhanvienhetong/addaccount', [UserController::class, 'storenhanvienhetong'])->name('admin.account.store.nhanvienhetong');
    });

    // Nhóm quản lý sân (courts)
    Route::prefix('manage-courts')->group(function () {
        // Route xem danh sách sân
        Route::get('courts', [CourtsController::class, 'index'])->name('courts.index');

        // Route xem chi tiết giờ được đặt của sân
        Route::get('courts/{id}', [CourtsController::class, 'show'])->name('courts.show');
        // Route để hiển thị form chỉnh sửa sân
        Route::get('courts/{id}/edit', [CourtsController::class, 'edit'])->name('courts.edit');

        // Route để cập nhật thông tin sân sau khi chỉnh sửa
        Route::put('courts/{id}', [CourtsController::class, 'update'])->name('courts.update');

        // Route để xóa sân
        Route::delete('courts/{id}', [CourtsController::class, 'destroy'])->name('courts.destroy');

        // route tạo sân cho chi nhánh
        Route::get('create', [CourtsController::class, 'viewCreate'])->name('manage-courts.getCreate');
        Route::post('single-create', [CourtsController::class, 'CourtCreate'])->name('single.court.create');
        Route::post('bulk-create', [CourtsController::class, 'CourtCreate'])->name('bulk.court.create');
    });

    // Nhóm quản lý bảng giá (courts)
    Route::prefix('manage-price-list')->group(function () {
        // route hiển thị danh sách bảng giá
        Route::get('/', [PriceListController::class, 'index'])->name('price_list.index');

        // route hiển thị form tạo bảng giá
        Route::get('/price-list/create', [PriceListController::class, 'create'])->name('price_list.create');
        // route tạo bảng giá
        Route::post('/price-list/store', [PriceListController::class, 'store'])->name('price_list.store');

        // route hiển thị show để sửa
        Route::get('/price-list/{id}', [PriceListController::class, 'show'])->name('price_list.show');
        Route::get('/price-list/{id}/edit', [PriceListController::class, 'edit'])->name('price_list.edit');
        Route::post('/price-list-update/{id}', [PriceListController::class, 'update'])->name('price_list.update');
        Route::post('/price-list/{id}', [PriceListController::class, 'destroy'])->name('price_list.destroy');
    });

    // nhóm quản lý booking
    Route::prefix('booking')->group(function () {
        // Route xem lịch đặt sân trong trong manager-role 3
        Route::get('booking-calendar/{date}', [BookingController::class, 'bookingCalendar'])->name('booking.calendar');
        Route::get('forDate', [PaymentController::class, 'index'])->name('booking.lichtheongay');
        Route::get('booking-calendar-search', [BookingController::class, 'bookingCalendarSearch'])->name('booking.calendar.search');
        // lịch sử đặt sân customer
        Route::get('booking-history', [BookingController::class, 'bookingHistory'])->name('booking.history');
        // khách hàng đặt sân
        Route::post('dat-booking-calendar/', [BookingController::class, 'reserve'])->name('booking.reserve');

        // quản lý và nhân viên đặt sân
        Route::post('quanly-dat-booking-calendar/', [BookingController::class, 'managerreserve'])->name('manager.booking.reserve');
        // đặt lịch cố định
        Route::post('dat-co-dinh-booking-calendar/', [BookingController::class, 'datCoDinh'])->name('dat.co.dinh.booking.reserve');
    });

    // nhóm quản lý thanh toán
    Route::prefix('payment')->group(function () {
        // Route xem
        Route::get('manage', [PaymentController::class, 'index'])->name('manager.payment'); //hiển thị
        Route::post('payment-court', [PaymentController::class, 'paymentCourt'])->name('manager.paymentCourt'); //thanh toán sân
        Route::post('cancel-court', [PaymentController::class, 'cancelCourt'])->name('manager.cancelCourt'); //hủy sân
        Route::get('searchBookings', [PaymentController::class, 'index'])->name('manager.searchBookings'); //hiển thị
    });

    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile/update/{id}', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::get('profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.changePassword');
    Route::post('profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
});
