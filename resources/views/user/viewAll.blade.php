@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Account ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 0; @endphp
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
                            <form id="deleteForm{{ $account->User_id }}" action="{{ route('manage-account.destroy') }}"
                                method="POST" class="d-inline">
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
