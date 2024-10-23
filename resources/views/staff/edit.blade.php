@extends('layouts.app')

@section('content')
    <div class="container py-3">
        {{-- <h1>Sửa thông tin nhân viên</h1> --}}

        <form action="{{ route('manage-branches.updateStaff', $staff->user_id) }}" method="POST">
            @csrf
            @method('post')

            <div class="form-group">
                <label for="name">Tên</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $staff->user->Name }}"
                    required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $staff->user->Email }}"
                    required>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ $staff->user->Phone }}"
                    required>
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <input type="text" class="form-control" id="address" name="address" value="{{ $staff->user->Address }}"
                    required>
            </div>

            <div class="form-group">
                <label for="branch">Chi Nhánh</label>
                <select name="branch_id" id="branch" class="form-control" required>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->Branch_id }}"
                            {{ $branch->Branch_id == $staff->branch_id ? 'selected' : '' }}>{{ $branch->Name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    </div>
@endsection
