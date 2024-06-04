<?php 
     include_once $g['path_module'].$module.'/var/var.php';
     $snsSettingsArray = array(
	'n' => array('네이버','naver','https://nid.naver.com/devcenter/register.nhn','Client ID','Client Secret'),
	'k' => array('카카오톡','kakao','https://developers.kakao.com/apps','REST API 키',''),
	'f' => array('페이스북','facebook','https://developers.facebook.com/','API Key','Secret Code'),
	'g' => array('구글','google','https://console.developers.google.com/apis/library','Client ID','Client Secret'),
	't' => array('트위터','twitter','http://dev.twitter.com/apps','Consumer Key','Consumer Secret'),
	//'i' => array('인스타그램','https://www.instagram.com/developer/','instagram'),
    );
?>
<form class="form-horizontal" role="form" name="procForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return saveCheck(this);">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $module?>" />
	<input type="hidden" name="a" value="config" />
	<div class="page-header">
		소셜연동 설정
	</div>
	<div class="alert alert-warning">
		소셜네트워크 연동을 위해서는 각각의 SNS의 APP등록을 하셔야 합니다.<br />
		APP 등록을 하면 컨슈머키와 같은 특정 인증키를 받게되며 그 값을 등록해 주시면 됩니다.<br />
		인증키를 등록한 후에는 반드시 각 SNS APP등록페이지에서 콜백주소 및 기타 설정을 해 주세요. <br/>
		<strong>이 모듈은 서버에 PHP CURL 모듈이 설치되어 있어야 사용가능합니다.</strong>
	</div>
  	<div class="form-group">
		<label class="col-sm-2 control-label">사용할 SNS</label>
		<div class="col-sm-10 shift">
			<?php foreach($snsSettingsArray as $key => $val):?>
			<div class="checkbox-inline">
			     <label>
			            <input type="checkbox" name="use_<?php echo $key?>" id="chk_<?php echo $key?>" value="1"<?php if($d[$module]['use_'.$key]):?> checked="checked"<?php endif?> onclick="chkSNScheck(this);" />
			            <img src="<?php echo $g['url_module_img']?>/sns_<?php echo $key?>0.gif" alt="" /> <label for="chk_<?php echo $key?>"><?php echo $val[0]?></label>
		          </label>
		     </div>
		     <?php endforeach?>

		</div>
	</div>
	
	<!-- 트위터 -->
	<?php foreach ($snsSettingsArray as $key => $val):?>
	<div id="snsdiv_<?php echo $key?>" class="panel <?php if(!$d[$module]['use_'.$key]):?>hide<?php endif?>">
	   <div class="form-group">
			<label class="col-sm-2 control-label"><img src="<?php echo $g['url_module_img']?>/sns_<?php echo $key?>0.gif" alt="" /> <?php echo $val[3]?></label>
			<div class="col-sm-10">
				<input type="text" name="key_<?php echo $key?>" value="<?php echo $d[$module]['key_'.$key]?>" class="form-control" />
			</div>
		</div>
		<?php if($val[1]!=='kakao'):?>
		<div class="form-group">
			<label class="col-sm-2 control-label"><img src="<?php echo $g['url_module_img']?>/sns_<?php echo $key?>0.gif" alt="" /> <?php echo $val[4]?></label>
			<div class="col-sm-10">
				<input type="text" name="secret_<?php echo $key?>" value="<?php echo $d[$module]['secret_'.$key]?>" class="form-control" />
			</div>
		</div>
	      <?php endif?>
		<div class="form-group">
			<label class="col-sm-2 control-label"><img src="<?php echo $g['url_module_img']?>/sns_<?php echo $key?>0.gif" alt="" /> Callback Url</label>
			<div class="col-sm-10">
				<?php echo $g['url_root'].'/?r='.$r.'&m='.$module.'&a=slogin&sloginReturn='.$val[1]?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label"><img src="<?php echo $g['url_module_img']?>/sns_<?php echo $key?>0.gif" alt="" /> APP 등록페이지</label>
			<div class="col-sm-10">
				<a href="<?php echo $val[2]?>" target="_blank"><?php echo $val[2]?></a>
			</div>
		</div>
	</div>
     <?php endforeach?>

	
    <div class="form-group">
			<div class="col-md-offset-2 col-md-9">
				<button type="submit" class="btn btn-primary btn-lg">저장하기</button>
			</div>
	</div>
</form>
<script type="text/javascript">
//<![CDATA[
function chkSNScheck(obj)
{
	if (obj.checked == true)
	{
		getId(obj.name.replace('use_','snsdiv_')).style.display = 'block';
	}
	else {
		getId(obj.name.replace('use_','snsdiv_')).style.display = 'none';
	}
}
function saveCheck(f)
{
	return confirm('정말로 실행하시겠습니까?         ');
}
//]]>
</script>
