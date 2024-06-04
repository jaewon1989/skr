<?php
include $g['path_module'].$module.'/includes/tree.func.php';
include $g['path_module'].$module.'/includes/base.class.php';
include $g['path_module'].$module.'/includes/module.class.php';
$chatbot = new Chatbot();
if (!$_SESSION['upsescode']) $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
$sescode = $_SESSION['upsescode'];
$_SESSION['wcode']=$date['totime'];

$sort	= $sort ? $sort : 'uid';
$orderby= $orderby ? $orderby : 'desc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 50;

$_WHERE='uid>0';
if($where && $keyw) $_WHERE .= " and ".$where." like '%".trim($keyw)."%'";

$RCD = getDbArray($table[$module.'vendor'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$module.'vendor'],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);

if($uid)
{
	$R = getUidData($table[$module.'reply'],$uid);
	
	// 업체 정보 
	$V = getDbData($table[$module.'vendor'],'uid='.$R['vendor'],'name,id,mbruid');
	$M = getDbData($table['s_mbrid'],'uid='.$V['mbruid'],'id');
	$vendor_info = $V['name'].'('.$M['id'].')';
}
?>

<div class="row">
   <div class="col-sm-4 col-lg-3"> 
   	    <div class="panel panel-default">  <!-- 메뉴 리스트 패털 시작 -->
   			<div class="panel-heading rb-icon">
				<div class="icon">
					<i class="fa fa-file-text-o fa-2x"></i>
				</div>
				<h4 class="panel-title">
					업체 리스트 
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
				<input type="hidden" name="front" value="addReply">
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
				<div class="list-group" id="addReply-mlist">
				
					<?php while($V = db_fetch_array($RCD)):?>
					<?php $M = getDbData($table['s_mbrid'],'uid='.$V['mbruid'],'id');?>
					<a href="#" class="list-group-item" data-act="sel-vendor" data-uid="<?php echo $V['uid']?>" data-info="<?php echo $V['name']?>(<?php echo $M['id']?>)" >
			    		<?php echo $V['name']?>(<?php echo $M['id']?>)
			    	</a>
					<?php endwhile?>
				</div>
				
				<?php else:?>
				<div class="none">등록된 업체가 없습니다.</div>
				<?php endif?>
				
         </div>  
        	<div class="panel-footer rb-panel-footer">
				<ul class="pagination">
				<script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
				<?php //echo getPageLink(5,$p,$TPG,'')?>
				</ul>
			</div>
		</div> <!-- 좌측 패널 끝 -->  
        <!-- 카테고리 패널 시작 -->
        <div class="panel panel-default" >
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
					<div style="height:200px;overflow:auto;">
						<link href="<?php echo $g['s']?>/_core/css/tree.css" rel="stylesheet">
			            <?php $_treeOptions=array('table'=>$table[$module.'category'],'uid'=>$R['uid'],'dispNum'=>false,'dispCheckbox'=>false,'allOpen'=>false)?>
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
		<input type="hidden" name="a" value="_admin/regis_reply" />
		<input type="hidden" name="uid" value="<?php echo $R['uid']?>" />
		<input type="hidden" name="vendor" value="<?php echo $R['vendor']?$R['vendor']:1?>" />
		<input type="hidden" name="quesCat" value="<?php echo $R['quesCat']?>" />
	
        <?php include $g['path_module'].$module.'/admin/_add_reply.php';?>
	
		<div class="form-group">
			<div class="col-sm-12">
				<button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fa fa-check fa-lg"></i> <?php echo $R['uid']?'수정하기':'신규 등록하기'?></button>
			</div>
		</div>

	</form>
		
  </div> <!-- 우측내용 끝 --> 
</div> <!-- .row 전체 box --> 

<script type="text/javascript">


// 카테고리 선택 이벤트 
$('[data-role="a-cat"]').click(function(e){
    e.preventDefault();
	$('[data-role="a-cat"]').css({"color":"#666","font-weight":"normal"});
    $(this).css({"color":"#428bca","font-weight":"bold"});
    var rcode = $(this).data('rcode');
    rcode ='/'+rcode+'/';
    $('input[name="quesCat"]').val(rcode);
    $('input[name="catName"]').val(rcode);

});

// 업체 선택 이벤트 
$('[data-act="sel-vendor"]').click(function(e){
	e.preventDefault();
	$('[data-act="sel-vendor"]').removeClass('active');
	$(this).addClass('active');
    var vendor_info = $(this).data('info');
    var vendor_uid = $(this).data('uid');

    $('input[name="vendor_info"]').val(vendor_info);
    $('input[name="vendor"]').val(vendor_uid);
});


function saveCheck(f)
{
    if (f.vendor.value == '')
	{
		alert('업체를 선택해주세요.     ');
		return false;
	}

	if (f.quesCat.value == '')
	{
		alert('답변 카테고리를 선택해주세요.     ');
		return false;
	}
	if (f.content.value == '')
	{
		alert('답변 내용을 입력해주세요.     ');
		return false;
	}

  	getIframeForAction(f);
	f.submit();
			
}

</script>

