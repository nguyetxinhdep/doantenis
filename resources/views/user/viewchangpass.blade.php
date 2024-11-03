@extends('layouts.app')

@section('content')
    <div class="container my-4">

        <form action="{{ route('manage-account.changePassword', ['id' => $account->User_id]) }}" method="POST">
            @csrf
            @method('POST')

            <div class="mb-3">
                <label for="new_password" class="form-label">Mật khẩu mới</label>
                <input type="password" name="new_password" id="new_password" class="form-control" required>
                @error('new_password')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                    required>
            </div>

            <button type="submit" class="btn btn-success">Đổi Mật Khẩu</button>
            <a href="javascript:history.back()" class="btn btn-secondary">Trở lại</a>
        </form>
    </div>
@endsection
