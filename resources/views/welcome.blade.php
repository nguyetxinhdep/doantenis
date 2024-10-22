@extends('layouts.customer.customerApp')

@section('content')
    <div class="container-fluid d-flex align-items-center" style="height: calc(100vh - 60px)">
        <div class="container ">
            <div class="row">
                <!-- Left content area -->
                <div class="col-md-6 d-flex align-items-center justify-content-center text-white background-bong-tenis">
                    <div class="text-center">
                        <h1>Welcome to Tennis Court Booking</h1>
                        <p>Book your court now and enjoy the game!</p>

                        <div class="position-relative">
                            <form class="d-flex" id="search-form">
                                <input class="form-control" type="search" placeholder="Search" aria-label="Search"
                                    id="search-input">
                                {{-- <button class="btn btn-outline-light" type="submit">Search</button> --}}
                            </form>
                            <ul id="suggestions-list" class="list-group position-absolute mt-1 text-start"
                                style="display: none; width: 100%; z-index: 1000;">
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Right image area -->
                <div class="col-md-6 py-5 d-md-block text-center">
                    <!-- Đặt ảnh ở đây -->
                    <img src="/images/khachhang/background_welcome.png" class="img-fluid"
                        style="background-color: transparent;" alt="Tennis Court Image">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal hiển thị sân trang welcome -->
    <div class="modal fade" id="suggestionModal" tabindex="-1" aria-labelledby="suggestionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Thêm lớp modal-lg -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="suggestionModalLabel">Court Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <!-- Ảnh bìa -->
                    <div class="position-relative mb-3" style="height: 200px;">
                        <img id="modal-cover-image" src="" alt="Cover Image" class="img-fluid"
                            style="width: 100%; height: 100%; object-fit: cover;">
                        <!-- Ảnh đại diện -->
                        <img id="modal-image" src="" alt="Court Image" class="img-fluid rounded-circle"
                            style="width: 100px; height: 100px; position: absolute; bottom: -50px; left: 50%; transform: translateX(-50%); border: 3px solid white;">
                    </div>
                    <!-- Tên sân -->
                    <h3 id="modal-name" class="mt-5"></h3>
                    <!-- Địa chỉ -->
                    <h6 id="modal-address"></h6>
                    <!-- Bản đồ Google Maps -->
                    <div id="map-container" class="mt-3">
                        <iframe id="modal-map" src="" width="100%" height="450" style="border:0;"
                            allowfullscreen="" loading="lazy"></iframe>
                    </div>
                    <div class="">
                        <a href="" id="modal-url-branch" class="btn btn-primary">Đặt sân ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>





    <script>
        $(document).ready(function() {
            $('#search-input').on('keyup', function() {
                var query = $(this).val();

                if (query.length > 0) {
                    $.ajax({
                        url: '{{ route('search') }}',
                        method: 'GET',
                        data: {
                            query: query
                        },
                        success: function(data) {
                            $('#suggestions-list').empty().show();
                            $.each(data, function(index, suggestion) {
                                $('#suggestions-list').append(
                                    '<li class="list-group-item suggestion-item" style="cursor: pointer;" ' +
                                    'data-name="' + suggestion.Name + '" ' +
                                    'data-branchid="' + suggestion.Branch_id +
                                    '" ' +
                                    'data-address="' + suggestion.Location + '" ' +
                                    'data-image="' + suggestion.Image + '" ' +
                                    'data-cover-image="' + suggestion.Cover_image +
                                    '" ' +
                                    'data-map-url="' + suggestion.link_map.split(
                                        '"')[1] + '">' +
                                    '<strong>' + suggestion.Name + '</strong><br>' +
                                    '<small>' + suggestion.Location + '</small>' +
                                    '</li>');
                            });
                        }
                    });
                } else {
                    $('#suggestions-list').hide();
                }
            });

            $(document).on('click', function() {
                $('#suggestions-list').hide();
            });

            // Khi nhấp vào một gợi ý
            $(document).on('click', '.suggestion-item', function() {
                // Lấy thông tin từ thuộc tính dữ liệu
                var name = $(this).data('name');
                var address = $(this).data('address');
                var image = $(this).data('image');
                var coverImage = $(this).data('cover-image'); // Lấy ảnh bìa
                var mapUrl = $(this).data('map-url');
                var url_branch = $(this).data('branchid');
                var fullUrl = 'welcome-booking-calendar/?branch_id=' +
                    url_branch; // Nối chuỗi với url_branch

                // Cập nhật thông tin cho modal
                $('#modal-image').attr('src', image);
                $('#modal-cover-image').attr('src', coverImage); // Cập nhật ảnh bìa
                $('#modal-name').text(name);
                $('#modal-address').text(address);
                $('#modal-map').attr('src', mapUrl); // Cập nhật src của iframe
                $('#modal-url-branch').attr('href', fullUrl); // Cập nhật link href của thẻ a

                // Hiển thị modal
                $('#suggestionModal').modal('show');
            });
        });

        $('#search-form').on('submit', function(event) {
            event.preventDefault(); // Ngăn chặn việc gửi form mặc định

            var formData = $(this).serialize(); // Lấy dữ liệu từ form

            // Hiển thị overlay và spinner khi gửi yêu cầu AJAX
            $('#overlay-spinner').removeClass('d-none');

            $.ajax({
                url: '{{ route('search') }}',
                method: 'get',
                data: formData,
                success: function(response) {
                    // Ẩn overlay và spinner sau khi nhận phản hồi
                    $('#overlay-spinner').addClass('d-none');

                    $('#suggestions-list').empty().show();
                    $.each(data, function(index, suggestion) {
                        $('#suggestions-list').append(
                            '<li class="list-group-item suggestion-item" style="cursor: pointer;" ' +
                            'data-name="' + suggestion.Name + '" ' +
                            'data-branchid="' + suggestion.Branch_id +
                            '" ' +
                            'data-address="' + suggestion.Location + '" ' +
                            'data-image="' + suggestion.Image + '" ' +
                            'data-cover-image="' + suggestion.Cover_image +
                            '" ' +
                            'data-map-url="' + suggestion.link_map.split(
                                '"')[1] + '">' +
                            '<strong>' + suggestion.Name + '</strong><br>' +
                            '<small>' + suggestion.Location + '</small>' +
                            '</li>');
                    });
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
    </script>
@endsection
