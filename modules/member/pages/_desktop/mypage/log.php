<?php
$sort	= $sort ? $sort : 'uid';
$orderby= $orderby ? $orderby : 'asc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 5;

$sqlque = 'mbruid='.$my['uid'].' and site='.$s;
if ($category) $sqlque .= " and category='".$category."'";
if ($d_start) $sqlque .= ' and d_regis > '.str_replace('/','',$d_start).'000000';
if ($d_finish) $sqlque .= ' and d_regis < '.str_replace('/','',$d_finish).'240000';
if ($where && $keyw)
{
	$sqlque .= getSearchSql($where,$keyw,$ikeyword,'or');
}
$RCD = getDbArray($table['s_referer'],$sqlque,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table['s_referer'],$sqlque);
$TPG = getTotalPage($NUM,$recnum);

?>
<div id="page-profile">
	<?php include $g['dir_module_skin'].'_cover.php';?>
	<p>
		<small><?php echo sprintf('총 %d건',$NUM)?>  (<?php echo $p?>/<?php echo $TPG?> page<?php if($TPG>1):?>s<?php endif?>)</small>
	</p>

	<form name="adm_list_form" class="form-horizontal" action="<?php echo $g['s']?>/" method="post">
	<input type="hidden" name="r" value="<?php echo $r?>">
   <input type="hidden" name="module" value="<?php echo $module?>">
	<input type="hidden" name="front" value="<?php echo $front?>">
	<input type="hidden" name="page" value="<?php echo $page?>">
	<input type="hidden" name="p" value="<?php echo $p?>">
	<input type="hidden" name="iframe" value="<?php echo $iframe?>">
	<input type="hidden" name="m" value=""> <!-- 액션파일이 있는 모듈명  -->
	<input type="hidden" name="a" value=""> <!-- 액션명  -->	
	    <div class="table-responsive">
			<table class="table table-hover" style="border-bottom:#ccc solid 1px;">
					<thead>
						<tr>
							<th>번호</th>
							<th>아이피</th>
							<th class="rb-url">접속경로</th>
							<th>브라우져</th>
							<th>기기</th>
							<th>날짜</th>
						</tr>
					</thead>
					<tbody>
						<?php while($R=db_fetch_array($RCD)):?>
						<?php $_browzer=getBrowzer($R['agent'])?>
						<?php $_deviceKind=isMobileConnect($R['agent'])?>
						<?php $_deviceType=getDeviceKind($R['agent'],$_deviceKind)?>
						<tr>
							<td><?php echo $NUM-((($p-1)*$recnum)+$_rec++)?></td>
							<td><?php echo $R['ip']?></td>
							<td class="rb-url"><a href="<?php echo $R['referer']?>" target="_blank"><?php echo getDomain($R['referer'])?></a></td>
							<td><?php echo strtoupper($_browzer)?></td>
							<td>
								<?php if($_browzer=='Mobile'):?>
								<small class="label label-<?php echo $_deviceType=='tablet'?'danger':'warning'?>" data-tooltip="tooltip" title="<?php echo $_deviceKind?>"><?php echo $_deviceType?></small>
								<?php else:?>
								<small class="label label-default">desktop</small>
								<?php endif?>
							</td>
							<td class="rb-update">
								<time class="timeago" data-toggle="tooltip" datetime="<?php echo getDateFormat($R['d_regis'],'c')?>" data-tooltip="tooltip" title="<?php echo getDateFormat($R['d_regis'],'Y.m.d H:i')?>"></time>	
							</td>
						</tr>
						<?php endwhile?>
					</tbody>
				</table>
		    <?php if(!$NUM):?>
	          <div class="rb-none">데이타가 없습니다.</div>
	       <?php endif?>
	 </div>	
	 <div class="text-center">
	    <ul class="pagination pagination-sm" style="padding:0;margin:0">
	       <script type="text/javascript">getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
	     </ul>
	 </div>
	</form>
</div>
<!-- 공통 스크립트 -->
<?php include $g['dir_module_skin'].'_common_script.php'?>


