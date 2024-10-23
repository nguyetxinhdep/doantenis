@extends('layouts.app')

@section('content')
    <div class="container my-3">
        <!-- Form tìm kiếm -->
        <form action="{{ route('manager.searchBookings') }}" method="GET" class="mb-4">
            <div class="row">
                <!-- Ô input tìm kiếm tên -->
                <div class="col-md-3">
                    <label for="name">Tên</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên người đặt"
                        value="{{ request('name') }}">
                </div>

                <!-- Ô input tìm kiếm ngày tháng năm -->
                <div class="col-md-3">
                    <label for="date">Ngày tháng năm</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
                </div>

                <!-- Ô input tìm kiếm số điện thoại -->
                <div class="col-md-3">
                    <label for="phone">Số điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone"
                        placeholder="Nhập số điện thoại" value="{{ request('phone') }}">
                </div>

                <!-- Ô input tìm kiếm trạng thái -->
                <div class="col-md-3">
                    <label for="status">Trạng thái</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Tất cả</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Chưa thu đủ</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đã thu đủ (OK)</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Cần thanh toán để giữ sân
                        </option>
                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
            </div>

            <!-- Nút tìm kiếm -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                </div>
            </div>
        </form>
        {{-- ----------------------------------------- --}}

        {{-- bảng hiển thị nội dung --}}
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>STT</th>
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
                        <td colspan="10" class="text-center">Chưa có lịch sử đặt sân nào.</td>
                    </tr>
                @else
                    @php $i = 1; @endphp
                    @foreach ($history as $booking)
                        <tr>
                            <td>{{ $i++ }}</td>
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
                                @if ($booking->Debt != 0 && $booking->Status != 3)
                                    <!-- Nút mở modal thanh toán -->
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
                                                    <h5 class="modal-title" id="paymentModalLabel">Nhập số tiền thanh toán
                                                    </h5>
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
                                                    <h5 class="modal-title" id="deleteModalLabel">Xác nhận hủy đặt sân
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form id="deleteForm{{ $booking->Booking_id }}"
                                                    action="{{ route('manager.cancelCourt') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="Payment_id"
                                                        value="{{ $booking->Payment_id }}">
                                                    <input type="hidden" name="Booking_id"
                                                        value="{{ $booking->Booking_id }}">

                                                    <div class="modal-body">
                                                        Bạn có chắc chắn muốn hủy đặt sân cho sân này không?
                                                    </div>
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
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <!-- Hiển thị các liên kết phân trang -->
        <!-- Hiển thị phân trang -->
        <div class="">
            {{ $history->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
