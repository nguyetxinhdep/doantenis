@extends('layouts.app')

@section('content')
    <div class="container my-4">

        <form action="{{ route('admin.account.store.nhanvienhetong') }}" method="POST">
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
                <label for="phone" class="form-label">Số điện thoại <span style="color:red">*</span></label>
                <input type="text" name="phone" id="phone" class="form-control">
            </div>

            {{-- <div class="mb-3" id="branchSelection">
                <label for="branch" class="form-label">Chọn địa điểm <span style="color:red">*</span></label>
                <select name="branch_id" id="branch" class="form-control" required>
                    <option value="">-- Choose Branch --</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->Branch_id }}">
                            {{ $branch->Name }}
                        </option>
                    @endforeach
                </select>
            </div> --}}

            <button type="submit" class="btn btn-primary">Thêm Tài Khoản</button>
            <a href="javascript:history.back()" class="btn btn-secondary">Trở lại</a>
        </form>
    </div>
@endsection
