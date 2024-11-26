@extends('layouts.app')

@section('content')
    <div class="row justify-content-center mt-3">
        <div class="col-md-10">
            <div class="card">
                {{-- <div class="card-header">{{ __('Đăng Ký Chi Nhánh') }}</div> --}}

                <div class="card-body">
                    <form id="branch-form">
                        @csrf
                        <div class="row mb-3">
                            <label for="username" class="col-md-4 col-form-label text-md-end">Họ tên Khách hàng <span
                                    style="color:red">*</span></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    name="username" required>
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="userphone" class="col-md-4 col-form-label text-md-end">Số điện thoại cá nhân <span
                                    style="color:red">*</span></label>
                            <div class="col-md-6">
                                <input id="userphone" type="text"
                                    class="form-control @error('userphone') is-invalid @enderror" name="userphone" required>
                                @error('userphone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="useremail" class="col-md-4 col-form-label text-md-end">Email cá nhân <span
                                    style="color:red">*</span></label>
                            <div class="col-md-6">
                                <input id="useremail" type="email"
                                    class="form-control @error('useremail') is-invalid @enderror" name="useremail" required>
                                @error('useremail')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="Name" class="col-md-4 col-form-label text-md-end">Tên địa điểm kinh
                                doanh <span style="color:red">*</span></label>
                            <div class="col-md-6">
                                <input id="Name" type="text"
                                    class="form-control @error('Name') is-invalid @enderror" name="Name" required>
                                @error('Name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="Location" class="col-md-4 col-form-label text-md-end">Địa chỉ kinh doanh <span
                                    style="color:red">*</span></label>
                            <div class="col-md-6">
                                <input id="Location" type="text"
                                    class="form-control @error('Location') is-invalid @enderror" name="Location" required>
                                @error('Location')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="Phone" class="col-md-4 col-form-label text-md-end">Hotline địa điểm kinh
                                doanh <span style="color:red">*</span></label>
                            <div class="col-md-6">
                                <input id="Phone" type="text"
                                    class="form-control @error('Phone') is-invalid @enderror" name="Phone" required>
                                @error('Phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="Email" class="col-md-4 col-form-label text-md-end">Email kinh doanh <span
                                    style="color:red">*</span></label>
                            <div class="col-md-6">
                                <input id="Email" type="email"
                                    class="form-control @error('Email') is-invalid @enderror" name="Email" required>
                                @error('Email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Đăng ký
                                </button>

                                <a href="javascript:history.back()" class="btn btn-secondary">Trở lại</a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- </div> --}}

    <script>
        $(document).ready(function() {
            $('#branch-form').on('submit', function(event) {
                event.preventDefault(); // Ngăn chặn việc gửi form mặc định

                var formData = $(this).serialize(); // Lấy dữ liệu từ form

                // Hiển thị overlay và spinner khi gửi yêu cầu AJAX
                $('#overlay-spinner').removeClass('d-none');

                $.ajax({
                    url: '{{ route('admin.account.store.chusan') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Ẩn overlay và spinner sau khi nhận phản hồi
                        $('#overlay-spinner').addClass('d-none');

                        // Reset form sau khi gửi thành công
                        $('#branch-form')[0].reset(); // Sử dụng jQuery để reset form

                        showAlert('success', response.message);
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
