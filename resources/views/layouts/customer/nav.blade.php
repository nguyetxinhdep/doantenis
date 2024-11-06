    <nav class="navbar navbar-expand-md shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <b class="text-white">Website Tenis</b>
            </a>
            {{-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button> --}}

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="text-white nav-link {{ request()->routeIs('danhsachsan') ? 'fw-bold text-info' : '' }}"
                            aria-current="page" href="{{ route('danhsachsan') }}">Danh sách sân</a>
                    </li>
                </ul>
                <!-- Left Side Of Navbar -->
                @auth
                    @if (Auth()->user()->Role == '5')
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="text-white nav-link {{ request()->routeIs('booking.history') ? 'fw-bold text-info' : '' }}"
                                    aria-current="page" href="{{ route('booking.history') }}">Lịch sử đặt sân</a>
                            </li>
                            <li class="nav-item">
                                <a class="text-white nav-link {{ request()->routeIs('register') ? 'fw-bold text-info' : '' }}"
                                    aria-current="page" href="{{ route('register') }}">Đăng ký kinh doanh</a>
                            </li>
                        </ul>
                    @endif
                @endauth

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <b class="text-white" style="font-size: 17px">Đăng nhập</b>
                                </a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.register') }}">
                                    <b class="text-white" style="font-size: 17px">Đăng ký</b>
                                </a>
                            </li>
                        @endif
                    @else
                        @if (Auth::user()->Role != '5')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">
                                    <b class="text-white" style="font-size: 17px"> Trang Quản Lý</b>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <b class="text-white" style="font-size: 17px"> {{ Auth::user()->Name }}</b>

                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">

                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    Thông tin cá nhân
                                </a><a class="dropdown-item" href="{{ route('profile.changePassword') }}">
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
            </div>
        </div>
    </nav>
