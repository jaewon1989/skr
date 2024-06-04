<?php 
$SITES = getDbArray($table['s_site'],'','*','gid','asc',0,1);
$year	= $year		? $year		: substr($date['today'],0,4);
$month	= $month	? $month	: substr($date['today'],4,2);
$day	= $day		? $day		: substr($date['today'],6,2);
$accountQue = $account ? 'site='.$account.' and ':'';
$numarr = array(
'visit' => _LANG('a1001','count'),
'login' => _LANG('a1002','count'),
'mbrjoin' => _LANG('a1003','count'),
'mbrout' => _LANG('a1004','count'),
'comment' => _LANG('a1005','count'),
'oneline' => _LANG('a1006','count'),
'upload' => _LANG('a1007','count'),
'download' => _LANG('a1008','count'),
'rcvtrack' => _LANG('a1009','count'),
'sndtrack' => _LANG('a1010','count')
);  //방문자,로그인,회원가입,탈퇴,댓글,한줄의견,파일첨부,다운로드,받은트랙백,보낸트랙백
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
			<option value="-1"<?php if($month==-1):?> selected<?php endif?>><?php echo _LANG('a1016','count'); //전체?></option>
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
			<?php foreach($numarr as $key => $val):?>
			<th width="100"><?php echo $val?></th>
			<?php endforeach?>
		</tr>
		</thead>
	
		<tbody>
		<?php $_S=array()?>
		<?php if($month>0):?>
		<?php $numofmonth = date('t',mktime(0,0,0,$month,$i,$year))?>
		<?php for($i = 1; $i <= $numofmonth; $i++):?>
		<tr>
			<td><?php echo sprintf('%02d',$month)?>/<?php echo sprintf('%02d',$i)?> (<?php echo getWeekday(date('w',mktime(0,0,0,$month,$i,$year)))?>)</td>
			<?php $_D=getDbData($table['s_numinfo'],$accountQue."date='".$year.sprintf('%02d',$month).sprintf('%02d',$i)."'",'*')?>
			<?php foreach($numarr as $key => $val):$_S[$key]+=$_D[$key]?>
			<td><?php echo $_D[$key]?number_format($_D[$key]):'&nbsp;'?></td>
			<?php endforeach?>
		</tr>
		<?php endfor?>
		<?php else:?>
		<?php foreach($numarr as $key => $val):?>
		<?php $_sumque.='sum('.$key.'),'?>
		<?php endforeach?>
		<?php $_sumque=substr($_sumque,0,strlen($_sumque)-1)?>
		<?php for($i = 1; $i < 13; $i++):?>
		<tr>
			<td class="hand" onclick="document.procForm.month.value='<?php echo sprintf('%02d',$i)?>';document.procForm.submit();"><?php echo $year?> / <?php echo sprintf('%02d',$i)?></td>
			<?php $_D=getDbData($table['s_numinfo'],$accountQue."date like '".$year.sprintf('%02d',$i)."%'",$_sumque)?>
			<?php $j=0;foreach($numarr as $key => $val):$_S[$key]+=$_D[$j]?>
			<td><?php echo $_D[$j]?number_format($_D[$j]):'&nbsp;'?></td>
			<?php $j++;endforeach?>
		</tr>
		<?php endfor?>
		<?php endif?>

		<tr>
			<td><strong><?php echo _LANG('a1018','count'); //합계?></strong></td>
			<?php foreach($numarr as $key => $val):?>
			<td><?php echo $_S[$key]?number_format($_S[$key]):'&nbsp;'?></td>
			<?php endforeach?>
		</tr>
		</tbody>
	</table>
</div>

