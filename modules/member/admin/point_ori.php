<?php
$SITES = getDbArray($table['s_site'],'','*','gid','asc',0,1);
$year1	= $year1  ? $year1  : substr($date['today'],0,4);
$month1	= $month1 ? $month1 : substr($date['today'],4,2);
$day1	= $day1   ? $day1   : 1;//substr($date['today'],6,2);
$year2	= $year2  ? $year2  : substr($date['today'],0,4);
$month2	= $month2 ? $month2 : substr($date['today'],4,2);
$day2	= $day2   ? $day2   : substr($date['today'],6,2);


$type	= $type ? $type : 'point';
$sort	= $sort ? $sort : 'uid';
$orderby= $orderby ? $orderby : 'desc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 20;

$_WHERE = 'd_regis > '.$year1.sprintf('%02d',$month1).sprintf('%02d',$day1).'000000 and d_regis < '.$year2.sprintf('%02d',$month2).sprintf('%02d',$day2).'240000';
if ($flag == '+') $_WHERE .= ' and price > 0';
if ($flag == '-') $_WHERE .= ' and price < 0';
if ($where && $keyw)
{
	if ($keyw=='my_mbruid') $_WHERE .= ' and my_mbruid='.$keyw;
	else $_WHERE .= getSearchSql($where,$keyw,$ikeyword,'or');
}
$RCD = getDbArray($table['s_'.$type],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table['s_'.$type],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);
?>


