
<section id="cb-login-section">
    <div id="cb-login">
        <h1>LOGIN</h1>
  
        <form name="loginform" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return loginCheck(this);">
			<input type="hidden" name="r" value="<?php echo $r?>" />
			<input type="hidden" name="a" value="login" />
			<input type="hidden" name="referer" value="<?php echo $referer ? $referer : $_SERVER['HTTP_REFERER']?>" />
			<input type="hidden" name="usessl" value="<?php echo $d['member']['login_ssl']?>" />
            <div class="cb-inputnaked">
                <input name="id" value="" type="text" placeholder="아이디" style="color:#666">
            </div>
            <div class="cb-inputnaked">
                <input name="pw" value="" type="password" placeholder="비밀번호" style="color:#666">
            </div>
            <input type="submit" value="로그인 하기">
            <!-- 소셜 로그인 위젯  -->
            <?php //getWidget('default/slogin',array())?>

          <!--   <div class="cb-lost-n-found">
                <a href="<?php echo RW('mod=idpwsearch')?>" id="cb-go-findpassword">
                    비밀번호 찾기
                </a>
                <a href="<?php echo RW('mod=join')?>" id="cb-go-signup">
                    회원가입 하기
                </a>
            </div> -->
        </form>
    </div>    
</section>
<script>
function loginCheck(f)
{
    if (f.id.value == '')
    {
        alert('<?php echo $d['member']['login_emailid']?'이메일을':'아이디를'?> 입력해 주세요.');
        f.id.focus();
        return false;
    }
    if (f.pw.value == '')
    {
        alert('비밀번호를 입력해 주세요.');
        f.pw.focus();
        return false;
    }
    if (f.usessl.value == '1')
    {
        if (f.ssl.checked == true)
        {
            var fs = document.SSLLoginForm;
            fs.id.value = f.id.value;
            fs.pw.value = f.pw.value;
            if(f.idpwsave.checked == true) fs.idpwsave.value
            fs.submit();
            return false;
        }
    }
}
</script>