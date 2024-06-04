<div id="page-profile">
	 	<?php include $g['dir_module_skin'].'_cover.php';?>
</div>
<article id="pages-signup">
	<form name="procForm" class="form-horizontal" role="form" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return saveCheck(this);">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="front" value="<?php echo $front?>" />
	<input type="hidden" name="a" value="out" />

		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-default">
				    <div class="panel-heading well">
				        회원탈퇴를 원하시면 비밀번호를 입력하신 후 회원탈퇴 버튼을 클릭해 주세요.<br />
		              탈퇴하시면 회원정보가 데이터베이스에서 완전히 삭제됩니다.<br />
				    </div>
				    <div class="panel-body">
							<div class="form-group">
								 <label for="pw1" class="col-sm-3 control-label">비밀번호</label>
								 <div class="col-sm-8">
								     <input type="password" class="form-control" name="pw1" id="pw1" >
	  							  </div>	 
							</div>
							<div class="form-group">
								<label for="pw2" class="col-sm-3 control-label">비밀번호 확인</label>
								<div class="col-sm-8">
								    <input type="password" class="form-control" name="pw2" id="pw2">
			  					</div>	 
							</div>
							<hr>
					      <div class="rb-form-footer text-center">
							   <button type="submit" class="btn btn-primary"><i class="fa fa-check fa-lg"></i> 회원탈퇴</button>
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
	if (f.pw1.value == '')
	{
		alert('패스워드를 입력해 주세요.');
		f.pw1.focus();
		return false;
	}
	if (f.pw2.value == '')
	{
		alert('패스워드를 한번더 입력해 주세요.');
		f.pw2.focus();
		return false;
	}
	if (f.pw1.value != f.pw2.value)
	{
		alert('패스워드가 일치하지 않습니다.');
		f.pw1.focus();
		return false;
	}

	return confirm('정말로 탈퇴하시겠습니까?       ');
}

//]]>
</script>