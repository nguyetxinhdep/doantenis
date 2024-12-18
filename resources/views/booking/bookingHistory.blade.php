@extends('layouts.customer.customerApp')

@section('content')
    @php
        $groupedBookings = [];

        // Duyệt qua danh sách lịch sử đặt sân
        foreach ($history as $booking) {
            // Nếu mã đặt sân chưa tồn tại, khởi tạo một mảng cho nó
            if (!isset($groupedBookings[$booking->booking_code])) {
                $groupedBookings[$booking->booking_code] = [];
            }
            // Thêm booking vào nhóm của booking_code tương ứng
            $groupedBookings[$booking->booking_code][] = $booking;
        }
        // dd($groupedBookings);
    @endphp

    <div class="container my-3">
        <!-- Form tìm kiếm -->
        <!-- Form tìm kiếm -->
        <form action="{{ route('booking.history') }}" method="GET" class="mb-4">
            <div class="row" style="color: white">
                <!-- Ô input tìm kiếm ngày tháng năm -->
                <div class="col-md-3">
                    <label for="date">Ngày đặt</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
                </div>

                <!-- Ô input tìm kiếm trạng thái -->
                <div class="col-md-3">
                    <label for="status">Trạng thái</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Tất cả</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Đã đặt cọc</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Chưa thanh toán
                        </option>
                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>

                <!-- Ô input tìm kiếm sân đã đặt -->
                <div class="col-md-3">
                    <label for="branch">Sân đã đặt</label>
                    <select class="form-control" id="" name="branch">
                        <option value="">Tất cả các sân</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->Branch_id }}"
                                {{ request('branch') == $branch->Branch_id ? 'selected' : '' }}>
                                {{ $branch->Name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Nút tìm kiếm -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-sm">Tìm kiếm</button>
                    <a href="{{ route('booking.history') }}" class="btn btn-warning btn-sm">Đặt lại</a>
                </div>
            </div>
        </form>
        {{-- ----------------------------------------- --}}

        {{-- Bảng hiển thị nội dung --}}
        <table class="table table-bordered" style="font-size: 12px">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Ngày đặt</th>
                    <th>Người đặt</th>
                    <th>Số điện thoại</th>
                    <th>Giờ vào</th>
                    <th>Giờ ra</th>
                    <th>Tên địa điểm</th>
                    <th>Sân</th>
                    <th>Tổng tiền</th>
                    <th>Đã trả</th>
                    <th>Còn nợ</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @if (empty($groupedBookings))
                    <tr>
                        <td colspan="11" class="text-center">Chưa có lịch sử đặt sân nào.</td>
                        <td></td>
                        <td></td>
                    </tr>
                @else
                    @php $i = 1; @endphp
                    @foreach ($groupedBookings as $bookingCode => $bookings)
                        @php
                            $totalBookings = count($bookings);
                            $totalDebt = 0; // Khởi tạo biến tổng nợ
                            $totalamount = 0; // Khởi tạo biến tổng nợ
                            $totalpaid = 0; // Khởi tạo biến tổng nợ
                            $listBooking_id = []; //biến lưu danh sách booking id thi mã booking_code
                            $listPayment_id = []; //biến lưu danh sách payment id thi mã booking_code

                            // Tính tổng nợ cho các booking
                            foreach ($bookings as $booking) {
                                $totalDebt += $booking->Debt; // Cộng dồn nợ
                                $totalamount += $booking->Amount; // Cộng dồn nợ
                                $totalpaid += $booking->Paid; // Cộng dồn nợ
                                // dd($bookings);
                                // dd($booking->Paid, $booking->Booking_id);
                                // Tách các Payment_id có dấu phẩy
                                $paymentIds = explode(',', $booking->Payment_id);
                                // Lưu các giá trị đã tách vào mảng listPayment_id
                                foreach ($paymentIds as $id) {
                                    $listPayment_id[] = trim($id); // trim() để loại bỏ khoảng trắng
                                }
                            }

                            // Sau khi vòng lặp kết thúc, gộp các Payment_id lại thành chuỗi
                            $listPayment_id_string = implode(',', $listPayment_id); // Không có dấu giữa các Payment_id
                        @endphp

                        @foreach ($bookings as $index => $booking)
                            <tr>
                                @if ($index === 0)
                                    <td style="vertical-align: middle;" rowspan="{{ $totalBookings }}">{{ $i++ }}
                                    </td>
                                    <td>
                                        @php
                                            $dates = explode(',', $booking->Date_booking);
                                            $uniqueDates = array_unique(array_map('trim', $dates)); // Lấy các ngày duy nhất
                                            $formattedDates = array_map(function ($date) {
                                                return \Carbon\Carbon::parse($date)->format('d/m/Y');
                                            }, $uniqueDates);
                                        @endphp
                                        {!! implode('<br>', $formattedDates) !!}
                                    </td>
                                    <td>{{ $booking->user_name }}</td>
                                    <td>0{{ $booking->user_phone }}</td>
                                    <td>
                                        @php
                                            $startTimes = explode(',', $booking->Start_time); // Tách giờ vào
                                            $formattedStartTimes = array_map(function ($time) {
                                                return \Carbon\Carbon::parse(trim($time))->format('H:i'); // Định dạng giờ vào
                                            }, $startTimes);
                                        @endphp
                                        {!! implode('<br>', $formattedStartTimes) !!} <!-- In ra các giờ vào, mỗi giờ trên một dòng -->
                                    </td>
                                    <td>
                                        @php
                                            $endTimes = explode(',', $booking->End_time); // Tách giờ ra
                                            $formattedEndTimes = array_map(function ($time) {
                                                return \Carbon\Carbon::parse(trim($time))->format('H:i'); // Định dạng giờ ra
                                            }, $endTimes);
                                        @endphp
                                        {!! implode('<br>', $formattedEndTimes) !!} <!-- In ra các giờ ra, mỗi giờ trên một dòng -->
                                    </td>
                                    <td>
                                        {{-- tên địa điểm --}}
                                        <a
                                            href="{{ route('welcome.booking.calendar') . '?branch_id=' . $booking->branch_id }}">{{ $booking->branch_address }}
                                        </a>
                                    </td>
                                    <td>
                                        @php
                                            $courtNames = explode(',', $booking->court_name); // Tách tên sân
                                        @endphp
                                        {!! implode('<br>', array_map('trim', $courtNames)) !!} <!-- In ra các sân, mỗi sân trên một dòng -->
                                    </td>
                                    <td style="vertical-align: middle;" rowspan="{{ $totalBookings }}">
                                        {{ number_format($totalamount, 0, ',', '.') }} đ <!-- Hiển thị tổng nợ -->
                                    </td>
                                    <td style="vertical-align: middle;" rowspan="{{ $totalBookings }}">
                                        {{ number_format($totalpaid, 0, ',', '.') }} đ <!-- Hiển thị tổng nợ -->
                                    </td>
                                    <td style="vertical-align: middle;" rowspan="{{ $totalBookings }}">
                                        {{ number_format($totalDebt, 0, ',', '.') }} đ <!-- Hiển thị tổng nợ -->
                                    </td>
                                    <td style="vertical-align: middle;" rowspan="{{ $totalBookings }}">
                                        @switch($booking->Status)
                                            @case(0)
                                                <span class="badge bg-warning">Đã đặt cọc</span>
                                            @break

                                            @case(1)
                                                <span class="badge bg-success">Đã thanh toán</span>
                                            @break

                                            @case(2)
                                                <span class="badge bg-info">Chưa thanh toán</span>
                                            @break

                                            @case(3)
                                                <span class="badge bg-danger">Đã hủy</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">Không xác định</span>
                                        @endswitch
                                    </td>
                                    <td style="vertical-align: middle;" rowspan="{{ $totalBookings }}">
                                        @if ($booking->Debt != 0 && $booking->Status != 3)
                                            <form action="/momo_paymentQR" method="POST">
                                                @csrf
                                                <input type="hidden" name="total" value="{{ $totalDebt }}">
                                                <input type="hidden" name="Payment_id"
                                                    value="{{ $listPayment_id_string }}">
                                                <button type="submit" value="full" name="redirect"
                                                    class="btn btn-primary btn-sm">Thanh
                                                    toán</button>
                                                @if ($booking->Status != 0)
                                                    <button type="submit" name="redirect" value="half"
                                                        class="btn btn-warning btn-sm">Đặt
                                                        cọc</button>
                                                @endif

                                                <!-- Nút hủy chỉ hiển thị một lần cho mỗi nhóm -->
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    data-toggle="modal" data-target="#cancelModal-{{ $bookingCode }}">
                                                    Hủy đặt sân
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted">Không có hành động nào</span>
                                        @endif
                                    </td>
                                @else
                                    <td>
                                        @php
                                            $dates = explode(',', $booking->Date_booking);
                                            $uniqueDates = array_unique(array_map('trim', $dates)); // Lấy các ngày duy nhất
                                            $formattedDates = array_map(function ($date) {
                                                return \Carbon\Carbon::parse($date)->format('d/m/Y');
                                            }, $uniqueDates);
                                        @endphp
                                        {!! implode('<br>', $formattedDates) !!}
                                    </td>
                                    <td>{{ $booking->user_name }}</td>
                                    <td>0{{ $booking->user_phone }}</td>
                                    <td>
                                        @php
                                            $startTimes = explode(',', $booking->Start_time); // Tách giờ vào
                                            $formattedStartTimes = array_map(function ($time) {
                                                return \Carbon\Carbon::parse(trim($time))->format('H:i'); // Định dạng giờ vào
                                            }, $startTimes);
                                        @endphp
                                        {!! implode('<br>', $formattedStartTimes) !!} <!-- In ra các giờ vào, mỗi giờ trên một dòng -->
                                    </td>
                                    <td>
                                        @php
                                            $endTimes = explode(',', $booking->End_time); // Tách giờ ra
                                            $formattedEndTimes = array_map(function ($time) {
                                                return \Carbon\Carbon::parse(trim($time))->format('H:i'); // Định dạng giờ ra
                                            }, $endTimes);
                                        @endphp
                                        {!! implode('<br>', $formattedEndTimes) !!} <!-- In ra các giờ ra, mỗi giờ trên một dòng -->
                                    </td>
                                    <td>
                                        {{-- tên địa điểm --}}
                                        <a
                                            href="{{ route('welcome.booking.calendar') . '?branch_id=' . $booking->branch_id }}">{{ $booking->branch_address }}
                                        </a>
                                    </td>
                                    <td>
                                        @php
                                            $courtNames = explode(',', $booking->court_name); // Tách tên sân
                                        @endphp
                                        {!! implode('<br>', array_map('trim', $courtNames)) !!} <!-- In ra các sân, mỗi sân trên một dòng -->
                                    </td>
                                @endif
                            </tr>
                        @endforeach

                        <!-- Modal hủy chỉ hiển thị một lần cho mỗi nhóm -->
                        <div class="modal fade" id="cancelModal-{{ $bookingCode }}" tabindex="-1" role="dialog"
                            aria-labelledby="cancelModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="cancelModalLabel">HỦY ĐẶT SÂN</h5>
                                    </div>
                                    <form action="{{ route('manager.cancelCourt') }}" method="POST"
                                        id="cancelForm-{{ $bookingCode }}">
                                        @csrf
                                        <input type="hidden" name="bookingCode" value="{{ $bookingCode }}">
                                        <input type="hidden" name="listPayment_id_string"
                                            value="{{ $listPayment_id_string }}">

                                        <div class="modal-body">
                                            <p>Bạn có chắc chắn muốn hủy đặt sân này</p>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Đóng</button>
                                            <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </tbody>
        </table>

        {{-- Phân trang --}}
        <div class="">
            {{ $history->links('pagination::bootstrap-5') }}
        </div>
    </div>
    <script>
        function selectPaymentOption(selectedName, otherName, amount, bookingId) {
            // Gán giá trị cho name đã chọn
            document.getElementsByName(selectedName)[0].checked = true;
            // Bỏ checked cho name còn lại
            document.getElementsByName(otherName)[0].checked = false;

            // Gán giá trị cho hidden input để submit khi gửi form
            document.getElementById('paymentAmount-' + bookingId).value = amount;

        }
    </script>
@endsection
