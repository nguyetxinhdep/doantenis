<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link" style="text-decoration: none; font-size: 16px">
        <img src="/template/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        @auth

            @if (Auth()->user()->Role == '5')
                {{-- khách hàng --}}
                <span class="brand-text"> Khách hàng</span>
            @elseif (Auth()->user()->Role == '4')
                {{-- Nhân viên --}}
                <span class="brand-text"> Nhân viên</span>
            @elseif (Auth()->user()->Role == '3')
                {{-- chủ sân --}}
                <span class="brand-text"> Chủ sân</span>
            @elseif (Auth()->user()->Role == '2')
                {{-- subadmin --}}
                <span class="brand-text"> Nhân viên hệ thống</span>
            @elseif (Auth()->user()->Role == '1')
                {{-- admin --}}
                <span class="brand-text "> Admin</span>
            @endif
        @endauth
        @guest
            <span class="brand-text ">Trang Guest</span>
        @endguest
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        @auth
            <!-- Sidebar user (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                {{-- <div class="image">
                <a class="rounded" href="/profile">
                    <img class="rounded" src="{{ Auth::user()->image }}" alt="User Image">
                </a>
            </div> --}}
                <div class="info">
                    <a style="text-decoration: none" href="#" class="d-block">
                        @auth
                            @if (Auth()->user()->Role == '4')
                                Sân {{ session('branch_active')->Name }}<br>
                                Tên nhân viên: {{ Auth::user()->Name }}
                            @else
                                Họ tên: {{ Auth::user()->Name }}
                            @endif
                            {{-- <p>Your email: {{ Auth::user()->email }}</p> --}}
                            {{-- <p>Your role: {{ Auth::user()->role }}</p> --}}
                        @endauth
                    </a>
                </div>
            </div>
        @endauth

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                @auth
                    @if (Auth::user()->Role == '1')
                        {{-- level 0 Quản lý Duyệt --}}


                        {{-- level 0 quản lí chi nhánh --}}
                        <li
                            class="nav-item {{ request()->routeIs('manage-branches.viewAll') ||
                            request()->routeIs('admin.branch.register') ||
                            request()->routeIs('admin.manage-branches.detail') ||
                            request()->routeIs('pending.approval') ||
                            request()->routeIs('approveBranch.selecttime') ||
                            request()->routeIs('pending.agree')
                                ? 'menu-open'
                                : '' }}">
                            <a href="#" class="nav-link">

                                <p> <i class="bi bi-geo-alt-fill"></i>
                                    Quản lý địa điểm kinh doanh
                                    {{-- cấp 0 quản lí chi nhánh --}}
                                    {{-- <i class="right fas fa-angle-left"></i> --}}
                                </p>
                            </a>
                            {{-- cấp 1 DS Chi Nhánh --}}
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('manage-branches.viewAll') ||
                                    request()->routeIs('admin.branch.register') ||
                                    request()->routeIs('admin.manage-branches.detail')
                                        ? 'active'
                                        : '' }}"
                                        href="{{ route('manage-branches.viewAll') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- DS Chi Nhánh --}}
                                            Danh sách địa điểm
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('pending.approval') || request()->routeIs('approveBranch.selecttime')
                                        ? 'active fw-bold'
                                        : '' }}"
                                        href="{{ route('pending.approval') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- Chờ duyệt --}}
                                            Chờ Duyệt
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('pending.agree') ? 'active fw-bold' : '' }}"
                                        href="{{ route('pending.agree') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- Chờ thỏa thuận --}}
                                            Chờ thỏa thuận
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- level 0 Quản lý tài khoản --}}
                        <li class="nav-item ">
                            <a href="{{ route('manage-account.viewAll') }}"
                                class="nav-link {{ request()->routeIs('manage-account.viewAll') ||
                                request()->routeIs('admin.account.create') ||
                                request()->routeIs('admin.manage-account.detail')
                                    ? 'bg-light'
                                    : '' }}">
                                <p> <i class="bi bi-person-circle"></i>
                                    Quản lý tài khoản
                                </p>
                            </a>
                        </li>

                        {{-- level 0 Quản lý khách hàng --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.account.khachang') }}"
                                class="nav-link {{ request()->routeIs('admin.account.khachang') ||
                                request()->routeIs('admin.account.create.khachang') ||
                                request()->routeIs('admin.account.edit.khachang')
                                    ? 'bg-light'
                                    : '' }}">
                                <p><i class="bi bi-person-vcard-fill"></i>
                                    Quản lý khách hàng
                                    {{-- cấp 0 quản lý sân --}}

                                </p>
                            </a>
                        </li>

                        {{-- level 0 Quản lý nhân viên --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.account.nhanvien') }}"
                                class="nav-link {{ request()->routeIs('admin.account.nhanvien') ||
                                request()->routeIs('admin.account.create.nhanvien') ||
                                request()->routeIs('admin.account.edit.nhanvien')
                                    ? 'bg-light'
                                    : '' }}">
                                <p><i class="bi bi-people"></i>
                                    Quản lý nhân viên
                                    {{-- cấp 0 quản lý sân --}}

                                </p>
                            </a>
                        </li>

                        {{-- level 0 Quản lý nhân viên --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.account.nhanvienhetong') }}"
                                class="nav-link {{ request()->routeIs('admin.account.nhanvienhetong') ||
                                request()->routeIs('admin.account.create.nhanvienhetong') ||
                                request()->routeIs('admin.account.edit.nhanvienhetong')
                                    ? 'bg-light'
                                    : '' }}">
                                <p><i class="bi bi-people"></i>
                                    Quản lý nhân viên hệ thống
                                    {{-- cấp 0 quản lý sân --}}

                                </p>
                            </a>
                        </li>

                        {{-- level 0 Quản lý chủ sân --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.account.chusan') }}"
                                class="nav-link {{ request()->routeIs('admin.account.chusan') ||
                                request()->routeIs('admin.account.create.chusan') ||
                                request()->routeIs('admin.account.edit.chusan')
                                    ? 'bg-light'
                                    : '' }}">
                                <p><i class="bi bi-person-hearts"></i>
                                    Quản lý chủ sân
                                    {{-- cấp 0 quản lý sân --}}

                                </p>
                            </a>
                        </li>
                        {{-- --------------------------------------------------------------------------------------------------- --}}
                    @elseif (Auth::user()->Role == '2')
                        {{-- Sub Admin --}}
                        {{-- level 0 quản lí chi nhánh --}}
                        <li
                            class="nav-item {{ request()->routeIs('manage-branches.viewAll') ||
                            request()->routeIs('admin.branch.register') ||
                            request()->routeIs('admin.manage-branches.detail') ||
                            request()->routeIs('pending.approval') ||
                            request()->routeIs('approveBranch.selecttime') ||
                            request()->routeIs('pending.agree')
                                ? 'menu-open'
                                : '' }}">
                            <a href="#" class="nav-link">

                                <p> <i class="bi bi-geo-alt-fill"></i>
                                    Quản lý địa điểm kinh doanh
                                    {{-- cấp 0 quản lí chi nhánh --}}
                                    {{-- <i class="right fas fa-angle-left"></i> --}}
                                </p>
                            </a>
                            {{-- cấp 1 DS Chi Nhánh --}}
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('manage-branches.viewAll') || request()->routeIs('admin.manage-branches.detail')
                                        ? 'active'
                                        : '' }}"
                                        href="{{ route('manage-branches.viewAll') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- DS Chi Nhánh --}}
                                            Danh sách địa điểm
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('pending.approval') || request()->routeIs('approveBranch.selecttime')
                                        ? 'active fw-bold'
                                        : '' }}"
                                        href="{{ route('pending.approval') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- Chờ duyệt --}}
                                            Chờ Duyệt
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('pending.agree') ? 'active fw-bold' : '' }}"
                                        href="{{ route('pending.agree') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- Chờ thỏa thuận --}}
                                            Chờ thỏa thuận
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- level 0 Quản lý tài khoản --}}
                        <li class="nav-item ">
                            <a href="{{ route('manage-account.viewAll') }}"
                                class="nav-link {{ request()->routeIs('manage-account.viewAll') ||
                                request()->routeIs('admin.account.create') ||
                                request()->routeIs('admin.manage-account.detail')
                                    ? 'bg-light'
                                    : '' }}">
                                <p> <i class="bi bi-person-circle"></i>
                                    Quản lý tài khoản
                                </p>
                            </a>
                        </li>

                        {{-- level 0 Quản lý khách hàng --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.account.khachang') }}"
                                class="nav-link {{ request()->routeIs('admin.account.khachang') ||
                                request()->routeIs('admin.account.create.khachang') ||
                                request()->routeIs('admin.account.edit.khachang')
                                    ? 'bg-light'
                                    : '' }}">
                                <p><i class="bi bi-person-vcard-fill"></i>
                                    Quản lý khách hàng
                                    {{-- cấp 0 quản lý sân --}}

                                </p>
                            </a>
                        </li>

                        {{-- level 0 Quản lý nhân viên --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.account.nhanvien') }}"
                                class="nav-link {{ request()->routeIs('admin.account.nhanvien') ||
                                request()->routeIs('admin.account.create.nhanvien') ||
                                request()->routeIs('admin.account.edit.nhanvien')
                                    ? 'bg-light'
                                    : '' }}">
                                <p><i class="bi bi-people"></i>
                                    Quản lý nhân viên
                                    {{-- cấp 0 quản lý sân --}}

                                </p>
                            </a>
                        </li>

                        {{-- level 0 Quản lý chủ sân --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.account.chusan') }}"
                                class="nav-link {{ request()->routeIs('admin.account.chusan') ||
                                request()->routeIs('admin.account.create.chusan') ||
                                request()->routeIs('admin.account.edit.chusan')
                                    ? 'bg-light'
                                    : '' }}">
                                <p><i class="bi bi-person-hearts"></i>
                                    Quản lý chủ sân
                                    {{-- cấp 0 quản lý sân --}}

                                </p>
                            </a>
                        </li>
                        {{-- ------------------------------------------------------------------------------------------------ --}}
                    @elseif (Auth::user()->Role == '3')
                        {{-- Branch Manager  --}}

                        {{-- level 0 quản lí chi nhánh --}}
                        <li
                            class="nav-item {{ request()->routeIs('manage-branches.detail') ||
                            // request()->routeIs('manage-branches.create-staff') ||
                            // request()->routeIs('manage-branches.createStaff') ||
                            // request()->routeIs('manage-branches.viewStaff') ||
                            request()->routeIs('branch.email.exists') ||
                            request()->routeIs('branch.delete.reuired')
                                ? // request()->routeIs('manage-branches.editStaff')
                                'menu-open'
                                : '' }}">
                            <a href="#" class="nav-link">

                                <p><i class="bi bi-geo-alt-fill"></i>
                                    Quản lý địa điểm
                                    {{-- cấp 0 quản lí chi nhánh --}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            {{-- cấp 1 DS Chi Nhánh --}}
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('manage-branches.detail') ? 'active' : '' }}"
                                        href="{{ route('manage-branches.detail') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- DS Chi Nhánh --}}
                                            Cập nhật thông tin địa điểm
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('branch.email.exists') ? 'active' : '' }}"
                                        href="{{ route('branch.email.exists') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- DS Chi Nhánh --}}
                                            Đăng ký thêm địa điểm
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('branch.delete.reuired') ? 'active' : '' }}"
                                        href="{{ route('branch.delete.reuired') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- DS Chi Nhánh --}}
                                            Yêu cầu xóa địa điểm
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        {{-- level 0 Lịch tổng quan --}}
                        <li class="nav-item">
                            <a href="{{ route('booking.calendar', ['date' => date('Y-m-d')]) }}"
                                class="nav-link {{ request()->routeIs('booking.calendar') ? 'bg-light' : '' }}">
                                <p><i class="bi bi-calendar-week-fill"></i>
                                    Lịch tổng quan
                                </p>
                            </a>
                        </li>

                        {{-- level 0 Lịch theo ngày --}}
                        {{-- <li class="nav-item">
                            <a href="{{ route('booking.lichtheongay') }}"
                                class="nav-link {{ request()->routeIs('booking.lichtheongay') ? 'bg-light' : '' }}">
                                <p><i class="bi bi-calendar-week-fill"></i>
                                    Lịch theo ngày
                                </p>
                            </a>
                        </li> --}}

                        {{-- level 0 quản lý nhân viên --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('manage-branches.viewStaff') ||
                            request()->routeIs('manage-branches.editStaff') ||
                            request()->routeIs('manage-branches.createStaff')
                                ? 'bg-light'
                                : '' }}"
                                href="{{ route('manage-branches.viewStaff') }}">
                                {{-- <i class="far fa-circle nav-icon"></i> --}}
                                <p><i class="bi bi-people-fill"></i>
                                    {{-- Tạo nhân viên chi nhánh --}}
                                    Quản lý nhân viên
                                </p>
                            </a>
                        </li>

                        {{-- level 0 Quản lý sân --}}
                        <li
                            class="nav-item {{ request()->routeIs('manage-courts.getCreate') ||
                            request()->routeIs('courts.index') ||
                            // request()->routeIs('booking.calendar') ||
                            request()->routeIs('courts.show')
                                ? 'menu-open'
                                : '' }}">
                            <a href="#" class="nav-link">
                                <p><i class="bi bi-bounding-box"></i>
                                    Quản lý sân
                                    {{-- cấp 0 quản lý sân --}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            {{-- cấp 1 DS sân --}}
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('courts.index') || request()->routeIs('courts.show') ? 'active' : '' }}"
                                        href="{{ route('courts.index') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- DS sân --}}
                                            Danh sách sân
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                            </ul>

                            {{-- cấp 1 Tạo sân --}}
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('manage-courts.getCreate') ? 'active' : '' }}"
                                        href="{{ route('manage-courts.getCreate') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- Tạo sân --}}
                                            Tạo sân
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- level 0 Quản lý Bảng giá --}}
                        <li
                            class="nav-item {{ request()->routeIs('price_list.create') || request()->routeIs('price_list.index') || request()->routeIs('price_list.edit') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <p><i class="bi bi-coin"></i>
                                    Quản lý Bảng giá
                                    {{-- cấp 0 quản lý Bảng giá --}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            {{-- cấp 1 DS Bảng giá --}}
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('price_list.index') || request()->routeIs('price_list.edit') ? 'active' : '' }}"
                                        href="{{ route('price_list.index') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- DS bảng giá --}}
                                            Danh sách bảng giá
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                            </ul>
                            {{-- cấp 1 Thêm bảng giá --}}
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('price_list.create') ? 'active' : '' }}"
                                        href="{{ route('price_list.create') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- Thêm bảng giá --}}
                                            Thêm bảng giá
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        {{-- level 0 Quản lý thanh toán --}}
                        <li class="nav-item">
                            <a href="{{ route('manager.payment') }}"
                                class="nav-link {{ request()->routeIs('manager.payment') || request()->routeIs('manager.searchBookings') ? 'bg-light' : '' }}">
                                <p><i class="bi bi-credit-card-2-back"></i>
                                    Quản lý thanh toán
                                    {{-- cấp 0 quản lý sân --}}

                                </p>
                            </a>
                        </li>
                        {{-- ----------------------------------------------------------------------------------------- --}}
                    @elseif (Auth::user()->Role == '4')
                        {{-- Branch Staff --}}
                        {{-- level 0 Lịch tổng quan --}}
                        <li class="nav-item">
                            <a href="{{ route('booking.calendar', ['date' => date('Y-m-d')]) }}"
                                class="nav-link {{ request()->routeIs('booking.calendar') ? 'bg-light' : '' }}">
                                <p><i class="bi bi-calendar-week-fill"></i>
                                    Lịch tổng quan
                                </p>
                            </a>
                        </li>

                        {{-- level 0 Lịch theo ngày --}}
                        {{-- <li class="nav-item">
                            <a href="{{ route('booking.lichtheongay') }}"
                                class="nav-link {{ request()->routeIs('booking.lichtheongay') ? 'bg-light' : '' }}">
                                <p><i class="bi bi-calendar-week-fill"></i>
                                    Lịch theo ngày
                                </p>
                            </a>
                        </li> --}}

                        {{-- level 0 Quản lý sân --}}
                        <li
                            class="nav-item {{ request()->routeIs('manage-courts.getCreate') ||
                            request()->routeIs('courts.index') ||
                            // request()->routeIs('booking.calendar') ||
                            request()->routeIs('courts.show')
                                ? 'menu-open'
                                : '' }}">
                            <a href="#" class="nav-link">
                                <p><i class="bi bi-bounding-box"></i>
                                    Quản lý sân
                                    {{-- cấp 0 quản lý sân --}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            {{-- cấp 1 DS sân --}}
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('courts.index') || request()->routeIs('courts.show') ? 'active' : '' }}"
                                        href="{{ route('courts.index') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- DS sân --}}
                                            Danh sách sân
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- level 0 Quản lý Bảng giá --}}
                        <li
                            class="nav-item {{ request()->routeIs('price_list.create') || request()->routeIs('price_list.index') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <p><i class="bi bi-coin"></i>
                                    Quản lý Bảng giá
                                    {{-- cấp 0 quản lý Bảng giá --}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            {{-- cấp 1 DS Bảng giá --}}
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('price_list.index') ? 'active' : '' }}"
                                        href="{{ route('price_list.index') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- DS bảng giá --}}
                                            Danh sách bảng giá
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        {{-- level 0 Quản lý thanh toán --}}
                        <li class="nav-item">
                            <a href="{{ route('manager.payment') }}"
                                class="nav-link {{ request()->routeIs('manager.payment') || request()->routeIs('manager.searchBookings') ? 'bg-light' : '' }}">
                                <p><i class="bi bi-credit-card-2-back"></i>
                                    Quản lý thanh toán
                                    {{-- cấp 0 quản lý sân --}}

                                </p>
                            </a>
                        </li>
                        {{-- ------------------------------------------------------------------------------------------- --}}
                    @endif

                @endauth
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
