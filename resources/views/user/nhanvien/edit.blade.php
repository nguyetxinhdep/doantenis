@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <form action="{{ route('admin.account.update.nhanvien', ['id' => $account->User_id]) }}" method="POST">
            @csrf
            @method('POST')

            <div class="mb-3">
                <label for="name" class="form-label">Name <span style="color:red">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $account->Name }}" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email <span style="color:red">*</span></label>
                <input type="email" name="email" id="email" class="form-control" value="{{ $account->Email }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone <span style="color:red">*</span></label>
                <input type="text" name="phone" id="phone" class="form-control" value="0{{ $account->Phone }}">
            </div>

            <div class="mb-3" id="branchSelection">
                <label for="branch" class="form-label">Chọn địa điểm <span style="color:red">*</span></label>
                <select name="branch_id" id="branch" class="form-control" required>
                    <option value="">-- Choose Branch --</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->Branch_id }}"
                            {{ $staff->branch_id == $branch->Branch_id ? 'selected' : '' }}>
                            {{ $branch->Name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">Update Account</button>
            <a href="javascript:history.back()" class="btn btn-secondary">Trở lại</a>
            <a href="{{ route('manage-account.changePasswordForm', ['id' => $account->User_id]) }}"
                class="btn btn-warning">Đổi mật khẩu</a>
        </form>
    </div>
@endsection
