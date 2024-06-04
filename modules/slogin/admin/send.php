<?php

$sort	= $sort ? $sort : 'gid';
$orderby= $orderby ? $orderby : 'asc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 20;

$_WHERE = 'uid>0';
if ($d_start) $_WHERE .= ' and d_regis > '.str_replace('/','',$d_start).'000000';
if ($d_finish) $_WHERE .= ' and d_regis < '.str_replace('/','',$d_finish).'240000';
if ($provider) $_WHERE .= " and provider='".$provider."'";
if ($where && $keyw)
{
	$_WHERE .= getSearchSql($where,$keyw,$ikeyword,'or');
}
$RCD = getDbArray($table[$module.'data'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$module.'data'],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);

$snsUrlset = array
(
	't' => 'http://twitter.com/',
	'f' => 'http://facebook.com/profile.php?id=',
	'm' => 'http://me2day.net/',
	'y' => 'http://yozm.daum.net/',
);
?>


<form name="procForm" class="form-horizontal rb-form" action="<?php echo $g['s']?>/" method="get">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="module" value="<?php echo $module?>" />
	<input type="hidden" name="front" value="<?php echo $front?>" />

	<div class="rb-heading well well-sm search-area">
		 <div class="form-group">
			<label class="col-sm-1 control-label">기간</label>
			<div class="col-sm-10">
				<div class="row">
					<div class="col-sm-4">
						<div class="input-daterange input-group input-group-sm" id="datepicker">
							<input type="text" class="form-control" name="d_start" placeholder="시작일 선택" value="<?php echo $d_start?>">
							<span class="input-group-addon">~</span>
							<input type="text" class="form-control" name="d_finish" placeholder="종료일 선택" value="<?php echo $d_finish?>">
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit">기간적용</button>
							</span>
						</div>
					</div>
					<div class="col-sm-3 hidden-xs">
						<span class="input-group-btn">
							<button class="btn btn-default" onclick="dropDate('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-1,substr($date['today'],0,4)))?>','<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-1,substr($date['today'],0,4)))?>');">어제</button>
							<button class="btn btn-default" onclick="dropDate('<?php echo getDateFormat($date['today'],'Y/m/d')?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">오늘</button>
							<button class="btn btn-default" onclick="dropDate('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-7,substr($date['today'],0,4)))?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">일주</button>
							<button class="btn btn-default" onclick="dropDate('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">한달</button>
							<button class="btn btn-default" onclick="dropDate('<?php echo getDateFormat(substr($date['today'],0,6).'01','Y/m/d')?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">당월</button>
							<button class="btn btn-default" onclick="dropDate('<?php echo date('Y/m/',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>01','<?php echo date('Y/m/',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>31');">전월</button>
							<button class="btn btn-default" onclick="dropDate('','');">전체</button>
						</span>
					</div>							
				</div>
			</div>
		</div>
		<div class="form-group">
	 	 	  <label class="col-sm-1 control-label">필터</label>
	 	 	  <div class="col-sm-10">
	 	 	  	  <div class="row">
	 	 	  	  	   <div class="col-sm-3">
	 	 	  	  	       <select name="provider" class="form-control input-sm" onchange="this.form.submit();">
	 	 	  	  	       	<option value="">&nbsp;+ SNS구분</option>
								<option value="">--------------------</option>
								<option value="t"<?php if($provider=='t'):?> selected="selected"<?php endif?>>트위터</option>
								<option value="f"<?php if($provider=='f'):?> selected="selected"<?php endif?>>페이스북</option>
								<option value="g"<?php if($provider=='g'):?> selected="selected"<?php endif?>>구글+</option>
							 </select>
	 	 	  	  	    </div>
	 	 	  	  	 </div> <!-- .row -->
	 	 	  	 </div> <!-- .col-sm-10 -->
	 	 	 </div> <!-- .form-group -->
	 	 	 <div class="form-group">
 				 <label class="col-sm-1 control-label">검색</label>
				 <div class="col-sm-8">
					<div class="input-group input-group-sm">
						<span class="input-group-btn hidden-xs" style="width:165px">
							<select name="where" class="form-control btn btn-default input-sm">
								<option value="id"<?php if($where=='id'):?> selected="selected"<?php endif?>>회원아이디</option>
								<option value="snsid"<?php if($where=='snsid'):?> selected="selected"<?php endif?>>소셜아이디</option>
						    </select>
						</span>
						<input type="text" name="keyw" value="<?php echo stripslashes($keyw)?>" class="form-control">
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit">검색</button>
						</span>
					</div>
				</div>
			 </div>
   </div>
