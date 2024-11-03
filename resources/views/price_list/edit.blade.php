@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <form action="{{ route('price_list.update', $priceListVangLai->time_slot_id) }}" method="POST">
            @csrf
            @method('post') <!-- Thay đổi thành PUT để phản ánh chính xác phương thức HTTP -->
            <!-- Ngày -->
            <div class="row mb-3">
                <label for="status" class="col-md-4 col-form-label text-md-end">Ngày <span style="color:red">*</span></label>
                <div class="col-md-6">
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="1" {{ $timeSlot->Status == 1 ? 'selected' : '' }}>T2-T6</option>
                        <option value="2" {{ $timeSlot->Status == 2 ? 'selected' : '' }}>T7-CN</option>
                    </select>
                </div>
            </div>

            <!-- Khung giờ -->
            <div class="row mb-3">
                <label for="start_time" class="col-md-4 col-form-label text-md-end">Thời gian bắt đầu <span
                        style="color:red">*</span></label>
                <div class="col-md-6">
                    <input id="start_time" type="time" class="form-control @error('start_time') is-invalid @enderror"
                        name="start_time" value="{{ \Carbon\Carbon::parse($timeSlot->Start_time)->format('H:i') }}"
                        required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="end_time" class="col-md-4 col-form-label text-md-end">Thời gian kết thúc <span
                        style="color:red">*</span></label>
                <div class="col-md-6">
                    <input id="end_time" type="time" class="form-control @error('end_time') is-invalid @enderror"
                        name="end_time" value="{{ \Carbon\Carbon::parse($timeSlot->End_time)->format('H:i') }}" required>
                </div>
            </div>

            <!-- Giá Cố định -->
            <div class="row mb-3">
                <label for="co_dinh_price" class="col-md-4 col-form-label text-md-end">Giá Cố định <span
                        style="color:red">*</span></label>
                <div class="col-md-6">
                    <input id="co_dinh_price" type="number" step="0.01"
                        class="form-control @error('co_dinh_price') is-invalid @enderror" name="co_dinh_price"
                        value="{{ old('co_dinh_price', $priceListCoDinh->Price ?? 0) }}" required>
                </div>
            </div>

            <!-- Giá Vãng lai -->
            <div class="row mb-3">
                <label for="vang_lai_price" class="col-md-4 col-form-label text-md-end">Giá Vãng lai <span
                        style="color:red">*</span></label>
                <div class="col-md-6">
                    <input id="vang_lai_price" type="number" step="0.01"
                        class="form-control @error('vang_lai_price') is-invalid @enderror" name="vang_lai_price"
                        value="{{ old('vang_lai_price', $priceListVangLai->Price ?? 0) }}" required>
                </div>
            </div>

            <input type="hidden" name="priceListCoDinh" value="{{ $priceListCoDinh->Price_list_id }}">
            <input type="hidden" name="priceListVangLai" value="{{ $priceListVangLai->Price_list_id }}">
            <!-- Nút Submit -->
            <div class="row mb-0">
                <div class="col-md-8 offset-md-4">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="{{ route('price_list.index') }}" class="btn btn-secondary">Trở lại</a>
                </div>
            </div>
        </form>
    </div>
@endsection
