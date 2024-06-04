<?php
if ($p > 200) getLink('','',_LANG('a2010','count'),'-1');  //200페이지까지만 조회가능합니다.

$SITES = getDbArray($table['s_site'],'','*','gid','asc',0,1);

$p		= $p ? $p : 1;
$recnum	= $recnum && $recnum < 201 ? $recnum : 20;

$sort	= $sort		? $sort		: 'uid';
$orderby= $orderby	? $orderby	: 'desc';

$_WHERE='uid';
if($account) $_WHERE .=' and site='.$account;
if ($d_start) $_WHERE .= ' and d_regis > '.str_replace('/','',$d_start).'000000';
if ($d_finish) $_WHERE .= ' and d_regis < '.str_replace('/','',$d_finish).'240000';
if ($where && $keyw) $_WHERE .= " and ".$where." like '%".trim($keyw)."%'";

$RCD = getDbArray($table['s_referer'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table['s_referer'],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);
?>


<div id="rb-count">
	<form class="form-horizontal rb-form"  role="form" name="procForm" action="<?php echo $g['s']?>/" method="get">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="module" value="<?php echo $module?>" />
	<input type="hidden" name="front" value="<?php echo $front?>" />

	<div class="rb-heading well well-sm">
		<div class="form-group">
			<label class="col-sm-1 control-label"><?php echo _LANG('a2024','count'); //필터?></label>
			<div class="col-sm-10">
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

		<!-- 고급검색 시작 -->
		<div id="search-more" class="collapse<?php if($_SESSION['sh_countpost']):?> in<?php endif?>">
			<div class="form-group">
				<label class="col-sm-1 control-label"><?php echo _LANG('a2025','count'); //기간?></label>
				<div class="col-sm-10">
					<div class="row">
						<div class="col-sm-5">
							<div class="input-daterange input-group input-group-sm" id="datepicker">
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
				</div>
			</div>

			<div class="form-group hidden-xs">
				<label class="col-sm-1 control-label"><?php echo _LANG('a2026','count'); //정렬?></label>
				<div class="col-sm-10">
					<div class="row">
						<div class="col-sm-3">
							<select class="form-control input-sm" name="sort" onchange="this.form.submit();">
								<option value="uid"<?php if($sort=='uid'):?> selected<?php endif?>><?php echo _LANG('a2027','count'); //접속순?></option>
								<option value="mbruid"<?php if($sort=='mbruid'):?> selected<?php endif?>><?php echo _LANG('a2028','count'); //회원별?></option>
							</select>
						</div>
						<div class="col-sm-2">	
							<select class="form-control input-sm" name="orderby" onchange="this.form.submit();">
								<option value="desc"<?php if($orderby=='desc'):?> selected<?php endif?>><?php echo _LANG('a2029','count'); //역순?></option>
								<option value="asc"<?php if($orderby=='asc'):?> selected<?php endif?>><?php echo _LANG('a2030','count'); //정순?></option>
							</select>
						</div>
						
						<label class="col-sm-2 control-label"><?php echo _LANG('a2031','count'); //출력?></label>
						<div class="col-sm-2">
							<select class="form-control input-sm" name="recnum" onchange="this.form.submit();">
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
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-1 control-label"><?php echo _LANG('a2032','count'); //검색?></label>
				<div class="col-sm-8">
					<div class="input-group input-group-sm">
						<span class="input-group-btn" style="width:110px">
							<select name="where"  class="form-control btn btn-default">
								<option value="ip"<?php if($where=='ip'):?> selected<?php endif?>>IP</option>
								<option value="id"<?php if($where=='id'):?> selected<?php endif?>><?php echo _LANG('a2033','count'); //회원UID?></option>
								<option value="referer"<?php if($where=='referer'):?> selected<?php endif?>><?php echo _LANG('a2034','count'); //접속경로?></option>
							</select>
						</span>
						<input type="text" name="keyw" value="<?php echo stripslashes($keyw)?>" class="form-control">
						<span class="input-group-btn">
							<button class="btn btn-primary" type="submit"><?php echo _LANG('a2032','count'); //검색?></button>
						</span>
						<span class="input-group-btn">
							<button class="btn btn-default" type="button" onclick="location.href='<?php echo $g['adm_href']?>';"><?php echo _LANG('a2035','count'); //리셋?></button>
						</span>
					</div>
				</div>
			</div>

		</div>

		<div class="form-group">
			<div class="col-sm-offset-1 col-sm-10">
				<button type="button" class="btn btn-link rb-advance<?php if(!$_SESSION['sh_countpost']):?> collapsed<?php endif?>" data-toggle="collapse" data-target="#search-more" onclick="sessionSetting('sh_countpost','1','','1');"><?php echo _LANG('a2036','count'); //고급검색?><small></small></button>
				<a href="<?php echo $g['adm_href']?>&amp;account=<?php echo $account?>" class="btn btn-link"><?php echo _LANG('a2037','count'); //초기화?></a>
			</div>
		</div>
	</div>
	
	</form>


	<small class="margin-left-15"><?php echo number_format($NUM)?> <?php echo _LANG('a2013','count'); //개?> ( <?php echo $p?> / <?php echo $TPG.($TPG>1?' pages':' page')?> )</small>


	<form role="form" name="listForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $module?>" />
	<input type="hidden" name="a" value="" />

	<div class="table-responsive">
		<table class="table table-striped table-hover">			
			<thead>
			<tr>
				<th width="30"><span data-tooltip="tooltip" title="<?php echo _LANG('a2038','count'); //선택?>" class="glyphicon glyphicon-ok-circle hand" onclick="chkFlag('members[]');"></span></th>
				<th width="70"><?php echo _LANG('a2039','count'); //번호?></th>
				<th>IP</th>
				<th><?php echo _LANG('a2040','count'); //회원여부?></th>
				<th><?php echo _LANG('a2034','count'); //접속경로?></th>
				<th><?php echo _LANG('a2041','count'); //브라우져?></th>
				<th><?php echo _LANG('a2042','count'); //엔터티?></th>
				<th width="105"><?php echo _LANG('a2043','count'); //접속시간?></th>
			</tr>
			</thead>
			
			<tbody>
			<?php $j=0;while($R=db_fetch_array($RCD)):$j++?>
			<?php $_engine = getSearchEngine($R['referer'])?>
			<?php $_outkey = getKeyword($R['referer'])?>
			<?php $_browse = getBrowzer($R['agent'])?>
			<?php $_domain = getDomain($R['referer'])?>
			<?php $_mobile = isMobileConnect($R['agent'])?>

			<tr>
				<td><input type="checkbox" name="members[]" value="<?php echo $R['uid']?>" /></td>
				<td><?php echo ($NUM-((($p-1)*$recnum)+$_recnum++))?></td>
				<td><a href="http://domain.whois.co.kr/whois/?domain=<?php echo $R['ip']?>" title="<?php echo _LANG('a2044','count'); //후이즈 IP정보?>"><?php echo $R['ip']?></a></td>
				<td>
					<?php if($R['mbruid']):?>
					<?php $M=getDbData($table['s_mbrdata'],'memberuid='.$R['mbruid'],'*')?>
					<a href="javascript:OpenWindow('<?php echo $g['s']?>/?r=<?php echo $r?>&iframe=Y&m=member&front=manager&page=log&mbruid=<?php echo $M['memberuid']?>');" title="<?php echo _LANG('a2045','count'); //접속기록?>"><?php echo $M[$_HS['nametype']]?></a>
					<?php endif?>
				</td>
				<td><a href="<?php echo $R['referer']?>" target="_blank"><?php if($_engine=='etc'):?><?php echo $_domain?><?php else:?><img src="<?php echo $g['img_module_admin']?>/ico_<?php echo $_engine?>.gif" title="<?php echo $_domain?>" /><?php endif?></a></td>
				<td>
					<?php if($_mobile):?>
					<i class="glyphicon glyphicon-phone" data-toggle="tooltip" title="<?php echo $_mobile.' '._LANG('a2046','count'); //접속?>"></i> 
					<?php endif?>		
					<?php echo strtoupper($_browse)?>
				</td>
				<td class="sbj"><a href="<?php echo $R['referer']?>" target="_blank"><?php echo $_outkey?></a></td>
				<td><?php echo getDateFormat($R['d_regis'],'Y.m.d H:i:s')?></td>
			</tr>

			<?php endwhile?>
			</tbody>
		</table>

		<?php if(!$j):?>
			<div class="rb-none"><?php echo _LANG('a2047','count'); //지정된 기간내에 유입된 접속기록이 없습니다.?></div>
		<?php endif?>
	</div>

	<div class="rb-footer clearfix">
		<div class="pull-right">
			<ul class="pagination">
				<script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
				<?php //echo getPageLink(5,$p,$TPG,'')?>
			</ul>
		</div>

		<div>
			<button type="button" onclick="chkFlag('members[]');" class="btn btn-default btn-sm"><?php echo _LANG('a2048','count'); //선택/해제?></button>
			<button type="button" onclick="actQue('referer_delete');" class="btn btn-default btn-sm"><?php echo _LANG('a2049','count'); //삭제?></button>
		</div>
	</div>

	</form>
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
function actQue(flag)
{
	var f = document.listForm;
    var l = document.getElementsByName('members[]');
    var n = l.length;
    var i;
	var j=0;
	var s='';

	for	(i = 0; i < n; i++)
	{
		if (l[i].checked == true)
		{
			j++;
			s += l[i].value +',';
		}
	}
	if (!j)
	{
		alert('<?php echo _LANG('a2050','count'); //내역을 선택해 주세요.?>');
		return false;
	}
	
	
	if (flag == 'referer_delete')
	{
		if (!confirm('<?php echo _LANG('a2051','count'); //정말로 삭제하시겠습니까??>'))
		{
			return false;
		}
	}
	f.a.value = flag;
	f.submit();
}
//]]>
</script>
