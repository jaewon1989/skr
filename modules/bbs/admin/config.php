<?php
include_once $g['path_module'].$module.'/var/var.php';
?>
<form class="form-horizontal rb-form" role="form" name="procForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return saveCheck(this);">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $module?>" />
	<input type="hidden" name="a" value="config" />
	<div class="page-header">
		<h4>게시판 기초환경</h4>
	</div>
   <div class="form-group">
  	  <label class="col-sm-2 control-label">대표테마 <small class="text-muted"><a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#skin_main-guide"><i class="fa fa-question-circle fa-fw"></i></a></small></label> 
     <div class="col-sm-10">
  		   <div class="row">
  		   	 <div class="col-sm-5">
			  		    <select name="skin_main" class="form-control">
							<option value="">&nbsp;+ 선택하세요</option>
							<option value="">--------------------------------</option>
							<?php $tdir = $g['path_module'].$module.'/theme/_pc/'?>
							<?php $dirs = opendir($tdir)?>
							<?php while(false !== ($skin = readdir($dirs))):?>
							<?php if($skin=='.' || $skin == '..' || is_file($tdir.$skin))continue?>
							<option value="_pc/<?php echo $skin?>" title="<?php echo $skin?>"<?php if($d['bbs']['skin_main']=='_pc/'.$skin):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($tdir.$skin)?>(<?php echo $skin?>)</option>
							<?php endwhile?>
							<?php closedir($dirs)?>
						</select>						
			    </div> <!-- .col-sm-3  -->
			</div> <!-- .row  -->
			<p class="help-block collapse" id="skin_main-guide">
				<small>
				지정된 대표테마는 게시판설정시 별도의 테마지정없이 자동으로 적용됩니다.<br />
				가장 많이 사용하는 테마를 지정해 주세요.
			   </small>
			</p>
		</div> <!-- .col-sm-10  -->
	</div> <!-- .form-group  -->
	<div class="form-group">
  	  <label class="col-sm-2 control-label">모바일 테마 <small class="text-muted"><a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#skin_mobile-guide"><i class="fa fa-question-circle fa-fw"></i></a></small></label>
     <div class="col-sm-10">
  		   <div class="row">
  		   	 <div class="col-sm-5">
			  		    <select name="skin_mobile" class="form-control">
							<option value="">&nbsp;+ 모바일 테마 사용안함</option>
							<option value="">--------------------------------</option>
							<?php $tdir = $g['path_module'].$module.'/theme/_mobile/'?>
							<?php $dirs = opendir($tdir)?>
							<?php while(false !== ($skin = readdir($dirs))):?>
							<?php if($skin=='.' || $skin == '..' || is_file($tdir.$skin))continue?>
							<option value="_mobile/<?php echo $skin?>" title="<?php echo $skin?>"<?php if($d['bbs']['skin_mobile']=='_mobile/'.$skin):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($tdir.$skin)?>(<?php echo $skin?>)</option>
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
  	  <label class="col-sm-2 control-label">통합보드테마 <small class="text-muted"><a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#skin_total-guide"><i class="fa fa-question-circle fa-fw"></i></a></small></label>
     <div class="col-sm-10">
  		   <div class="row">
  		   	 <div class="col-sm-5">
			  		    <select name="skin_total" class="form-control">
							<option value="">&nbsp;+ 통합보드 사용안함</option>
							<option value="">--------------------------------</option>
							<?php $tdir = $g['path_module'].$module.'/theme/_pc/'?>
							<?php $dirs = opendir($tdir)?>
							<?php while(false !== ($skin = readdir($dirs))):?>
							<?php if($skin=='.' || $skin == '..' || is_file($tdir.$skin))continue?>
							<option value="_pc/<?php echo $skin?>" title="<?php echo $skin?>"<?php if($d['bbs']['skin_main']=='_pc/'.$skin):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($tdir.$skin)?>(<?php echo $skin?>)</option>
							<?php endwhile?>
							<?php closedir($dirs)?>
						</select>
			    </div> <!-- .col-sm-3  -->
			</div> <!-- .row  -->
			<p class="help-block collapse" id="skin_total-guide">
				<small>
				통합보드란 모든 게시판의 전체 게시물을 하나의 게시판으로 출력해 주는 서비스입니다.<br />
				사용하시려면 통합보드용 테마를 지정해 주세요.<br />
				통합보드의 호출은 <code><a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $module?>" target="_blank"><?php echo $g['r']?>/?m=<?php echo $module?></a></code> 입니다.
			   </small>
			</p>
		</div> <!-- .col-sm-10  -->
	 </div> <!-- .form-group  -->
	 <div class="form-group">
			<label class="col-sm-2 control-label">RSS 발행</label>
			<div class="col-sm-10">
				<div class="checkbox">
					<label>
						<input  type="checkbox" name="rss" value="1"  <?php if($d['bbs']['rss']):?> checked<?php endif?>  class="form-control">
						<i></i>RSS발행을 허용합니다.(개별게시판별 RSS발행은 개별게시판 설정을 따름)				
					</label>
				</div>
			</div>
	 </div>
	 <div class="form-group">
			<label class="col-sm-2 control-label">게시물 출력</label>
			<div class="col-sm-10">
				<div class="row">
					<div class="col-sm-3">
						<div class="input-group">
							<input type="text" name="recnum" value="<?php echo $d['bbs']['recnum']?$d['bbs']['recnum']:20?>" class="form-control">
							<span class="input-group-addon">개</span>
						</div>
					</div>
				</div>
				<p class="form-control-static text-muted">
				    <small>한페이지에 출력할 게시물의 수</small>
				</p>
			</div>
	 </div>
	 <div class="form-group">
			<label class="col-sm-2 control-label">제목 끊기</label>
			<div class="col-sm-10">
				<div class="row">
					<div class="col-sm-3">
						<div class="input-group">
							<input type="text" name="sbjcut" value="<?php echo $d['bbs']['sbjcut']?$d['bbs']['sbjcut']:40?>" class="form-control">
							<span class="input-group-addon">자</span>
						</div>
					</div>
				</div>
				<p class="form-control-static text-muted">
				    <small>제목이 길 경우 보여줄 글자 수 </small>
				</p>
			</div>
	 </div>
    <div class="form-group">
			<label class="col-sm-2 control-label">새글 유지시간</label>
			<div class="col-sm-10">
				<div class="row">
					<div class="col-sm-3">
						<div class="input-group">
							<input type="text" name="newtime" value="<?php echo $d['bbs']['newtime']?$d['bbs']['newtime']:24?>" class="form-control">
							<span class="input-group-addon">시간</span>
						</div>
					</div>
				</div>
				<p class="form-control-static text-muted">
				   <small> 새글로 인식되는 시간 </small>
				</p>
			</div>
	 </div>  
	 <div class="form-group">
			<label class="col-sm-2 control-label">답글 인식문자</label>
			<div class="col-sm-10">
				<div class="row">
					<div class="col-sm-3">
						<div class="input-group">
							<input type="text" name="restr" value="<?php echo $d['bbs']['restr']?>" class="form-control">
		 				</div>
					</div>
				</div>
			</div>
	 </div>
	 <div class="form-group">
			<label class="col-sm-2 control-label">삭제 제한</label>
			<div class="col-sm-10">
				<div class="checkbox">
					<label>
						<input  type="checkbox" name="replydel" value="1"  <?php if($d['bbs']['replydel']):?> checked<?php endif?>  class="form-control">
						<i></i>답변글이 있는 원본글의 삭제를 제한합니다.			
					</label>
				</div>
				<div class="checkbox">
					<label>
						<input  type="checkbox" name="commentdel" value="1"  <?php if($d['bbs']['commentdel']):?> checked<?php endif?>  class="form-control">
						<i></i>댓글이 있는 원본글의 삭제를 제한합니다.		
					</label>
				</div>
			</div>
	 </div>
	 <div class="form-group">
		 <label class="col-sm-2 control-label">불량글 처리</label>
		  <div class="col-sm-10">
				<div class="row">
					<div class="col-sm-3">
						<div class="checkbox">
								<label>
									<input  type="checkbox" name="singo_del" value="1" <?php if($d['bbs']['singo_del']):?> checked<?php endif?>  class="form-control">
									<i></i>신고건 수가   	
								</label>
						 </div>
					</div>
					<div class="col-sm-4">
						<div class="input-group">
							<input type="text" name="singo_del_num" value="<?php echo $d['bbs']['singo_del_num']?>" class="form-control">
							<span class="input-group-addon">건 이상인 경우</span>
						</div>
					</div>
					<div class="col-sm-3">
						<select name="singo_del_act" class="form-control">
								<option value="1"<?php if($d['bbs']['singo_del_act']==1):?> selected="selected"<?php endif?>>자동삭제</option>
								<option value="2"<?php if($d['bbs']['singo_del_act']==2):?> selected="selected"<?php endif?>>비밀처리</option>
						</select>
					</div>	
				</div> <!-- .row -->
		</div> <!-- .col-sm-10 -->
	</div> <!-- .form-group -->
   <div class="form-group">
       <label class="col-sm-2 control-label">제한단어</label>
	     <div class="col-sm-10">
             <p>
						<textarea name="badword" rows="5" class="form-control" onfocus="this.style.color='#000000';" onblur="this.style.color='#ffffff';" style="color:#fff" ><?php echo $d['bbs']['badword']?></textarea>
 				 </p>
          </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">제한단어 처리</label>
	  	  <div class="col-sm-10">
	  	  	   <p>
	  	  	   	  <label>
				        		<input type="radio" name="badword_action" value="0" <?php if($d['bbs']['badword_action']==0):?> checked<?php endif?> />
					         제한단어 체크하지 않음
					   </label>
              </p>
              <p> 	
               	 <label>
					     <input type="radio" name="badword_action" value="1"<?php if($d['bbs']['badword_action']==1):?> checked<?php endif?> />
                      등록을 차단함
                    </label> 
                </p>
                <p>
				   	 <label>
					      <input type="radio" name="badword_action" value="2"<?php if($d['bbs']['badword_action']==2):?> checked<?php endif?> />
					      제한단어를 다음의 문자로 치환하여 등록함
				       </label>
				        <input type="text" name="badword_escape" value="<?php echo $d['bbs']['badword_escape']?>" maxlength="1"   />
                </p>
		   </div><!-- .col-sm-10 -->
		 </div>
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
	if (f.skin_main.value == '')
	{
		alert('대표테마를 선택해 주세요.       ');
		f.skin_main.focus();
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
