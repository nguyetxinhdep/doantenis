@extends('layouts.app')

@section('content')
    <div class="container my-3">
        <form action="{{ route('manage-branches.storeStaff') }}" method="POST">
            @csrf
            <!-- Chọn chi nhánh -->
            <div class="form-group">
                <label for="branch">Chi Nhánh</label>
                <select name="branch_id" id="branch" class="form-control">
                    @foreach ($branches as $branch)
                        <option {{ session('branch_active')->Branch_id == $branch->Branch_id ? 'selected' : '' }}
                            value="{{ $branch->Branch_id }}">{{ $branch->Name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nhập tên nhân viên -->
            <div class="form-group">
                <label for="name">Tên Nhân Viên</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Nhập tên nhân viên"
                    required>
            </div>

            <!-- Nhập email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Nhập email" required>
            </div>

            <!-- Nhập số điện thoại -->
            <div class="form-group">
                <label for="phone">Số Điện Thoại</label>
                <input type="text" name="phone" id="phone" class="form-control" placeholder="Nhập số điện thoại"
                    required>
            </div>

            <!-- Nhập chức vụ -->
            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <input type="text" name="address" id="address" class="form-control" placeholder="Nhập địa chỉ"
                    required>
            </div>

            <!-- Nút submit -->
            <button type="submit" class="btn btn-primary">Thêm Nhân Viên</button>
        </form>
    </div>
@endsection
