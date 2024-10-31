<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sân Tenis nè</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    {{-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet"> --}}
    <link rel="stylesheet" href="/template/plugins/icheck-bootstrap/icheck-bootstrap.min.css">


    {{-- link file styles.css --}}
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="/template/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>


    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="gradient-background">
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

    @include('layouts.customer.nav')

    @yield('content')
    {{-- <div class="container-fluid d-flex align-items-center" style="height: calc(100vh - 60px)">

    </div> --}}



</body>

</html>
