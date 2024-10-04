@extends('layouts.app')

@section('content')
    <div class="container-fluid py-3">
        @php
            // Lấy ngày người dùng chọn, hoặc mặc định là ngày hôm nay
            $selectedDate = request('date', date('Y-m-d'));
            $formattedDate = date('d/m/Y', strtotime($selectedDate));
            $currentDate = date('Y-m-d');
        @endphp

        <center>
            <h2>Lịch đặt sân ngày {{ $formattedDate }}</h2>
        </center>

        <form method="GET" action="{{ route('booking.calendar.search') }}">
            <label for="selected_date" style="font-size:16px" class="mr-1">Chọn ngày:</label>
            <input type="date" id="selected_date" name="date" value="{{ request('date', date('Y-m-d')) }}">
            <button type="submit" class="btn btn-primary">Xem lịch</button>
        </form>

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


        @php
        @endphp

        <div style="overflow-x: auto;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center bg-info">Thời gian</th>
                        @for ($time = 5.5; $time <= 23; $time += 0.5)
                            <th class="text-center bg-info">
                                {{-- floor -> lấy phần nguyên của $time --}}
                                {{ sprintf('%02d:%02d', floor($time), ($time - floor($time)) * 60) }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    {{-- duyệt 2 vòng for qua các sân trong chi nhánh và qua các item trong bookings --}}
                    @foreach ($courts as $court)
                        <tr>
                            <td class="bg-primary text-white">{{ $court->Name }}</td>
                            @for ($time = 5.5; $time <= 23; $time += 0.5)
                                @php
                                    // Kiểm tra xem sân có được đặt trong khoảng thời gian này vào ngày đã chọn không
                                    $isBooked = false;
                                    foreach ($bookings as $booking) {
                                        if (
                                            $booking->court_id == $court->Court_id && // id trong bảng booking bằng id sân của chi nhánh
                                            $booking->Date_booking == $selectedDate && // lấy ngày tháng năm từ url kiểm tra xem có bằng ngày trong booking không
                                            $time >= (float) date('H.i', strtotime($booking->Start_time)) && // hàm date('H.i') sẽ trả về chuỗi như 6.3 cho dữ liệu 6:30, ...
                                            $time < (float) date('H.i', strtotime($booking->End_time))
                                        ) {
                                            $isBooked = true;
                                            break;
                                        }
                                    }
                                @endphp
                                <td class="{{ $isBooked ? 'bg-danger' : 'bg-light' }}" data-bs-toggle="tooltip"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $court->Name }}">
                                    {{ $isBooked ? '' : 'x' }}
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Khởi tạo tooltip của Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection
