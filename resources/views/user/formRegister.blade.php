@extends('layouts.app')

@section('content')
    <div class="row justify-content-center mt-3">
        <div class="col-md-10">
            <div class="card">
                {{-- <div class="card-header">{{ __('Đăng Ký Chi Nhánh') }}</div> --}}

                <div class="card-body">
                    <form id="user-form" action="{{ route('user.register.store') }}" method="post">
                        @csrf

                        <div class="row mb-3">
                            <label for="Name" class="col-md-4 col-form-label text-md-end">Họ Tên <span
                                    style="color:red">*</span></label>
                            <div class="col-md-6">
                                <input id="Name" type="text"
                                    class="form-control @error('Name') is-invalid @enderror" name="Name" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="Phone" class="col-md-4 col-form-label text-md-end">Số điện thoại <span
                                    style="color:red">*</span></label>
                            <div class="col-md-6">
                                <input id="Phone" type="text"
                                    class="form-control @error('Phone') is-invalid @enderror" name="Phone" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="Email" class="col-md-4 col-form-label text-md-end">Email <span
                                    style="color:red">*</span></label>
                            <div class="col-md-6">
                                <input id="Email" type="email"
                                    class="form-control @error('Email') is-invalid @enderror" name="Email" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="Password" class="col-md-4 col-form-label text-md-end">Mật khẩu <span
                                    style="color:red">*</span></label>
                            <div class="col-md-6">
                                <input id="Password" type="password"
                                    class="form-control @error('Password') is-invalid @enderror" name="Password" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="Password_confirmation" class="col-md-4 col-form-label text-md-end">Xác nhận mật
                                khẩu <span style="color:red">*</span></label>
                            <div class="col-md-6">
                                <input id="Password_confirmation" type="password"
                                    class="form-control @error('Password_confirmation') is-invalid @enderror"
                                    name="Password_confirmation" required>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Đăng ký
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </form>
                    {{-- <div class="mt-3 text-center"><span style="color:red"><span class="fw-bold">Lưu ý:</span>
                            Nếu bạn muốn dùng 1 gmail để quản lý nhiều chi nhánh thì vui lòng
                            đăng nhập để đăng ký chi nhánh khác!</span>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#user-form').on('submit', function(event) {
                event.preventDefault(); // Ngăn chặn việc gửi form mặc định

                var formData = $(this).serialize(); // Lấy dữ liệu từ form

                // Hiển thị overlay và spinner khi gửi yêu cầu AJAX
                $('#overlay-spinner').removeClass('d-none');

                $.ajax({
                    url: '{{ route('user.register.store') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Ẩn overlay và spinner sau khi nhận phản hồi
                        $('#overlay-spinner').addClass('d-none');

                        showAlert('success', response.message);
                        // Reset form sau khi thành công
                        $('#user-form')[0].reset();

                        setTimeout(function() {
                            // Nếu xóa địa điểm đang là địa điểm hiện tại thì logout
                            window.location.href = "{{ route('login') }}";
                        }, 2000); // 2000 ms = 2 giây
                    },
                    error: function(xhr) {
                        $('#overlay-spinner').addClass('d-none');
                        if (xhr.status === 422) { //dữ liệu không hợp lệ
                            // Hiển thị lỗi xác thực
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = '';
                            $.each(errors, function(key, value) {
                                errorMessage += value[0] +
                                    '<br>'; // Lấy thông điệp lỗi đầu tiên
                            });
                            showAlert('danger', errorMessage); // Hiển thị thông báo lỗi
                        } else {
                            showAlert('danger', 'Đã có lỗi xảy ra. Vui lòng thử lại!');
                        }
                    }
                });
            });
        });
    </script>
@endsection
