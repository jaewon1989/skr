<?php
include $g['path_module'].$module.'/includes/tree.func.php';
include $g['path_module'].$module.'/includes/base.class.php';
include $g['path_module'].$module.'/includes/ad.class.php';
$Ad = new Ad();
if (!$_SESSION['upsescode']) $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
$sescode = $_SESSION['upsescode'];
$_SESSION['wcode']=$date['totime'];

$sort	= $sort ? $sort : 'memberuid';
$orderby= $orderby ? $orderby : 'desc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 10;

$member_q ='auth=1';
// 키원드 검색 추가 
if ($keyw)
{
	$member_q .= " and (id like '%".$keyw."%' or name like '%".$keyw."%')";
}

$RCD = getDbArray($table['s_mbrdata'].' left join '.$table['s_mbrid'].' on memberuid=uid',$member_q,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table['s_mbrdata'].' left join '.$table['s_mbrid'].' on memberuid=uid',$member_q);
$TPG = getTotalPage($NUM,$recnum);

if($uid)
{
	$R = getUidData($table[$module.'post'],$uid);
	$d_start = $R['d_start'];
	$d_end = $R['d_end'];
	$ad_start = substr($R['d_start'],0,4).'/'.substr($R['d_start'],4,2).'/'.substr($R['d_start'],6,2);
	$ad_end = substr($R['d_end'],0,4).'/'.substr($R['d_end'],4,2).'/'.substr($R['d_end'],6,2);
    $ad_type_print = $Ad->getAdTypePrint($R['ad_type']);
	
	// 이미지 처리 
	$photos=$Ad->getUploadPhotoData($module,$R['uid']); 
    $IMG=$photos[0]; 
    $photo_uid = $IMG['uid'];
	$R_f_img_src = $IMG['url'].$IMG['folder'].'/'.$IMG['tmpname'];
    
	// category 정보
	$cat_info = $Ad->getCatInfo($R['category']); 
	$category = $cat_info['uid'];
	$cat_name = $cat_info['name'];
	$cat_depth = $cat_info['depth'];
    
    // 회원 정보 
    $mbruid = $R['mbruid'];
    $mbr_info = $R['name'].'('.$R['id'].')';

}
$f_img_src = $R_f_img_src?$R_f_img_src:'http://placehold.it/200x150';
?>
<link rel="stylesheet" href="<?php echo $g['path_module'].'ad/admin/addAdv.css'?>">
<div class="row">
   <div class="col-sm-4 col-lg-3"> 
   	    <div class="panel panel-default">  <!-- 메뉴 리스트 패털 시작 -->
   			<div class="panel-heading rb-icon">
				<div class="icon">
					<i class="fa fa-file-text-o fa-2x"></i>
				</div>
				<h4 class="panel-title">
					회원 리스트 
					<span class="pull-right">
						<button type="button" class="btn btn-default btn-xs<?php if(!$_SESSION['sh_ad_member_search']):?> collapsed<?php endif?>" data-toggle="collapse" data-target="#panel-search" data-tooltip="tooltip" title="검색필터" onclick="sessionSetting('sh_ad_member_search','1','','1');"><i class="glyphicon glyphicon-search"></i></button>
					</span>
				</h4>
			</div>
			<div id="panel-search" class="collapse<?php if($_SESSION['sh_ad_member_search']):?> in<?php endif?>">
				<form role="form" action="<?php echo $g['s']?>/" method="get">
				<input type="hidden" name="r" value="<?php echo $r?>">
				<input type="hidden" name="m" value="<?php echo $m?>">
				<input type="hidden" name="module" value="<?php echo $module?>">
				<input type="hidden" name="front" value="addAdv">
				<input type="hidden" name="recnum" value="<?php echo $recnum?>">

					<div class="panel-heading rb-search-box">
						<div class="input-group">
							<div class="input-group-addon"><small>출력수</small></div>
							<div class="input-group-btn">
								<select class="form-control" name="recnum" onchange="this.form.submit();">
							    <option value="10"<?php if($recnum==10):?> selected<?php endif?>>10</option>
								<option value="15"<?php if($recnum==15):?> selected<?php endif?>>15</option>
								<option value="30"<?php if($recnum==30):?> selected<?php endif?>>30</option>
								<option value="60"<?php if($recnum==60):?> selected<?php endif?>>60</option>
								<option value="100"<?php if($recnum==100):?> selected<?php endif?>>100</option>
								</select>
							</div>
						</div>
					</div>
					<div class="rb-keyword-search input-group input-group-sm">
						<input type="text" name="keyw" class="form-control" value="<?php echo $keyw?>" placeholder="아이디 or 이름">
						<span class="input-group-btn">
							<button class="btn btn-primary" type="submit">검색</button>
						</span>
					</div>
				</form>
			</div>
			<div class="panel-body" style="border-top:1px solid #DEDEDE;height:250px;overflow:auto;">
				<?php if($NUM):?>
				<div class="list-group" id="addAdv-mlist">
				
					<?php while($M = db_fetch_array($RCD)):?>
					<a href="#" class="list-group-item" data-act="sel-mbr" data-uid="<?php echo $M['memberuid']?>" data-info="<?php echo $M['name']?>(<?php echo $M['id']?>)" >
			    		<?php echo $M['name']?>(<?php echo $M['id']?>)
			    	</a>
					<?php endwhile?>
				</div>
				
				<?php else:?>
				<div class="none">등록된 회원이 없습니다.</div>
				<?php endif?>
				
         </div>  
        	<div class="panel-footer rb-panel-footer">
				<ul class="pagination">
				<script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
				<?php //echo getPageLink(5,$p,$TPG,'')?>
				</ul>
			</div>
		</div> <!-- 좌측 패널 끝 -->  
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
						<div style="height:250px;overflow:auto;">
							<link href="<?php echo $g['s']?>/_core/css/tree.css" rel="stylesheet">
				            <?php $_treeOptions=array('table'=>$table[$module.'category'],'dispNum'=>false,'dispHidden'=>true,'dispCheckbox'=>false,'allOpen'=>false)?>
                            <?php echo getTreeCategoryForWrite($_treeOptions,$code,0,0,'')?>

						</div>
					</div>
				</div>
		</div>
   </div><!-- 좌측  내용 끝 -->	

   <!-- 우측 내용 시작 -->
   <div id="tab-content-view" class="col-sm-8 col-lg-9">
		<form name="procForm" class="form-horizontal rb-form" role="form" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
		<input type="hidden" name="r" value="<?php echo $r?>" />
		<input type="hidden" name="m" value="<?php echo $module?>" />
		<input type="hidden" name="a" value="post_regis" />
		<input type="hidden" name="uid" value="<?php echo $R['uid']?>" />
		<input type="hidden" name="category" value="<?php echo $category?>" />
		<input type="hidden" name="cat_depth" value="<?php echo $cat_depth?>" />
		<input type="hidden" name="mbruid" value="<?php echo $mbruid?>" />
		<input type="hidden" name="platform" value="web" />
		<input type="hidden" name="regis_mod" value="admin" />
		<input type="hidden" name="pcode" value="<?php echo $date['totime']?>" />
		<input type="hidden" name="wcode" value="<?php echo $_SESSION['wcode']?>" />
		<input type="hidden" name="sescode" value="<?php echo $sescode?>" />
		<input type="hidden" name="saveDir" value="<?php echo $g['path_file'].$module?>/" /> <!-- 포토 업로드 폴더 -->
		<input type="hidden" name="del_photos[]" value="" /> 
	
	 
	     <ul class="nav nav-tabs" role="tablist">
	      	 <li<?php if($_COOKIE['regisTap']=='default'||!$_COOKIE['regisTap']):?> class="active"<?php endif?> data-tabName="기본정보">
	      	    <a href="#default-settings" role="tab" data-toggle="tab" onclick="setCookie('regisTap','default',1);">기본 정보</a>
	      	 </li>
	      	 	 <li<?php if($_COOKIE['regisTap']=='area'):?> class="active"<?php endif?> data-tabName="지역">
	      	    <a href="#area-settings" role="tab" data-toggle="tab" onclick="setCookie('regisTap','area',1);">지역 정보</a>
	         </li>
         </ul>
		 <div class="tab-content" id="textarea-wrapper">
		     <div class="tab-pane<?php if($_COOKIE['regisTap']=='default'||!$_COOKIE['regisTap']):?> active<?php endif?>" id="default-settings">
		         <?php include $g['path_module'].$module.'/admin/_add_default.php';?>
	         </div>
	         <div class="tab-pane<?php if($_COOKIE['regisTap']=='area'):?> active<?php endif?>" id="area-settings">
	         	 <?php include $g['path_module'].$module.'/admin/_add_area.php';?>
			 </div>
	    </div>
	
		<div class="form-group">
			<div class="col-sm-12">
				<button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fa fa-check fa-lg"></i> 광고 <?php echo $R['uid']?'수정하기':'등록하기'?></button>
			</div>
		</div>

	</form>
		
  </div> <!-- 우측내용 끝 --> 