</form>
<div class="panel panel-default table-responsive">
	<div class="panel-heading btn-toolbar">
		<span class="pull-left">
			 총<code><?php echo number_format($NUM)?></code>건 (<?php echo $p?>/<?php echo $TPG?>페이지)
		</span>

		<div class="btn-group pull-right">
         <a href="<?php echo '/?'.$_SERVER['QUERY_STRING']?>&amp;p=<?php echo $p-1?>" class="btn btn-default btn-page" <?php echo $p>1?'':'disabled'?> data-toggle="tooltip" data-placement="bottom" title="" data-original-title="이전">
            <i class="fa fa-chevron-left fa-lg"></i>
         </a>
         <a href="<?php echo '/?'.$_SERVER['QUERY_STRING']?>&amp;p=<?php echo $p+1?>" class="btn btn-default btn-page" <?php echo $NUM>($p*$recnum)?'':'disabled'?> data-toggle="tooltip" data-placement="bottom" title="" data-original-title="다음">
            <i class="fa fa-chevron-right fa-lg"></i>
          </a>
      </div>
		<div class="btn-group pull-right">
			 <div class="btn-group dropup hidden-xs">
		      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
		        <i class="fa fa-list"></i> <?php echo $recnum?>개씩  <span class="caret"></span>
		       </button> 
		      <ul class="dropdown-menu pull-right" role="menu">
		        <li <?php $recnum=='20'?'class="active"':''?>><a href="<?php echo $g['adm_href']?>&amp;recnum=20">20개 출력</a></li>
		        <li <?php $recnum=='35'?'class="active"':''?>> <a href="<?php echo $g['adm_href']?>&amp;recnum=35">35개 출력</a></li>
		        <li <?php $recnum=='50'?'class="active"':''?>><a href="<?php echo $g['adm_href']?>&amp;recnum=50">50개 출력</a></li>
		        <li <?php $recnum=='75'?'class="active"':''?>><a href="<?php echo $g['adm_href']?>&amp;recnum=75">75개 출력</a></li>
		        <li <?php $recnum=='90'?'class="active"':''?>><a href="<?php echo $g['adm_href']?>&amp;recnum=90">90개 출력</a></li>
		      </ul>
		    </div>
	   </div>
	</div>  
   <form name="listForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>">
		<input type="hidden" name="r" value="<?php echo $r?>" />
		<input type="hidden" name="m" value="<?php echo $module?>" />
		<input type="hidden" name="a" value="" />
		<table class="table table-hover">
		<thead>
			<tr>
				<th class="text-center"><input type="checkbox"  class="checkAll-member" data-toggle="tooltip" title="전체선택"></th>
				<th class="text-center">번호</th>
				<th>제목</th>
				<th>보낸이</th>
				<th>보낸곳</th>
				<th>소셜ID</th>
				<th class="text-center">날짜</th>
			</tr>
		</thead>
		<tbody>

		<?php $_HS['rewrite']=false?>
		<?php while($R=db_fetch_array($RCD)):?>
		<tr>
			<td><input type="checkbox" name="snssend_members[]" value="<?php echo $R['uid']?>" /></td>
			<td><?php echo $NUM-((($p-1)*$recnum)+$_rec++)?></td>
			<td class="sbj">
				<a href="<?php echo getCyncUrl($R['cync'])?><?php if(strpos($R['cync'],'CMT')):?>#CMT<?php endif?>" target="_blank"><?php echo $R['subject']?></a>
				<?php if(getNew($R['d_regis'],24)):?><span class="new">new</span><?php endif?>
			</td>
			<td>
				<?php if($R['mbruid']):?>
				<a href="javascript:OpenWindow('<?php echo $g['s']?>/?r=<?php echo $r?>&iframe=Y&m=member&front=manager&page=main&mbruid=<?php echo $R['mbruid']?>');" title="회원메니져"><?php echo $R[$_HS['nametype']]?></a>
				<?php else:?>
				<?php echo $R[$_HS['nametype']]?>
				<?php endif?>
			</td>
			<td class="name"><a href="<?php echo $R['targeturl']?>" target="_blank"><img src="<?php echo $g['img_core']?>/_public/sns_<?php echo $R['provider']?>0.gif" alt="" /></a></td>
			<td><a href="<?php echo $snsUrlset[$R['provider']].$R['snsid']?>" target="_blank"><?php echo $R['snsid']?></a></td>
			<td><?php echo getDateFormat($R['d_regis'],'Y.m.d H:i')?></td>
		</tr> 
		<?php endwhile?> 
	    </tbody>
   </table>
	<?php if($NUM):?>
   <!--목록에 체크된 항목이 없을 경우  fieldset이 disabled 됨-->
   <div class="rb-footer clearfix">
		<div class="pull-right">
			<ul class="pagination">
				<script type="text/javascript">getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
				<?php //echo getPageLink(5,$p,$TPG,'')?>
			</ul>
		</div>	
		<div>
			<button type="button" onclick="chkFlag('lang_members[]');checkboxCheck();" class="btn btn-default btn-sm">선택/해제</button>
			<button type="button" onclick="actCheck('multi_delete');" class="btn btn-default btn-sm" id="rb-action-btn" disabled>삭제</button>
			<a href="#top"><button type="button" class="btn btn-default btn-sm" id="rb-action-btn" ><i class="fa fa-top"></i>위로</button></a>
 		</div>
	</div>
 </form>
  <?php else:?>
	<hr>
	<div class="well text-center text-muted" style="margin-top:15px">
		<i class="fa fa-exclamation-circle fa-lg"></i> 조건에 해당하는 회원이 없습니다.
	</div>
 <?php endif?>

