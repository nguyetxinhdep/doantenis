@extends('layouts.app')

@section('content')
    <div class="row justify-content-center py-3">
        <form id="court-form">
            @csrf

            <div class="row mb-3">
                <label for="creationType" class="col-md-4 col-form-label text-md-end">Loại tạo sân</label>
                <div class="col-md-6">
                    <select id="creationType" name="creationType" class="form-select">
                        <option value="single">Tạo từng sân</option>
                        <option value="bulk">Tạo hàng loạt</option>
                    </select>
                </div>
            </div>

            {{-- tạo từng sân --}}
            <div id="single-court-form">
                <!-- Single court creation form fields -->
                <div class="row mb-3">
                    <label for="Name" class="col-md-4 col-form-label text-md-end">Tên sân</label>
                    <div class="col-md-6">
                        <input id="Name" placeholder="Sân 1, 2, 3,..." type="text"
                            class="form-control @error('Name') is-invalid @enderror" name="Name">
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="Availability" class="col-md-4 col-form-label text-md-end">Tình trạng</label>
                    <div class="col-md-6">
                        <select name="Availability" class="form-select">
                            <option value="1" selected>Hoạt động</option>
                            <option value="0">Đang bảo trì</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- tạo hàng loạt --}}
            <div id="bulk-court-form" style="display: none;">
                <!-- Bulk creation fields -->
                <div class="row mb-3">
                    <label for="courtRange" class="col-md-4 col-form-label text-md-end">Số lượng sân</label>
                    <div class="col-md-6">
                        <input id="minCourts" type="number" class="form-control" name="minCourts" placeholder="Từ số">
                        <input id="maxCourts" type="number" class="form-control mt-2" name="maxCourts"
                            placeholder="Đến số">
                    </div>
                </div>
            </div>

            <input type="hidden" name="branch_id" value="{{ session('branch_active')->Branch_id }}">

            <div class="row mb-0">
                <div class="col-md-8 offset-md-4">
                    <button type="submit" class="btn btn-primary">Tạo</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </div>
        </form>

    </div>

    <script>
        $(document).ready(function() {
            // Khi loại tạo sân thay đổi, hiển thị form tương ứng
            $('#creationType').on('change', function() {
                var selectedType = $(this).val();
                if (selectedType === 'single') {
                    $('#single-court-form').show();
                    $('#bulk-court-form').hide();
                } else if (selectedType === 'bulk') {
                    $('#single-court-form').hide();
                    $('#bulk-court-form').show();
                }
            });

            // Khi submit form
            $('#court-form').on('submit', function(event) {
                event.preventDefault(); // Ngăn chặn việc gửi form mặc định

                var formData = $(this).serialize(); // Lấy dữ liệu từ form
                var creationType = $('#creationType').val(); // Lấy loại tạo sân (single/bulk)

                // Hiển thị overlay và spinner khi gửi yêu cầu AJAX
                $('#overlay-spinner').removeClass('d-none');

                // Xử lý logic khác nhau dựa trên loại tạo sân
                if (creationType === 'single') {
                    // Gửi yêu cầu AJAX cho form tạo từng sân
                    $.ajax({
                        url: '{{ route('single.court.create') }}', // URL cho route tạo từng sân
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            $('#overlay-spinner').addClass('d-none');
                            showAlert('success', response.message);
                        },
                        error: function(xhr) {
                            $('#overlay-spinner').addClass('d-none');
                            handleAjaxError(xhr); // Hàm xử lý lỗi chung
                        }
                    });
                } else if (creationType === 'bulk') {
                    // Gửi yêu cầu AJAX cho form tạo hàng loạt
                    $.ajax({
                        url: '{{ route('bulk.court.create') }}', //  URL cho route tạo hàng loạt sân
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            $('#overlay-spinner').addClass('d-none');
                            showAlert('success', response.message);
                        },
                        error: function(xhr) {
                            $('#overlay-spinner').addClass('d-none');
                            handleAjaxError(xhr); // Hàm xử lý lỗi chung
                        }
                    });
                }
            });


        });
    </script>
@endsection
