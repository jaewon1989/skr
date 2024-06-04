<div class="modal-login-2">
	<div style="z-index:100">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">회원 로그인</h4>
			</div>
		<form name="loginform" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return loginCheck(this);">
			<input type="hidden" name="r" value="<?php echo $r?>" />
			<input type="hidden" name="a" value="login" />
			<input type="hidden" name="referer" value="<?php echo $referer ? $referer : $_SERVER['HTTP_REFERER']?>" />
			<input type="hidden" name="usessl" value="<?php echo $d['member']['login_ssl']?>" />
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6 col-sm-push-6 rb-social">
                    <p class="lead hidden-xs text-muted">광고영역</p>					
					</div>
					<div class="col-sm-6 col-sm-pull-6 orbullet-divider rb-form">
						
							<div class="form-group">
								<label for="id" class="sr-only"><?php echo $d['member']['login_emailid']?'이메일':'아이디'?></label>
								<div class="input-icon">
									<i class="fa fa-user"></i>									
									<input type="text" name="id" value="<?php echo getArrayCookie($_COOKIE['svshop'],'|',0)?>" placeholder="<?php echo $d['member']['login_emailid']?'이메일':'아이디'?>을 입력해 주세요" id="id" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label for="pw" class="sr-only">Password</label>
								<div class="input-icon">
									<i class="fa fa-lock"></i>
									<input type="password" name="pw" value="<?php echo getArrayCookie($_COOKIE['svshop'],'|',1)?>" placeholder="비밀번호를 입력해주세요." id="pw" class="form-control">
								</div>
							</div>
							<p>
								<label class="checkbox-inline">
									<input type="checkbox" name="idpwsave" value="checked" onclick="remember_idpw(this)"<?php if($_COOKIE['svshop']):?> checked="checked"<?php endif?> id="inlineCheckbox1"> 정보기억 
								</label>
								<?php if($d['member']['login_ssl']):?>
								<label class="checkbox-inline">
									<input type="checkbox"  name="ssl" value="checked" id="inlineCheckbox2"> <abbr title="SSL:Secure Sockets Layer">보안접속</abbr>
								</label>
							  <?php endif?>
							</p>
							<div class="well well-sm">
						   	<ul class="list-unstyled kr-ul" >
									  <li>비밀번호를 잊으셨나요 ? <a href="<?php echo $g['url_reset']?>&page=idpwsearch">도움이 필요하세요?</a></li>
									  <li>회원계정이 없으신가요 ? <a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $m?>&amp;front=join">가입하기</a></li>
							   </ul>
						</div>
					</div>
				</div>		
			</div>
			<div class="modal-footer">
					<button class="btn btn-primary" type="input">로그인</button>
			</div>
		  </form>
		</div>
	</div>
</div>
<form name="SSLLoginForm" action="https://<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']?>" method="post" target="_action_frame_<?php echo $m?>">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="a" value="login" />
	<input type="hidden" name="referer" value="<?php echo $referer?$referer:$_SERVER['HTTP_REFERER']?>" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="pw" value="" />
	<input type="hidden" name="idpwsave" value="" />
</form>
<script type="text/javascript">
//<![CDATA[
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
function remember_idpw(ths)
{
	if (ths.checked == true)
	{
		if (!confirm('패스워드정보를 저장할 경우 다음접속시 \n\n패스워드를 입력하지 않으셔도 됩니다.\n\n그러나, 개인PC가 아닐 경우 타인이 로그인할 수 있습니다.     \n\nPC를 여러사람이 사용하는 공공장소에서는 체크하지 마세요.\n\n정말로 패스워드를 기억시키겠습니까?\n\n'))
		{
			ths.checked = false;
		}
	}
}

window.onload = function()
{
	document.loginform.id.focus();
}
//]]>
</script>