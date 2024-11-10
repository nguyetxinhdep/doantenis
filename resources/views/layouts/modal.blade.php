<script>
    // hiển thị thông báo
    // function showAlert(type, message) {
    //     var icon = '';
    //     if (type === 'success') {
    //         // Sử dụng đường dẫn tới ảnh dấu tick
    //         icon = '<img src="/images/alerts/success.png" alt="Success" width="24" height="24" class="me-2">';
    //     } else if (type === 'danger') {
    //         // Sử dụng đường dẫn tới ảnh dấu X
    //         icon = '<img src="/images/alerts/error.png" alt="Error" width="24" height="24" class="me-2">';
    //     }

    //     var alertHtml = `<div class="alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert"
    //                             style="top: 20px; right: 20px; z-index: 1050;">
    //                     ${icon} ${message}
    //                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    //                  </div>`;
    //     $('#alert-container').html(alertHtml);

    //     // Tự động ẩn thông báo sau 5 giây
    //     setTimeout(function() {
    //         $('.alert').fadeOut('slow', function() {
    //             $(this).remove();
    //         });
    //     }, 5000);
    // }

    //---------------- xử lý modal xóa branch
    $(document).ready(function() {
        let formToSubmit; // Biến để lưu trữ form sẽ được submit

        // Hàm hiển thị modal xác nhận xóa chi nhánh
        window.showDeleteModal = function(id) {
            // Hiển thị modal
            $('#deleteModal').modal('show');

            // Lưu trữ form tương ứng vào biến formToSubmit
            formToSubmit = $('#deleteForm' + id);
        }

        // Khi người dùng nhấn nút "Xóa" trong modal
        $('#confirmDelete').on('click', function() {
            // Ngăn chặn form submit mặc định
            event.preventDefault();

            // Gửi dữ liệu bằng AJAX
            if (formToSubmit) {
                // Hiển thị overlay và spinner khi gửi yêu cầu AJAX
                $('#overlay-spinner').removeClass('d-none');

                $.ajax({
                    url: $(formToSubmit).attr('action'),
                    type: $(formToSubmit).attr('method'),
                    data: $(formToSubmit).serialize(),
                    success: function(response) {
                        // Xử lý thành công
                        console.log("Đã xóa thành công");
                        // Ẩn overlay và spinner sau khi nhận phản hồi
                        $('#overlay-spinner').addClass('d-none');

                        // Đóng modal
                        $('#deleteModal').modal('hide');

                        // Xóa <tr> với ID tương ứng
                        $('#' + response.branch_id).remove();

                        showAlert('success', response.message);
                        // Đợi 2 giây trước khi kiểm tra điều kiện và chuyển hướng
                        setTimeout(function() {
                            if (response.isBranch) {
                                // Nếu xóa địa điểm đang là địa điểm hiện tại thì logout
                                window.location.href = "{{ route('logout') }}";
                            }
                        }, 2000); // 2000 ms = 2 giây
                    },
                    error: function(xhr) {
                        // Xử lý lỗi
                        console.error("Có lỗi xảy ra: " + xhr.statusText);
                        // Ẩn overlay và spinner sau khi nhận phản hồi
                        $('#overlay-spinner').addClass('d-none');

                        showAlert('danger', 'Đã có lỗi xảy ra. Vui lòng thử lại!');
                    }
                });
            }
        });
    });

    // Hàm xử lý lỗi chung(lỗi trả về khi gửi bằng ajax)
    function handleAjaxError(xhr) {
        if (xhr.status === 422) {
            var errors = xhr.responseJSON.errors;
            var errorMessage = '';
            $.each(errors, function(key, value) {
                errorMessage += value[0] + '<br>';
            });
            showAlert('danger', errorMessage);
        } else {
            showAlert('danger', 'Đã có lỗi xảy ra. Vui lòng thử lại!');
        }
    }
</script>
<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa và không thể quay lại?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Xóa</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="/template/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="/template/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="/template/dist/js/adminlte.min.js"></script>
{{-- <!-- AdminLTE for demo purposes -->
<script src="/template/dist/js/demo.js"></script> --}}
