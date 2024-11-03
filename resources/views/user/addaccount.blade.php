@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <form action="{{ route('admin.account.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Tên <span style="color:red">*</span></label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email <span style="color:red">*</span></label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Điện thoại <span style="color:red">*</span></label>
                <input type="text" name="phone" id="phone" class="form-control">
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Vai trò <span style="color:red">*</span></label>
                <select name="role" id="role" class="form-control" required onchange="toggleBranchSelection()">
                    {{-- <option value="1">Admin</option> --}}
                    <option value="2">SubAdmin</option>
                    <option value="3">Chủ sân</option>
                    <option value="4">Nhân viên</option>
                    <option value="5">Khách hàng</option>
                </select>
            </div>

            <div class="mb-3" id="branchSelection" style="display: none;">
                <label for="branch" class="form-label">Chọn Địa điểm <span style="color:red">*</span></label>
                <select name="branch_id" id="branch" class="form-control">
                    <option value="">-- Chọn Chi Nhánh --</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->Branch_id }}">{{ $branch->Name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Thêm Tài Khoản</button>
            <a href="{{ route('manage-account.viewAll') }}" class="btn btn-secondary">Trở lại</a>
        </form>
    </div>

    <script>
        function toggleBranchSelection() {
            const role = document.getElementById('role').value;
            const branchSelection = document.getElementById('branchSelection');
            // Hiển thị lựa chọn chi nhánh nếu vai trò là "Chủ sân" hoặc "Nhân viên"
            if (role === '3' || role === '4') {
                branchSelection.style.display = 'block';
            } else {
                branchSelection.style.display = 'none';
            }
        }
    </script>
@endsection
