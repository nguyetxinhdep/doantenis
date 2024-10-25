@extends('layouts.customer.customerApp')

@section('content')
    <div class="container my-3">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Ngày Đặt</th>
                    <th>Thời Gian Bắt Đầu</th>
                    <th>Thời Gian Kết Thúc</th>
                    <th>Chi Nhánh</th>
                    <th>Sân</th>
                    <th>Còn nợ</th>
                    <th>Trạng Thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if ($history->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center">Chưa có lịch sử đặt sân nào.</td>
                    </tr>
                @else
                    @foreach ($history as $booking)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($booking->Date_booking)->format('d/m/Y') }}</td>
                            <td>{{ $booking->Start_time }}</td>
                            <td>{{ $booking->End_time }}</td>
                            <td>{{ $booking->branch_name }}</td>
                            <td>{{ $booking->court_name }}</td>
                            <td>{{ number_format($booking->Debt, 0, ',', '.') }} đ</td>
                            <td>
                                @switch($booking->Status)
                                    @case(0)
                                        <span class="badge bg-warning">Chưa thu đủ</span>
                                    @break

                                    @case(1)
                                        <span class="badge bg-success">Đã thu đủ (OK)</span>
                                    @break

                                    @case(2)
                                        <span class="badge bg-info">Cần thanh toán để giữ sân</span>
                                    @break

                                    @case(3)
                                        <span class="badge bg-danger">Đã hủy</span>
                                    @break

                                    @default
                                        <span class="badge bg-secondary">Không xác định</span>
                                @endswitch
                            </td>
                            <td>
                                @if ($booking->Debt != 0 && $booking->Status != 3)
                                    <!-- Chỉ hiển thị nút thanh toán nếu cần thanh toán -->
                                    <form action="/momo_paymentQR" method="POST">
                                        @csrf
                                        <input type="hidden" name="total" value="{{ $booking->Debt }}">
                                        <input type="hidden" name="Payment_id" value="{{ $booking->Payment_id }}">
                                        <input type="hidden" name="Booking_id" value="{{ $booking->Booking_id }}">
                                        <button type="submit" name="thanhtoan" class="btn btn-primary">Thanh toán</button>
                                        @if ($booking->Status != 0)
                                            <button type="submit" name="datcoc" class="btn btn-warning">Đặt cọc</button>
                                        @endif
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

    </div>
@endsection
