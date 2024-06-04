<?php 
include_once $g['dir_module_skin'].'_pc/my/_menu.php';

$year	= $year		? $year		: substr($date['today'],0,4);
$month	= $month	? $month	: substr($date['today'],4,2);
$day	= $day		? $day		: substr($date['today'],6,2);
?>




<form name="procForm" action="<?php echo $g['s']?>/" method="get">
<input type="hidden" name="r" value="<?php echo $r?>" />
<input type="hidden" name="m" value="<?php echo $m?>" />
<input type="hidden" name="page" value="<?php echo $page?>" />

<div class="sbox">

	<select name="year" onchange="this.form.submit();">
	<?php for($i=2009;$i<substr($date['today'],0,4)+1;$i++):?><option value="<?php echo $i?>"<?php if($year==$i):?> selected="selected"<?php endif?>><?php echo $i?>년</option><?php endfor?>
	</select>
	<select name="month" onchange="this.form.submit();">
	<?php for($i=1;$i<13;$i++):?><option value="<?php echo sprintf('%02d',$i)?>"<?php if($month==$i):?> selected="selected"<?php endif?>><?php echo sprintf('%02d',$i)?>월</option><?php endfor?>
	<option value="-1"<?php if($month==-1):?> selected="selected"<?php endif?> class="mall">전체</option>
	</select>

	<input type="button" value="<?php echo substr($date['today'],0,4)?>년" class="btngray" onclick="this.form.year.value='<?php echo substr($date['today'],0,4)?>',this.form.month.value='-1',this.form.submit();" />
	<input type="button" value="<?php echo substr($date['today'],4,2)?>월" class="btngray" onclick="this.form.year.value='<?php echo substr($date['today'],0,4)?>',this.form.month.value='<?php echo substr($date['today'],4,2)?>',this.form.submit();" />

</div>

</form>


<table cellspacing="1">
	<thead>
	<tr class="sbjtr">
		<th>날짜/구분</th>
		<th>판매건수</th>
		<th>판매금액</th>
		<th>정산금액</th>
	</tr>
	</thead>
	
	<tbody>
	<?php if($month>0):?>
	<?php $numofmonth = date('t',mktime(0,0,0,$month,$i,$year))?>
	<?php for($i = 1; $i <= $numofmonth; $i++):?>
	<tr class="looptr">
		<td class="datetd"><?php echo sprintf('%02d',$month)?>/<?php echo sprintf('%02d',$i)?> (<?php echo getWeekday(date('w',mktime(0,0,0,$month,$i,$year)))?>)</td>
		<?php $DayOf1=getDbData($table[$m.'orders'],"b_mbruid=".$my['uid']." and orderstep=1 and d_bank like '".$year.sprintf('%02d',$month).sprintf('%02d',$i)."%'",'count(*),sum(price),sum(g_price)')?>
		<?php $TOT1+=$DayOf1[0]?>
		<?php $TOT2+=$DayOf1[1]?>
		<?php $TOT3+=$DayOf1[2]?>

		<td class="sumtd1"><?php echo $DayOf1[0]?number_format($DayOf1[0]):'&nbsp;'?></td>
		<td class="sumtd1"><?php echo $DayOf1[1]?number_format($DayOf1[1]):'&nbsp;'?></td>
		<td class="sumtd2"><?php echo $DayOf1[2]?number_format($DayOf1[2]):'&nbsp;'?></td>
	</tr>
	<?php endfor?>
	<?php else:?>
	<?php for($i = 1; $i < 13; $i++):?>
	<tr class="looptr">
		<td class="datetd hand" onclick="document.procForm.month.value='<?php echo sprintf('%02d',$i)?>';document.procForm.submit();"><?php echo $year?> / <?php echo sprintf('%02d',$i)?></td>
		<?php $DayOf1=getDbData($table[$m.'orders'],"b_mbruid=".$my['uid']." and orderstep=1 and d_bank like '".$year.sprintf('%02d',$i)."%'",'count(*),sum(price),sum(g_price)')?>
		<?php $TOT1+=$DayOf1[0]?>
		<?php $TOT2+=$DayOf1[1]?>
		<?php $TOT3+=$DayOf1[2]?>

		<td class="sumtd1"><?php echo $DayOf1[0]?number_format($DayOf1[0]):'&nbsp;'?></td>
		<td class="sumtd1"><?php echo $DayOf1[1]?number_format($DayOf1[1]):'&nbsp;'?></td>
		<td class="sumtd2"><?php echo $DayOf1[2]?number_format($DayOf1[2]):'&nbsp;'?></td>
	</tr>
	<?php endfor?>
	<?php endif?>

	<tr class="sumtr">
		<td class="datetd"><b>합 계</b></td>
		<td class="sumtd1"><?php echo $TOT1?number_format($TOT1):'&nbsp;'?></td>
		<td class="sumtd1"><?php echo $TOT2?number_format($TOT2):'&nbsp;'?></td>
		<td class="sumtd2"><?php echo $TOT3?number_format($TOT3):'&nbsp;'?></td>
	</tr>
	</tbody>
</table>

