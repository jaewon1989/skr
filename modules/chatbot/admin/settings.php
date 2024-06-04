<?php
include_once $g['path_module'].$module.'/var/var.php';
?>
<form class="form-horizontal rb-form" role="form" name="procForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return saveCheck(this);">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $module?>" />
	<input type="hidden" name="a" value="_admin/config" />
	<div class="page-header">
		<h4>챗봇 기초환경</h4>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">사용자 레이아웃(D) <small class="text-muted"><a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#layout_main-guide"><i class="fa fa-question-circle fa-fw"></i></a></small></label>
		<div class="col-sm-10">
	   	    <select name="layout_desktop" class="form-control">
			 	<option value="">+ 사이트 대표 데스크탑 레이아웃</option>
				<option value="">--------------------------------</option>
					<?php $dirs = opendir($g['path_layout'])?>
					<?php while(false !== ($tpl = readdir($dirs))):?>
					<?php if($tpl=='.' || $tpl == '..' || $tpl == '_blank' || is_file($g['path_layout'].$tpl))continue?>
					<?php $dirs1 = opendir($g['path_layout'].$tpl)?>
					<option value="">--------------------------------</option>
					<?php while(false !== ($tpl1 = readdir($dirs1))):?>
					<?php if(!strstr($tpl1,'.php') || $tpl1=='_main.php')continue?>
					<option value="<?php echo $tpl?>/<?php echo $tpl1?>"<?php if($d['chatbot']['layout_desktop']==$tpl.'/'.$tpl1):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($g['path_layout'].$tpl)?>(<?php echo str_replace('.php','',$tpl1)?>)</option>
					<?php endwhile?>
					<?php closedir($dirs1)?>
					<?php endwhile?>
					<?php closedir($dirs)?>
			</select>
			<p class="help-block collapse" id="layout_main-guide">
				<small>
				사용자 대표레이아웃(<code>데스크탑 전용</code>) 을 선택해주세요.<br />
				선택하지 않으면 사이트 대표레이아웃이 설정됩니다.
			   </small>
			</p>
		</div>				
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">사용자 레이아웃(M) <small class="text-muted"><a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#layout_mobile-guide"><i class="fa fa-question-circle fa-fw"></i></a></small></label>
		<div class="col-sm-10">
	   	    <select name="layout_mobile" class="form-control">
			 	<option value="">+ 사이트 대표 모바일 레이아웃</option>
				<option value="">--------------------------------</option>
					<?php $dirs = opendir($g['path_layout'])?>
					<?php while(false !== ($tpl = readdir($dirs))):?>
					<?php if($tpl=='.' || $tpl == '..' || $tpl == '_blank' || is_file($g['path_layout'].$tpl))continue?>
					<?php $dirs1 = opendir($g['path_layout'].$tpl)?>
					<option value="">--------------------------------</option>
					<?php while(false !== ($tpl1 = readdir($dirs1))):?>
					<?php if(!strstr($tpl1,'.php') || $tpl1=='_main.php')continue?>
					<option value="<?php echo $tpl?>/<?php echo $tpl1?>"<?php if($d['chatbot']['layout_mobile']==$tpl.'/'.$tpl1):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($g['path_layout'].$tpl)?>(<?php echo str_replace('.php','',$tpl1)?>)</option>
					<?php endwhile?>
					<?php closedir($dirs1)?>
					<?php endwhile?>
					<?php closedir($dirs)?>
			</select>
			<p class="help-block collapse" id="layout_mobile-guide">
				<small>
				사용자 대표레이아웃(<code>모바일 전용</code>) 을 선택해주세요.<br />
				선택하지 않으면 모바일 사이트 대표레이아웃이 설정됩니다.
			   </small>
			</p>
		</div>				
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">관리자 레이아웃(D) <small class="text-muted"><a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#layout_admin_main-guide"><i class="fa fa-question-circle fa-fw"></i></a></small></label>
		<div class="col-sm-10">
	   	    <select name="layout_desktop_admin" class="form-control">
			 	<option value="">+ 사이트 대표 데스크탑 레이아웃</option>
				<option value="">--------------------------------</option>
					<?php $dirs = opendir($g['path_layout'])?>
					<?php while(false !== ($tpl = readdir($dirs))):?>
					<?php if($tpl=='.' || $tpl == '..' || $tpl == '_blank' || is_file($g['path_layout'].$tpl))continue?>
					<?php $dirs1 = opendir($g['path_layout'].$tpl)?>
					<option value="">--------------------------------</option>
					<?php while(false !== ($tpl1 = readdir($dirs1))):?>
					<?php if(!strstr($tpl1,'.php') || $tpl1=='_main.php')continue?>
					<option value="<?php echo $tpl?>/<?php echo $tpl1?>"<?php if($d['chatbot']['layout_desktop_admin']==$tpl.'/'.$tpl1):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($g['path_layout'].$tpl)?>(<?php echo str_replace('.php','',$tpl1)?>)</option>
					<?php endwhile?>
					<?php closedir($dirs1)?>
					<?php endwhile?>
					<?php closedir($dirs)?>
			</select>
			<p class="help-block collapse" id="layout_admin_main-guide">
				<small>
				관리자 대표레이아웃(<code>데스크탑 전용</code>) 을 선택해주세요.<br />
				선택하지 않으면 사이트 대표레이아웃이 설정됩니다.
			   </small>
			</p>
		</div>				
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">관리자 레이아웃(M) <small class="text-muted"><a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#layout_mobile_admin-guide"><i class="fa fa-question-circle fa-fw"></i></a></small></label>
		<div class="col-sm-10">
	   	    <select name="layout_mobile_admin" class="form-control">
			 	<option value="">+ 사이트 대표 모바일 레이아웃</option>
				<option value="">--------------------------------</option>
					<?php $dirs = opendir($g['path_layout'])?>
					<?php while(false !== ($tpl = readdir($dirs))):?>
					<?php if($tpl=='.' || $tpl == '..' || $tpl == '_blank' || is_file($g['path_layout'].$tpl))continue?>
					<?php $dirs1 = opendir($g['path_layout'].$tpl)?>
					<option value="">--------------------------------</option>
					<?php while(false !== ($tpl1 = readdir($dirs1))):?>
					<?php if(!strstr($tpl1,'.php') || $tpl1=='_main.php')continue?>
					<option value="<?php echo $tpl?>/<?php echo $tpl1?>"<?php if($d['chatbot']['layout_mobile_admin']==$tpl.'/'.$tpl1):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($g['path_layout'].$tpl)?>(<?php echo str_replace('.php','',$tpl1)?>)</option>
					<?php endwhile?>
					<?php closedir($dirs1)?>
					<?php endwhile?>
					<?php closedir($dirs)?>
			</select>
			<p class="help-block collapse" id="layout_mobile_admin-guide">
				<small>
				관리자 대표레이아웃(<code>모바일 전용</code>) 을 선택해주세요.<br />
				선택하지 않으면 모바일 사이트 대표레이아웃이 설정됩니다.
			   </small>
			</p>
		</div>				
	</div>

    <div class="form-group">
  	   <label class="col-sm-2 control-label">챗봇 테마(D) <small class="text-muted"><a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#skin_desktop-guide"><i class="fa fa-question-circle fa-fw"></i></a></small></label> 
        <div class="col-sm-10">
  		   <div class="row">
  		   	 <div class="col-sm-5">
	  		    <select name="skin_desktop" class="form-control">
					<option value="">&nbsp;+ 선택하세요</option>
					<option value="">--------------------------------</option>
					<?php $tdir = $g['path_module'].$module.'/theme/'?>
					<?php $dirs = opendir($tdir)?>
					<?php while(false !== ($skin = readdir($dirs))):?>
					<?php if($skin=='.' || $skin == '..' || is_file($tdir.$skin))continue?>
					<option value="<?php echo $skin?>" title="<?php echo $skin?>"<?php if($d['chatbot']['skin_desktop']==$skin):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($tdir.$skin)?>(<?php echo $skin?>)</option>
					<?php endwhile?>
					<?php closedir($dirs)?>
				</select>						
			    </div> <!-- .col-sm-3  -->
			</div> <!-- .row  -->
			<p class="help-block collapse" id="skin_desktop-guide">
				<small>
				지정된 대표테마는 챗봇 생성시 별도의 테마지정없이 자동으로 적용됩니다.<br />
				가장 많이 사용하는 테마를 지정해 주세요.
			   </small>
			</p>
		</div> <!-- .col-sm-10  -->
	</div> <!-- .form-group  -->
	<div class="form-group">
  	  <label class="col-sm-2 control-label">챗봇 테마(M) <small class="text-muted"><a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#skin_mobile-guide"><i class="fa fa-question-circle fa-fw"></i></a></small></label>
     <div class="col-sm-10">
  		   <div class="row">
  		   	 <div class="col-sm-5">
	  		    <select name="skin_mobile" class="form-control">
					<option value="">&nbsp;+ 모바일 테마 사용안함</option>
					<option value="">--------------------------------</option>
					<?php $tdir = $g['path_module'].$module.'/theme/'?>
					<?php $dirs = opendir($tdir)?>
					<?php while(false !== ($skin = readdir($dirs))):?>
					<?php if($skin=='.' || $skin == '..' || is_file($tdir.$skin))continue?>
					<option value="<?php echo $skin?>" title="<?php echo $skin?>"<?php if($d['chatbot']['skin_mobile']==$skin):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($tdir.$skin)?>(<?php echo $skin?>)</option>
					<?php endwhile?>
					<?php closedir($dirs)?>
				</select>
			    </div> <!-- .col-sm-3  -->
			</div> <!-- .row  -->
			<p class="help-block collapse" id="skin_mobile-guide">
				 <small>선택하지 않으면 데스크탑 대표테마로 설정됩니다.</small>
			</p>
		</div> <!-- .col-sm-10  -->
	</div> <!-- .form-group  --> 
    <div class="form-group">
        <label class="col-sm-2 control-label">업종</label>
	    <div class="col-sm-10">
            <p>
			   	<input type="text" name="upjong" class="form-control" value="<?php echo $d['chatbot']['upjong']?>" />
 			</p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">캠퍼스</label>
	    <div class="col-sm-10">
            <p>
			   	<input type="text" name="campus" class="form-control" value="<?php echo $d['chatbot']['campus']?>" />
 			</p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">인사</label>
	    <div class="col-sm-10">
            <p>
			   	<textarea name="helloword" rows="5" class="form-control"><?php echo $d['chatbot']['helloword']?></textarea>
 			</p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">유명인</label>
	    <div class="col-sm-10">
            <p>
			   	<textarea name="starword" rows="5" class="form-control"><?php echo $d['chatbot']['starword']?></textarea>
 			</p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">욕설</label>
	    <div class="col-sm-10">
            <p>
			   	<textarea name="badword" rows="5" class="form-control"><?php echo $d['chatbot']['badword']?></textarea>
 			</p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">금기어</label>
	    <div class="col-sm-10">
            <p>
			   	<textarea name="tabooword" rows="5" class="form-control"><?php echo $d['chatbot']['tabooword']?></textarea>
 			</p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">검색요청</label>
	    <div class="col-sm-10">
            <p>
			   	<textarea name="searchword" rows="5" class="form-control"><?php echo $d['chatbot']['searchword']?></textarea>
 			</p>
        </div>
    </div>
   <!--  <div class="form-group">
        <label class="col-sm-2 control-label">추천요청</label>
	    <div class="col-sm-10">
            <p>
			   	<textarea name="recommendword" rows="5" class="form-control"><?php echo $d['chatbot']['recommendword']?></textarea>
 			</p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">트랜드요청</label>
	    <div class="col-sm-10">
            <p>
			   	<textarea name="trendword" rows="5" class="form-control"><?php echo $d['chatbot']['trendword']?></textarea>
 			</p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">감정 요청</label>
	    <div class="col-sm-10">
            <p>
			   	<textarea name="feelingword" rows="5" class="form-control"><?php echo $d['chatbot']['feelingword']?></textarea>
 			</p>
        </div>
    </div> -->

    <div class="form-group">
		<div class="col-md-offset-2 col-md-9">
			<button type="submit" class="btn btn-primary btn-lg">저장하기</button>
		</div>
	</div>
</form>
<script type="text/javascript">
//<![CDATA[
function saveCheck(f)
{
	if (f.skin_desktop.value == '')
	{
		alert('대표테마를 선택해 주세요.       ');
		f.skin_desktop.focus();
		return false;
	}
	// if (f.skin_mobile.value == '')
	// {
	// 	alert('모바일테마를 선택해 주세요.       ');
	// 	f.skin_mobile.focus();
	// 	return false;
	// }
	  if (confirm('정말로 실행하시겠습니까?         '))
		{
			getIframeForAction(f);
			f.submit();
		}
}
//]]>
</script>
