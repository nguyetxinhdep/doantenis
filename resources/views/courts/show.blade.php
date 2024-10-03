@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <h1>Tên: {{ $court->Name }}</h1>
        <h5>Tình trạng: {{ $court->Availability ? 'Hoạt động' : 'Đang bảo trì' }}</h5>

        <h5>Giờ đã đặt</h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Đặt</th>
                    <th>Thời gian</th>
                    <th>Người đặt</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $booking)
                    <tr>
                        <td>{{ $booking->id }}</td>
                        <td>{{ $booking->time }}</td>
                        <td>{{ $booking->user->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Hiển thị phân trang -->
        <div class="">
            {{ $bookings->links('pagination::bootstrap-5') }}
        </div>

        <a href="{{ route('courts.index') }}" class="btn btn-secondary">Quay lại danh sách sân</a>
    </div>
@endsection
