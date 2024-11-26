@extends('layouts.customer.customerApp')

@section('content')
    <div class="container mt-4">
        <form class="form-inline w-100 px-3" style="color: white">
            <div class="d-flex w-100">
                <input name="search" class="form-control flex-grow-1 search-input" value="{{ request()->get('search', '') }}"
                    style="margin-right: 5px; border-radius: 20px; border:1px solid #ffffff;color: white;background-color: #51A7BF;"
                    type="search" placeholder="Tìm kiếm" aria-label="Search">
                <button class="btn rounded-circle" style="background-color: #51A7BF;color:white; border:1px solid #ffffff" type="submit"><i
                        class="bi bi-search"></i></button>
            </div>
        </form>

        {{-- {{ dd($branches) }} --}}
        @if ($branches->count() > 0)
            @foreach ($branches as $branch)
                <div class="card card-custom p-3 pt-2"
                    style="background-color: transparent!important; border:none; color:white;margin-bottom: 0px;padding-bottom: 0px !important;">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img src="{{ $branch->Image }}" alt="Logo" class="rounded-circle"
                                style="width: 50px; height: 50px;">
                        </div>
                        <div class="col">
                            <h5 class="mb-0"><b>{{ $branch->Name }}</b></h5>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <p class="mb-0">{{ $branch->Location }}</p>
                        </div>
                        <div class="col-auto">
                            <a href="welcome-booking-calendar/?branch_id={{ $branch->Branch_id }}"
                                class="btn btn-warning btn-sm" style="color:#ffffff; background-color: #fba5a3; border:1px solid #fc9b98"><b>Đặt Lịch</b></a>
                        </div>
                    </div>
                </div>
                <hr style="background-color: white; color:white; height: 2.5px;border: none;">
            @endforeach
        @else
            <p class="text-center">Hiện không có sân nào.</p>
        @endif
    </div>
@endsection

<style>
    .card-custom {
        background-color: #006400;
        /* Dark green background */
        color: white;
        margin-bottom: 1rem;
    }

    .btn-schedule {
        background-color: #FFD700;
        /* Gold color for "Đặt Lịch" button */
        color: white;
    }

    .btn-icon {
        background-color: #FFD700;
        /* Gold color for icon button */
        color: white;
        border: none;
    }

    .star-rating {
        color: #FFD700;
        /* Gold stars */
    }

    .search-input::placeholder {
        color: white !important;
    }

    .rounded-circle:hover {
        opacity: 0.7;
    }
</style>