<div id="pointlist">


	<div class="sbox">
		<form name="procForm" action="<?php echo $g['s']?>/" method="get">
		<input type="hidden" name="r" value="<?php echo $r?>" />
		<input type="hidden" name="m" value="<?php echo $m?>" />
		<input type="hidden" name="module" value="<?php echo $module?>" />
		<input type="hidden" name="front" value="<?php echo $front?>" />
		<input type="hidden" name="type" value="<?php echo $type?>" />

		<div>
		<select name="year1">
		<?php for($i=$date['year'];$i>2000;$i--):?><option value="<?php echo $i?>"<?php if($year1==$i):?> selected="selected"<?php endif?>><?php echo $i?>년</option><?php endfor?>
		</select>
		<select name="month1">
		<?php for($i=1;$i<13;$i++):?><option value="<?php echo sprintf('%02d',$i)?>"<?php if($month1==$i):?> selected="selected"<?php endif?>><?php echo sprintf('%02d',$i)?>월</option><?php endfor?>
		</select>
		<select name="day1">
		<?php for($i=1;$i<32;$i++):?><option value="<?php echo sprintf('%02d',$i)?>"<?php if($day1==$i):?> selected="selected"<?php endif?>><?php echo sprintf('%02d',$i)?>일(<?php echo getWeekday(date('w',mktime(0,0,0,$month1,$i,$year1)))?>)</option><?php endfor?>
		</select> ~
		<select name="year2">
		<?php for($i=$date['year'];$i>2000;$i--):?><option value="<?php echo $i?>"<?php if($year2==$i):?> selected="selected"<?php endif?>><?php echo $i?>년</option><?php endfor?>
		</select>
		<select name="month2">
		<?php for($i=1;$i<13;$i++):?><option value="<?php echo sprintf('%02d',$i)?>"<?php if($month2==$i):?> selected="selected"<?php endif?>><?php echo sprintf('%02d',$i)?>월</option><?php endfor?>
		</select>
		<select name="day2">
		<?php for($i=1;$i<32;$i++):?><option value="<?php echo sprintf('%02d',$i)?>"<?php if($day2==$i):?> selected="selected"<?php endif?>><?php echo sprintf('%02d',$i)?>일(<?php echo getWeekday(date('w',mktime(0,0,0,$month2,$i,$year2)))?>)</option><?php endfor?>
		</select>

		<input type="button" class="btn btn-default" value="기간적용" onclick="this.form.submit();" />
		<input type="button" class="btn btn-default" value="어제" onclick="dropDate('<?php echo date('Ymd',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-1,substr($date['today'],0,4)))?>','<?php echo date('Ymd',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-1,substr($date['today'],0,4)))?>');" />
		<input type="button" class="btn btn-default" value="오늘" onclick="dropDate('<?php echo $date['today']?>','<?php echo $date['today']?>');" />
		<input type="button" class="btn btn-default" value="일주" onclick="dropDate('<?php echo date('Ymd',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-7,substr($date['today'],0,4)))?>','<?php echo $date['today']?>');" />
		<input type="button" class="btn btn-default" value="한달" onclick="dropDate('<?php echo date('Ymd',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>','<?php echo $date['today']?>');" />
		<input type="button" class="btn btn-default" value="당월" onclick="dropDate('<?php echo substr($date['today'],0,6)?>01','<?php echo $date['today']?>');" />
		<input type="button" class="btn btn-default" value="전월" onclick="dropDate('<?php echo date('Ym',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>01','<?php echo date('Ym',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>31');" />
		<input type="button" class="btn btn-default" value="전체" onclick="dropDate('20090101','<?php echo $date['today']?>');" />
		</div>

		<div>
		<select name="flag" onchange="this.form.submit();">
		<option value="">&nbsp;+ 구분</option>
		<option value="">--------</option>
		<option value="+"<?php if($flag=='+'):?> selected="selected"<?php endif?>>획득</option>
		<option value="-"<?php if($flag=='-'):?> selected="selected"<?php endif?>>사용</option>
		</select>

		<select name="sort" onchange="this.form.submit();">
		<option value="uid"<?php if($sort=='uid'):?> selected="selected"<?php endif?>>등록일</option>
		<option value="price"<?php if($sort=='price'):?> selected="selected"<?php endif?>>금액</option>
		</select>
		<select name="orderby" onchange="this.form.submit();">
		<option value="desc"<?php if($orderby=='desc'):?> selected="selected"<?php endif?>>역순</option>
		<option value="asc"<?php if($orderby=='asc'):?> selected="selected"<?php endif?>>정순</option>
		</select>

		<select name="recnum" onchange="this.form.submit();">
		<option value="20"<?php if($recnum==20):?> selected="selected"<?php endif?>>20개</option>
		<option value="35"<?php if($recnum==35):?> selected="selected"<?php endif?>>35개</option>
		<option value="50"<?php if($recnum==50):?> selected="selected"<?php endif?>>50개</option>
		<option value="75"<?php if($recnum==75):?> selected="selected"<?php endif?>>75개</option>
		<option value="90"<?php if($recnum==90):?> selected="selected"<?php endif?>>90개</option>
		</select>

		<select name="where">
		<option value="content"<?php if($where=='content'):?> selected="selected"<?php endif?>>내용</option>
		<option value="my_mbruid"<?php if($where=='my_mbruid'):?> selected="selected"<?php endif?>>회원코드</option>
		</select>

		<input type="text" name="keyw" value="<?php echo stripslashes($keyw)?>" class="input" />

		<input type="submit" value="검색" class="btn btn-primary" />
		<input type="button" value="리셋" class="btn btn-default" onclick="location.href='<?php echo $g['adm_href']?>';" />
		</div>

		</form>
	</div>



	<form name="listForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $module?>" />
	<input type="hidden" name="a" value="" />
	<input type="hidden" name="pointType" value="<?php echo $type?>" />


	<div class="info">

		<div class="article">
			<?php echo number_format($NUM)?>건(<?php echo $p?>/<?php echo $TPG?>페이지)
			
			<span class="tx">
			<a class="<?php if($type=='point'):?>b <?php endif?>hand" onclick="document.procForm.type.value='point';document.procForm.submit();">포인트</a> |
			<a class="<?php if($type=='cash'):?>b <?php endif?>hand" onclick="document.procForm.type.value='cash';document.procForm.submit();">적립금</a> |
			<a class="<?php if($type=='money'):?>b <?php endif?>hand" onclick="document.procForm.type.value='money';document.procForm.submit();">예치금</a>
			</span>

		</div>
		
		<div class="category">

		</div>
		<div class="clear"></div>
	</div>


	<table class="table" summary="포인트 리스트입니다.">
	<caption>포인트리스트</caption> 
	<thead>
	<tr>
	<th scope="col" class="side1"><img src="<?php echo $g['img_core']?>/_public/ico_check_01.gif" alt="선택/반전" class="hand" onclick="chkFlag('comment_members[]');" /></th>
	<th scope="col">번호</th>
	<th scope="col">획득/사용자</th>
	<th scope="col">획득/사용액</th>
	<th scope="col">지급자</th>
	<th scope="col">내용</th>
	<th scope="col">날짜</th>
	<th scope="col" class="side2"></th>
	</tr>
	</thead>
	<tbody>

	<?php while($R=db_fetch_array($RCD)):?>
	<?php $M1=getDbData($table['s_mbrdata'],'memberuid='.$R['my_mbruid'],'*')?>
	<?php if($R['by_mbruid']){$M2=getDbData($table['s_mbrdata'],'memberuid='.$R['by_mbruid'],'*');}else{$M2=array();}?>
	<tr>
	<td><input type="checkbox" name="point_members[]" value="<?php echo $R['uid']?>" /></td>
	<td><?php echo $NUM-((($p-1)*$recnum)+$_rec++)?></td>
	<td><a href="javascript:OpenWindow('<?php echo $g['s']?>/?r=<?php echo $r?>&iframe=Y&m=<?php echo $module?>&front=manager&page=point&price=1&mbruid=<?php echo $M1['memberuid']?>&type=<?php echo $type?>');" title="획득/사용내역"><?php echo $M1[$_HS['nametype']]?></a></td>
	<td class="price<?php echo ($R['price']>0?1:2)?>"><?php echo number_format($R['price'])?></td>
	<td>
		<?php if($M2['memberuid']):?>
		<a href="javascript:OpenWindow('<?php echo $g['s']?>/?r=<?php echo $r?>&iframe=Y&m=<?php echo $module?>&front=manager&page=point&price=1&mbruid=<?php echo $M2['memberuid']?>&type=<?php echo $type?>'');" title="획득/사용내역"><?php echo $M1[$_HS['nametype']]?></a>
		<?php else:?>
		시스템
		<?php endif?>
	</td>
	<td class="sbj"><?php echo strip_tags($R['content'])?></td>
	<td><?php echo getDateFormat($R['d_regis'],'Y.m.d H:i')?></td>
	<td></td>
	</tr> 
	<?php endwhile?> 

	<?php if(!$NUM):?>
	<tr>
	<td><input type="checkbox" disabled="disabled" /></td>
	<td>1</td>
	<td colspan="6" class="sbj1">내역이 없습니다.</td>
	</tr> 
	<?php endif?>

	</tbody>
	</table>


	<div class="pagebox01">
	<script type="text/javascript">getPageLink(10,<?php echo $p?>,<?php echo $TPG?>,'<?php echo $g['img_core']?>/page/default');</script>
	</div>


	<div class="prebox">
		<input type="button" class="btn btn-default" value="선택/해제" onclick="chkFlag('point_members[]');" />
		<input type="button" class="btn btn-primary" value="삭제" onclick="actQue('point_multi_delete');" />
	</div>
	</form>

