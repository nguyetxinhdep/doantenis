@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <h1>Danh sách sân</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tên sân</th>
                    <th>Tình trạng</th>
                    {{-- <th>Chi tiết</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($courts as $court)
                    <tr>
                        <td>{{ $court->Name }}</td>
                        <td>{{ $court->Availability ? 'Hoạt động' : 'Đang bảo trì' }}</td>
                        {{-- <td>
                            <a href="{{ route('courts.show', $court->Court_id) }}" class="btn btn-info">Xem chi tiết</a>
                        </td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Hiển thị phân trang -->
        <div class="">
            {{ $courts->links('pagination::bootstrap-5') }}
        </div>

    </div>
@endsection
