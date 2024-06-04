<?php
include $g['path_module'].$module.'/includes/tree.func.php';

if($cat)
{
	$CINFO = getUidData($table[$module.'entity'],$cat);
	$ctarr = getMenuCodeToPathBlog($table[$module.'entity'],$cat,0);
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
$is_fcategory =  $CINFO['uid'] && $vtype != 'sub';
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
#entityVal-list .input-group {
	margin-bottom: 10px;
}
</style>
<div id="catebody" class="row">
	<div id="category" class="col-sm-3 col-md-2 col-lg-2">
		<div class="panel-group" id="accordion">
			<div class="panel panel-default">
				<div class="panel-heading rb-icon">
					<div class="icon">
						<i class="fa fa-linkedin fa-2x"></i>
					</div>
					<h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapmetane">엔터티 이름</a></h4>
				</div>
				<div class="panel-collapse collapse in" id="collapmetane">
						
					<div class="panel-body">
						<div style="min-height:300px;">
							<link href="<?php echo $g['s']?>/_core/css/tree.css" rel="stylesheet">
							<?php $_treeOptions=array('table'=>$table[$module.'entity'],'dispNum'=>true,'dispHidden'=>false,'dispCheckbox'=>false,'allOpen'=>false,'bookmark'=>'blog-category-info')?>
							<?php $_treeOptions['link'] = $g['adm_href'].'&amp;&amp;cat='?>
							<?php $_treeOptions['add_where'] = "type='S'"?>

							<?php echo getTreeEntity($_treeOptions,$code,0,0,'')?>
						</div>
					</div>
				</div>
			</div>
			
			<?php if($g['device']):?><a name="site-menu-info"></a><?php endif?>

		</div>
	</div>
	<div id="catinfo" class="col-sm-9 col-md-10 col-lg-10">
		<form class="form-horizontal rb-form" name="procForm" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
		<input type="hidden" name="r" value="<?php echo $r?>">
		<input type="hidden" name="m" value="<?php echo $module?>">
		<input type="hidden" name="a" value="_admin/regis_entity">
		<input type="hidden" name="cat" value="<?php echo $CINFO['uid']?>" />
		<input type="hidden" name="vtype" value="<?php echo $vtype?>" />
		<input type="hidden" name="depth" value="<?php echo intval($CINFO['depth'])?>" />
		<input type="hidden" name="parent" value="<?php echo intval($CINFO['uid'])?>" />
		<div class="page-header">
			<h4>
				<?php if($is_regismode):?>
				    <?php if($vtype == 'sub'):?>서브 엔터티 만들기<?php else:?>엔터티 만들기<?php endif?>
				<?php else:?>
				  엔터티 등록정보
				  <div class="pull-right rb-top-btnbox hidden-xs">
					<a href="<?php echo $g['adm_href']?>" class="btn btn-default"><i class="fa fa-plus"></i> 엔터티 추가하기</a>
				  </div>
				<?php endif?>
			</h4>
		</div>
		<div class="form-group rb-outside">
			<label class="col-md-2 control-label">엔터티 명칭 <?php if($is_regismode):?><a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#guide_name"><i class="fa fa-question-circle fa-fw"></i><?php endif?></a></label>
			<div class="col-md-10">
				<?php if($is_fcategory):?>
				<div class="input-group">
					<span class="input-group-addon">@</span>
					<input class="form-control" placeholder="" type="text" name="name" value="<?php echo $CINFO['name']?>">
					<span class="input-group-btn">
						<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $module?>&amp;a=_admin/delete_entity&amp;cat=<?php echo $cat?>&amp;parent=<?php echo $delparent?>" onclick="return hrefCheck(this,true,'정말로 삭제하시겠습니까?');" class="btn btn-default" data-tooltip="tooltip" title="엔터티 삭제">
							<i class="fa fa-trash-o fa-lg"></i>
						</a>
					</span>
				</div>
				<?php else:?>
				<input class="form-control" placeholder="진료과목(내과|외과|방사선과),진료센터(암센터|인공와우센터|파킨슨센터)" type="text" name="name" value="<?php echo $CINFO['name']?>"<?php if(!$g['device']):?> autofocus<?php endif?>>
				<div id="guide_name" class="collapse help-block alert alert-info">
				
						<code>@</code>엔터티를 콤마(,)로 구분해서 등록해주세요.<br />
						엔터티 항목까지 함께 등록하려면 괄로()안에 '|'로 구분해서 등록해주세요.<br />
						보기) 진료과목(내과|외과|방사선과),진료센터(암센터|인공와우센터|파킨슨센터)<br />
				
				</div>
				<?php endif?>
			</div>
		</div>
		<?php if(!$is_regismode):?>
		<!-- <div class="form-group rb-outside">
			<label class="col-md-2 control-label">치환 문장<a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#guide_sentence"><i class="fa fa-question-circle fa-fw"></i></a></label>
			<div class="col-md-10">
				<textarea class="form-control" name="rp_sentence" rows="5"<?php if(!$g['device']):?> autofocus<?php endif?>><?php echo $CINFO['rp_sentence']?></textarea>
				<div id="guide_sentence" class="collapse help-block alert alert-info">
				
						엔터티를 의미하는 다른 문장 혹은 단어를 콤마(,)로 구분해서 등록해주세요.<br />
						보기) 어떤 진료 가능한가요?,진료과목이 궁금해요,내과 있나요?<br />
			
				</div>
			</div>
		</div> -->
		<div class="form-group rb-outside">
			<label class="col-md-2 control-label">엔터티 항목<a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#guide_list"><i class="fa fa-question-circle fa-fw"></i></a></label>
			<div class="col-md-10" id="entityVal-list">
				<?php $ECD = getDbArray($table[$module.'entityVal'],'entity='.$CINFO['uid'],'*','gid','asc','',1);?>
				<?php while($E = db_fetch_array($ECD)):?>
                    <div class="input-group" style="width:100%">
                    	<div class="input-group-btn" style="width:13%">
                    		<input type="hidden" name="entityVal_uid[]" value="<?php echo $E['uid']?>" />
							<input class="form-control" type="text" name="entityVal_name[]" placeholder="명칭"  value="<?php echo $E['name']?>">
						</div>	
						<div class="input-group-btn" style="width:77%">
				            <input class="form-control" type="text" name="entityVal_synonyms[]" placeholder="유사어를 콤마(,)로 구분해서 입력해주세요" value="<?php echo $E['synonyms']?>" >
						</div>
						<span class="input-group-btn" style="width:5%;text-align:center">
							<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $module?>&amp;a=_admin/delete_entityVal&amp;uid=<?php echo $E['uid']?>" onclick="return hrefCheck(this,true,'정말로 삭제하시겠습니까?');" class="btn btn-default" data-tooltip="tooltip" title="엔터티 삭제">
								<i class="fa fa-trash-o fa-lg"></i>
							</a>
						</span>
					</div>
			    <?php endwhile?>

			
				<div class="input-group" style="width:100%">
					<div class="input-group-btn" style="width:95%">
			            <input class="form-control" data-role="input-regisEntityVal" placeholder="엔터티 항목을 콤마(,)로 구분해서 입력해주세요" type="text">
					</div>
					<div class="input-group-btn" style="width:5%">
			            <button class="btn btn-primary" style="width:100%;" data-role="btn-regisEntityVal">추가</button>
					</div>
				</div>
				<div id="guide_list" class="collapse help-block alert alert-info">
				
						엔터티의 실제 항목을 등록해주세요.<br />
						보기) <code>'@진료과목'</code> 엔터티에 해당하는 항목은 <code>내과,외과,방사선과</code>... 등이 됩니다.<br />
				
				</div>
			</div>
		</div>
	   	<div class="form-group rb-outside">
			<label class="col-md-2 control-label">엔터티 옵션 <a data-toggle="collapse" data-tooltip="tooltip" title="도움말" href="#guide_option"><i class="fa fa-question-circle fa-fw"></i></a></label>
			<div class="col-md-10">
				<div class="row">			
						<div class="col-md-4">
							<div class="checkbox">
								<label>
									<input  type="checkbox" name="hidden" value="1"<?php if($CINFO['hidden']):?> checked<?php endif?>   class="form-control">
									<i></i>엔터티 숨김		
								</label>
							</div>
						</div>
			    </div>
				<div id="guide_option" class="collapse help-block alert alert-info">
			
					    <strong>엔터티 숨김 : </strong>엔터티  출력하지 않습니다.(링크접근가능)<br />
				
				</div>						
			 </div>
	    </div>
	    <?php endif?>
	
	    <div class="form-group">
		   <div class="col-md-12 col-lg-12">
			   <button class="btn btn-primary btn-block btn-lg" id="rb-submit-button" type="submit"><i class="fa fa-check fa-lg"></i> <?php echo $is_fcategory?'엔터티 및 엔터티 항목 속성 변경':'신규 엔터티 등록'?></button>
		   </div>
	    </div>
	</form>			
	</div>
