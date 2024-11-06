@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>ID địa điểm</th>
                    <th>Tên địa điểm</th>
                    <th>Địa Chỉ </th>
                    <th>Hotline</th>
                    <th>Email</th>
                    <th>Người Đăng Ký</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $dem = 0;
                @endphp
                @if ($branches->isEmpty())
                    <tr>
                        <td colspan="8" style="text-align: center">Không có dữ liệu</td>
                    </tr>
                @else
                    @foreach ($branches as $branch)
                        <tr id = "{{ $branch->Branch_id }}">
                            <td>{{ ++$dem }}</td>
                            <td>{{ $branch->Branch_id }}</td>
                            <td>{{ $branch->Name }}</td>
                            <td>{{ $branch->Location }}</td>
                            <td>0{{ $branch->Phone }}</td>
                            <td>{{ $branch->Email }}</td>
                            <td>{{ $branch->user_name }}</td>
                            <td>
                                <form action="{{ route('agree.success') }}" method="POST"
                                    id="form-agree-{{ $branch->Branch_id }}" style="display:inline;">
                                    @csrf
                                    @method('POST')

                                    <input type="hidden" name="User_id" value = "{{ $branch->user_id }}">
                                    <input type="hidden" name="Manager_id" value = "{{ $branch->manager_id }}">
                                    <input type="hidden" name="Branch_id" value = "{{ $branch->Branch_id }}">
                                    <input type="hidden" name="Email" value = "{{ $branch->Email }}">

                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i>{{-- icon đồng ý --}}
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
                                        <i class="fas fa-trash"></i>{{-- icon từ chối xóa --}}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('form[id^="form-agree-"]').on('submit', function(event) {
                event.preventDefault(); // Ngăn chặn việc gửi form mặc định

                var formData = $(this).serialize(); // Lấy dữ liệu từ form

                // Hiển thị overlay và spinner khi gửi yêu cầu AJAX
                $('#overlay-spinner').removeClass('d-none');

                $.ajax({
                    url: '{{ route('agree.success') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Ẩn overlay và spinner sau khi nhận phản hồi
                        $('#overlay-spinner').addClass('d-none');

                        // Xóa <tr> với ID tương ứng
                        $('#' + response.branch_id).remove();

                        showAlert('success', response.message);
                    },
                    error: function(xhr) {

                        $('#overlay-spinner').addClass('d-none');

                        showAlert('danger', 'Đã có lỗi xảy ra. Vui lòng thử lại!');
                    }
                });
            });
        });
    </script>
@endsection