</div>

<div id="qTilePopDiv"></div>
<script type="text/javascript">
//<![CDATA[
function qTilePop(obj)
{
    var content ='<div style="width:300px;line-height:150%;font-family:dotum;color:#666666;border:#999999 solid 1px;padding:3px;background:lightyellow;">'+obj.title+'</div>';
	skn.style.position= 'absolute';
	skn.style.display = 'block';
	skn.style.zIndex = '1';
	itt = obj.title;
	obj.title = '';
	skn.innerHTML = content;
}
function get_mouse(e) 
{
    var x = myagent != 'ie' ? e.pageX : event.x+(document.body.scrollLeft || document.documentElement.scrollLeft);
    var y = myagent != 'ie' ? e.pageY : event.y+(document.body.scrollTop || document.documentElement.scrollTop);
    skn.style.left = (x - 0) + 'px';
    skn.style.top  = (y + 20) + 'px';
}
function qTilePopKill(obj) 
{
	obj.title = itt;
	itt = '';
	skn.style.top = '10000';
	skn.style.display = 'none';
}

if (myagent != 'ie') document.captureEvents(Event.MOUSEMOVE);
document.onmousemove = get_mouse;

var skn = getId('qTilePopDiv');
var itt = '';


function dropDate(date1,date2)
{
	var f = document.procForm;
	f.year1.value = date1.substring(0,4);
	f.month1.value = date1.substring(4,6);
	f.day1.value = date1.substring(6,8);
	
	f.year2.value = date2.substring(0,4);
	f.month2.value = date2.substring(4,6);
	f.day2.value = date2.substring(6,8);

	f.submit();
}
function actQue(flag)
{
	var f = document.listForm;
    var l = document.getElementsByName('point_members[]');
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
		alert('내역을 선택해 주세요.     ');
		return false;
	}
	
	
	if (flag == 'point_multi_delete')
	{
		if (!confirm('정말로 삭제하시겠습니까?     '))
		{
			return false;
		}
	}
	f.a.value = flag;
	f.submit();
}
//]]>
</script>
