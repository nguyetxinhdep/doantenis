@extends('layouts.app')

@section('content')
    @if (Auth()->user()->Role == '3' || Auth()->user()->Role == '4')
        <div class="row justify-content-center mt-3 px-2">
            {{-- {{ dd($data) }} --}}
            <form action="{{ route('home') }}" method="GET" class="mb-4">
                <div class="row">
                    <!-- Ô input chọn ngày -->
                    <div class="col-md-3">
                        <label for="date">Ngày</label>
                        <input type="date" class="form-control" id="date" name="date"
                            value="{{ request('date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                    </div>

                    <!-- Ô input thời gian bắt đầu -->
                    <div class="col-md-3">
                        <label for="start_time">Thời gian bắt đầu</label>
                        <input type="time" class="form-control" id="start_time" name="start_time"
                            value="{{ request('start_time', \Carbon\Carbon::now()->addHours(7)->format('H:i')) }}">
                    </div>

                    <!-- Ô input thời gian kết thúc -->
                    <div class="col-md-3">
                        <label for="end_time">Thời gian kết thúc</label>
                        <input type="time" class="form-control" id="end_time" name="end_time"
                            value="{{ request('end_time', '23:30') }}">
                    </div>

                    <!-- Ô chọn trạng thái -->
                    <div class="col-md-3">
                        <label for="branch_id">Địa điểm</label>
                        <select class="form-control" id="branch_id" name="branch_id">
                            <option value="">Tất cả</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->Branch_id }}"
                                    {{ request('branch_id') == $branch->Branch_id ? 'selected' : '' }}>
                                    {{ $branch->Name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ô input năm -->
                    <div class="col-md-3">
                        <label for="year">Năm</label>
                        <input type="number" class="form-control" id="year" name="year"
                            value="{{ request('year', date('Y')) }}" min="2000" max="2100" step="1"
                            placeholder="Nhập năm">
                    </div>
                </div>

                <!-- Nút tìm kiếm -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-sm">Tìm kiếm</button>
                        <a href="{{ route('home') }}" class="btn btn-warning btn-sm">Đặt lại</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="row px-2">
            <div class="col-lg-6 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $data->courts_booked }}</h3>

                        <p>Sân đã đặt</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                    </div>
                    {{-- <a href="" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> --}}
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-6 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $data->courts_available }}</h3>


                        <p>Sân còn trống</p>
                    </div>
                    <div class="icon">
                        <!-- Icon phù hợp với sân còn trống -->
                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                    </div>
                    {{-- <a href="" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> --}}
                </div>
            </div>
            <!-- ./col -->
        </div>

        <!-- Biểu đồ -->
        <div class="row px-2 mt-4">
            <div class="col-lg-12">
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>

        <script>
            // Dữ liệu doanh thu
            const labels = {!! json_encode($chartData['labels']) !!}; // Danh sách tháng/năm
            const data = {!! json_encode($chartData['data']) !!}; // Tổng doanh thu mỗi tháng

            const ctx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Tổng doanh thu (VND)',
                        data: data,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)', // Màu cột
                        borderColor: 'rgba(54, 162, 235, 1)', // Màu viền
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Doanh thu (VND)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Thời gian (Tháng/Năm)'
                            }
                        }
                    }
                }
            });
        </script>
    @else
        <div class="row justify-content-center my-3 px-2">
            Chào mừng bạn!
        </div>
    @endif
@endsection
