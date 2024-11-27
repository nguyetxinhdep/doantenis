@extends('layouts.app')

@section('content')
    @if ($errors->any())
        <script>
            showAlert('danger', {!! json_encode($errors->first()) !!});
        </script>
    @endif
    <div class="container py-4">
        <!-- Form thay đổi mật khẩu -->
        <form id="form-changepass" action="{{ route('profile.updatePassword') }}" method="POST">
            @csrf
            @method('post')

            <div class="form-group">
                <label for="current_password">Mật khẩu hiện tại <span style="color:red">*</span></label>
                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                    id="current_password" name="current_password" required>
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="new_password">Mật khẩu mới <span style="color:red">*</span></label>
                <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password"
                    name="new_password" required>
                @error('new_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="new_password_confirmation">Xác nhận mật khẩu mới <span style="color:red">*</span></label>
                <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror"
                    id="new_password_confirmation" name="new_password_confirmation" required>
                @error('new_password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
        </form>
    </div>
@endsection
