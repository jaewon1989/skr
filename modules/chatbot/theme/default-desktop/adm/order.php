<?php
include_once $g['dir_module_skin'].'_pc/my/_menu.php';

$sort	= $sort ? $sort : 'uid';
$orderby= $orderby ? $orderby : 'desc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 20;

$sqlque = 'b_mbruid='.$_useruid;
if ($orderstep) $sqlque .= ' and orderstep='.($orderstep-1);
if ($where && $keyword)
{
	$sqlque .= getSearchSql($where,$keyword,$ikeyword,'or');
}
$RCD = getDbArray($table[$m.'orders'],$sqlque,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$m.'orders'],$sqlque);
$TPG = getTotalPage($NUM,$recnum);
$payset = array('','무통장','신용카드','계좌이체','핸드폰');
?>


<div id="orderlist">

	<form action="<?php echo $g['s']?>/">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="page" value="<?php echo $page?>" />
	<div class="info">

		<div class="article">
			<?php echo number_format($NUM)?>개(<?php echo $p?>/<?php echo $TPG?>페이지)
		</div>
		<div class="category">

			<select name="orderstep" onchange="this.form.submit();">
			<option value="">&nbsp;+ 상태</option>
			<option value="">-------------</option>
			<option value="1"<?php if($orderstep=='1'):?> selected="selected"<?php endif?>>미입금</option>
			<option value="2"<?php if($orderstep=='2'):?> selected="selected"<?php endif?>>입금</option>
			</select>

			&nbsp;&nbsp;
			<select name="where">
			<option value="s_name"<?php if($where=='s_name'):?> selected="selected"<?php endif?>>주문인</option>
			<option value="b_name"<?php if($where=='b_name'):?> selected="selected"<?php endif?>>입금인</option>
			</select>
			
			<input type="text" name="keyword" size="20" value="<?php echo $_keyword?>" class="input" />
			<input type="submit" value=" 검색 " class="btngray" />
			<input type="button" value=" 리셋 " class="btngray" onclick="this.form.keyword.value='';this.form.submit();" />

		</div>
		<div class="clear"></div>
	</div>
	</form>

	<form name="procForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return submitCheck(this);">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="a" value="" />

	<table summary="상품주문 리스트입니다.">
	<caption>상품주문</caption> 
	<colgroup> 
	<col width="30"> 
	<col width="50"> 
	<col> 
	<col width="60"> 
	<col width="70"> 
	<col width="80"> 
	<col width="70"> 
	<col width="80"> 
	</colgroup> 


	<thead>
	<tr>
	<th scope="col" class="side1"><img src="<?php echo $g['img_core']?>/_public/ico_check_01.gif" class="hand" alt="" onclick="chkFlag('members[]');" /></th>
	<th scope="col">번호</th>
	<th scope="col">상품명</th>
	<th scope="col">판매가</th>
	<th scope="col">구매자</th>
	<th scope="col">주문일</th>
	<th scope="col">입금일</th>
	<th scope="col" class="side2">정산일</th>
	</tr>
	</thead>
	<tbody>

	<?php while($R=db_fetch_array($RCD)):?>
	<?php $G=getUidData($table[$m.'goods'],$R['goodsuid'])?>
	<tr>
	<td>
		<?php if(!$R['orderstep']):?>
		<input type="checkbox" name="members[]" value="<?php echo $R['uid']?>" />
		<?php else:?>
		<input type="checkbox" disabled="disabled" />
		<?php endif?>
	</td>
	<td><?php echo $NUM-((($p-1)*$recnum)+$_rec++)?></td>
	<td class="sbj">
		<a href="<?php echo $g['qmk_view'].$R['goodsuid']?>" target="_blank"><?php echo $G['name']?></a>
		<?php if(getNew($R['d_regis'],24)):?><span class="new">new</span><?php endif?>
	</td>
	<td class="price"><?php echo number_format($R['price'])?></td>
	<td class="brand"><a class="hand" onclick="getMemberLayer('<?php echo $R['s_mbruid']?>',event);" title="<?php echo $R['s_tel']?>"><?php echo $R['s_name']?></a></td>
	<td><?php echo getDateFormat($R['d_regis'],'Y.m.d H:i')?></td>
	<td><?php echo $R['d_bank'] ? getDateFormat($R['d_bank'],'Y.m.d') : '미입금'?></td>
	<td>
		<?php if($R['d_give']):?>
		<?php echo getDateFormat($R['d_give'],'Y.m.d')?>
		<?php else:?>
		지급전
		<?php endif?>
	</td>
	</tr> 
	<?php endwhile?> 

	<?php if(!$NUM):?>
	<tr>
	<td colspan="8" class="sbj1">접수된 주문이 없습니다.</td>
	</tr> 
	<?php endif?>

	</tbody>
	</table>
	

	<div class="pagebox01">
	<script type="text/javascript">getPageLink(10,<?php echo $p?>,<?php echo $TPG?>,'<?php echo $g['img_core']?>/page/default');</script>
	</div>

	<input type="button" value="주문삭제" class="btngray" onclick="actCheck('order_cancel');" />

	</form>
	

</div>


<script type="text/javascript">
//<![CDATA[
function submitCheck(f)
{
	if (f.a.value == '')
	{
		return false;
	}
}
function actCheck(act)
{
	var f = document.procForm;
    var l = document.getElementsByName('members[]');
    var n = l.length;
	var j = 0;
    var i;

    for (i = 0; i < n; i++)
	{
		if(l[i].checked == true)
		{
			j++;	
		}
	}
	if (!j)
	{
		alert('선택된 주문이 없습니다.      ');
		return false;
	}
	
	if(confirm('정말로 실행하시겠습니까?    '))
	{
		f.a.value = act;
		f.submit();
	}
}
//]]>
</script>


