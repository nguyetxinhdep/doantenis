<h2 style ="text-align:center;">Xin Chào Quý khách hàng!</h2>
<div class="testmail" style ="text-align:center;">
    @if (isset($soluongBranch))
        <p>
            Cảm ơn anh/chị <b>{{ $user->Name }}</b> vì đã quan tâm đến phần mềm của chúng tôi.
            <br>Chúng tôi đã xóa 1 chi nhánh theo yêu cầu của anh/chị <b>{{ $user->Name }}</b>.
        </p>
    @else
        <p>
            Cảm ơn anh/chị <b>{{ $user->Name }}</b> vì đã quan tâm đến phần mềm của chúng tôi.
            <br>Rất tiếc chúng tôi không thể chấp nhận yêu cầu đăng ký chi nhánh vì một số lý do.
        </p>
    @endif
</div>
