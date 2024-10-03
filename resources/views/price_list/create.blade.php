@extends('layouts.app')

@section('content')
    <div class="row justify-content-center py-3">
        <form id="price-create" action="{{ route('price_list.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <label for="start_time" class="col-md-4 col-form-label text-md-end">Từ giờ</label>
                <div class="col-md-6">
                    <input id="start_time" type="time" class="form-control @error('start_time') is-invalid @enderror"
                        name="start_time" value="{{ old('start_time') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="end_time" class="col-md-4 col-form-label text-md-end">Đến giờ</label>
                <div class="col-md-6">
                    <input id="end_time" type="time" class="form-control @error('end_time') is-invalid @enderror"
                        name="end_time" value="{{ old('end_time') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="status" class="col-md-4 col-form-label text-md-end">Loại ngày</label>
                <div class="col-md-6">
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror"
                        required>
                        <option selected value="1">Ngày thường (T2-T6)</option>
                        <option value="2">Ngày cuối tuần (T7-CN)</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <label for="fixed_price" class="col-md-4 col-form-label text-md-end">Giá cố định</label>
                <div class="col-md-6">
                    <input id="fixed_price" type="number" step="0.01"
                        class="form-control @error('fixed_price') is-invalid @enderror" name="fixed_price" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="walk_in_price" class="col-md-4 col-form-label text-md-end">Giá vãng lai</label>
                <div class="col-md-6">
                    <input id="walk_in_price" type="number" step="0.01"
                        class="form-control @error('walk_in_price') is-invalid @enderror" name="walk_in_price" required>
                </div>
            </div>

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
            $('#price-create').on('submit', function(event) {
                event.preventDefault(); // Ngăn chặn việc gửi form mặc định

                var formData = $(this).serialize(); // Lấy dữ liệu từ form

                // Hiển thị overlay và spinner khi gửi yêu cầu AJAX
                $('#overlay-spinner').removeClass('d-none');

                $.ajax({
                    url: '{{ route('price_list.store') }}',
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

                        handleAjaxError(xhr); // Hàm xử lý lỗi chung
                    }
                });
            });
        });
    </script>
@endsection
