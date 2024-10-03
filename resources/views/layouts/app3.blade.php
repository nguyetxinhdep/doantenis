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

    {{-- link file styles.css --}}
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div id="app">
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
        <div id="alert-container"></div>

        {{-- header --}}
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    Website Tenis
                </a>
                {{-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button> --}}

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('home') ? 'active fw-bold' : '' }}"
                                    href="{{ route('home') }}">
                                    Home
                                </a>
                            </li>
                            @if (Auth::user()->Role == '1')
                                {{-- Admin --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('pending.approval') ? 'active fw-bold' : '' }}"
                                        href="{{ route('pending.approval') }}">
                                        Chờ Duyệt
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('pending.agree') ? 'active fw-bold' : '' }}"
                                        href="{{ route('pending.agree') }}">
                                        Chờ Thỏa Thuận
                                    </a>
                                </li>

                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->routeIs('dropdown') ? 'active fw-bold' : '' }}"
                                        href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        QL Chí Nhánh
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item {{ request()->routeIs('manage-branches.viewAll') ? 'active fw-bold' : '' }}"
                                                href="{{ route('manage-branches.viewAll') }}">DS Chi Nhánh</a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('another-action') ? 'active fw-bold' : '' }}"
                                                href="">Another action</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item {{ request()->routeIs('something-else') ? 'active fw-bold' : '' }}"
                                                href="">Something else here</a></li>
                                    </ul>
                                </li>
                            @elseif (Auth::user()->Role == '2')
                                {{-- Sub Admin --}}
                            @elseif (Auth::user()->Role == '3')
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
                                        @else
                                            <hr>
                                            <li><a class="dropdown-item" href="{{ route('branch.email.exists') }}">Đăng ký
                                                    thêm chi nhánh</a></li>
                                        @endif
                                    </ul>
                                </li>
                            @elseif (Auth::user()->Role == '4')
                                {{-- Branch Staff --}}
                            @endif
                        @endauth

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav
                                            ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">Branch Register</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->Name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="get" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        {{-- @include('layouts.sidebar') --}}


        {{-- content --}}
        <main class="py-4">
            @yield('content')
        </main>

        {{-- footer --}}
        @include('layouts.footer')
    </div>

</body>

</html>
