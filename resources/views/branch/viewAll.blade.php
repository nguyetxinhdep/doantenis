@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-3">
        <a href="{{ route('admin.branch.register') }}" class="btn btn-primary mb-3">Thêm địa điểm</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên địa điểm</th>
                    <th>Địa Chỉ</th>
                    <th>Hotline</th>
                    <th>Email</th>
                    <th>Người Đăng Ký</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 0;
                @endphp
                @foreach ($branches as $branch)
                    @php
                        ++$i;
                    @endphp
                    <tr id = "{{ $branch->Branch_id }}">
                        <td>{{ $i }}</td>
                        <td>{{ $branch->Name }}</td>
                        <td>{{ $branch->Location }}</td>
                        <td>0{{ $branch->Phone }}</td>
                        <td>{{ $branch->Email }}</td>
                        <td>{{ $branch->user_name }}</td>
                        <td class="d-flex align-items-center">
                            <a href="{{ route('admin.manage-branches.detail', ['id' => $branch->Branch_id]) }}"
                                class="btn btn-primary btn-sm me-1"><i class="fas fa-eye"></i></a>

                            <form id="deleteForm{{ $branch->Branch_id }}" action="{{ route('rejectBranch') }}"
                                method="POST" class="d-inline">
                                @csrf
                                @method('POST')

                                <input type="hidden" name="User_id" value="{{ $branch->user_id }}">
                                <input type="hidden" name="Manager_id" value="{{ $branch->manager_id }}">
                                <input type="hidden" name="Branch_id" value="{{ $branch->Branch_id }}">
                                <input type="hidden" name="Email" value="{{ $branch->Email }}">

                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="showDeleteModal({{ $branch->Branch_id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
