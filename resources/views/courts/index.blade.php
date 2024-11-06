@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <h1>Danh sách sân</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tên sân</th>
                    <th>Tình trạng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($courts as $court)
                    <tr>
                        <td>{{ $court->Name }}</td>
                        <td>{{ $court->Availability ? 'Hoạt động' : 'Đang bảo trì' }}</td>
                        <td>
                            <a href="{{ route('courts.edit', $court->Court_id) }}" class="btn btn-primary btn-sm">Sửa</a>
                            <form action="{{ route('courts.destroy', $court->Court_id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa sân này?');">Xóa</button>
                            </form>
                        </td>
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
