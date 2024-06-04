<?php $id_or_email='회원가입시 등록한 '.($d['member']['login_emailid']?'아이디':'이메일').'을 입력해주세요.'?>
<div id="pages_idpw">
	<div class="page-header">
		<h1><?php echo $d['member']['login_emailid']?'이메일':'아이디'?>찾기 / 임시비밀번호 요청</h1>
	</div>
	<!-- Nav tabs -->
	<ul class="nav nav-tabs hidden-xs">
		<li class="active"><a href="#idpwsearch-1" data-toggle="tab"><i class="fa fa-search fa-lg"></i> <?php echo $d['member']['login_emailid']?'이메일':'아이디'?> 찾기</a></li>
		<li><a href="#idpwsearch-2" data-toggle="tab"><i class="fa fa-key fa-lg"></i> 임시비밀번호 요청</a></li>
	</ul>
	<ul class="nav nav-tabs visible-xs">
		<li class="active"><a href="#idpwsearch-1" data-toggle="tab"><?php echo $d['member']['login_emailid']?'이메일':'아이디'?>찾기</a></li>
		<li><a href="#idpwsearch-2" data-toggle="tab"><i class="fa fa-key fa-lg"></i> PW 요청</a></li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">

		<!-- 아이디 찾기 -->
		<div class="tab-pane active" id="idpwsearch-1">
			<div class="row">
				<div class="col-sm-3 text-center">
					<span class="fa-stack fa-5x hidden-xs">
						<i class="fa fa-circle fa-stack-2x"></i>
						<i class="fa fa-search fa-stack-1x fa-inverse"></i>
					</span>
				</div>
				<div class="col-sm-9">
					<form name="procForm1" class="form-horizontal" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return idCheck(this);">
						<input type="hidden" name="r" value="<?php echo $r?>" />
						<input type="hidden" name="m" value="<?php echo $m?>" />
						<input type="hidden" name="a" value="id_search" />
						<div class="form-group">
							<label for="join_name" class="col-sm-2 control-label">이름</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="name" id="join_name" placeholder="회원가입시에 등록한 이름을 입력 해주세요.">
							</div>
						</div>
						<div class="form-group">
							<label for="join_email" class="col-sm-2 control-label"><?php echo $d['member']['login_emailid']?'아이디':'이메일'?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="email" id="join_email" placeholder="<?php echo $id_or_email?>">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-9">
								 <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> <?php echo $d['member']['login_emailid']?'이메일':'아이디'?> 찾기</button>
							    <a href="<?php echo $g['url_reset']?>&amp;page=main" class="btn btn-default">로그인 페이지로 돌아가기</a>
							</div>
						</div>
					</form>
				</div>
			</div>
			<p class="text-left text-muted small well well-default">
					<?php if($d['member']['login_emailid']):?>
						이메일은 회원가입시 등록한 이름(실명)과 아이디를 입력하시면 정보를 확인하실 수 있습니다.
					<?php else:?>
						아이디는 회원가입시 등록한 이름(실명)과 이메일을 입력하시면 정보를 확인하실 수 있습니다.
					<?php endif?>
			</p>
		</div>

		<!-- 비밀번호 요청  -->
		<div class="tab-pane" id="idpwsearch-2">
			<div class="row">
				<div class="col-sm-3 text-center">
					<span class="fa-stack fa-5x hidden-xs">
						<i class="fa fa-circle fa-stack-2x"></i>
						<i class="fa fa-key fa-stack-1x fa-inverse"></i>
					</span>
				</div>
				<div class="col-sm-9">
					<form name="procForm3" class="form-horizontal" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return idCheck(this);">
						<input type="hidden" name="r" value="<?php echo $r?>" />
						<input type="hidden" name="m" value="<?php echo $m?>" />
						<input type="hidden" name="a" value="id_auth" />
						<div class="form-group">
							<label for="join_name" class="col-sm-2 control-label">이름</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="name" id="join_name" placeholder="회원가입시에 등록한 이름을 입력 해주세요.">
							</div>
						</div>
						<div class="form-group">
							<label for="join_email" class="col-sm-2 control-label"><?php echo $d['member']['login_emailid']?'아이디':'이메일'?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="email" id="join_email" placeholder="<?php echo $id_or_email?>">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-9">
								 <button type="submit" class="btn btn-primary"><i class="fa fa-envelope"></i> 임시비밀번호 요청</button>
							    <a href="<?php echo $g['url_reset']?>&amp;page=main" class="btn btn-default">로그인 페이지로 돌아가기</a>
							</div>
						</div>
					</form>
				</div>
			</div>
			<p class="text-left text-muted small well well-default">
				메일수신이 안되었을 경우, 스펨함을 확인해 보시고 기타사항에 대해서는 <strong>관리자</strong>에게  문의해주세요. <br />
				<span class="text-danger">임시비밀번호로 로그인한 후 비밀번호를 변경해주세요.</span>
			</p>
		</div>		
	</div> <!-- .tab-content -->
</div>


<script type="text/javascript">
//<![CDATA[

function idCheck(f)
{
	if (f.name.value == '')
	{
		alert('이름을 입력해 주세요.   ');
		f.name.focus();
		return false;
	}
	if (f.email.value == '')
	{
		alert('<?php echo $d['member']['login_emailid']?'아이디를':'이메일을'?> 입력해 주세요.   ');
		f.email.focus();
		return false;
	}
}
function pwCheck(f)
{
	if (f.new_id.value == '')
	{
		alert('<?php echo $d['member']['login_emailid']?'이메일을':'아이디를'?> 입력해 주세요.   ');
		f.new_id.focus();
		return false;
	}
	if (f.id_auth.value == '2')
	{
		if (f.new_pw_a.value == '')
		{
			alert('답변을 입력해 주세요.   ');
			f.new_pw_a.focus();
			return false;
		}
	}
	if (f.id_auth.value == '3')
	{
		if (f.new_pw1.value == '')
		{
			alert('새 패스워드를 입력해 주세요.');
			f.new_pw1.focus();
			return false;
		}
		if (f.new_pw2.value == '')
		{
			alert('새 패스워드를 한번더 입력해 주세요.');
			f.new_pw2.focus();
			return false;
		}
		if (f.new_pw1.value != f.new_pw2.value)
		{
			alert('새 패스워드가 일치하지 않습니다.');
			f.new_pw1.focus();
			return false;
		}

		alert('입력하신 패스워드로 재등록 되었습니다.');
	}
}

window.onload = function()
{
	<?php if($ftype == 'pw'):?>
	tabShow(2);
	<?php elseif($ftype == 'auth'):?>
	tabShow(3);
	<?php else:?>
	document.procForm1.name.focus();
	<?php endif?>
}
//]]>
</script>