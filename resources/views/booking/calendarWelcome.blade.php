@extends('layouts.customer.customerApp')

@section('content')
    <div class="container-fluid py-3" style="color: white">
        @php
            // Lấy ngày người dùng chọn, hoặc mặc định là ngày hôm nay
            $selectedDate = request('date', date('Y-m-d'));
            $formattedDate = date('d/m/Y', strtotime($selectedDate));
            $currentDate = date('Y-m-d');
        @endphp

        <center>
            <h2>Lịch đặt sân ngày {{ $formattedDate }}</h2>

            <form method="GET" action="{{ route('booking.calendar.search') }}">
                <label for="selected_date" style="font-size:16px" class="mr-1">Chọn ngày:</label>
                <input type="date" id="selected_date" name="date" value="{{ request('date', date('Y-m-d')) }}">
                <button type="submit" class="btn btn-primary">Xem lịch</button>
            </form>
        </center>

        <p class="d-flex align-items-center">
            <span class="bg-light mx-2"
                style="width: 20px; height: 20px; border: 1px solid black; margin-right: 5px;"></span>
            Trống
            <span class="bg-danger mx-2"
                style="width: 20px; height: 20px; border: 1px solid black; margin-right: 5px;"></span>
            Đã đặt
            <span class="bg-secondary mx-2"
                style="width: 20px; height: 20px; border: 1px solid black; margin-right: 5px;"></span>
            Khóa
        </p>

        <div style="overflow-x: auto;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center bg-info">Thời gian</th>
                        @for ($time = 5.5; $time <= 23; $time += 0.5)
                            <th class="text-center bg-info">
                                {{ sprintf('%02d:%02d', floor($time), ($time - floor($time)) * 60) }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courts as $court)
                        <tr>
                            <td class="bg-primary text-white">{{ $court->Name }}</td>
                            @for ($time = 5.5; $time <= 23; $time += 0.5)
                                @php
                                    $isBooked = false;
                                    foreach ($bookings as $booking) {
                                        if (
                                            $booking->court_id == $court->Court_id &&
                                            $booking->Date_booking == $selectedDate &&
                                            $time >= (float) date('H.i', strtotime($booking->Start_time)) &&
                                            $time < (float) date('H.i', strtotime($booking->End_time))
                                        ) {
                                            $isBooked = true;
                                            break;
                                        }
                                    }
                                @endphp
                                <td class="{{ $isBooked ? 'bg-danger' : 'bg-light' }}" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="{{ $court->Name }}"
                                    @if (!$isBooked) data-court-id="{{ $court->Court_id }}" 
                                    data-time-start="{{ sprintf('%02d:%02d', floor($time), ($time - floor($time)) * 60) }}" 
                                    style="cursor: pointer;" @endif>
                                    {{ $isBooked ? '' : 'x' }}
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Nút đặt sân -->
        <div class="text-center mt-3">
            <button id="reserve-button" class="btn btn-success">Đặt sân</button>
        </div>
    </div>

    <script>
        // Khởi tạo tooltip của Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Biến để lưu ô được chọn
        var selectedCell = null;

        $(document).on('click', 'td.bg-light[data-court-id]', function() {
            // Nếu có ô đã chọn trước đó
            if (selectedCell) {
                // Nếu ô hiện tại đã được chọn trước đó, xóa lớp 'bg-success' và trả về màu ban đầu
                if (selectedCell === this) {
                    $(selectedCell).removeClass('bg-success');
                    $(selectedCell).addClass('bg-light');
                    selectedCell = null; // Đặt lại ô đã chọn
                    return; // Thoát ra
                } else {
                    // Nếu chọn ô khác, xóa lớp 'bg-success' của ô đã chọn
                    $(selectedCell).removeClass('bg-success');
                    $(selectedCell).addClass('bg-light');
                }
            }

            // Đặt ô hiện tại là ô đã chọn
            selectedCell = this;
            $(selectedCell).removeClass('bg-light');
            $(selectedCell).addClass('bg-success'); // Thêm lớp màu xanh lá
        });

        // Xử lý sự kiện click trên nút Đặt sân
        $('#reserve-button').on('click', function() {
            if (!selectedCell) {
                alert('Vui lòng chọn một sân trước khi đặt!');
                return;
            }

            var courtId = $(selectedCell).data('court-id');
            var timeStart = $(selectedCell).data('time-start');
            var date = '{{ $selectedDate }}'; // Ngày đã chọn

            // Hiển thị thông báo đặt sân
            if (confirm('Bạn có chắc chắn muốn đặt sân ' + courtId + ' vào lúc ' + timeStart + ' ngày ' + date +
                    '?')) {
                // Thực hiện hành động đặt sân, ví dụ gửi yêu cầu đến máy chủ
                $.ajax({
                    url: '{{ route('booking.reserve') }}', // Đường dẫn đến route đặt sân
                    method: 'POST',
                    data: {
                        court_id: courtId,
                        date: date,
                        time_start: timeStart,
                        _token: '{{ csrf_token() }}' // CSRF token
                    },
                    success: function(response) {
                        alert('Đặt sân thành công!');
                        location.reload(); // Tải lại trang để cập nhật lịch
                    },
                    error: function(xhr) {
                        alert('Đặt sân không thành công. Vui lòng thử lại.');
                    }
                });
            }
        });
    </script>
@endsection
