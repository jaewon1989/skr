<?php 
$SITES = getDbArray($table['s_site'],'','*','gid','asc',0,1);
$year	= $year		? $year		: substr($date['today'],0,4);
$month	= $month	? $month	: substr($date['today'],4,2);
$day	= $day		? $day		: substr($date['today'],6,2);
$accountQue = $account ? 'site='.$account.' and ':'';
?>

<form class="form-inline" name="procForm" action="<?php echo $g['s']?>/" method="get">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="module" value="<?php echo $module?>" />
	<input type="hidden" name="front" value="<?php echo $front?>" />
	
	<div class="form-group">
		<label class="sr-only"><?php echo _LANG('a1011','count'); //사이트선택?></label>    
		<select name="account" class="form-control input-sm" onchange="this.form.submit();">
			<option value="">&nbsp;+ <?php echo _LANG('a1012','count'); //전체사이트?></option>

			<option value="">---------------------------</option>
			<?php while($S = db_fetch_array($SITES)):?>
			<option value="<?php echo $S['uid']?>"<?php if($account==$S['uid']):?> selected<?php endif?>>ㆍ<?php echo $S['name']?></option>
			<?php endwhile?>
			<?php if(!db_num_rows($SITES)):?>
			<option value=""><?php echo _LANG('a1013','count'); //등록된 사이트가 없습니다.?></option>
			<?php endif?>
		</select>
	</div>

	<div class="form-group">
		<label class="sr-only"><?php echo _LANG('a1014','count'); //년?></label> 
		<select name="year" class="form-control input-sm" onchange="this.form.submit();">
			<?php for($i=$date['year'];$i>2009;$i--):?><option value="<?php echo $i?>"<?php if($year==$i):?> selected<?php endif?>><?php echo $i?><?php echo _LANG('a1014','count'); //년?></option><?php endfor?>
		</select>
	</div>
	<div class="form-group">
		<label class="sr-only"><?php echo _LANG('a1015','count'); //월?></label> 
		<select name="month" class="form-control input-sm" onchange="this.form.submit();">
			<?php for($i=1;$i<13;$i++):?><option value="<?php echo sprintf('%02d',$i)?>"<?php if($month==$i):?> selected<?php endif?>><?php echo sprintf('%02d',$i)?><?php echo _LANG('a1015','count'); //월?></option><?php endfor?>
			<option value="-1"<?php if($month==-1):?> selected<?php endif?> class="mall"><?php echo _LANG('a1016','count'); //전체?></option>
		</select>
	</div>

	<input type="button" value="<?php echo substr($date['today'],0,4)?><?php echo _LANG('a1014','count'); //년?>" class="btn btn-primary btn-sm" onclick="this.form.year.value='<?php echo substr($date['today'],0,4)?>',this.form.month.value='-1',this.form.submit();" />
	<input type="button" value="<?php echo substr($date['today'],4,2)?><?php echo _LANG('a1015','count'); //월?>" class="btn btn-success btn-sm" onclick="this.form.year.value='<?php echo substr($date['today'],0,4)?>',this.form.month.value='<?php echo substr($date['today'],4,2)?>',this.form.submit();" />
</form>


<div class="table-responsive">
	<table class="table table-striped table-hover table-bordered">			
		<thead>
		<tr>
			<th width="100"><?php echo _LANG('a1017','count'); //날짜/구분?></th>
			<th width="150"><?php echo _LANG('a1019','count'); //순방문?></th>
			<th width="150"><?php echo _LANG('a1020','count'); //페이지뷰?></th>
			<th width="150"><?php echo _LANG('a1021','count'); //평균뷰?></th>
			<th width="150"><?php echo _LANG('a1022','count'); //모바일접속?></th>
			<th width="150"><?php echo _LANG('a1023','count'); //비율?></th>
		</tr>
		</thead>
	
		<tbody>
		<?php if($month>0):?>
		<?php $numofmonth = date('t',mktime(0,0,0,$month,$i,$year))?>
		<?php for($i = 1; $i <= $numofmonth; $i++):?>
		<tr>
			<td><?php echo sprintf('%02d',$month)?>/<?php echo sprintf('%02d',$i)?> (<?php echo getWeekday(date('w',mktime(0,0,0,$month,$i,$year)))?>)</td>
			<?php $DayOf1=getDbData($table['s_counter'],$accountQue."date='".$year.sprintf('%02d',$month).sprintf('%02d',$i)."'",'*')?>
			<?php $DayOf2=getDbCnt($table['s_browser'],'sum(hit)',$accountQue."date='".$year.sprintf('%02d',$month).sprintf('%02d',$i)."' and browser='Mobile'")?>
			<?php $TOT1+=$DayOf1['hit']?>
			<?php $TOT2+=$DayOf1['page']?>
			<?php $TOT3+=$DayOf2?>

			<td><?php echo $DayOf1['hit']?number_format($DayOf1['hit']):'&nbsp;'?></td>
			<td><?php echo $DayOf1['page']?number_format($DayOf1['page']):'&nbsp;'?></td>
			<td><?php echo $DayOf1['hit']?round($DayOf1['page']/$DayOf1['hit'],1):'&nbsp;'?></td>
			<td><?php echo $DayOf2?$DayOf2:'&nbsp;'?></td>
			<td><?php echo $DayOf2?round(($DayOf2/$DayOf1['hit'])*100,1).'%':'&nbsp;'?></td>
		</tr>
		<?php endfor?>
		<?php else:?>
		<?php for($i = 1; $i < 13; $i++):?>
		<tr>
			<td class="hand" onclick="document.procForm.month.value='<?php echo sprintf('%02d',$i)?>';document.procForm.submit();"><?php echo $year?> / <?php echo sprintf('%02d',$i)?></td>
			<?php $DayOf1=getDbData($table['s_counter'],$accountQue."date like '".$year.sprintf('%02d',$i)."%'",'sum(hit),sum(page)')?>
			<?php $DayOf2=getDbCnt($table['s_browser'],'sum(hit)',$accountQue."date like '".$year.sprintf('%02d',$i)."%' and browser='Mobile'")?>
			<?php $TOT1+=$DayOf1[0]?>
			<?php $TOT2+=$DayOf1[1]?>
			<?php $TOT3+=$DayOf2?>

			<td><?php echo $DayOf1[0]?number_format($DayOf1[0]):'&nbsp;'?></td>
			<td><?php echo $DayOf1[1]?number_format($DayOf1[1]):'&nbsp;'?></td>
			<td><?php echo $DayOf1[0]?round($DayOf1[1]/$DayOf1[0],1):'&nbsp;'?></td>
			<td><?php echo $DayOf2?$DayOf2:'&nbsp;'?></td>
			<td><?php echo $DayOf2?round(($DayOf2/$DayOf1[0])*100,1).'%':'&nbsp;'?></td>
		</tr>
		<?php endfor?>
		<?php endif?>

		<tr>
			<td><strong><?php echo _LANG('a1018','count'); //합계?></strong></td>
			<td><?php echo $TOT1?number_format($TOT1):'&nbsp;'?></td>
			<td><?php echo $TOT2?number_format($TOT2):'&nbsp;'?></td>
			<td><?php echo $TOT1?round($TOT2/$TOT1,1):'&nbsp;'?></td>
			<td><?php echo $TOT3?$TOT3:'&nbsp;'?></td>
			<td><?php echo $TOT3?round(($TOT3/$TOT1)*100,1).'%':'&nbsp;'?></td>
		</tr>
		</tbody>
	</table>
</div>

