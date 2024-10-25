@extends('layouts.app')

@section('content')
    <div class="container py-4">

        <form action="{{ route('profile.update', Auth::user()->User_id) }}" method="POST">
            @csrf
            @method('post')

            <div class="form-group">
                <label for="name">Họ tên</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="{{ old('name', Auth::user()->Name) }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="{{ old('email', Auth::user()->Email) }}" required>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone"
                    value="0{{ old('phone', Auth::user()->Phone) }}" required>
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <input type="text" class="form-control" id="address" name="address"
                    value="{{ old('address', Auth::user()->Address) }}" {{ Auth::user()->Role == '5' ? '' : 'required' }}>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    </div>
@endsection
