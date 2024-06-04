<?php
include $g['path_module'].$module.'/var/var.php';
$bbs_time=$d['bbs']['time']; // 아래 $d 배열과 충돌을 피하기 위해서 별도로 지정
$sort	= $sort ? $sort : 'gid';
$orderby= $orderby ? $orderby : 'asc';
$recnum	= $recnum && $recnum < 301 ? $recnum : 30;
$bbsque	= 'uid';

if ($where && $keyw)
{
	if (strstr('[id]',$where)) $bbsque .= " and ".$where."='".$keyw."'";
	else $bbsque .= getSearchSql($where,$keyw,$ikeyword,'or');	
}

$RCD = getDbArray($table[$module.'list'],$bbsque,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$module.'list'],$bbsque);
$TPG = getTotalPage($NUM,$recnum);

$_LEVELNAME = array('l0'=>'전체허용');
$_LEVELDATA=getDbArray($table['s_mbrlevel'],'','*','uid','asc',0,1);
while($_L=db_fetch_array($_LEVELDATA)) $_LEVELNAME['l'.$_L['uid']] = $_L['name'].' 이상';
?>


<div class="page-header">
 <h4>게시판 전체현황 
       <a href="<?php echo $g['adm_href']?>&amp;front=makebbs"  class="pull-right btn btn-link"><i class="fa fa-plus"></i> 새 게시판 만들기</a>
 </h4>
</div>

<div class="rb-heading well well-sm">
	<form name="procForm" action="<?php echo $g['s']?>/" method="get" class="form-horizontal">
		 <input type="hidden" name="r" value="<?php echo $r?>" />
		 <input type="hidden" name="m" value="<?php echo $m?>" />
		 <input type="hidden" name="module" value="<?php echo $module?>" />
		 <input type="hidden" name="front" value="<?php echo $front?>" />

	    <div class="form-group hidden-xs">
		 	 <label class="col-sm-1 control-label">정렬</label>
			 <div class="col-sm-10">
				 <div class="btn-toolbar">
					 <div class="btn-group btn-group-sm" data-toggle="buttons">
						<label class="btn btn-default<?php if($sort=='gid'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
							<input type="radio" value="gid" name="sort"<?php if($sort=='gid'):?> checked<?php endif?>> 지정순서
						</label>
						<label class="btn btn-default<?php if($sort=='uid'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
							<input type="radio" value="uid" name="sort"<?php if($sort=='uid'):?> checked<?php endif?>> 개설일
						</label>
						<label class="btn btn-default<?php if($sort=='num_r'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
							<input type="radio" value="num_r" name="sort"<?php if($sort=='num_r'):?> checked<?php endif?>> 게시물수
						</label>
						<label class="btn btn-default<?php if($sort=='d_last'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
							<input type="radio" value="d_last" name="sort"<?php if($sort=='d_last'):?> checked<?php endif?>> 최근게시
						</label>
					 </div>
					 <div class="btn-group btn-group-sm" data-toggle="buttons">
						<label class="btn btn-default<?php if($orderby=='desc'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
							<input type="radio" value="desc" name="orderby"<?php if($orderby=='desc'):?> checked<?php endif?>> <i class="fa fa-sort-amount-desc"></i> 정순
						</label>
						<label class="btn btn-default<?php if($orderby=='asc'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
							<input type="radio" value="asc" name="orderby"<?php if($orderby=='asc'):?> checked<?php endif?>> <i class="fa fa-sort-amount-asc"></i> 역순
						</label>
					 </div>
				 </div>
			 </div> <!-- .col-sm-10 -->
	    </div> <!-- .form-group -->

	   <!-- 고급검색 시작 -->
	   <div id="search-more" class="collapse<?php if($_SESSION['sh_mediaset']):?> in<?php endif?>">
	       <div class="form-group">
				 <label class="col-sm-1 control-label">출력</label>
				 <div class="col-sm-10">
					 <div class="row">
						<div class="col-sm-2">
							<select name="recnum" onchange="this.form.submit();" class="form-control input-sm">
								<option value="20"<?php if($recnum==20):?> selected="selected"<?php endif?>>20 개</option>
								<option value="35"<?php if($recnum==35):?> selected="selected"<?php endif?>>35 개</option>
								<option value="50"<?php if($recnum==50):?> selected="selected"<?php endif?>>50 개</option>
								<option value="75"<?php if($recnum==75):?> selected="selected"<?php endif?>>75 개</option>
								<option value="90"<?php if($recnum==90):?> selected="selected"<?php endif?>>90 개</option>
							</select>
						</div>
						<div class="col-sm-2">

						</div>
					 </div>
				 </div>
		    </div> <!-- .form-group -->
	       <div class="form-group">
				 <label class="col-sm-1 control-label">검색</label>
				 <div class="col-sm-10">
					 <div class="input-group input-group-sm">
						<span class="input-group-btn hidden-xs" style="width:165px">
							<select name="where" class="form-control btn btn-default">
								<option value="name"<?php if($where=='name'):?> selected="selected"<?php endif?>>게시판명</option>
	                     <option value="id"<?php if($where=='id'):?> selected="selected"<?php endif?>>아이디</option>
							</select>
						</span>
						<input type="text" name="keyw" value="<?php echo stripslashes($keyw)?>" class="form-control">
						<span class="input-group-btn">
							<button class="btn btn-primary" type="submit">검색</button>
						</span>
						<span class="input-group-btn">
							<button class="btn btn-default" type="button" onclick="location.href='<?php echo $g['adm_href']?>';">리셋</button>
						</span>
					 </div>
				</div>
		    </div> <!-- .form-group -->
		 </div>
		 <!-- 고급검색 끝 -->   
		 
	   <div class="form-group">
				<div class="col-sm-offset-1 col-sm-10">
					<button type="button" class="btn btn-link rb-advance<?php if(!$_SESSION['sh_mediaset']):?> collapsed<?php endif?>" data-toggle="collapse" data-target="#search-more" onclick="sessionSetting('sh_mediaset','1','','1');">고급검색 <small></small></button>
					<a href="<?php echo $g['adm_href']?>" class="btn btn-link">초기화</a>
				</div>
		</div>
	</form>    
