@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <form action="{{ route('manage-account.viewAll') }}" method="GET" class="mb-4">
            <div class="row">
                <!-- Ô input tìm kiếm tên -->
                <div class="col-md-3">
                    <label for="name">Tên</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên người đặt"
                        value="{{ request('name') }}">
                </div>

                <!-- Ô input tìm kiếm ngày tháng năm -->
                <div class="col-md-3">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email" name="email" value="{{ request('email') }}">
                </div>

                <!-- Ô input tìm kiếm số điện thoại -->
                <div class="col-md-3">
                    <label for="phone">Số điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone"
                        placeholder="Nhập số điện thoại" value="{{ request('phone') }}">
                </div>

                <!-- Ô input tìm kiếm trạng thái -->
                <div class="col-md-3">
                    <label for="role">Vai trò</label>
                    <select class="form-control" id="role" name="role">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('role') == '1' ? 'selected' : '' }}>Admin</option>
                        <option value="2" {{ request('role') == '2' ? 'selected' : '' }}>Subadmin</option>
                        <option value="3" {{ request('role') == '3' ? 'selected' : '' }}>Chủ sân</option>
                        <option value="4" {{ request('role') == '4' ? 'selected' : '' }}>Nhân viên</option>
                        <option value="5" {{ request('role') == '5' ? 'selected' : '' }}>Khách hàng</option>
                    </select>
                </div>
            </div>

            <!-- Nút tìm kiếm -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-sm">Tìm kiếm</button>
                    <a href="{{ route('manage-account.viewAll') }}" class="btn btn-warning btn-sm">Đặt lại</a>
                </div>
            </div>
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Vai trò</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 0; @endphp
                @if ($accounts->isEmpty())
                    <tr>
                        <td colspan="7" style="text-align: center">Không tìm thấy tài khoản</td>
                    </tr>
                @else
                    @foreach ($accounts as $account)
                        @php ++$i; @endphp
                        <tr id="{{ $account->User_id }}">
                            <td>{{ $i }}</td>
                            <td>{{ $account->User_id }}</td>
                            <td>{{ $account->Name }}</td>
                            <td>{{ $account->Email }}</td>
                            <td>0{{ $account->Phone }}</td>
                            <td>{{ $account->Role }}</td>
                            <td class="d-flex align-items-center">
                                <a href="{{ route('admin.manage-account.detail', ['id' => $account->User_id]) }}"
                                    class="btn btn-primary btn-sm me-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form id="deleteForm{{ $account->User_id }}"
                                    action="{{ route('manage-account.destroy') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" name="account_id" value="{{ $account->User_id }}">
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="modalxoa({{ $account->User_id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <!-- Modal Xác Nhận Xóa -->
    <div class="modal fade" id="deleteModaltaikhoan" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Xác Nhận Xóa Tài Khoản</h5>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa tài khoản này không?
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="modalhide()" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <form id="confirmDeleteForm" action="" method="POST">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="account_id" id="account_id">
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function modalxoa(accountId) {
            // Lấy URL của form xóa và gán vào modal
            const form = document.getElementById('deleteForm' + accountId);
            const actionUrl = form.getAttribute('action');

            // Gán action cho form xác nhận xóa
            const confirmDeleteForm = document.getElementById('confirmDeleteForm');
            confirmDeleteForm.setAttribute('action', actionUrl);

            // Gán account_id vào form xác nhận
            document.getElementById('account_id').value = accountId;

            // Hiển thị modal
            const modal = new bootstrap.Modal(document.getElementById('deleteModaltaikhoan'));
            modal.show();
        }

        function modalhide() {
            const modal = new bootstrap.Modal(document.getElementById('deleteModaltaikhoan'));
            modal.hide();
        }
    </script>
@endsection