</div>
<form class="form-horizontal rb-form" name="regisEntityValForm" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data">
	<input type="hidden" name="r" value="<?php echo $r?>">
	<input type="hidden" name="m" value="<?php echo $module?>">
	<input type="hidden" name="a" value="_admin/regis_entityVal">
	<input type="hidden" name="entity" value="<?php echo $CINFO['uid']?>" />
	<input type="hidden" name="vtype" value="<?php echo $vtype?>" />
	<input type="hidden" name="depth" value="<?php echo intval($CINFO['depth'])?>" />
	<input type="hidden" name="parent" value="<?php echo intval($CINFO['uid'])?>" />
	<input type="hidden" name="entityVal" />

</form>	
<script>
function saveCheck(f)
{
	if (f.name.value == '')
	{
		alert('엔터티 명칭을 입력해 주세요.      ');
		f.name.focus();
		return false;
	}

	if(confirm('정말로 실행하시겠습니까?         '))
	 {
		getIframeForAction(f);
		f.submit();
	}
}

// 엔터티 항목 등록  
$('[data-role="btn-regisEntityVal"]').on('click',function(e){
    e.preventDefault();
    var entityVal = $('[data-role="input-regisEntityVal"]').val();
    var f = document.regisEntityValForm;
    f.entityVal.value=entityVal;
    
    getIframeForAction(f);
	f.submit();

 });

</script>
