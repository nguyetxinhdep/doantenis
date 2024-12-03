@extends('layouts.app')

@section('content')
    <div class="container my-3">
        <form action="{{ route('manage-branches.storeStaff') }}" method="POST">
            @csrf
            <!-- Chọn chi nhánh -->
            <div class="form-group">
                <label for="branch">Địa điểm kinh doanh <span style="color:red">*</span></label>
                <select name="branch_id" id="branch" class="form-control">
                    @foreach ($branches as $branch)
                        <option
                            {{ old('branch_id', session('branch_active')->Branch_id) == $branch->Branch_id ? 'selected' : '' }}
                            value="{{ $branch->Branch_id }}">{{ $branch->Name }}</option>
                    @endforeach
                </select>
                @error('branch_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">Tên Nhân Viên <span style="color:red">*</span></label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Nhập tên nhân viên"
                    value="{{ old('name') }}" required>
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email <span style="color:red">*</span></label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Nhập email"
                    value="{{ old('email') }}" required>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone">Số Điện Thoại <span style="color:red">*</span></label>
                <input type="text" name="phone" id="phone" class="form-control" placeholder="Nhập số điện thoại"
                    value="{{ old('phone') }}" required>
                @error('phone')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <input type="text" name="address" id="address" class="form-control" placeholder="Nhập địa chỉ"
                    value="{{ old('address') }}" required>
                @error('address')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>


            <!-- Nút submit -->
            <button type="submit" class="btn btn-primary">Thêm Nhân Viên</button>
            <a href="javascript:history.back()" class="btn btn-secondary">Trở lại</a>
        </form>
    </div>
@endsection
