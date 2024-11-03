<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/template/plugins/fontawesome-free/css/all.min.css">
    <!-- overlayScrollbars -->
    {{-- <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css"> --}}
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="/template/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/template/dist/css/adminlte.min.css">

    {{-- link file styles.css --}}
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    <script src="/template/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>


    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <!-- Overlay and Spinner -->
    {{-- làm mờ và tạo máy quay spinner trong khi chờ phản hồi --}}
    <div id="overlay-spinner" class=d-none>
        <div class="overlay"></div>
        <div class="spinner-border" style="color: #44bf44" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    {{-- chỗ hiển thị thông báo lỗi --}}
    <!-- Thông báo sẽ được chèn vào đây -->
    <div id="alert-container" style="z-index: 1060"></div>

    {{-- include script aler ở đầu khi load cho khỏi bị mất --}}
    @include('layouts.alert')

    @if ($message = session('success'))
        <script>
            showAlert('success', {!! json_encode($message) !!});
        </script>
    @elseif($message = session('danger'))
        <script>
            showAlert('danger', {!! json_encode($message) !!});
        </script>
    @endif
    {{-- ----------------------------------------------------------------------------------- --}}
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/">
                        Trang chủ</a>
                </li>
                @auth
                    @if (Auth::user()->Role == '3')
                        {{-- Branch Manager  --}}

                        {{-- hiển thi dchi nhánh --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Chi Nhánh {{ session('branch_active')->Name }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

                                @foreach (session('all_branch') as $branch)
                                    @if ($branch->Branch_id != session('branch_active')->Branch_id)
                                        <li><a class="dropdown-item"
                                                href="{{ route('setBranchActive', [$branch->Branch_id]) }}">Chi
                                                Nhánh
                                                {{ $branch->Name }}</a>
                                        </li>
                                    @endif
                                @endforeach
                                @if (count(session('all_branch')) == 1)
                                    <li><a class="dropdown-item" href="{{ route('branch.email.exists') }}">Đăng ký
                                            thêm chi nhánh</a></li>
                                    <li><a class="dropdown-item" href="{{ route('manage-branches.reload') }}">Reload Chi
                                            nhánh</a></li>
                                @else
                                    {{-- Thêm dấu gạch --}}
                                    <hr>
                                    <li><a class="dropdown-item" href="{{ route('branch.email.exists') }}">Đăng ký
                                            thêm địa điểm kinh doanh</a></li>
                                    <li><a class="dropdown-item" href="{{ route('manage-branches.reload') }}">Reload Chi
                                            nhánh</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endauth
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                {{-- <li class="nav-item">
                    @auth
                        <a class="nav-link" href="/admin/logout" role="button">
                            Đăng xuất
                        </a>
                    @endauth
                </li> --}}
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Đăng nhập</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.register') }}">Đăng ký</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->Name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                Thông tin cá nhân
                            </a>

                            <a class="dropdown-item" href="{{ route('profile.changePassword') }}">
                                Đổi mật khẩu
                            </a>

                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                LogOut
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="get" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('layouts.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    {{-- @include('alert') --}}
                    <div class="row">
                        <!-- left column -->
                        <div class="col-md-12">
                            <!-- jquery validation -->
                            <div class="card card-primary mt-3">
                                <div class="card-header">
                                    <h3 class="card-title">{{ $title }}</h3>
                                </div>
                                {{-- Nội dung --}}
                                @yield('content')
                            </div>
                        </div>

                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </section>
        </div>


        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Welcome!</b>
            </div>
            <strong>Quản Lý Sân Tenis</strong>
        </footer>


        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    @include('layouts.modal')

</body>

</html>
