@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1>{{ $title }}</h1>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td>
                        <form id="checkform" action="{{ route('approveBranch') }}" method="POST" style="display:inline;">
                            @csrf
                            @method('POST')

                            <input type="hidden" name="User_id" value = "{{ $User_id }}">
                            <input type="hidden" name="Manager_id" value = "{{ $Manager_id }}">
                            <input type="hidden" name="Branch_id" value = "{{ $Branch_id }}">
                            <input type="hidden" name="Email" value = "{{ $Email }}">
                            <div class="mb-3">
                                <label for="date" class="form-label">Chọn Ngày</label>
                                <input type="date" id="date" name="date" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="time" class="form-label">Chọn Giờ</label>
                                <input type="time" id="time" name="time" class="form-control" required>
                            </div>


                            <button type="submit" class="btn btn-success btn-sm">Đồng Ý</button>
                            <a href="{{ route('pending.approval') }}" class="btn btn-warning btn-sm">Trở lại</a>
                        </form>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#checkform').on('submit', function(event) {
                event.preventDefault(); // Ngăn chặn việc gửi form mặc định

                var formData = $(this).serialize(); // Lấy dữ liệu từ form

                // Hiển thị overlay và spinner khi gửi yêu cầu AJAX
                $('#overlay-spinner').removeClass('d-none');

                $.ajax({
                    url: '{{ route('approveBranch') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Ẩn overlay và spinner sau khi nhận phản hồi
                        $('#overlay-spinner').addClass('d-none');

                        // Reset form sau khi gửi thành công
                        $('#checkform')[0].reset(); // Sử dụng jQuery để reset form

                        showAlert('success', response.message);

                        // window.location.href = response.redirect;
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
