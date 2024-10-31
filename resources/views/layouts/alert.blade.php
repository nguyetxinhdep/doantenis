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


    // hiển thị thông báo
    function showAlert(type, message) {
        var icon = '';
        if (type === 'success') {
            // Sử dụng đường dẫn tới ảnh dấu tick
            icon = '<img src="/images/alerts/success.png" alt="Success" width="24" height="24" class="me-2">';
        } else if (type === 'danger') {
            // Sử dụng đường dẫn tới ảnh dấu X
            icon = '<img src="/images/alerts/error.png" alt="Error" width="24" height="24" class="me-2">';
        }

        var alertHtml = `<div class="alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert"
                                style="top: 20px; right: 20px; z-index: 1050; opacity: 0; transform: translateY(-20px); transition: all 0.5s ease;">
                        ${icon} ${message}
                        
                     </div>`;

        // Thêm thông báo vào container
        $('#alert-container').html(alertHtml);

        // Hiệu ứng xuất hiện
        setTimeout(function() {
            $('.alert').css({
                'opacity': '1',
                'transform': 'translateY(0)'
            });
        }, 100); // Đợi một chút để kích hoạt animation

        // Tự động ẩn thông báo sau 5 giây
        setTimeout(function() {
            $('.alert').css({
                'opacity': '0',
                'transform': 'translateY(-20px)'
            });
            setTimeout(function() {
                $('.alert').remove(); // Xóa phần tử sau khi animation hoàn tất
            }, 500); // Đợi 0.5s cho animation ẩn
        }, 5000);
    }
</script>
