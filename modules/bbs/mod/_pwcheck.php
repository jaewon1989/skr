  <div class="panel panel-default" id="pw-confirm-panel">			
		 <div class="panel-heading">
			    <h3 class="panel-title"><i class="fa fa-lock fa-lg"></i> 비밀번호 확인</h3>			    
		  </div>  
	     <div class="panel-body">
					<form name="checkForm" method="post" class="form-horizontal rb-form" action="<?php echo $g['s']?>/" target="_action_frame_<?php echo $m?>" onsubmit="return permCheck(this);">
						<input type="hidden" name="r" value="<?php echo $r?>" />	
						<input type="hidden" name="a" value="<?php echo $mod=='delete'?$mod:'pwcheck'?>" />
						<input type="hidden" name="c" value="<?php echo $c?>" />
						<input type="hidden" name="cuid" value="<?php echo $_HM['uid']?>" />
						<input type="hidden" name="m" value="<?php echo $m?>" />
						<input type="hidden" name="bid" value="<?php echo $R['bbsid']?$R['bbsid']:$bid?>" />
						<input type="hidden" name="uid" value="<?php echo $R['uid']?>" />

						<input type="hidden" name="p" value="<?php echo $p?>" />
						<input type="hidden" name="cat" value="<?php echo $cat?>" />
						<input type="hidden" name="sort" value="<?php echo $sort?>" />
						<input type="hidden" name="orderby" value="<?php echo $orderby?>" />
						<input type="hidden" name="recnum" value="<?php echo $recnum?>" />
						<input type="hidden" name="type" value="<?php echo $type?>" />
						<input type="hidden" name="iframe" value="<?php echo $iframe?>" />
						<input type="hidden" name="skin" value="<?php echo $skin?>" />
						<input type="hidden" name="where" value="<?php echo $where?>" />
						<input type="hidden" name="keyword" value="<?php echo $_keyword?>" />

						<div class="input-group">
							<div class="input-group input-group-justified">
									<span class="input-group-btn input-sm">
									   <input type="password" name="pw" class="form-control" />
									 </span>
							 </div>
							 <span class="input-group-btn input-sm">
							    <input type="submit" value=" 확인 " class="btn btn-primary" />
							    <input type="button" value=" 취소 " class="btn btn-default" onclick="history.go(-1);" />
							 </span>   
						</div>
					</form>
		    </div>	<!-- .panel-body -->
			 <div class="panel-footer">
			     <p class="text-muted">게시물 등록시에 입력했던 비밀번호를 입력해 주세요.</p>
			 </div>	
   </div>
   <style>
   #pw-confirm-panel {width:50%;margin:0 auto;}
   </style>

<script type="text/javascript">
//<![CDATA[
var checkFlag = false;
function permCheck(f)
{
	if (checkFlag == true)
	{
		alert('확인중입니다. 잠시만 기다려 주세요.   ');
		return false;
	}
	
	if (f.pw.value == '')
	{
		alert('비밀번호를 입력해 주세요.   ');
		f.pw.focus();
		return false;
	}
	checkFlag = true;
}
window.onload = function(){document.checkForm.pw.focus();}
//]]>
</script>