</div> <!-- .row 전체 box --> 
<iframe name="_orderframe_" class="hide"></iframe>
<!-- bootstrap-datepicker,  http://eternicode.github.io/bootstrap-datepicker/  -->
<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
<script type="text/javascript">

// 이미지 클릭 이벤트 
$('#preview-photo').on('click',function(){
      $('#ad-inputPhoto').click();
});

// 광고타입 선택시 이벤트 (노출단계 출력 )

$('#ad-type').on('change',function(){
    var ad_type = $(this).val();
    if(ad_type=='B01') $('#area_depth-wrap').removeClass('hidden');
    else $('#area_depth-wrap').addClass('hidden');
});

// 대표이미지 사진 선택시 이벤트 
$('#img-fgroup').on('change','#ad-inputPhoto',function(e){
     var files=e.target.files;
     var file=files[0];
     var reader = new FileReader();
     var photo_uid = $(this).data('photo');
     reader.readAsDataURL(file);
      //로드 한 후
     reader.onload = function  () {
        // 프로필 페이지 사진 업데이트 
        $('#preview-photo').attr("src", reader.result);
        if(photo_uid) $('input[name="del_photos[]"]').val(photo_uid);
     } 
});
// 카테고리 선택 이벤트 
$('[data-role="a-cat"]').click(function(e){
    e.preventDefault();
	$('[data-role="a-cat"]').css({"color":"#666","font-weight":"normal"});
    $(this).css({"color":"#428bca","font-weight":"bold"});
    var cat_uid = $(this).data('uid');
    var cat_name = $(this).data('name');
    var cat_depth =$(this).data('depth');

    $('input[name="category"]').val(cat_uid);
    $('input[name="cat_name"]').val(cat_name);
    $('input[name="cat_depth"]').val(cat_depth);    
});

