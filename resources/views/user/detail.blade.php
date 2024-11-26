@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <form action="{{ route('manage-account.update', ['id' => $account->User_id]) }}" method="POST">
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

            <div class="mb-3">
                <label for="role" class="form-label">Role <span style="color:red">*</span></label>
                @if ($account->Role == '1')
                    <select name="role" id="role" class="form-control" required onchange="toggleBranchSelection()">
                        <option value="1" {{ $account->Role == '1' ? 'selected' : '' }}>Admin</option>
                    </select>
                @else
                    <select name="role" id="role" class="form-control" required onchange="toggleBranchSelection()">
                        {{-- <option value="1" {{ $account->Role == '1' ? 'selected' : '' }}>Admin</option> --}}
                        @if ($account->Role == '3')
                            <option value="3" {{ $account->Role == '3' ? 'selected' : '' }}>Chủ sân</option>
                        @elseif ($account->Role == '2')
                            <option value="2" {{ $account->Role == '2' ? 'selected' : '' }}>SubAdmin</option>
                        @elseif ($account->Role == '4')
                            {{-- <option value="3" {{ $account->Role == '3' ? 'selected' : '' }}>Chủ sân</option> --}}
                            <option value="4" {{ $account->Role == '4' ? 'selected' : '' }}>Nhân viên</option>
                        @elseif ($account->Role == '5')
                            <option value="5" {{ $account->Role == '5' ? 'selected' : '' }}>Khách hàng</option>
                        @endif
                    </select>
                @endif
            </div>

            @if ($account->Role == '3')
                <div class="mb-3">
                    <label for="branchowned" class="form-label">Select branches <span style="color:red">*</span></label>
                    @foreach ($branches as $branch)
                        @if ($branch->manager_id == $manager->Manager_id)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="branch_ids[]"
                                    value="{{ $branch->Branch_id }}" id="branch_{{ $branch->Branch_id }}" checked disabled>
                                <label class="form-check-label" for="branch_{{ $branch->Branch_id }}">
                                    {{ $branch->Name }}
                                </label>
                            </div>
                        @else
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="branch_ids[]"
                                    value="{{ $branch->Branch_id }}" id="branch_{{ $branch->Branch_id }}">
                                <label class="form-check-label" for="branch_{{ $branch->Branch_id }}">
                                    {{ $branch->Name }}
                                </label>
                            </div>
                        @endif
                    @endforeach
                </div>
            @elseif($account->Role == '4')
                <div class="form-group">
                    <label for="branch">Địa điểm <span style="color:red">*</span></label>
                    <select name="branch_id" id="branch" class="form-control" required>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->Branch_id }}"
                                {{ $staff->branch_id == $branch->Branch_id ? 'selected' : '' }}>{{ $branch->Name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button type="submit" class="btn btn-success">Cập nhật thông tin</button>
            <a href="{{ route('manage-account.viewAll') }}" class="btn btn-secondary">Trở lại</a>
            <a href="{{ route('manage-account.changePasswordForm', ['id' => $account->User_id]) }}"
                class="btn btn-warning">Đổi mật khẩu</a>
        </form>
    </div>
    <script>
        function toggleBranchSelection() {
            const role = document.getElementById('role').value;
            const branchSelection = document.getElementById('branchSelection');
            // Show the branch selection if the role is "Chủ sân" or "Nhân viên"
            if (role === '3' || role === '4') {
                branchSelection.style.display = 'block';
            } else {
                branchSelection.style.display = 'none';
            }
        }

        // Initialize the display of branch selection based on the current role
        document.addEventListener('DOMContentLoaded', toggleBranchSelection);
    </script>
@endsection
