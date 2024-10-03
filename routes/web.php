<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\branch\BranchController;
use App\Http\Controllers\courts\CourtsController;
use App\Http\Controllers\custemer_type\CustomerTypeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\price_list\PriceListController;

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

//trang khách hàng vào thấy
Route::get('/', function () {
    return view('welcome');
});
// search sân tenis
// routes/web.php
Route::get('/search', [BranchController::class, 'search'])->name('search');

Route::middleware('guest')->group(function () { //chưa đăng nhập mới vào được những route này
    Route::get('admin/login', [LoginController::class, 'index'])->name('login'); //đặt tên route là login cho thuận tiện
    Route::post('admin/login/store', [LoginController::class, 'store'])->name('login.store');
    Route::get('/branch/register', [BranchController::class, 'showForm'])->name('register');
});

//đăng ký chi nhánh
Route::post('/branch/register', [BranchController::class, 'register'])->name('branch.store');

//--------------------------------------------------------------------------------------------------

//route đổi mật khẩu
Route::get('/forgot-password', [LoginController::class, 'forgotPass'])->name('password.request');
Route::post('/forgot-password', [LoginController::class, 'sendMailCofirm'])->name('send.email.change.pass');
Route::get('xacnhan/{id}/{token}', [LoginController::class, 'accept'])->name('xacnhan');
Route::post('doimatkhau/{id}/{token}', [LoginController::class, 'changPass']);

//--------------------------------------------------------------------------------------------------

//đăng nhập rồi mới vào những route bên dưới được
Route::middleware(['auth'])->group(function () {
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

    // đăng kí chi nhánh với email đã tồn tại
    Route::get('/branch/register-emails-exists', [BranchController::class, 'showformEmaiExists'])->name('branch.email.exists');
    Route::post('/branch/register-emails-exists', [BranchController::class, 'registerBranchEmaiExists'])->name('branch.email.exists.post');

    // ---------------------------------------------------------------------------------------
    // Nhóm quản lý chi nhánh
    Route::prefix('manage-branches')->group(function () {
        Route::get('viewAll', [BranchController::class, 'viewAll'])->name('manage-branches.viewAll');
    });

    // Nhóm quản lý sân (courts)
    Route::prefix('manage-courts')->group(function () {
        // Route xem danh sách sân
        Route::get('courts', [CourtsController::class, 'index'])->name('courts.index');

        // Route xem chi tiết giờ được đặt của sân
        Route::get('courts/{id}', [CourtsController::class, 'show'])->name('courts.show');

        // Route xem lịch đặt sân
        Route::get('booking-calendar', [CourtsController::class, 'bookingCalendar'])->name('booking.calendar');

        // route tạo sân cho chi nhánh
        Route::get('create', [CourtsController::class, 'viewCreate'])->name('manage-courts.getCreate');
        Route::post('single-create', [CourtsController::class, 'CourtCreate'])->name('single.court.create');
        Route::post('bulk-create', [CourtsController::class, 'CourtCreate'])->name('bulk.court.create');
    });

    // Nhóm quản lý giá sân (courts)
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
        Route::post('/price-list/{id}', [PriceListController::class, 'update'])->name('price_list.update');
        Route::delete('/price-list/{id}', [PriceListController::class, 'destroy'])->name('price_list.destroy');
    });
});
