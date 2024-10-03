@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <h1>{{ $title }}</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Chi Nhánh</th>
                    <th>Tên Chi Nhánh</th>
                    <th>Địa Chỉ CN</th>
                    <th>Hotline</th>
                    <th>Email</th>
                    <th>Người Đăng Ký</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($branches as $branch)
                    <tr id = "{{ $branch->Branch_id }}">
                        <td>{{ $branch->Branch_id }}</td>
                        <td>{{ $branch->Name }}</td>
                        <td>{{ $branch->Location }}</td>
                        <td>0{{ $branch->Phone }}</td>
                        <td>{{ $branch->Email }}</td>
                        <td>{{ $branch->user_name }}</td>
                        <td>
                            <form action="#" method="get" style="display:inline;">
                                @csrf
                                @method('get')

                                <input type="hidden" name="User_id" value = "{{ $branch->user_id }}">
                                <input type="hidden" name="Manager_id" value = "{{ $branch->manager_id }}">
                                <input type="hidden" name="Branch_id" value = "{{ $branch->Branch_id }}">
                                <input type="hidden" name="Email" value = "{{ $branch->Email }}">

                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> {{-- icon chi tiết --}}
                                </button>
                            </form>
                            <form id="deleteForm{{ $branch->Branch_id }}" action="{{ route('rejectBranch') }}"
                                method="POST" style="display:inline;">
                                @csrf
                                @method('POST')

                                <input type="hidden" name="User_id" value = "{{ $branch->user_id }}">
                                <input type="hidden" name="Manager_id" value = "{{ $branch->manager_id }}">
                                <input type="hidden" name="Branch_id" value = "{{ $branch->Branch_id }}">
                                <input type="hidden" name="Email" value = "{{ $branch->Email }}">


                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="showDeleteModal({{ $branch->Branch_id }})">
                                    <i class="fas fa-trash"></i>{{-- icon xóa  --}}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
