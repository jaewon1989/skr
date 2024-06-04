<?php
include_once $g['dir_module_skin'].'_pc/my/_menu.php';

$sort	= $sort ? $sort : 'uid';
$orderby= $orderby ? $orderby : 'desc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 20;

$bbsque = 'my_mbruid='.$_useruid;
if ($where && $keyword)
{
	$bbsque .= getSearchSql($where,$keyword,$ikeyword,'or');
}
$RCD = getDbArray($table[$m.'comment'],$bbsque,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$m.'comment'],$bbsque);
$TPG = getTotalPage($NUM,$recnum);
?>

<div id="bbslist">
	<form action="<?php echo $g['s']?>/">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="page" value="<?php echo $page?>" />
	<div class="info">

		<div class="article">
			<?php echo number_format($NUM)?>개(<?php echo $p?>/<?php echo $TPG?>페이지)
		</div>
		
		<div class="category">
			&nbsp;&nbsp;
			<select name="where">
			<option value="subject"<?php if($where=='subject'):?> selected="selected"<?php endif?>>제목</option>
			<option value="content"<?php if($where=='content'):?> selected="selected"<?php endif?>>내용</option>
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

	<table summary="사용후기 입니다.">
	<caption>사용후기</caption> 
	<colgroup> 
	<col width="30"> 
	<col width="50">
	<col width="50">
	<col> 
	<col width="90"> 
	<col width="90"> 
	<col width="90"> 
	</colgroup> 
	<thead>
	<tr>
	<th scope="col" class="side1"><img src="<?php echo $g['img_core']?>/_public/ico_check_01.gif" class="hand" alt="" onclick="chkFlag('members[]');" /></th>
	<th scope="col">번호</th>
	<th scope="col">상품</th>
	<th scope="col">제목</th>
	<th scope="col">이름</th>
	<th scope="col">평가</th>
	<th scope="col" class="side2">날짜</th>
	</tr>
	</thead>
	<tbody>

	<?php while($R=db_fetch_array($RCD)):?>
	<?php $G=getUidData($table[$m.'goods'],$R['goodsuid'])?>
	<?php $M=getDbData($table['s_mbrdata'],'memberuid='.$R['by_mbruid'],'*')?>
	<tr>
	<td>
		<input type="checkbox" name="members[]" value="<?php echo $R['uid']?>" />
	</td>
	<td><?php echo $NUM-((($p-1)*$recnum)+$_rec++)?></td>
	<td class="pic"><a href="<?php echo $g['qmk_reset']?>&amp;page=view&amp;uid=<?php echo $R['goodsuid']?>" target="_blank" title="<?php echo $G['name']?>"><img src="<?php echo $g['url_module']?>/upload/preview/<?php echo $G['preview']?>" width="50" height="50" alt="" /></a></td>
	<td class="sbj<?php if($R['uid']==$uid):?> b<?php endif?>">
		<!--<?php if($R['isbuyer']):?><img src="<?php echo $g['img_module_skin']?>/btn_buyer.gif" alt="구매자" /><?php endif?>-->
		<a href="<?php echo $g['qmk_reset']?>&amp;page=view&amp;uid=<?php echo $R['goodsuid']?>&amp;comment_uid=<?php echo $R['uid']?>" target="_blank"><?php echo $R['subject']?></a>
		<?php if(getNew($R['d_regis'],24)):?><span class="new">new</span><?php endif?>
	</td>
	<td class="hand" onclick="getMemberLayer('<?php echo $M['memberuid']?>',event);"><?php echo $M[$_HS['nametype']]?></td>
	<td><img src="<?php echo $g['img_core']?>/star/default/<?php echo intval($R['score']/2)?>.gif" alt="<?php echo $R['score']?>점" title="<?php echo $R['score']?>점" /></td>
	<td><?php echo getDateFormat($R['d_regis'],'Y.m.d H:i')?></td>
	</tr> 
	<?php endwhile?> 

	<?php if(!$NUM):?>
	<tr>
	<td><input type="checkbox" disabled="disabled" /></td>
	<td colspan="6" class="sbj1">등록된 사용자평가글이 없습니다.</td>
	</tr> 
	<?php endif?>

	</tbody>
	</table>


	<div class="pagebox01">
	<script type="text/javascript">getPageLink(10,<?php echo $p?>,<?php echo $TPG?>,'<?php echo $g['img_core']?>/page/default');</script>
	</div>
	<input type="button" value="삭제" class="btngray" onclick="actCheck('comment_delete_multi');" />

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
		alert('선택된 평가글이 없습니다.      ');
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