// 회원 선택 이벤트 
$('[data-act="sel-mbr"]').click(function(e){
	e.preventDefault();
	$('[data-act="sel-mbr"]').removeClass('active');
	$(this).addClass('active');
    var mbr_info = $(this).data('info');
    var mbr_uid = $(this).data('uid');

    $('input[name="mbr_info"]').val(mbr_info);
    $('input[name="mbruid"]').val(mbr_uid);

    // 업체정보 업데이트 
    getCompany(mbr_uid);

});

// 날짜 선택 
$('.input-daterange').datepicker({
	format: "yyyy/mm/dd",
	todayBtn: "linked",
	language: "kr",
	calendarWeeks: true,
	todayHighlight: true,
	autoclose: true
});

 // 회원별 업체정보 가져오기
 function getCompany(val)
 {   
    var module='<?php echo $module?>';
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_Company',{
       val : val
    },function(response){
       var result = $.parseJSON(response);
       var content=result.content;
       $('#company-select').html(content);
    }); 
}

// 주소 가져오기 함수 val : 값 , depth : 구군 or 동, soe : start or end
function getAddr(val,depth,layer)
{   
    var module='<?php echo $module?>';
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=getAddr',{
       val : val,
       depth : depth
    },function(response){
       var result = $.parseJSON(response);
       var content=result.content;
       $(layer).html(content);
       if(depth=='sido'){
       	   	  $('#dong-wrap').html('<option value="">+ 동 선택 </option>');
       }   
    }); 
}

function saveCheck(f)
{
    if (f.mbruid.value == '')
	{
		alert('광고주 회원을 선택하세요.     ');
		return false;
	}
	if(f.company.value==''){
        alert('광고회사를 선택하세요.     ');
		return false; 
	}

	if (f.category.value == '')
	{
		alert('광고 카테고리를 선택해주세요.     ');
		return false;
	}
	if (f.ad_type.value == '')
	{
		alert('광고 타입을 선택해주세요.     ');
		return false;
	}

	if(f.ad_start.value ==''){
	   alert('광고 시작일을 선택해주세요.     ');
	   f.ad_start.focus();
	   return false; 	
	}
	if(f.ad_end.value ==''){
	   alert('광고 마감일을 선택해주세요.     ');
	   f.ad_end.focus();
	   return false; 	
	}
	if (f.sido.value == '')
	{
		alert('광고 지역(시/도)을 선택해주세요.     ');
		return false;
	}
	if (f.gugun.value == '')
	{
		alert('광고 지역(구/군)을 선택해주세요.     ');
		return false;
	}
	if (f.subject.value == '')
	{
		alert('광고 제목을 입력해주세요.     ');
		f.subject.focus();
		return false;
	}

  	getIframeForAction(f);
	f.submit();
			
}

</script>

