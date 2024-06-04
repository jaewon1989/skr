<?php

$sort	= $sort ? $sort : 'uid';
$orderby= $orderby ? $orderby : 'desc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 15;

if ($inbox == 3)
{
	$sqlque = 'by_mbruid='.$_M['uid'];
}
else
{
	$sqlque = 'my_mbruid='.$_M['memberuid'];
	if ($inbox) $sqlque .= " and inbox='".$inbox."'";
	if ($where && $keyw)
	{
		$sqlque .= getSearchSql($where,$keyw,$ikeyword,'or');
	}
}
if ($d_start) $sqlque .= ' and d_regis > '.str_replace('/','',$d_start).'000000';
if ($d_finish) $sqlque .= ' and d_regis < '.str_replace('/','',$d_finish).'240000';

$RCD = getDbArray($table['s_paper'],$sqlque,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table['s_paper'],$sqlque);
$TPG = getTotalPage($NUM,$recnum);
?>

<div class="manager-list">
	<p>
		<small><?php echo sprintf('총 %d건',$NUM)?>  (<?php echo $p?>/<?php echo $TPG?> page<?php if($TPG>1):?>s<?php endif?>)</small>
	</p>

	<form name="searchForm" class="form-horizontal" action="<?php echo $g['s']?>/" method="get">
	<input type="hidden" name="r" value="<?php echo $r?>">
	<input type="hidden" name="m" value="<?php echo $m?>">
	<input type="hidden" name="module" value="<?php echo $module?>">
	<input type="hidden" name="front" value="<?php echo $front?>">
	<input type="hidden" name="tab" value="<?php echo $tab?>">
	<input type="hidden" name="uid" value="<?php echo $_M['uid']?>">
	<input type="hidden" name="p" value="<?php echo $p?>">
	<input type="hidden" name="iframe" value="<?php echo $iframe?>">

	   <div class="well well-sm search-area">
	   	<div class="form-group">
	   		 <label class="col-sm-1 control-label">필터</label>
				 <div class="col-sm-11">	
						<div class="col-sm-4">
							<select name="inbox" class="form-control input-sm" onchange="this.form.submit();">
								<option value="">구분</option>
								<option value="1"<?php if($inbox==1):?> selected="selected"<?php endif?>>받은쪽지함</option>
								<option value="2"<?php if($inbox==2):?> selected="selected"<?php endif?>>쪽지보관함</option>
								<option value="3"<?php if($inbox==3):?> selected="selected"<?php endif?>>보낸쪽지함</option>
							</select>
						</div>
						<div class="col-sm-5 pull-right">
							<div class="input-daterange input-group input-group-sm" id="datepicker">
								<input type="text" class="form-control" name="d_start" placeholder="시작일" value="<?php echo $d_start?>">
								<span class="input-group-addon">~</span>
								<input type="text" class="form-control" name="d_finish" placeholder="종료일" value="<?php echo $d_finish?>">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
								</span>
							</div>
						</div>
				 </div> <!-- .col-sm-11 -->	 
			 </div><!-- .form-group -->	 
		</div> <!-- .panel -->
   </form>
	<form name="adm_list_form" class="form-horizontal" action="<?php echo $g['s']?>/" method="get">
	<input type="hidden" name="r" value="<?php echo $r?>">
	<input type="hidden" name="m" value="<?php echo $m?>">
	<input type="hidden" name="module" value="comment">
	<input type="hidden" name="front" value="<?php echo $front?>">
	<input type="hidden" name="tab" value="<?php echo $tab?>">
	<input type="hidden" name="p" value="<?php echo $p?>">
	<input type="hidden" name="iframe" value="<?php echo $iframe?>">
	<input type="hidden" name="a" value="">
       <div class="table-responsive">
			<table class="table table-hover" style="border-bottom:#ccc solid 1px;">
				<thead>
					<tr>
				      <th><input type="checkbox"  class="checkAll-act-list" data-toggle="tooltip" title="전체선택"></th>
						<th>번호</th>
						<th><?php echo $inbox==3?'받는이':'보낸이'?></th>
						<th></th>
						<th>내용</th>
						<th>날짜</th>
					</tr>
				</thead>
				<tbody>
					<?php while($R=db_fetch_array($RCD)):?>
				  	<?php $R['content']=str_replace('&nbsp;',' ',$R['content'])?>
	            <?php $M=getDbData($table['s_mbrdata'],'memberuid='.$R[($index==3?'m':'b').'y_mbruid'],'*')?>
					<tr>
						<td><input type="checkbox" name="members[]"  onclick="checkboxCheck();" class="mbr-act-list" value="<?php echo $R['uid']?>"></td>
						<td><?php echo $NUM-((($p-1)*$recnum)+$_rec++)?></td>
						<td><?php echo $M[$_HS['nametype']]?$M[$_HS['nametype']]:'시스템'?></td>
						<td><i class="fa fa-paper" data-toggle="tooltip" title="<?php echo $R['d_read']?getDateFormat($R['d_read'],'Y.m.d H:i 열람'):'읽지않음'?>"></i></td>
						<td class="rb-sbj">
					       <?php echo getStrCut(strip_tags($R['content']),50,'..')?>
                     <?php if(getNew($R['d_regis'],24)):?><span class="label label-danger"><small>New</small></span><?php endif?>            
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
