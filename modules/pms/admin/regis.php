<?php
include_once $g['path_module'].$module.'/var/var.php';
include_once $g['path_module'].$module.'/_main.php';

$R=array();
$upfile = '';

if($uid)
{
	$R=getUidData($table[$module.'product'],$uid);
}else{
	$lastuid= getDbCnt($table[$moduel.'product'],'max(uid)','');
	$parent_uid=$lastuid+1;
}
$blank_shot="http://placehold.it/300x400&text=Image";
$shot_img=getPic($R,'');
$shot=$R['uid']?$shot_img:$blank_shot;
?>

<div class="row">
   <div class="col-sm-4 col-lg-3">
    	<div class="panel panel-default">  <!-- 메뉴 리스트 패털 시작 -->
             		<div class="panel panel-default">
				<div class="panel-heading rb-icon">
					<div class="icon">
						<i class="fa fa-sitemap fa-2x"></i>
					</div>
					<h4 class="panel-title">
						<a class="accordion-toggle collapsed" data-parent="#accordion" data-toggle="collapse" href="#collapseTwo">카테고리 선택</a>
					</h4>
				</div>
				
				<div class="panel-collapse collapse in" id="collapseTwo">
	                <div class="panel-body">
						<div style="overflow:auto;">
							<link href="<?php echo $g['s']?>/_core/css/tree.css" rel="stylesheet">
				            <?php $_treeOptions=array('table'=>$table[$module.'category'],'uid'=>$R['uid'],'dispNum'=>false,'dispCheckbox'=>false,'allOpen'=>false)?>
                            <?php echo getTreeCategoryForWrite($_treeOptions,$code,0,0,'')?>

						</div>
					</div>
				</div>
		</div>   		
   		</div> <!-- 좌측 패널 끝 -->
   </div><!-- 좌측  내용 끝 -->	

   <!-- 우측 내용 시작 -->
   <div id="tab-content-view" class="col-sm-8 col-lg-9">
		<form name="procForm" class="form-horizontal rb-form" role="form" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
		<input type="hidden" name="r" value="<?php echo $r?>" />
		<input type="hidden" name="m" value="<?php echo $module?>" />
		<input type="hidden" name="a" value="regis_product" />
		<input type="hidden" name="saveDir" value="<?php echo $g['path_file'].$module?>/" /> 
		<input type="hidden" name="uid" value="<?php echo $R['uid']?>" />
		 <div class="form-group">
			<label class="col-sm-2 control-label">분류</label>
			<div class="col-sm-10">
				<input class="form-control" placeholder="좌측 카테고리에서 패널에서 선택 " type="text" name="category" value="<?php echo $R['category']?>">			
			
			</div>				
		 </div>
		<div class="form-group">
			<label class="col-md-2 control-label">제목</label>
			<div class="col-md-10">
			    <input type="text" class="form-control" name="name" value="<?php echo $R['name']?>">
			 </div>   
		</div>
		<div class="form-group">
			<label class="col-md-2 control-label">설명</label>
			<div class="col-md-10">
			      <textarea class="form-control" name="content" rows="5"><?php echo getContents($R['content'],'HTML')?></textarea>
			 </div>   
		</div>
		<div class="form-group">
			<label class="col-md-2 control-label">상태</label>
			<div class="col-md-10">
		           <label class="radio-inline">
		                <input type="radio" name="dispaly" value="0" <?php if(!$R['display']):?> checked="checked"<?php endif?> /><i></i>  노출
		          </label>
		           <label class="radio-inline">
		                <input type="radio" name="dispaly" value="2" <?php if($R['display']==2):?> checked="checked"<?php endif?> /><i></i> 숨김
		          </label>
			 </div>     
		</div>
		<div class="form-group">
			<label class="col-md-2 control-label">진행률</label>
			<div class="col-md-10">
				<div class="input-group">
			       <input type="text" class="form-control" name="vote" value="<?php echo $R['vote']?>">
			       <span class="input-group-addon">
			       	  %
			       </span>
			    </div>
			 </div>   
		</div>
		<div class="form-group">
			<label class="col-md-2 control-label">퍼블리싱 파일</label>
			<div class="col-md-10">
		       <input type="text" class="form-control" name="review" value="<?php echo $R['review']?>">
			   <p class="help-block">파일 URL 은 공통으로 사용한다는 전제 </p>
			 </div>   
		</div>	

         <div class="form-group">
			<label class="col-md-2 control-label">스크린샷</label>
			<div class="col-md-10" id="shot-wrap">
				<span style="display:none"><input type="file" name="shot_photo"  id="shot-inputPhoto"/></span>
				<div id="shot-preview" style="border:solid 1px #d9d9d9;text-align:center;">
			       <img src="<?php echo $shot?>" style="width:300px;height:400px;" alt="" />
				</div>
				<button type="button" class="btn btn-default btn-block" id="btn-shotPhoto">
					<i class="fa fa-upload fa-fw"></i> <span>업로드</span>
				</button>

			</div>
		</div>	
		<div class="form-group">
			<div class="col-sm-12">
				<button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fa fa-check fa-lg"></i> <?php echo $R['uid']?'가이드 수정하기':'가이드 등록하기'?></button>
			</div>
		</div>

	</form>
		
  </div> <!-- 우측내용 끝 --> 
</div> <!-- .row 전체 box --> 
<iframe name="_orderframe_" class="hide"></iframe>

<script type="text/javascript">
//<![CDATA[

$('#shot-wrap').on("click","#btn-shotPhoto", function() {
       $('#shot-inputPhoto').click();    
});

// 스샷 사진 선택시 이벤트 
$('#shot-wrap').on('change','#shot-inputPhoto',function(e){
     var files=e.target.files;
     var file=files[0];
     var reader = new FileReader();
     reader.readAsDataURL(file);
      //로드 한 후
     reader.onload = function  () {
        //로컬 이미지를 보여주기
        $("#shot-preview").find('img').attr("src",reader.result);
  
    } 
});

$('[data-role="a-cat"]').click(function(){
	$('[data-role="a-cat"]').css({"color":"#666","font-weight":"normal"});
    $(this).css({"color":"#428bca","font-weight":"bold"});
     var rcode=$(this).data('rcode');
    var name=$(this).data('name');
    $('input[name="category"]').val(rcode);
    $('input[name="name"]').val(name);
})
function saveCheck(f)
{
   	if (f.category.value == '')
	{
		alert('가이드 카테고리를 선택해주세요.     ');
		f.category.focus();
		return false;
	} 

    if (f.name.value == '')
	{
		alert('가이드 제목을 입력해 주세요.     ');
		f.name.focus();
		return false;
	}


    getIframeForAction(f);
	f.submit();
     
			
}

//]]>
</script>

