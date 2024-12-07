@extends('layouts.app')

@section('content')
    <div class="container">
        <form method="GET" action="">
            @csrf
            <div class="form-group">
                <label for="date_from">Thời gian</label>
                <input type="date" name="date_from" id="date_from" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="branch_id">Chi nhánh</label>
                <select name="branch_id" id="branch_id" class="form-control">
                    <option value="">Tất cả</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->Branch_id }}">{{ $branch->Name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="time_slot_id">Khung giờ</label>
                <select name="time_slot_id" id="time_slot_id" class="form-control">
                    <option value="">Tất cả</option>
                    @foreach ($timeSlots as $timeSlot)
                        <option value="{{ $timeSlot->Time_slot_id }}">{{ $timeSlot->Start_time }} -
                            {{ $timeSlot->End_time }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="customer_type_id">Loại khách hàng</label>
                <select name="customer_type_id" id="customer_type_id" class="form-control">
                    <option value="">Tất cả</option>
                    @foreach ($customerTypes as $customerType)
                        <option value="{{ $customerType->Customer_type_id }}">{{ $customerType->Type_name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Lọc</button>
        </form>


    </div>
@endsection
