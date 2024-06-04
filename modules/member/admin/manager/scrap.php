<?php

$sort	= $sort ? $sort : 'uid';
$orderby= $orderby ? $orderby : 'asc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 5;

$sqlque = 'mbruid='.$_M['uid'];
if ($category) $sqlque .= " and category='".$category."'";
if ($d_start) $sqlque .= ' and d_regis > '.str_replace('/','',$d_start).'000000';
if ($d_finish) $sqlque .= ' and d_regis < '.str_replace('/','',$d_finish).'240000';
if ($where && $keyw)
{
	$sqlque .= getSearchSql($where,$keyw,$ikeyword,'or');
}
$RCD = getDbArray($table['s_scrap'],$sqlque,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table['s_scrap'],$sqlque);
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
							<select name="category" class="form-control input-sm" onchange="this.form.submit();">
							<option value="">분류</option>
							<?php $_CATS = getDbSelect($table['s_scrap'],"mbruid=".$M['memberuid']." and category<>'' group by category",'category')?>
							<?php while($_R=db_fetch_array($_CATS)):?>
							<option value="<?php echo $_R['category']?>"<?php if($_R['category']==$category):?> selected="selected"<?php endif?>>ㆍ<?php echo $_R['category']?></option>
							<?php endwhile?>
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
						<th>분류</th>
						<th>제목</th>
						<th>날짜</th>
					</tr>
				</thead>
				<tbody>
					<?php while($R=db_fetch_array($RCD)):?>
				    <?php $R['mobile']=isMobileConnect($R['agent'])?>
					<tr>
						<td><input type="checkbox" name="members[]"  onclick="checkboxCheck();" class="mbr-act-list" value="<?php echo $R['uid']?>"></td>
						<td><?php echo $NUM-((($p-1)*$recnum)+$_rec++)?></td>
						<td><?php echo $R['category']?></td>
						<td class="rb-sbj">
					       <a href="<?php echo $R['url']?>" target="_blank"><?php echo $R['subject']?></a>
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
