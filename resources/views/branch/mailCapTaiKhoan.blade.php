<h2 style ="text-align:center;">Xin Chào Quý khách hàng!</h2>
<div class="testmail" style ="text-align:center;">
    <p>
        Cảm ơn anh/chị <b>{{ $user->Name }}</b> vì đã quan tâm đến phần mềm và đồng ý đồng hành cùng chúng tôi.
        <br>Đảm bảo rằng không cung cấp mail này cho người lạ.
        <br>Tài khoản đăng nhập của Quý Khách hàng:
    </p>
</div>
<table>
    <tr>
        <td>Tài khoản</td>
        <td>{{ $user->Email }}</td>
    </tr>
    <tr>
        <td>Mật khẩu</td>
        {{-- nếu là gửi mail khi admin thêm tài khoản thì gửi kèm mật khẩu --}}
        @if ($admin)
            <td>Tennis@123</td>
        @else
            <td>là mật khẩu Quý khách đã đăng ký trước đó</td>
        @endif

    </tr>
</table>
