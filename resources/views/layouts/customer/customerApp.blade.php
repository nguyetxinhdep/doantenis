<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sân Tenis nè</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">

    {{-- link file styles.css --}}
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="/template/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>


    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="gradient-background">
    @include('layouts.customer.nav')

    @yield('content')
    {{-- <div class="container-fluid d-flex align-items-center" style="height: calc(100vh - 60px)">

    </div> --}}



</body>

</html>
