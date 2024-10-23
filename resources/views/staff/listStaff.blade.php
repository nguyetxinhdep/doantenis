@extends('layouts.app')

@section('content')
    <div class="container py-3">

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Chi nhánh</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @if ($staffs->isEmpty())
                    <tr>
                        <td colspan="10" class="text-center">Chưa có nhân viên.</td>
                    </tr>
                @else
                    @foreach ($staffs as $staff)
                        <tr id={{ $staff->User_id }}>
                            <td>{{ $staff->Staff_id }}</td>
                            <td>{{ $staff->Name }}</td>
                            <td>{{ $staff->Email }}</td>
                            <td>{{ $staff->Phone }}</td>
                            <td>{{ $staff->Address }}</td>
                            <td>{{ $staff->branch_id }}</td>
                            <td>
                                <a href="{{ route('manage-branches.editStaff', $staff->User_id) }}"
                                    class="btn btn-warning btn-sm">Sửa</a>
                                <form id="deleteForm{{ $staff->User_id }}" action="{{ route('manage-branches.destroy') }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    @method('post')
                                    <input type="hidden" name="user_id" value="{{ $staff->User_id }}">
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="showDeleteModal({{ $staff->User_id }})">
                                        <i class="fas fa-trash"></i>{{-- icon từ chối xóa --}}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <!-- Hiển thị phân trang -->
        <div class="">
            {{ $staffs->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
