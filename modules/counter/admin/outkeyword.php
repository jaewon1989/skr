<?php 
$SITES = getDbArray($table['s_site'],'','*','gid','asc',0,1);
$searchset1 = array('naver','nate','daum','yahoo','google','etc');
$searchset2 = array
(
	_LANG('a2001','count')=>'http://search.naver.com/search.naver?query=',
	_LANG('a2002','count')=>'http://search.nate.com/search/all.html?q=',
	_LANG('a2003','count')=>'http://search.daum.net/search?q=',
	_LANG('a2004','count')=>'http://search.yahoo.com/search?p=',
	_LANG('a2005','count')=>'http://www.google.com/search?q=',
	_LANG('a2023','count')=>$g['s'].'/?r='.$r.'&amp;mod=search&amp;keyword='
);

$p		= $p ? $p : 1;
$recnum	= $recnum && $recnum < 201 ? $recnum : 20;
$sort	= $sort		? $sort		: 'total';
$orderby= $orderby	? $orderby	: 'desc';

$_WHERE='uid';
if($account) $_WHERE .=' and site='.$account;
if ($d_start) $_WHERE .= ' and d_regis > '.str_replace('/','',$d_start).'000000';
if ($d_finish) $_WHERE .= ' and d_regis < '.str_replace('/','',$d_finish).'240000';

if($where) $_WHERE .= ' and '.$where.'>0';
$_WHERE2= 'keyword,sum(naver) as naver,sum(nate) as nate,sum(daum) as daum,sum(yahoo) as yahoo,sum(google) as google,sum(etc) as etc,sum(total) as total';
$RCD	= getDbSelect($table['s_outkey'],$_WHERE.' group by keyword order by '.$sort.' '.$orderby.' limit 0,'.$recnum,$_WHERE2);
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
		<div class="col-sm-3">
			<select name="where" class="form-control input-sm" onchange="this.form.submit();">
				<option value=""><?php echo _LANG('a2006','count'); //전체유입량?></option>
				<option value="naver"<?php if($where=='naver'):?> selected<?php endif?>><?php echo _LANG('a2001','count'); //네이버?></option>
				<option value="nate"<?php if($where=='nate'):?> selected<?php endif?>><?php echo _LANG('a2002','count'); //네이트?></option>
				<option value="daum"<?php if($where=='daum'):?> selected<?php endif?>><?php echo _LANG('a2003','count'); //다음?></option>
				<option value="yahoo"<?php if($where=='yahoo'):?> selected<?php endif?>><?php echo _LANG('a2004','count'); //야후?></option>
				<option value="google"<?php if($where=='google'):?> selected<?php endif?>><?php echo _LANG('a2005','count'); //구글?></option>
				<option value="etc"<?php if($where=='etc'):?> selected<?php endif?>><?php echo _LANG('a2023','count'); //기타?></option>
			</select>
		</div>
		<div class="col-sm-2">
			<select name="recnum" class="form-control input-sm" onchange="this.form.submit();">
				<option value="10"<?php if($recnum==10):?> selected<?php endif?>>10<?php echo _LANG('a2013','count'); //개?></option>
				<option value="20"<?php if($recnum==20):?> selected<?php endif?>>20<?php echo _LANG('a2013','count'); //개?></option>
				<option value="35"<?php if($recnum==35):?> selected<?php endif?>>35<?php echo _LANG('a2013','count'); //개?></option>
				<option value="50"<?php if($recnum==50):?> selected<?php endif?>>50<?php echo _LANG('a2013','count'); //개?></option>
				<option value="75"<?php if($recnum==75):?> selected<?php endif?>>75<?php echo _LANG('a2013','count'); //개?></option>
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



	<div class="table-responsive">
		<table class="table table-striped table-hover table-bordered">		
			<thead>
			<tr>
				<th width="50"><?php echo _LANG('a2007','count'); //순위?></th>
				<th><?php echo _LANG('a2008','count'); //유입엔터티?></th>
				<?php $i=0;foreach($searchset2 as $key=>$val):?>
				<th width="110"><a href="<?php echo $val?>" target="_blank"><?php if($key==_LANG('a2023','count')):?><?php echo _LANG('a2023','count'); //기타?><?php else:?><img src="<?php echo $g['img_module_admin']?>/ico_<?php echo $searchset1[$i]?>.gif" alt="<?php echo strtoupper($searchset1[$i])?>" /><?php endif?></a></th>
				<?php $i++;endforeach?>
				<th width="120"><?php echo _LANG('a1018','count'); //합계?></th>		
			</tr>
			</thead>
			
			<tbody>
			<?php $j=0;while($G=db_fetch_array($RCD)):$j++?>

			<tr>
				<td><?php echo $j?></td>
				<td class="sbj"><?php echo $G['keyword']?></td>
				<?php $k=0;foreach($searchset2 as $key=>$val):?>
				<td><a href="<?php echo $val.urlencode($G['keyword'])?>" target="_blank"><?php echo $G[$searchset1[$k]]?$G[$searchset1[$k]]:''?></a></td>
				<?php $k++;endforeach?>
				<td><?php echo number_format($G['total'])?></td>
			</tr>

			<?php endwhile?>
			</tbody>
		</table>
		<?php if(!$j):?>
			<div class="rb-none"><?php echo _LANG('a2009','count'); //지정된 기간내에 유입된 엔터티가 없습니다.?></div>
		<?php endif?>
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
