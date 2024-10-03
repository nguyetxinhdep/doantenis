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
                            <label for="Name"
                                class="col-md-4 col-form-label text-md-end">{{ __('Tên Chi Nhánh') }}</label>
                            <div class="col-md-6">
                                <input id="Name" type="text" class="form-control @error('Name') is-invalid @enderror"
                                    name="Name" required>
                                @error('Name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="Location" class="col-md-4 col-form-label text-md-end">Địa Chỉ Chi Nhánh</label>
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
                            <label for="Phone" class="col-md-4 col-form-label text-md-end">Hotline Chi Nhánh</label>
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
                            <label for="Email" class="col-md-4 col-form-label text-md-end">{{ __('Email') }}</label>
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

                        <div class="row mb-3">
                            <label for="HoTen" class="col-md-4 col-form-label text-md-end">Họ Tên</label>
                            <div class="col-md-6">
                                <input id="HoTen" type="text"
                                    class="form-control @error('HoTen') is-invalid @enderror" name="HoTen" required>
                                @error('HoTen')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="Address" class="col-md-4 col-form-label text-md-end">Đ/Chỉ Nhà Riêng</label>
                            <div class="col-md-6">
                                <input id="Address" type="text"
                                    class="form-control @error('Address') is-invalid @enderror" name="Address" required>
                                @error('Address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="SDTCaNhan" class="col-md-4 col-form-label text-md-end">SĐT Cá Nhân</label>
                            <div class="col-md-6">
                                <input id="SDTCaNhan" type="text"
                                    class="form-control @error('SDTCaNhan') is-invalid @enderror" name="SDTCaNhan" required>
                                @error('SDTCaNhan')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Đăng Ký') }}
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    {{ __('Reset') }}
                                </button>
                            </div>

                        </div>
                    </form>
                    <div class="mt-3 text-center"><span style="color:red"><span class="fw-bold">Lưu ý:</span>
                            Nếu bạn muốn dùng 1
                            gmail để quản lý nhiều chi
                            nhánh thì vui lòng
                            đăng nhập để đăng ký chi nhánh khác!</span></div>
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
                    url: '{{ route('branch.store') }}',
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
