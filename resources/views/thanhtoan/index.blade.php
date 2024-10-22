@extends('layouts.app')

@section('content')
    <div class="container my-3">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Ngày Đặt</th>
                    <th>Người đặt</th>
                    <th>Số điện thoại</th>
                    <th>Giờ vào</th>
                    <th>Giờ ra</th>
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
                            <td>{{ $booking->user_name }}</td>
                            <td>0{{ $booking->user_phone }}</td>
                            <td>{{ $booking->Start_time }}</td>
                            <td>{{ $booking->End_time }}</td>
                            <td>{{ session('branch_active')->Name }}</td>
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
                                @if ($booking->Debt != 0)
                                    <!-- Chỉ hiển thị nút thanh toán nếu cần thanh toán -->

                                    <!-- Nút mở modal -->
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                        data-target="#paymentModal-{{ $booking->Booking_id }}">
                                        Thanh toán
                                    </button>

                                    <!-- Modal nhập số tiền -->
                                    <div class="modal fade" id="paymentModal-{{ $booking->Booking_id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="paymentModalLabel">Nhập số tiền thanh
                                                        toán</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('manager.paymentCourt') }}" method="POST"
                                                    id="paymentForm-{{ $booking->Booking_id }}">
                                                    @csrf
                                                    <input type="hidden" name="Booking_id"
                                                        value="{{ $booking->Booking_id }}">
                                                    <input type="hidden" name="Payment_id"
                                                        value="{{ $booking->Payment_id }}">

                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="paymentAmount">Số tiền cần thanh toán (ít nhất
                                                                1/2 số tiền nợ: {{ $booking->Debt / 2 }} VND):</label>
                                                            <input type="number" class="form-control" name="paymentAmount"
                                                                id="paymentAmount-{{ $booking->Booking_id }}"
                                                                placeholder="Nhập số tiền" min="{{ $booking->Debt / 2 }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Đóng</button>
                                                        <button type="submit" class="btn btn-primary">OK</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Nút Hủy -->
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                        data-target="#deleteModal-{{ $booking->Booking_id }}">
                                        Hủy
                                    </button>
                                    <!-- Modal xác nhận hủy sân -->
                                    <div class="modal fade" id="deleteModal-{{ $booking->Booking_id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel">Xác nhận hủy đặt sân</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form id="deleteForm{{ $booking->Booking_id }}"
                                                    action="{{ route('manager.cancelCourt') }}" method="POST"
                                                    style="display:inline;">
                                                    <div class="modal-body">
                                                        Bạn có chắc chắn muốn hủy đặt sân cho sân này không?
                                                        @csrf
                                                        @method('POST')

                                                        <input type="hidden" name="Payment_id"
                                                            value="{{ $booking->Payment_id }}">
                                                        <input type="hidden" name="Booking_id"
                                                            value="{{ $booking->Booking_id }}">
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Đóng</button>
                                                            <button type="submit" class="btn btn-danger">Xác Nhận
                                                                Hủy</button>
                                                        </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
    </div>
    @endif
    </td>
    </tr>
    @endforeach
    @endif
    </tbody>
    </table>

    </div>
    <script>
        // function submitPaymentForm(bookingId, minAmount) {
        //     const paymentAmountInput = document.getElementById('paymentAmount-' + bookingId);
        //     const paymentAmount = parseFloat(paymentAmountInput.value);

        //     if (paymentAmount < minAmount) {
        //         // showAlert('danger', 'Số tiền thanh toán phải lớn hơn hoặc bằng 1/2 số tiền nợ.');
        //         return;
        //     }

        //     // Tạo input chứa số tiền đã nhập và thêm vào form
        //     const form = document.getElementById('form-agree-' + bookingId);
        //     const input = document.createElement('input');
        //     input.type = 'hidden';
        //     input.name = 'amount'; // Tên của field số tiền đã thanh toán
        //     input.value = paymentAmount;
        //     form.appendChild(input);

        //     // Submit form
        //     form.submit();
        // }
    </script>
@endsection
