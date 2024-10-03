<h2 style ="text-align:center;">Hi Bạn</h2>
<div class="testmail" style ="text-align:center;">
    <p>
        Bạn đã gửi yêu cầu đổi mật khẩu. Nếu đúng hãy nhấn xác nhận
    </p>
    <a href="{{ route('xacnhan', ['id' => $user->User_id, 'token' => $user->token_change_pass]) }}"
        style="padding:3px 5px; background-color: green; color:white; text-decoration: none;">Xác nhận đổi</a>
</div>
