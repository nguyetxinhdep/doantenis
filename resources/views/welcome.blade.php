<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sân Tenis</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    {{-- link file styles.css --}}
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="gradient-background">
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
                <!-- Left Side Of Navbar -->

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <b class="text-white" style="font-size: 17px">Login</b>
                                </a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <b class="text-white" style="font-size: 17px">Branch Register</b>
                                </a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">
                                <b class="text-white" style="font-size: 17px"> Trang Quản Lý</b>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <b class="text-white" style="font-size: 17px"> {{ Auth::user()->Name }}</b>

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

    <div class="container-fluid d-flex align-items-center" style="height: calc(100vh - 60px)">

        <div class="container ">
            <div class="row">
                <!-- Left content area -->
                <div class="col-md-6 d-flex align-items-center justify-content-center text-white background-bong-tenis">
                    <div class="text-center">
                        <h1>Welcome to Tennis Court Booking</h1>
                        <p>Book your court now and enjoy the game!</p>

                        <div class="position-relative">
                            <form class="d-flex" id="search-form">
                                <input class="form-control" type="search" placeholder="Search" aria-label="Search"
                                    id="search-input">
                                {{-- <button class="btn btn-outline-light" type="submit">Search</button> --}}
                            </form>
                            <ul id="suggestions-list" class="list-group position-absolute mt-1 text-start"
                                style="display: none; width: 100%; z-index: 1000;">
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Right image area -->
                <div class="col-md-6 py-5 d-md-block text-center">
                    <!-- Đặt ảnh ở đây -->
                    <img src="/images/khachhang/background_welcome.png" class="img-fluid"
                        style="background-color: transparent;" alt="Tennis Court Image">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="suggestionModal" tabindex="-1" aria-labelledby="suggestionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Thêm lớp modal-lg -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="suggestionModalLabel">Court Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <!-- Ảnh bìa -->
                    <div class="position-relative mb-3" style="height: 200px;">
                        <img id="modal-cover-image" src="" alt="Cover Image" class="img-fluid"
                            style="width: 100%; height: 100%; object-fit: cover;">
                        <!-- Ảnh đại diện -->
                        <img id="modal-image" src="" alt="Court Image" class="img-fluid rounded-circle"
                            style="width: 100px; height: 100px; position: absolute; bottom: -50px; left: 50%; transform: translateX(-50%); border: 3px solid white;">
                    </div>
                    <!-- Tên sân -->
                    <h3 id="modal-name" class="mt-5"></h3>
                    <!-- Địa chỉ -->
                    <h6 id="modal-address"></h6>
                    <!-- Bản đồ Google Maps -->
                    <div id="map-container" class="mt-3">
                        <iframe id="modal-map" src="" width="100%" height="450" style="border:0;"
                            allowfullscreen="" loading="lazy"></iframe>
                    </div>
                    <div class="">
                        <a href="#" class="btn btn-primary">Đặt sân ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>





    <script>
        $(document).ready(function() {
            $('#search-input').on('keyup', function() {
                var query = $(this).val();

                if (query.length > 0) {
                    $.ajax({
                        url: '{{ route('search') }}',
                        method: 'GET',
                        data: {
                            query: query
                        },
                        success: function(data) {
                            $('#suggestions-list').empty().show();
                            $.each(data, function(index, suggestion) {
                                $('#suggestions-list').append(
                                    '<li class="list-group-item suggestion-item" style="cursor: pointer;" ' +
                                    'data-name="' + suggestion.Name + '" ' +
                                    'data-address="' + suggestion.Location + '" ' +
                                    'data-image="' + suggestion.Image + '" ' +
                                    'data-cover-image="' + suggestion.Cover_image +
                                    '" ' +
                                    'data-map-url="' + suggestion.link_map.split(
                                        '"')[1] + '">' +
                                    '<strong>' + suggestion.Name + '</strong><br>' +
                                    '<small>' + suggestion.Location + '</small>' +
                                    '</li>');
                            });
                        }
                    });
                } else {
                    $('#suggestions-list').hide();
                }
            });

            $(document).on('click', function() {
                $('#suggestions-list').hide();
            });

            // Khi nhấp vào một gợi ý
            $(document).on('click', '.suggestion-item', function() {
                // Lấy thông tin từ thuộc tính dữ liệu
                var name = $(this).data('name');
                var address = $(this).data('address');
                var image = $(this).data('image');
                var coverImage = $(this).data('cover-image'); // Lấy ảnh bìa
                var mapUrl = $(this).data('map-url');

                // Cập nhật thông tin cho modal
                $('#modal-image').attr('src', image);
                $('#modal-cover-image').attr('src', coverImage); // Cập nhật ảnh bìa
                $('#modal-name').text(name);
                $('#modal-address').text(address);
                $('#modal-map').attr('src', mapUrl); // Cập nhật src của iframe

                // Hiển thị modal
                $('#suggestionModal').modal('show');
            });
        });
    </script>

</body>

</html>
