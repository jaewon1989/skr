<?php 
$SITES = getDbArray($table['s_site'],'','*','gid','asc',0,1);

$_WHERE='uid';
if($account) $_WHERE .=' and site='.$account;
if ($d_start) $_WHERE .= ' and d_regis > '.str_replace('/','',$d_start).'000000';
if ($d_finish) $_WHERE .= ' and d_regis < '.str_replace('/','',$d_finish).'240000';

$DATNUM = getDbCnt($table['s_browser'],'sum(hit)',$_WHERE);
$brset = array('MSIE 11','MSIE 10','MSIE 9','MSIE 8','MSIE 7','MSIE 6','Firefox','Opera','Chrome','Safari','Mobile','');
?>

<div id="rb-count">
	<form class="form-horizontal rb-form"  role="form" name="procForm" action="<?php echo $g['s']?>/" method="get">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="module" value="<?php echo $module?>" />
	<input type="hidden" name="front" value="<?php echo $front?>" />

	<div class="form-group">
		<div class="col-sm-3">		
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
	</div>

	<div class="form-group">
		<div class="col-sm-5">
			<div class="input-daterange input-group" id="datepicker">
				<input type="text" class="form-control" name="d_start" placeholder="<?php echo _LANG('a2014','count'); //시작일 선택?>" value="<?php echo $d_start?>">
				<span class="input-group-addon">~</span>
				<input type="text" class="form-control" name="d_finish" placeholder="<?php echo _LANG('a2015','count'); //종료일 선택?>" value="<?php echo $d_finish?>">
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit"><?php echo _LANG('a2016','count'); //기간적용?></button>
				</span>
			</div>
		</div>
		<div class="col-sm-3 hidden-xs">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" onclick="dropDate('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-1,substr($date['today'],0,4)))?>','<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-1,substr($date['today'],0,4)))?>');"><?php echo _LANG('a2017','count'); //어제?></button>
				<button class="btn btn-default" type="button" onclick="dropDate('<?php echo getDateFormat($date['today'],'Y/m/d')?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');"><?php echo _LANG('a2018','count'); //오늘?></button>
				<button class="btn btn-default" type="button" onclick="dropDate('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-7,substr($date['today'],0,4)))?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');"><?php echo _LANG('a2019','count'); //일주?></button>
				<button class="btn btn-default" type="button" onclick="dropDate('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');"><?php echo _LANG('a2020','count'); //한달?></button>
				<button class="btn btn-default" type="button" onclick="dropDate('<?php echo getDateFormat(substr($date['today'],0,6).'01','Y/m/d')?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');"><?php echo _LANG('a2021','count'); //당월?></button>
				<button class="btn btn-default" type="button" onclick="dropDate('<?php echo date('Y/m/',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>01','<?php echo date('Y/m/',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>31');"><?php echo _LANG('a2022','count'); //전월?></button>
				<button class="btn btn-success" type="button" onclick="dropDate('','');"><?php echo _LANG('a1016','count'); //전체?></button>
			</span>
		</div>
	</div>

	</form>


	<div class="table-responsive">
		<table id="grptbl">
			<colgroup> 
			<col width="8.25%"> 
			<col width="8.25%"> 
			<col width="8.25%"> 
			<col width="8.25%"> 
			<col width="8.25%"> 
			<col width="8.25%"> 
			<col width="8.25%"> 
			<col width="8.25%"> 
			<col width="8.25%"> 
			<col width="8.25%"> 
			<col width="8.25%">
			<col width="8.25%">
			</colgroup>
			<thead>
			<tr class="grptr">
				<?php foreach($brset as $val):?>
				<?php $numOfBrowser=getDbCnt($table['s_browser'],'sum(hit)',$_WHERE." and browser='".$val."'")?>
				<th><?php if($numOfBrowser):?><div class="info"><?php echo number_format($numOfBrowser)?><br /><span>(<?php echo @intval($numOfBrowser/$DATNUM*100)?>%)</span></div><div class="grp" style="height:<?php echo @intval($numOfBrowser/$DATNUM*330)?>px;"></div><?php endif?></th>
				<?php endforeach?>
				<th></th>
			</tr>
			<thead>
			<tbody>
			<tr class="tabtr">
				<?php foreach($brset as $val):?>
				<td><?php echo $val?$val:_LANG('a2023','count'); //'기타'?></td>
				<?php endforeach?>
				<td></td>
			</tr>
			</tbody>
		</table>
	</div>
</div>

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

<script type="text/javascript">
//<![CDATA[
function dropDate(date1,date2)
{
	var f = document.procForm;
	f.d_start.value = date1;
	f.d_finish.value = date2;
	f.submit();
}
//]]>
</script>
