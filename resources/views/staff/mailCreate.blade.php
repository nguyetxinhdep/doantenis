<h2 style ="text-align:center;">Xin Chào!</h2>
<div class="testmail" style ="text-align:center;">
    <p>
        Bạn đã trở thành nhân viên của <b>{{ $branch->Name }}</b>
        <br>Đây là tài khoản và mật khẩu dùng để truy cập vào <a href="{{ route('welcome') }}">website</a>
        <br>Tài khoản: {{ $user->Email }}
        <br>Mật khẩu: Tennis@123
        <br>Sau khi đăng nhập, vui lòng đổi mật khẩu để đảm bảo quy tắc bảo mật.
        <br>Tennis cảm ơn!
    </p>
</div>