</div>  <!-- .rb-heading well well-sm : 검색영역 회색 박스  -->


<!-- 리스트 시작  -->
<div class="page-header">
	<h4>
		<small>개 ( <?php echo $p?>/<?php echo $TPG.($TPG>1?' pages':' page')?> )</small>
	</h4>
</div>
<form name="listForm" action="<?php echo $g['s']?>/" method="post">
		<input type="hidden" name="r" value="<?php echo $r?>">
		<input type="hidden" name="m" value="<?php echo $module?>">
		<input type="hidden" name="a" value="">

		<div class="table-responsive">
			<table class="table table-striped">
				<tr>
					<th><label data-tooltip="tooltip" title="선택"><input type="checkbox" class="checkAll-email-user"></label></th>
					<th>번호</th>
					<th>아이디</th>
					<th>게시판명</th>
					<th>게시물</th>
					<th>최근게시</th>
					<th>분류</th>
					<th>연결</th>
					<th>소셜</th>
					<th>헤더</th>
					<th>풋터</th>
					<th>레이아웃</th>
					<th>접근권한</th>
					<th>포인트</th>
					<th>관리</th>
				</tr>
				<?php while($R=db_fetch_array($RCD)):?>
				<?php $L=getOverTime($date['totime'],$R['d_last'])?>
				<?php $d=array();include $g['path_module'].$module.'/var/var.'.$R['id'].'.php';?>
				<?php 
					 $sbj_tooltip='<h6>'; // 제목 툴팁
					 $sbj_tooltip.='최신글제외 : '.($d['bbs']['display']?'Yes':'No').'<br />';
					 $sbj_tooltip.='쿼리생략 : '.($d['bbs']['hidelist']?'Yes':'No').'<br />';
					 $sbj_tooltip.='RSS발행 : '.($d['bbs']['rss']?'Yes':'No').'<br />';
					 $sbj_tooltip.='조회수증가 : '.($d['bbs']['hitcount']?'계속증가':'1회만증가(세션적용)').'<br />';
					 $sbj_tooltip.='게시물출력수 : '.$d['bbs']['recnum'].'개<br />';
					 $sbj_tooltip.='제목끊기 : '.$d['bbs']['sbjcut'].'자<br />';
					 $sbj_tooltip.='새글유지 : '.$d['bbs']['newtime'].'시간<br />';
					 $sbj_tooltip.='추차관리자 : '.($d['bbs']['admin']?$d['bbs']['admin']:'없음').'<br /><i></i>';
					 $sbj_tooltip .='</h6>';
                
                $lay_tooltip='<h6>';// 레이아웃 툴팁
					 $lay_tooltip .='레이아웃 : '.($d['bbs']['layout']?'':'사이트 대표레이아웃').'<br />';
					 $lay_tooltip .='게시판테마(pc) : '.($d['bbs']['skin']?getFolderName($g['path_module'].$module.'/theme/'.$d['bbs']['skin']).'('.basename($d['bbs']['skin']).')':'대표테마').'<br />';
					 $lay_tooltip .='게시판테마(mobile) : '.($d['bbs']['m_skin']?getFolderName($g['path_module'].$module.'/theme/'.$d['bbs']['m_skin']).'('.basename($d['bbs']['m_skin']).')':'대표테마').'<br />';
					 $lay_tooltip .='댓글테마(pc) : '.($d['bbs']['cskin']?getFolderName( $g['path_module'].'comment/theme/'.$d['bbs']['cskin']).'('.basename($d['bbs']['cskin']).')':'대표테마').'<br />';
					 $lay_tooltip .='댓글테마(mobile) : '.($d['bbs']['c_mskin']?getFolderName($g['path_module'].'comment/theme/'.$d['bbs']['c_mskin']).'('.basename($d['bbs']['c_mskin']).')':'대표테마').'<br /><i></i>';
					 $lay_tooltip .='</h6>';

					 $perm_tooltip='<h6>'; // 접근권한 툴팁
					 $perm_tooltip .='목록 : '.$_LEVELNAME['l'.$d['bbs']['perm_l_list']].'<br />';
					 $perm_tooltip .='열람 : '.$_LEVELNAME['l'.$d['bbs']['perm_l_view']].'<br />';
					 $perm_tooltip .='쓰기 : '.$_LEVELNAME['l'.$d['bbs']['perm_l_write']].'<br />';
					 $perm_tooltip .='다운 : '.$_LEVELNAME['l'.$d['bbs']['perm_l_down']].'<br /><i></i>';
                $perm_tooltip .='</h6>';

                $point_tooltip='<h6>'; // 포인트 툴팁
					 $point_tooltip .='등록 : '.number_format($d['bbs']['point1']).'P 지급<br />';
					 $point_tooltip .='열람 : '.number_format($d['bbs']['point2']).'P 차감<br />';
					 $point_tooltip .='다운 : '.number_format($d['bbs']['point3']).'P 차감<br /><i></i>';
                $point_tooltip .='</h6>'; 
				?>

				<tr>
					<td><input type="checkbox" name="bbs_members[]" value="<?php echo $R['uid']?>" class="rb-email-user" onclick="checkboxCheck();"/></td>
					<td><?php echo $NUM-((($p-1)*$recnum)+$_rec++)?></td>
					<td><a href="<?php echo RW('m='.$module.'&bid='.$R['id'])?>" target="_blank"><?php echo $R['id']?></a></td>
					<td><input type="text" name="name_<?php echo $R['uid']?>" value="<?php echo $R['name']?>" data-tooltip="tooltip" title="<?php echo $sbj_tooltip?>"/></td>
					<td><?php echo number_format($R['num_r'])?></td>
					<td><?php echo $R['d_last']?($L[1]<3?$L[0].$bbs_time[$L[1]].'전':getDateFormat($R['d_last'],'Y.m.d')):''?><?php if(getNew($R['d_last'],24)):?> <span class="label label-danger">new</span><?php endif?></td>
					<td><?php echo $R['category']?'<span>Y</span>':'N'?></td>
					<td><?php echo $d['bbs']['sosokmenu']?'<span>Y</span>':'N'?></td>
					<td><?php echo $d['bbs']['snsconnect']?'<span>Y</span>':'N'?></td>
					<td><?php echo $R['imghead']||is_file($g['path_module'].$module.'/var/code/'.$R['id'].'.header.php')?'<span>Y</span>':'N'?></td>
					<td><?php echo $R['imgfoot']||is_file($g['path_module'].$module.'/var/code/'.$R['id'].'.footer.php')?'<span>Y</span>':'N'?></td>
					<td><span data-tooltip="tooltip" title="<?php echo $lay_tooltip?>"><?php echo $d['bbs']['layout']?'<i>Y</i>':'N'?> / <?php echo $d['bbs']['skin']?'<i>Y</i>':'N'?> / <?php echo $d['bbs']['c_skin']?'<i>Y</i>':'N'?></span></td>
					<td><span data-tooltip="tooltip" title="<?php echo $perm_tooltip?>"><?php echo $d['bbs']['perm_l_list']?> / <?php echo $d['bbs']['perm_l_view']?> / <?php echo $d['bbs']['perm_l_write']?></span></td>
					<td><span data-tooltip="tooltip" title="<?php echo $point_tooltip?>"><?php echo number_format($d['bbs']['point1'])?> / <?php echo number_format($d['bbs']['point2'])?> / <?php echo number_format($d['bbs']['point3'])?></span></td>
					<td>
						<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $module?>&amp;a=deletebbs&amp;uid=<?php echo $R['uid']?>" onclick="return hrefCheck(this,true,'삭제하시면 모든 게시물이 지워지며 복구할 수 없습니다.\n정말로 삭제하시겠습니까?');" class="del">삭제</a>
						<a href="<?php echo $g['adm_href']?>&amp;front=makebbs&amp;uid=<?php echo $R['uid']?>">설정</a>
					</td>
		   	</tr>
				<?php endwhile?>
			</table>
		</div>

		<?php if(!$NUM):?>
		<div class="rb-none">데이타가 존재하지 않습니다. </div>
		<?php endif?>

		<div class="rb-footer clearfix">
			<div class="pull-right">
				<ul class="pagination">
				<script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
				<?php //echo getPageLink(5,$p,$TPG,'')?>
				</ul>
			</div>	
			<div>
				<button type="button" onclick="chkFlag('bbs_members[]');checkboxCheck();" class="btn btn-default btn-sm">선택/해제</button>
				<button type="button" onclick="actCheck('multi_config');" class="btn btn-default btn-sm" id="rb-action-btn">수정</button>
			</div>
		</div> <!-- .rb-footer --> 
