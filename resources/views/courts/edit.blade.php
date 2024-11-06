@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <h1>Chỉnh sửa sân</h1>
        <form action="{{ route('courts.update', $court->Court_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="Name" class="form-label">Tên sân</label>
                <input type="text" class="form-control" id="Name" name="Name" value="{{ $court->Name }}" required>
            </div>
            <div class="mb-3">
                <label for="Availability" class="form-label">Tình trạng</label>
                <select class="form-control" id="Availability" name="Availability">
                    <option value="1" {{ $court->Availability ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ !$court->Availability ? 'selected' : '' }}>Đang bảo trì</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Lưu</button>
            <a href="{{ route('courts.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
@endsection
