<?php 
$SITES = getDbArray($table['s_site'],'','*','gid','asc',0,1);

$p		= $p ? $p : 1;
$recnum	= $recnum && $recnum < 201 ? $recnum : 20;
$sort	= $sort		? $sort		: 'hit';
$orderby= $orderby	? $orderby	: 'desc';

$_WHERE='uid';
if($account) $_WHERE .=' and site='.$account;
if ($d_start) $_WHERE .= ' and d_regis > '.str_replace('/','',$d_start).'000000';
if ($d_finish) $_WHERE .= ' and d_regis < '.str_replace('/','',$d_finish).'240000';

$_WHERE2= ' sum(hit) as hit';
$RCD	= getDbSelect($table['s_inkey'],$_WHERE.' group by keyword order by '.$sort.' '.$orderby.' limit 0,'.$recnum,$_WHERE2);
?> 

<div id="rb-count">
	<form class="form-horizontal rb-form" name="procForm" action="<?php echo $g['s']?>/" method="get">
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
		<div class="col-sm-3">	
			<select name="recnum" class="form-control input-sm" onchange="this.form.submit();">
				<option value="20"<?php if($recnum==20):?> selected<?php endif?>>20<?php echo _LANG('a2013','count'); //개?></option>
				<option value="50"<?php if($recnum==50):?> selected<?php endif?>>50<?php echo _LANG('a2013','count'); //개?></option>
				<option value="100"<?php if($recnum==100):?> selected<?php endif?>>100<?php echo _LANG('a2013','count'); //개?></option>
				<option value="150"<?php if($recnum==150):?> selected<?php endif?>>150<?php echo _LANG('a2013','count'); //개?></option>
				<option value="200"<?php if($recnum==200):?> selected<?php endif?>>200<?php echo _LANG('a2013','count'); //개?></option>
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


	<div class="kbox">
		<?php $j=0;while($G=db_fetch_array($RCD)):$j++?>

		<div class="keywordarea">
			<span class="num"><?php echo $j?>.</span> 
			<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;mod=search&amp;keyword=<?php echo urlencode($G['keyword'])?>" target="_blank" title="<?php echo $G['keyword']?>"><?php echo getStrCut($G['keyword'],6,'..')?></a> 
			<span class="hit">(<?php echo $G['hit']?><?php echo _LANG('a2012','count'); //건?>)</span>
		</div>

		<?php endwhile?>
		<?php if(!$j):?>
			<div class="nodata"><img src="<?php echo $g['img_core']?>/_public/ico_notice.gif" alt="" /> <?php echo _LANG('a2011','count'); //지정된 기간내에 기록된 엔터티가 없습니다.?></div>
		<?php endif?>
		<div class="clear"></div>
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
