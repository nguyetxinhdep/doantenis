@extends('layouts.app')

@section('content')
    <div class="container-fluid py-3">
        <h1>Đặt lịch ngày trực quan</h1>

        <form method="GET" action="">
            <label for="selected_date">Chọn ngày:</label>
            <input type="date" id="selected_date" name="date" value="{{ request('date', date('Y-m-d')) }}">
            <button type="submit" class="btn btn-primary">Xem lịch</button>
        </form>

        @php
            // Lấy ngày người dùng chọn, hoặc mặc định là ngày hôm nay
            $selectedDate = request('date', date('Y-m-d'));
            $formattedDate = date('d/m/Y', strtotime($selectedDate));
        @endphp

        <h3>{{ $formattedDate }}</h3>

        @php
            // Dữ liệu mẫu cho các sân (có thể thay bằng dữ liệu từ DB)
            $courts = [
                (object) ['id' => 1, 'Name' => 'Sân 1'],
                (object) ['id' => 2, 'Name' => 'Sân 2'],
                (object) ['id' => 3, 'Name' => 'Sân 3'],
                (object) ['id' => 4, 'Name' => 'Sân 4'],
                (object) ['id' => 5, 'Name' => 'Sân 5'],
            ];

            // Dữ liệu mẫu cho các đặt sân (có thể thay bằng dữ liệu từ DB)
            $bookings = [
                (object) ['court_id' => 1, 'start_time' => '06:00', 'end_time' => '07:30', 'date' => '2024-10-01'],
                (object) ['court_id' => 2, 'start_time' => '08:00', 'end_time' => '09:00', 'date' => '2024-09-30'],
                (object) ['court_id' => 3, 'start_time' => '09:30', 'end_time' => '11:00', 'date' => '2024-10-01'],
                (object) ['court_id' => 4, 'start_time' => '11:30', 'end_time' => '13:00', 'date' => '2024-09-30'],
                (object) ['court_id' => 5, 'start_time' => '18:00', 'end_time' => '20:00', 'date' => '2024-10-01'],
                (object) ['court_id' => 5, 'start_time' => '21:00', 'end_time' => '22:30', 'date' => '2024-10-01'],
            ];
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
                    @foreach ($courts as $court)
                        <tr>
                            <td class="bg-primary text-white">{{ $court->Name }}</td>
                            @for ($time = 5.5; $time <= 23; $time += 0.5)
                                @php
                                    // Kiểm tra xem sân có được đặt trong khoảng thời gian này vào ngày đã chọn không
                                    $isBooked = false;
                                    foreach ($bookings as $booking) {
                                        if (
                                            $booking->court_id == $court->id &&
                                            $booking->date == $selectedDate &&
                                            $time >= (float) date('H.i', strtotime($booking->start_time)) &&
                                            $time < (float) date('H.i', strtotime($booking->end_time))
                                        ) {
                                            $isBooked = true;
                                            break;
                                        }
                                    }
                                @endphp
                                <td class="{{ $isBooked ? 'bg-danger' : 'bg-light' }}" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="{{ $court->Name }}">
                                    {{ $isBooked ? '' : 'Trống' }}
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p>Lưu ý: Trắng - Trống, Đỏ - Đã đặt, Xám - Khóa</p>
    </div>

    <script>
        // Khởi tạo tooltip của Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection
