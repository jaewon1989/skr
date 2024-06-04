<?php if(!defined('__KIMS__')) exit?>
<form name="procForm" class="form-horizontal rb-form" role="form" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return saveCheck(this);">
      <input type="hidden" name="r" value="<?php echo $r?>" />
      <input type="hidden" name="m" value="<?php echo $module?>" />
      <input type="hidden" name="a" value="config_makerbrand" />
      <input type="hidden" name="type" value="<?php echo $type?>" />
      
      <div class="page-header">
			<h4> 제조사/브랜드 세트 </h4>
	 </div>

	 <div class="form-group">
		<label class="col-md-2 control-label">제조사</label>
	      <div class="col-md-9">
			
			<input class="form-control" type="text" name="maker" value="<?php echo implode('',file($g['path_module'].$module.'/var/set.maker.txt'))?>">
			<div class="help-block">
				 <small>콤마(,)로 구분해서 등록해 주세요. (보기 : 삼성,LG,애플)</small>
			</div>
		</div>				
	 </div>
	  <div class="form-group">
		<label class="col-md-2 control-label">브랜드</label>
	      <div class="col-md-9">
			<input class="form-control" placeholder="" type="text" name="brand" value="<?php echo implode('',file($g['path_module'].$module.'/var/set.brand.txt'))?>">
			<div class="help-block">
				 <small>콤마(,)로 구분해서 등록해 주세요. (보기 : 겔럭시,G폰,아이폰)</small>
			</div>
		</div>				
	 </div>
	 <div class="form-group">
		<div class="col-sm-12">
			<button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fa fa-check fa-lg"></i> 저장하기 </button>
		</div>
	</div>
</form>
<script type="text/javascript">
//<![CDATA[
function saveCheck(f)
{
	return confirm('정말로 실행하시겠습니까?         ');
}
//]]>
</script>	