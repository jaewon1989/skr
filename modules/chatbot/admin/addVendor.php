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
$recnum	= $recnum && $recnum < 200 ? $recnum : 50;

$member_q ='auth=1 and mygroup=1';
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
	$R = getUidData($table[$module.'vendor'],$uid);
	
    // 회원 정보 
    $M = getDbData($table['s_mbrdata'],'memberuid='.$R['mbruid'],'name,tel2,email');
    $M1 = getDbData($table['s_mbrid'],'uid='.$R['mbruid'],'id');
    $mbruid = $R['mbruid'];
    $mbr_info = $M['name'].'('.$M1['id'].')';
    $mbr_tel2 = $R['tel2']?$R['tel2']:$M['tel2'];
    $mbr_email = $R['email']?$R['email']:$M['email'];
    $logo_img_src = $R['logo']?$R['logo']:'';
}
$logo_img = $logo_img_src?$logo_img_src:'http://placehold.it/200x150';
?>
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
				<input type="hidden" name="front" value="addComp">
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
			<div class="panel-body" style="border-top:1px solid #DEDEDE;height:100%;overflow:auto;">
				<?php if($NUM):?>
				<div class="list-group" id="addAdv-mlist">
				
					<?php while($M = db_fetch_array($RCD)):?>
					<a href="#" class="list-group-item" data-act="sel-mbr" data-uid="<?php echo $M['memberuid']?>" data-info="<?php echo $M['name']?>(<?php echo $M['id']?>)" data-email="<?php echo $M['email']?>" data-tel2="<?php echo $M['tel2']?>" >
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

   </div><!-- 좌측  내용 끝 -->	

   <!-- 우측 내용 시작 -->
   <div id="tab-content-view" class="col-sm-8 col-lg-9">
		<form name="procForm" class="form-horizontal rb-form" role="form" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
		<input type="hidden" name="r" value="<?php echo $r?>" />
		<input type="hidden" name="m" value="<?php echo $module?>" />
		<input type="hidden" name="a" value="_admin/regis_vendor" />
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

	   	 <?php include $g['path_module'].$module.'/admin/_add_vendor.php';?>
		

		<div class="form-group">
			<div class="col-sm-12">
				<button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fa fa-check fa-lg"></i> 업체 <?php echo $R['uid']?'수정하기':'등록하기'?></button>
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
// 날짜 선택 
$('.input-daterange').datepicker({
	format: "yyyy/mm/dd",
	todayBtn: "linked",
	language: "kr",
	calendarWeeks: true,
	todayHighlight: true,
	autoclose: true
});

</script>

