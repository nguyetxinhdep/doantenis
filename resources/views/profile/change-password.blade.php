@extends('layouts.app')

@section('content')
    @if ($errors->any())
        <script>
            showAlert('danger', {!! json_encode($errors->first()) !!});
        </script>
    @endif
    <div class="container py-4">
        <form id ="form-changepass" action="{{ route('profile.updatePassword') }}" method="POST">
            @csrf
            @method('post')

            <div class="form-group">
                <label for="current_password">Mật khẩu hiện tại <span style="color:red">*</span></label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>

            <div class="form-group">
                <label for="new_password">Mật khẩu mới <span style="color:red">*</span></label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>

            <div class="form-group">
                <label for="new_password_confirmation">Xác nhận mật khẩu mới <span style="color:red">*</span></label>
                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation"
                    required>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
        </form>
    </div>
@endsection
