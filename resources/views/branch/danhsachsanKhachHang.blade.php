@extends('layouts.customer.customerApp')

@section('content')
    <div class="container-fluid d-flex align-items-center" style="height: calc(100vh - 60px)">
        <div class="container">
            <div class="row">
                @if ($branches->count() > 0)
                    @foreach ($branches as $branch)
                        <div class="col-md-4 mb-4">
                            <div class="card branch-card">
                                <a href="welcome-booking-calendar/?branch_id={{ $branch->Branch_id }}"
                                    style="text-decoration: none; color:black">
                                    <div class="card-body">
                                        <h5 class="card-title"><b>{{ $branch->Name }}</b></h5>
                                        <p class="card-text">
                                            Địa chỉ: {{ $branch->Location }}
                                        </p>
                                        <p class="card-text">
                                            Trạng thái: {{ $branch->Status == '3' ? 'Hoạt động' : 'Không hoạt động' }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-center">Hiện không có sân nào.</p>
                @endif
            </div>
            <!-- Hiển thị phân trang -->
            <div class="">
                {{ $branches->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

<style>
    .branch-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .branch-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        background-color: rgb(62, 102, 222);
        cursor: pointer;
    }

    .branch-card:hover a {
        color: white !important;
    }
</style>
