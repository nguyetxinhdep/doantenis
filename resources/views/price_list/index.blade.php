@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Khung giờ</th>
                    <th>Vãng lai</th>
                    <th>Cố định</th>
                    @if (Auth()->user()->Role == '3')
                        <th>Hành động</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @php
                    $status1Rows = 0;
                    foreach ($list as $items) {
                        // duyệt qua từng hàng, xem có hàng nào có cột status = 1(sân ngày thường) thì + $status1Rows
                        if ($items->contains('Status', 1)) {
                            $status1Rows++;
                        }
                    }
                @endphp

                <!-- Hiển thị T2-T6 -->
                @foreach ($list as $key => $items)
                    @php
                        //kiểm tra xem trong hàng đang duyệt này có tồn tại cột nào có status = 1 không -> true/false
                        $status1Exists = $items->contains('Status', 1);

                        //kiểm tra xem trong hàng đang duyệt này có tồn tại cột nào có status = 2 không -> true/false
                        $status2Exists = $items->contains('Status', 2);
                    @endphp

                    <!-- Xử lý T2-T6 (Status = 1) -->
                    @if ($status1Exists)
                        <tr>
                            {{-- nếu là vòng lặp đầu tiên thì thực hiện(laravel hỗ trợ) --}}
                            @if ($loop->first)
                                <!-- Dùng rowspan để gộp T2-T6 -->
                                <td rowspan="{{ $status1Rows }}">T2-T6</td>
                            @endif

                            {{-- key này là khung giờ do group by bên controller rồi --}}
                            <td>{{ $key }}</td>
                            <td>
                                @php
                                    $vanglai = $items->firstWhere('customer_type_id', 1); //true,flase
                                @endphp
                                {{ $vanglai ? number_format($vanglai->Price, 0, ',', '.') . ' VND' : 'Không có giá' }}
                            </td>
                            <td>
                                @php
                                    $codinh = $items->firstWhere('customer_type_id', 2); // true,false
                                @endphp
                                {{ $codinh ? number_format($codinh->Price, 0, ',', '.') . ' VND' : 'Không có giá' }}
                            </td>
                            @if (Auth()->user()->Role == '3')
                                <td>
                                    @php
                                        $item = $items->first();
                                    @endphp
                                    <a href="{{ route('price_list.edit', $item->time_slot_id) }}"
                                        class="btn btn-warning btn-sm">Sửa</a>

                                    <form action="{{ route('price_list.destroy', $item->time_slot_id) }}" method="POST"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endif
                @endforeach

                <!-- Hiển thị T7-CN (Status = 2) -->
                @foreach ($list as $key => $items)
                    @php
                        $status1Exists = $items->contains('Status', 1);
                        $status2Exists = $items->contains('Status', 2);
                    @endphp

                    @if ($status2Exists)
                        <tr>
                            <td>T7-CN</td>
                            <td>{{ $key }}</td>
                            <td>
                                @php
                                    $vanglai = $items->firstWhere('customer_type_id', 1);
                                @endphp
                                {{ $vanglai ? number_format($vanglai->Price, 0, ',', '.') . ' VND' : 'Không có giá' }}
                            </td>
                            <td>
                                @php
                                    $codinh = $items->firstWhere('customer_type_id', 2);
                                @endphp
                                {{ $codinh ? number_format($codinh->Price, 0, ',', '.') . ' VND' : 'Không có giá' }}
                            </td>
                            @if (Auth()->user()->Role == '3')
                                <td>
                                    @php
                                        $item = $items->first();
                                    @endphp
                                    <a href="{{ route('price_list.edit', $item->time_slot_id) }}"
                                        class="btn btn-warning btn-sm">Sửa</a>

                                    <form action="{{ route('price_list.destroy', $item->time_slot_id) }}" method="POST"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
