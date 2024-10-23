<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link" style="text-decoration: none;">
        <img src="/template/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">Trang Quản Lý</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
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
                        {{ Auth::user()->Name }}
                        {{-- <p>Your email: {{ Auth::user()->email }}</p> --}}
                        {{-- <p>Your role: {{ Auth::user()->role }}</p> --}}
                    @endauth
                </a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                @auth
                    @if (Auth::user()->Role == '1')
                        {{-- level 0 Quản lý Duyệt --}}
                        <li
                            class="nav-item {{ request()->routeIs('pending.approval') ||
                            request()->routeIs('pending.agree') ||
                            request()->routeIs('approveBranch.selecttime')
                                ? 'menu-open'
                                : '' }}">
                            <a href="#" class="nav-link">

                                <p>
                                    Quản lý Duyệt
                                    {{-- cấp 0 quản lý Duyệt --}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            {{-- cấp 1 Chờ duyệt --}}
                            <ul class="nav nav-treeview">
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
                            </ul>
                            {{-- cấp 1 Chờ thỏa thuận --}}
                            <ul class="nav nav-treeview">
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

                        {{-- level 0 quản lí chi nhánh --}}
                        <li class="nav-item {{ request()->routeIs('manage-branches.viewAll') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">

                                <p>
                                    Quản lí chi nhánh
                                    {{-- cấp 0 quản lí chi nhánh --}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            {{-- cấp 1 DS Chi Nhánh --}}
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('manage-branches.viewAll') ? 'active' : '' }}"
                                        href="{{ route('manage-branches.viewAll') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- DS Chi Nhánh --}}
                                            DS Chi Nhánh
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        {{-- --------------------------------------------------------------------------------------------------- --}}
                    @elseif (Auth::user()->Role == '2')
                        {{-- Sub Admin --}}

                        {{-- ------------------------------------------------------------------------------------------------ --}}
                    @elseif (Auth::user()->Role == '3')
                        {{-- Branch Manager  --}}

                        {{-- level 0 quản lí chi nhánh --}}
                        <li
                            class="nav-item {{ request()->routeIs('manage-branches.detail') ||
                            request()->routeIs('manage-branches.create-staff') ||
                            request()->routeIs('manage-branches.createStaff') ||
                            request()->routeIs('manage-branches.viewStaff') ||
                            request()->routeIs('manage-branches.editStaff')
                                ? 'menu-open'
                                : '' }}">
                            <a href="#" class="nav-link">

                                <p>
                                    Quản lí chi nhánh
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
                                            Cập nhật thông tin Chi Nhánh
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('manage-branches.viewStaff') || request()->routeIs('manage-branches.editStaff')
                                        ? 'active'
                                        : '' }}"
                                        href="{{ route('manage-branches.viewStaff') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- Tạo nhân viên chi nhánh --}}
                                            Danh sách nhân viên
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('manage-branches.createStaff') ? 'active' : '' }}"
                                        href="{{ route('manage-branches.createStaff') }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- Tạo nhân viên chi nhánh --}}
                                            Tạo nhân viên chi nhánh
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- level 0 Quản lý sân --}}
                        <li
                            class="nav-item {{ request()->routeIs('manage-courts.getCreate') ||
                            request()->routeIs('courts.index') ||
                            request()->routeIs('booking.calendar') ||
                            request()->routeIs('courts.show')
                                ? 'menu-open'
                                : '' }}">
                            <a href="#" class="nav-link">
                                <p>
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
                                            DS sân
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                            </ul>
                            {{-- cấp 1 Lịch tổng quan --}}
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('booking.calendar') ? 'active' : '' }}"
                                        href="{{ route('booking.calendar', ['date' => date('Y-m-d')]) }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- Lịch tổng quan --}}
                                            Lịch tổng quan
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
                            class="nav-item {{ request()->routeIs('price_list.create') || request()->routeIs('price_list.index') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <p>
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
                                            DS bảng giá
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
                                class="nav-link {{ request()->routeIs('manager.payment') ? 'bg-light' : '' }}">
                                <p>
                                    Quản lý thanh toán
                                    {{-- cấp 0 quản lý sân --}}

                                </p>
                            </a>
                        </li>
                        {{-- ----------------------------------------------------------------------------------------- --}}
                    @elseif (Auth::user()->Role == '4')
                        {{-- Branch Staff --}}
                        {{-- level 0 Quản lý sân --}}
                        <li
                            class="nav-item {{ request()->routeIs('manage-courts.getCreate') ||
                            request()->routeIs('courts.index') ||
                            request()->routeIs('booking.calendar') ||
                            request()->routeIs('courts.show')
                                ? 'menu-open'
                                : '' }}">
                            <a href="#" class="nav-link">
                                <p>
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
                                            DS sân
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                            </ul>
                            {{-- cấp 1 Lịch tổng quan --}}
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('booking.calendar') ? 'active' : '' }}"
                                        href="{{ route('booking.calendar', ['date' => date('Y-m-d')]) }}">
                                        {{-- <i class="far fa-circle nav-icon"></i> --}}
                                        <p>
                                            {{-- Lịch tổng quan --}}
                                            Lịch tổng quan
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
                                <p>
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
                                            DS bảng giá
                                            {{-- <i class="right fas fa-angle-left"></i> --}}
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        {{-- level 0 Quản lý thanh toán --}}
                        <li class="nav-item">
                            <a href="{{ route('manager.payment') }}"
                                class="nav-link {{ request()->routeIs('manager.payment') ? 'bg-light' : '' }}">
                                <p>
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
