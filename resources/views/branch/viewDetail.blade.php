@extends('layouts.app')

@section('content')
    <style>
        .tooltip-inner {
            text-align: left;
        }
    </style>
    <div class="container-fluid mt-4">
        {{-- @dd($data) --}}
        <div class="d-flex pb-3">
            @php
                if (session()->has('branch_active')) {
                    $branch_id = session('branch_active')->Branch_id;
                } else {
                    $branch_id = request()->id;
                }
            @endphp

            <form id="branch-form" class="w-50" action="{{ route('manage-branches.update', $branch_id) }}" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <label for="Name" class="col-md-4 col-form-label text-md-end">Tên địa điểm kinh doanh <span
                            style="color:red">*</span></label>
                    <div class="col-md-6">
                        <input id="Name" type="text" class="form-control @error('Name') is-invalid @enderror"
                            name="Name" value="{{ $data->Name }}">
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
                        <input id="Location" type="text" class="form-control @error('Location') is-invalid @enderror"
                            name="Location" value="{{ $data->Location }}">
                        @error('Location')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="Phone" class="col-md-4 col-form-label text-md-end">Hotline kinh doanh <span
                            style="color:red">*</span></label>
                    <div class="col-md-6">
                        <input id="Phone" type="text" class="form-control @error('Phone') is-invalid @enderror"
                            name="Phone" value="0{{ $data->Phone }}">
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
                        <input id="Email" type="email" class="form-control @error('Email') is-invalid @enderror"
                            name="Email" value="{{ $data->Email }}">
                        @error('Email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="HoTen" class="col-md-4 col-form-label text-md-end">Quản lý chi nhánh <span
                            style="color:red">*</span></label>
                    <div class="col-md-6">
                        <input id="HoTen" type="text" class="form-control @error('HoTen') is-invalid @enderror"
                            name="user_name" value="{{ $data->user_name }}">
                        @error('HoTen')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="user_email" class="col-md-4 col-form-label text-md-end">Email quản lý <span
                            style="color:red">*</span></label>
                    <div class="col-md-6">
                        <input id="user_email" type="user_email"
                            class="form-control @error('user_email') is-invalid @enderror" name="user_email"
                            value="{{ $data->user_email }}">
                        @error('user_email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="Address" class="col-md-4 col-form-label text-md-end">Địa chỉ nhà </label>
                    <div class="col-md-6">
                        <input id="Address" type="text" class="form-control @error('Address') is-invalid @enderror"
                            name="user_address" value="{{ $data->user_address }}">
                        @error('Address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="SDTCaNhan" class="col-md-4 col-form-label text-md-end">Số điện thoại cá nhân <span
                            style="color:red">*</span></label>
                    <div class="col-md-6">
                        <input id="SDTCaNhan" type="text" class="form-control @error('SDTCaNhan') is-invalid @enderror"
                            name="user_phone" value="0{{ $data->user_phone }}">
                        @error('SDTCaNhan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="link_map" class="col-md-4 col-form-label text-md-end">
                        Vị trí sân <span style="color:red">*</span>
                        <span
                            class="ms-2 bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                            style="width: 20px; height: 20px; cursor: pointer;" data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Hướng dẫn: <br> 1. Vào Google Map nhập địa chỉ sân <br> 2. Click vào chia sẻ <br> 3. Click nhúng bản đồ <br> 4. Click sao chép HTML <br> 5. Dán vào trường 'Vị trí sân'">
                            ?
                            {{-- thêm dấu hỏi --}}
                        </span>
                    </label>
                    <div class="col-md-6">
                        <input id="link_map" type="text" class="form-control @error('link_map') is-invalid @enderror"
                            name="link_map" value="{{ $data->link_map }}">
                        @error('link_map')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="Image" class="col-md-4 col-form-label text-md-end">Ảnh đại diện</label>
                    <div class="col-md-6">
                        <input id="Image" type="file" class="form-control @error('Image') is-invalid @enderror"
                            name="Image" value="{{ $data->Image }}">
                        @error('Image')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="Cover_image" class="col-md-4 col-form-label text-md-end">Ảnh bìa</label>
                    <div class="col-md-6">
                        <input id="Cover_image" type="file"
                            class="form-control @error('Cover_image') is-invalid @enderror" name="Cover_image"
                            value="{{ $data->Cover_image }}">
                        @error('Cover_image')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-md-8 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Cập nhật') }}
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            {{ __('Đặt lại') }}
                        </button>
                    </div>
                </div>
            </form>
            <div id="demo-data" class="w-50 d-flex flex-wrap">
                {{-- data image --}}
                <div class="w-100 d-flex">
                    <div class="w-50 pe-3">
                        <h5>Ảnh đại diện</h5>
                        @if ($data->Image)
                            <img id="demo_Image" src="{{ $data->Image }}" class=" img-css-ggg" alt="">
                        @else
                            <h6>Chưa có dữ liệu</h6>
                        @endif
                    </div>
                    <div class="w-50 ps-3">
                        <h5>Ảnh bìa</h5>
                        @if ($data->Cover_image)
                            <img id="demo_Cover_image" src="{{ $data->Cover_image }}" class="img-css-ggg"
                                alt="">
                        @else
                            <h6>Chưa có dữ liệu</h6>
                        @endif
                    </div>
                </div>
                {{-- data link_map --}}
                <div class="w-100 ps-2 mt-3">
                    <h5>Vị trí sân</h5>
                    <div id="demo_link_map" class="w-100">
                        @if ($data->link_map)
                            {!! $data->link_map !!}
                        @else
                            <h6>Chưa có dữ liệu</h6>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


<style>
    iframe,
    .img-css-ggg {
        width: 100% !important;
        max-height: 290px;
        border-radius: 5px;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                html: true
            });
        });
    });
</script>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#Image').on('change', (event) => {
            event.preventDefault();
            const Image = event.target.files[0];
            if (Image) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    $('#demo_Image').attr('src', e.target.result).show(); // Hiển thị hình ảnh
                };

                reader.readAsDataURL(Image); // Đọc file dưới dạng URL
            }
        })
        $('#Cover_image').on('change', (event) => {
            event.preventDefault();
            const Image = event.target.files[0];
            if (Image) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    $('#demo_Cover_image').attr('src', e.target.result).show(); // Hiển thị hình ảnh
                };

                reader.readAsDataURL(Image); // Đọc file dưới dạng URL
            }
        })
        $('#link_map').on('change', (event) => {
            const iframe = event.target.value
            $('#demo_link_map').html(iframe);
        })

        $('#branch-form').on('submit', function(event) {
            event.preventDefault()
            var formData = $(this).serialize();
            $('#overlay-spinner').removeClass('d-none');

            $.ajax({
                url: '{{ route('manage-branches.update', ['id' => $data->Branch_id]) }}',
                method: 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: false, // Important
                processData: false, // Important
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

        })
    })
</script> --}}