</div>

<div id="qTilePopDiv"></div>
<!-- bootstrap-datepicker,  http://eternicode.github.io/bootstrap-datepicker/  -->
<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
<style type="text/css">
.datepicker {z-index: 1151 !important;}
</style>
<script>
$('.input-daterange').datepicker({
	format: "yyyy/mm/dd",
	todayBtn: "linked",
	language: "kr",
	calendarWeeks: true,
	todayHighlight: true,
	autoclose: true
});
</script>
<!-- bootstrap-datepicker,  http://eternicode.github.io/bootstrap-datepicker/  -->
<script type="text/javascript">
//<![CDATA[

// 기간 검색 적용 함수
function dropDate(date1,date2)
{
	var f = document.procForm;
	f.d_start.value = date1;
	f.d_finish.value = date2;
	f.submit();
}

function actQue(flag)
{
	var f = document.listForm;
    var l = document.getElementsByName('snssend_members[]');
    var n = l.length;
    var i;
	var j=0;
	var s='';

	for	(i = 0; i < n; i++)
	{
		if (l[i].checked == true)
		{
			j++;
			s += l[i].value +',';
		}
	}
	if (!j)
	{
		alert('보낸 트랙백을 선택해 주세요.     ');
		return false;
	}
	
	
	if (flag == 'multi_snssend_delete')
	{
		if (!confirm('정말로 삭제하시겠습니까?     '))
		{
			return false;
		}
	}
	f.a.value = flag;
	f.submit();
}
//]]>
</script>
