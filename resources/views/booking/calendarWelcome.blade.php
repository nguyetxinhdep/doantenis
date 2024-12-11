@extends('layouts.customer.customerApp')

@section('content')
    <div class="container-fluid py-3" style="color: white">
        @php
            // Lấy ngày người dùng chọn, hoặc mặc định là ngày hôm nay
            $selectedDate = request('date', date('Y-m-d'));
            $formattedDate = date('d/m/Y', strtotime($selectedDate));
            $currentDate = date('Y-m-d');
            $branch_id = request('branch_id');
            // Lấy thời gian hiện tại
            $currentTime = date('H:i', strtotime('+7 hours'));
        @endphp

        {{-- include modal đặt sân cố định --}}
        @include('booking.modalDatCoDinh')

        <center>
            <h2>{{ $branch->Name }}</h2>

            @auth
                <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#fixedScheduleModal">
                    Đặt lịch cố định
                </button>
            @endauth
            @guest
                <span style="color:yellow">
                    Vui lòng đăng nhập để đặt sân hoặc liên hệ qua Zalo:
                    <a href="https://zalo.me/0{{$branch->Phone}}" target="_blank" style="color: yellow; text-decoration: underline;">
                        0{{$branch->Phone}}
                    </a>
                </span>

            @endguest

            <form method="GET" action="{{ route('customer.calendar.search') }}">
                @csrf
                <label for="selected_date" style="font-size:16px" class="mr-1">Chọn ngày:</label>
                <input type="date" id="selected_date" name="date" value="{{ request('date', date('Y-m-d')) }}">
                <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                <button type="submit" class="btn btn-primary btn-sm">Xem lịch</button>
                <!-- Nút mở modal -->
                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#priceListModal">
                    Xem bảng giá
                </button>
            </form>
        </center>

        <p class="d-flex align-items-center">
            <span class="bg-light mx-2"
                style="width: 20px; height: 20px; border: 1px solid black; margin-right: 5px;"></span>
            Trống
            <span class="bg-danger mx-2"
                style="width: 20px; height: 20px; border: 1px solid black; margin-right: 5px;"></span>
            Đã đặt
            <span class="bg-warning mx-2"
                style="width: 20px; height: 20px; border: 1px solid black; margin-right: 5px;"></span>
            Sân của bạn
            <span class="bg-secondary mx-2"
                style="width: 20px; height: 20px; border: 1px solid black; margin-right: 5px;"></span>
            Khóa
        </p>

        <div style="overflow-x: auto;overflow-y: auto; max-height: 70vh;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center bg-info" style ="position: sticky; top:0; z-index:2">Thời gian</th>
                        @for ($time = 5.5; $time <= 23; $time += 0.5)
                            <th class="text-center bg-info" style ="position: sticky; top:0; z-index:2">
                                {{ sprintf('%02d:%02d', floor($time), ($time - floor($time)) * 60) }} -
                                {{ sprintf('%02d:%02d', floor($time + 0.5), ($time + 0.5 - floor($time + 0.5)) * 60) }}
                            </th>
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
                                    $ofcustomer = false;
                                    $timeStart = sprintf('%02d:%02d', floor($time), ($time - floor($time)) * 60);
                                    // So sánh thời gian để xác định sân nào không thể đặt
                                    $isTimeExpired =
                                        strtotime($selectedDate) < strtotime($currentDate) ||
                                        ($selectedDate == $currentDate &&
                                            strtotime($timeStart) <= strtotime($currentTime) + 30 * 60);

                                    foreach ($bookings as $booking) {
                                        if (
                                            $booking->court_id == $court->Court_id &&
                                            $booking->Date_booking == $selectedDate &&
                                            $time >= (float) date('H.i', strtotime($booking->Start_time)) &&
                                            $time < (float) date('H.i', strtotime($booking->End_time))
                                        ) {
                                            $isBooked = true;
                                            if ($booking->customer_id == session('customer_id')) {
                                                $ofcustomer = true;
                                            }
                                            break;
                                        }
                                    }
                                @endphp
                                <td class="{{ $isTimeExpired ? 'position-relative z-1' : '' }} {{ $isBooked ? ($ofcustomer ? 'bg-warning' : 'bg-danger') : 'bg-light' }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $court->Name }}"
                                    @if (!$isBooked && !$isTimeExpired) data-court-id="{{ $court->Court_id }}" 
                                    data-time-start="{{ $timeStart }}" 
                                    data-time-end="{{ sprintf('%02d:%02d', floor($time + 0.5), ($time + 0.5 - floor($time + 0.5)) * 60) }}"
                                    style="cursor: pointer;" @endif>
                                    {{ $isBooked || $isTimeExpired ? '' : '' }}
                                    @if ($isTimeExpired)
                                        <div class="overlay z-1"></div>
                                    @endif
                                </td>
                                {{-- <td class="{{ $isBooked ? ($ofcustomer ? 'bg-warning' : 'bg-danger') : 'bg-light' }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $court->Name }}"
                                    @if (!$isBooked) data-court-id="{{ $court->Court_id }}" 
                                    data-time-start="{{ $timeStart }}" 
                                    data-time-end="{{ sprintf('%02d:%02d', floor($time + 0.5), ($time + 0.5 - floor($time + 0.5)) * 60) }}"
                                    style="cursor: pointer;" @endif>
                                    {{ $isBooked ? '' : 'x' }}

                                </td> --}}
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

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

        // Mảng chứa các ô đã chọn
        var selectedCells = [];

        // Xử lý sự kiện click trên ô thời gian
        $(document).on('click', 'td.bg-light[data-court-id], td.bg-success[data-court-id]', function() {
            var courtId = $(this).data('court-id'); // Lấy ID sân
            var timeStart = $(this).data('time-start'); // Lấy thời gian bắt đầu
            var timeEnd = $(this).data('time-end'); // Lấy thời gian kết thúc
            var key = courtId + '-' + timeStart + '-' + timeEnd; // Tạo key duy nhất cho ô
            // console.log(courtId, timeStart, timeEnd);

            // Kiểm tra xem key này có trong mảng selectedCells hay không
            var index = selectedCells.indexOf(key);

            if (index === -1) { // Nếu key chưa có trong mảng
                selectedCells.push(key); // Thêm key vào mảng
                $(this).removeClass('bg-light').addClass('bg-success'); // Đổi màu nền sang màu đã chọn
            } else { // Nếu key đã có trong mảng
                selectedCells.splice(index, 1); // Xóa key khỏi mảng
                $(this).removeClass('bg-success').addClass('bg-light'); // Đổi lại màu nền ban đầu
            }
        });

        // Xử lý sự kiện click trên nút Đặt sân
        $('#reserve-button').on('click', function() {
            if (selectedCells.length === 0) {
                alert('Vui lòng chọn ít nhất một khung giờ trước khi đặt!');
                return;
            }

            // Lấy thông tin về hình thức thanh toán
            // var paymentOption = $('#payment-option').val();

            var date = '{{ $selectedDate }}'; // Lấy ngày đã chọn

            // Tạo mảng chứa thông tin về sân và giờ đặt
            var reservations = selectedCells.map(function(cell) {
                var parts = cell.split('-');
                return {
                    courtId: parts[0],
                    timeStart: parts[1],
                    timeEnd: parts[2]
                };
            });

            if (confirm('Bạn có chắc chắn muốn đặt các khung giờ đã chọn?')) {
                $.ajax({
                    url: '{{ route('booking.reserve') }}',
                    method: 'POST',
                    data: {
                        selectedCells: reservations, // Gửi mảng thông tin đặt sân
                        date: date,
                        // paymentOption: paymentOption, // Gửi thông tin về hình thức thanh toán
                        _token: '{{ csrf_token() }}' // CSRF token
                    },
                    success: function(response) {
                        alert('Đặt sân thành công!');
                        location.reload(); // Tải lại trang để cập nhật lịch
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            alert('Vui lòng đăng nhập để thực hiện đặt sân.');
                            window.location.href =
                                '{{ route('login') }}'; // Chuyển hướng tới trang đăng nhập
                        } else {
                            alert('Đặt sân không thành công. Vui lòng thử lại.');
                        }
                    }
                });
            }
        });
    </script>
    @php
        $status1Rows = 0;
        foreach ($groupedList as $items) {
            // duyệt qua từng hàng, xem có hàng nào có cột status = 1(sân ngày thường) thì + $status1Rows
            if ($items->contains('Status', 1)) {
                $status1Rows++;
            }
        }
    @endphp
    <!-- Modal chi tiết bảng giá -->
    <div class="modal fade" id="priceListModal" tabindex="-1" role="dialog" aria-labelledby="priceListModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="priceListModalLabel">Chi tiết bảng giá (giá tiền/1 giờ)</h5>

                </div>
                <div class="modal-body">
                    <!-- Sao chép bảng giá vào modal -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Khung giờ</th>
                                <th>Vãng lai</th>
                                <th>Cố định</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Hiển thị T2-T6 -->
                            @foreach ($groupedList as $key => $items)
                                @php
                                    //kiểm tra xem trong hàng đang duyệt này có tồn tại cột nào có status = 1 không -> true/false
                                    $status1Exists = $items->contains('Status', 1);

                                    //kiểm tra xem trong hàng đang duyệt này có tồn tại cột nào có status = 2 không -> true/false
                                    $status2Exists = $items->contains('Status', 2);
                                @endphp

                                <!-- Xử lý T2-T6 (Status = 1) -->
                                @if ($status1Exists)
                                    <tr>
                                        {{-- nếu là vòng lặp đầu tiên thì thực hiện(laravel hỗ trợ) --}}
                                        @if ($loop->first)
                                            <!-- Dùng rowspan để gộp T2-T6 -->
                                            <td rowspan="{{ $status1Rows }}">T2-T6</td>
                                        @endif

                                        {{-- key này là khung giờ do group by bên controller rồi --}}
                                        <td>{{ $key }}</td>
                                        <td>
                                            @php
                                                $vanglai = $items
                                                    ->where('customer_type_id', 1)
                                                    ->where('Status', 1)
                                                    ->first(); //true,flase
                                            @endphp
                                            {{ $vanglai ? number_format($vanglai->Price, 0, ',', '.') . ' VND' : 'Không có giá' }}
                                        </td>
                                        <td>
                                            @php
                                                $codinh = $items
                                                    ->where('customer_type_id', 2)
                                                    ->where('Status', 1)
                                                    ->first();
                                            @endphp
                                            {{ $codinh ? number_format($codinh->Price, 0, ',', '.') . ' VND' : 'Không có giá' }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                            <!-- Hiển thị T7-CN (Status = 2) -->
                            @foreach ($groupedList as $key => $items)
                                @php
                                    // dd($list);
                                    $status1Exists = $items->contains('Status', 1);
                                    $status2Exists = $items->contains('Status', 2);
                                @endphp

                                @if ($status2Exists)
                                    <tr>
                                        <td>T7-CN</td>
                                        <td>{{ $key }}</td>
                                        <td>
                                            @php
                                                $vanglai = $items
                                                    ->where('customer_type_id', 1)
                                                    ->where('Status', 2)
                                                    ->first(); //true,flase
                                            @endphp
                                            {{ $vanglai ? number_format($vanglai->Price, 0, ',', '.') . ' VND' : 'Không có giá' }}
                                        </td>
                                        <td>
                                            @php
                                                $codinh = $items
                                                    ->where('customer_type_id', 2)
                                                    ->where('Status', 2)
                                                    ->first(); //true,flase
                                            @endphp
                                            {{ $codinh ? number_format($codinh->Price, 0, ',', '.') . ' VND' : 'Không có giá' }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection
