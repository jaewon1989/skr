<?php
include $g['path_module'].$module.'/includes/tree.func.php';

if($cat)
{
	$CINFO = getUidData($table[$module.'intent'],$cat);
	$ctarr = getMenuCodeToPathBlog($table[$module.'intent'],$cat,0);
	$ctnum = count($ctarr);
	$CINFO['code'] = '';

	for ($i = 0; $i < $ctnum; $i++)
	{
		$CXA[] = $ctarr[$i]['uid'];
		$CINFO['code'] .= $ctarr[$i]['id'].($i < $ctnum-1 ? '/' : '');
		$_code .= $ctarr[$i]['uid'].($i < $ctnum-1 ? '/' : '');
	}
	$code = $code ? $code : $_code;

	for ($i = 0; $i < $ctnum; $i++) $CXA[] = $ctarr[$i]['uid'];
}

$catcode = '';
$is_regismod =  $CINFO['uid'] && $vtype != 'sub';
$is_regismode = !$CINFO['uid'] || $vtype == 'sub';
if ($is_regismode)
{
	$CINFO['name']	   = '';
	$CINFO['rp_sentence']  = '';
	$CINFO['mobile']   = '';
	$CINFO['hidden']   = '';
	$CINFO['metaurl']   = '';
	$CINFO['metause']   = '';
	$CINFO['recnum'] = 20;
}
?>
<style>
.checkbox, .checkbox-inline, .radio-inline {margin: 0px !important ;}
.radio-inline input {position:relative !important;left:0 !important;}
</style>
<div id="catebody" class="row">
	<div id="category" class="col-sm-5 col-md-4 col-lg-4">
		<div class="panel-group" id="accordion">
			<div class="panel panel-default">
				<div class="panel-heading rb-icon">
					<div class="icon">
						<i class="fa fa-linkedin fa-2x"></i>
					</div>
					<h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapmetane">인텐트 내역</a></h4>
				</div>
				<div class="panel-collapse collapse in" id="collapmetane">
						
					<div class="panel-body">
						<div style="min-height:300px;">
							<link href="<?php echo $g['s']?>/_core/css/tree.css" rel="stylesheet">
							<?php $_treeOptions=array('table'=>$table[$module.'intent'],'dispNum'=>true,'dispHidden'=>false,'dispCheckbox'=>false,'allOpen'=>false,'bookmark'=>'blog-category-info')?>
							<?php $_treeOptions['link'] = $g['adm_href'].'&amp;&amp;cat='?>
							<?php $_treeOptions['add_where'] = "type='S'"?>

							<?php echo getTreeIntent($_treeOptions,$code,0,0,'')?>
						</div>
					</div>
				</div>
			</div>
			
			<?php if($g['device']):?><a name="site-menu-info"></a><?php endif?>

		</div>
	</div>
	<div id="catinfo" class="col-sm-7 col-md-8 col-lg-8">
		<form class="form-horizontal rb-form" name="procForm" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
		<input type="hidden" name="r" value="<?php echo $r?>">
		<input type="hidden" name="m" value="<?php echo $module?>">
		<input type="hidden" name="a" value="_admin/regis_intent">
		<input type="hidden" name="cat" value="<?php echo $CINFO['uid']?>" />
		<input type="hidden" name="vtype" value="<?php echo $vtype?>" />
		<input type="hidden" name="depth" value="<?php echo intval($CINFO['depth'])?>" />
		<input type="hidden" name="parent" value="<?php echo intval($CINFO['uid'])?>" />
		<div class="page-header">
			<h4>
				<?php if($is_regismode):?>
				    <?php if($vtype == 'sub'):?>서브 인텐트 만들기<?php else:?>최상위 인텐트 만들기<?php endif?>
				<?php else:?>
				  인텐트 등록정보
				  <div class="pull-right rb-top-btnbox hidden-xs">
					<a href="<?php echo $g['adm_href']?>" class="btn btn-default"><i class="fa fa-plus"></i> 인텐트 추가하기</a>
				  </div>
				<?php endif?>
			</h4>
		</div>
		<div class="form-group rb-outside">
			<label class="col-md-3 control-label">인텐트 명칭  <?php if(!$is_regismod):?><a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#guide_name"><i class="fa fa-question-circle fa-fw"></i></a><?php endif?></label>
			<div class="col-md-9">
				<?php if($is_regismod):?>
				<div class="input-group">
					<span class="input-group-addon">#</span>
					<input class="form-control" placeholder="" type="text" name="name" value="<?php echo $CINFO['name']?>"<?php if(!$cat && !$g['device']):?> autofocus<?php endif?>>
					<span class="input-group-btn">
					<!-- 	<a href="<?php echo $g['adm_href']?>&amp;cat=<?php echo $cat?>&amp;vtype=sub" class="btn btn-default" data-tooltip="tooltip" title="서브인텐트 등록">
							<i class="fa fa-share fa-rotate-90 fa-lg"></i>
						</a> -->
						<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $module?>&amp;a=_admin/delete_intent&amp;cat=<?php echo $cat?>&amp;parent=<?php echo $delparent?>" onclick="return hrefCheck(this,true,'정말로 삭제하시겠습니까?');" class="btn btn-default" data-tooltip="tooltip" title="인텐트 삭제">
							<i class="fa fa-trash-o fa-lg"></i>
						</a>
					</span>
				</div>
				<?php else:?>
				<input class="form-control" placeholder="" type="text" name="name" value="<?php echo $CINFO['name']?>"<?php if(!$g['device']):?> autofocus<?php endif?>>
				<div id="guide_name" class="collapse help-block alert alert-warning">
					<small>
						복수의 인텐트를 한번에 등록하시려면 인텐트명을 콤마(,)로 구분해 주세요.<br />
						보기)회사소개,커뮤니티,고객센터<br />
					</small>
				</div>
				<?php endif?>
			</div>
		</div>
		<div class="form-group rb-outside">
			<label class="col-md-3 control-label">치환 문장<?php if(!$is_regismod):?><a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#guide_name"><i class="fa fa-question-circle fa-fw"></i></a><?php endif?></label>
			<div class="col-md-9">
				<textarea class="form-control" name="rp_sentence" rows="10"<?php if(!$g['device']):?> autofocus<?php endif?>><?php echo $CINFO['rp_sentence']?></textarea>
				<div id="guide_name" class="collapse help-block alert alert-warning">
					<small>
						복수의 문장을 한번에 등록하시려면 콤마(,)로 구분해 주세요.<br />
					</small>
				</div>
			</div>
		</div>
	   	<div class="form-group rb-outside">
			<label class="col-md-3 control-label">인텐트 옵션 <a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#guide_option"><i class="fa fa-question-circle fa-fw"></i></a></label>
			<div class="col-md-9">
				<div class="row">
			
						<div class="col-md-4">
							<div class="checkbox">
								<label>
									<input  type="checkbox" name="hidden" value="1"<?php if($CINFO['hidden']):?> checked<?php endif?>   class="form-control">
									<i></i>인텐트 숨김		
								</label>
							</div>
						</div>
			    </div>
				<div id="guide_option" class="collapse help-block alert alert-warning">
				   <small>
					    <strong>인텐트 숨김 : </strong>인텐트를 출력하지 않습니다.(링크접근가능)<br />
					 </small>
				</div>						
			 </div>
	    </div>
	
	    <div class="form-group">
		   <div class="col-md-12 col-lg-12">
			   <button class="btn btn-primary btn-block btn-lg" id="rb-submit-button" type="submit"><i class="fa fa-check fa-lg"></i> <?php echo $is_regismod?'인텐트 속성 변경':'신규 인텐트 등록'?></button>
		   </div>
	    </div>
	</form>			
	</div>
</div>
<script>
function saveCheck(f)
{
	if (f.name.value == '')
	{
		alert('인텐트 명칭을 입력해 주세요.      ');
		f.name.focus();
		return false;
	}

	if(confirm('정말로 실행하시겠습니까?         '))
	 {
			getIframeForAction(f);
			f.submit();
	}
}

</script>
