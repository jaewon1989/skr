<?php
include_once $g['dir_module_skin'].'_pc/my/_menu.php';
$year	= $year		? $year		: substr($date['today'],0,4);
?>




<form name="procForm" action="<?php echo $g['s']?>/" method="get">
<input type="hidden" name="r" value="<?php echo $r?>" />
<input type="hidden" name="m" value="<?php echo $m?>" />
<input type="hidden" name="page" value="<?php echo $page?>" />

<div class="sbox">

	<select name="year" onchange="this.form.submit();">
	<?php for($i=2009;$i<substr($date['today'],0,4)+1;$i++):?><option value="<?php echo $i?>"<?php if($year==$i):?> selected="selected"<?php endif?>><?php echo $i?>년</option><?php endfor?>
	</select>
    - 지급율 : <?php echo $MYMK['per']?>% / 지급계좌 : <?php echo $MYMK['bank']?> / 지급일 : 매월 <?php echo $d['qmarket']['jsday']?>일

 <?//php echo number_format($d['qmarket']['jsprice'])?>
</div>

</form>


<table cellspacing="1">
	<thead>
	<tr class="sbjtr">
		<th>지급월</th>
		<th>매출액</th>
		<th>지급율</th>
		<th>지급액</th>
		<th>지급일</th>
	</tr>
	</thead>
	
	<tbody>
	<?php for($i = 1; $i < 13; $i++):?>
	<tr class="looptr">
		<td class="datetd"><?php echo $year?> / <?php echo sprintf('%02d',$i)?></td>
		<?php $DayOf1=getDbData($table[$m.'check'],"mbruid=".$_useruid." and d_give like '".$year.sprintf('%02d',$i)."%'",'*')?>
		<?php $TOT1+=$DayOf1['price_total']?>
		<?php $TOT2+=$DayOf1['price_give']?>

		<td class="sumtd1"><?php echo $DayOf1['price_total']?number_format($DayOf1['price_total']):'&nbsp;'?></td>
		<td class="sumtd1"><?php echo $DayOf1['per']?$DayOf1['per'].'%':'&nbsp;'?></td>
		<td class="sumtd2"><?php echo $DayOf1['price_give']?number_format($DayOf1['price_give']):'&nbsp;'?></td>
		<td class="sumtd2"><?php echo $DayOf1['uid']?getDateFormat($DayOf1['d_give'],'Y.m.d'):'&nbsp;'?></td>
	</tr>
	<?php endfor?>

	<tr class="sumtr">
		<td class="datetd"><b>합 계</b></td>
		<td class="sumtd1"><?php echo $TOT1?number_format($TOT1):'&nbsp;'?></td>
		<td class="sumtd1">-</td>
		<td class="sumtd2"><?php echo $TOT2?number_format($TOT2):'&nbsp;'?></td>
		<td class="sumtd2">-</td>
	</tr>
	</tbody>
</table>

<div class="sbox">
- 마지막 정산 이후 누적 판매액이 30,000원 미만 일 경우에 정산을 이월 합니다.
</div>