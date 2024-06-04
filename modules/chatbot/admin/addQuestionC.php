<?php

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
	$R = getUidData($table[$module.'ruleC'],$uid);
	
	// 업체 정보 
	$V = getDbData($table[$module.'vendor'],'uid='.$R['vendor'],'name,id');
	$vendor_info = $V['name'].'('.$V['id'].')';
}
?>

<div class="row">

   <!-- 우측 내용 시작 -->
   <div id="tab-content-view" class="col-sm-12 col-lg-12">
		<form name="procForm" class="form-horizontal rb-form" role="form" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
		<input type="hidden" name="r" value="<?php echo $r?>" />
		<input type="hidden" name="m" value="<?php echo $module?>" />
		<input type="hidden" name="a" value="_admin/regis_replyC" />
		<input type="hidden" name="uid" value="<?php echo $R['uid']?>" />
		<input type="hidden" name="vendor" value="<?php echo $R['vendor']?$R['vendor']:1?>" />
		<input type="hidden" name="quesCat" value="<?php echo $R['quesCat']?>" />
	
        <?php include $g['path_module'].$module.'/admin/_add_replyC.php';?>
	
		<div class="form-group">
			<div class="col-sm-12">
				<button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fa fa-check fa-lg"></i> 답변<?php echo $R['uid']?'수정하기':'등록하기'?></button>
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
	// if (f.quesCat.value == '')
	// {
	// 	alert('답변 카테고리를 선택해주세요.     ');
	// 	return false;
	// }
	if (f.reply.value == '')
	{
		alert('답변 내용을 입력해주세요.     ');
		return false;
	}

  	getIframeForAction(f);
	f.submit();
			
}

</script>

