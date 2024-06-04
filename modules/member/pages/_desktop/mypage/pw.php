<div id="page-profile">
	 	<?php include $g['dir_module_skin'].'_cover.php';?>
</div>
<article id="pages-signup">
	<form name="procForm" class="form-horizontal" role="form" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return saveCheck(this);">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="front" value="<?php echo $front?>" />
	<input type="hidden" name="a" value="pw_update" />

		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-default">
				    <div class="panel-heading well">
				        현재 비밀번호는 <strong><?php echo getDateFormat($my['last_pw'],'Y.m.d')?></strong> 에 변경(등록)되었으며 <strong><?php echo -getRemainDate($my['last_pw'])?>일</strong>이 경과되었습니다.<br />
			             비밀번호는 가급적 주기적으로 변경해 주세요.<br />
				    </div>
				    <div class="panel-body">
							<div class="form-group">
								<label for="pw" class="col-sm-3 control-label">현재 비밀번호</label>
								<div class="col-sm-8">
									    <input type="password" class="form-control" name="pw" id="pw" placeholder="현재 비밀번호를 입력해주세요.">
	  							 </div>
							</div>	
							<div class="form-group">
								 <label for="pw1" class="col-sm-3 control-label">변경할 비밀번호</label>
								 <div class="col-sm-8">
								     <input type="password" class="form-control" name="pw1" id="pw1" placeholder="변경할 비밀번호를 입력해주세요.">
	  								  <p class="help-block">
	                            4~12자의 영문과 숫자만 사용할 수 있습니다.
	  							 	  </p>
	  							  </div>	 
							</div>
							<div class="form-group">
								<label for="pw2" class="col-sm-3 control-label">비밀번호 확인</label>
								<div class="col-sm-8">
								    <input type="password" class="form-control" name="pw2" id="pw2" placeholder="변경할 비밀번호를 입력해주세요.">
			  					    <p class="help-block">
			                        변경할 비밀번호를 한번 더 입력하세요. 비밀번호는 잊지 않도록 주의하시기 바랍니다
			  					    </p>
			  					</div>	 
							</div>
							<hr>
					      <div class="rb-form-footer text-center">
							   <button type="submit" class="btn btn-primary"><i class="fa fa-check fa-lg"></i> 정보수정</button>
					       </div>
					 </div>
				 </div>
			 </div>
	    </div>
   </form>	
</article>	

<script type="text/javascript">
//<![CDATA[
function saveCheck(f)
{
	if (f.pw.value == '')
	{
		alert('현재 패스워드를 입력해 주세요.');
		f.pw.focus();
		return false;
	}

	if (f.pw1.value == '')
	{
		alert('변경할 패스워드를 입력해 주세요.');
		f.pw1.focus();
		return false;
	}
	if (f.pw2.value == '')
	{
		alert('변경할 패스워드를 한번더 입력해 주세요.');
		f.pw2.focus();
		return false;
	}
	if (f.pw1.value != f.pw2.value)
	{
		alert('변경할 패스워드가 일치하지 않습니다.');
		f.pw1.focus();
		return false;
	}

	if (f.pw.value == f.pw1.value)
	{
		alert('현재 패스워드와 변경할 패스워드가 같습니다.');
		f.pw1.value = '';
		f.pw2.value = '';
		f.pw1.focus();
		return false;
	}

	return confirm('정말로 수정하시겠습니까?       ');
}
//]]>
</script>