</form>
<!-- basic -->
<script>
$(".checkAll-file-user").click(function(){
	$(".rb-file-user").prop("checked",$(".checkAll-file-user").prop("checked"));
	checkboxCheck();
});
function checkboxCheck()
{
	var f = document.listForm;
    var l = document.getElementsByName('bbs_members[]');
    var n = l.length;
    var i;
	var j=0;

	for	(i = 0; i < n; i++)
	{
		if (l[i].checked == true) j++;
	}
	if (j) getId('rb-action-btn').disabled = false;
	else getId('rb-action-btn').disabled = true;
}

function dropDate(date1,date2)
{
	var f = document.procForm;
	f.d_start.value = date1;
	f.d_finish.value = date2;
	f.submit();
}
function actCheck(act)
{
	var f = document.listForm;
    var l = document.getElementsByName('bbs_members[]');
    var n = l.length;
	var j = 0;
    var i;

    for (i = 0; i < n; i++)
	{
		if(l[i].checked == true)
		{
			j++;
		}
	}
	if (!j)
	{
		alert('선택된 게시판이 없습니다.     ');
		return false;
	}
	if (act == 'multi_config')
	{
		if (confirm('정말로 실행하시겠습니까?       '))
		{
			getIframeForAction(f);
			f.a.value = act;
			f.submit();
		}
	}

	return false;
}
</script>
