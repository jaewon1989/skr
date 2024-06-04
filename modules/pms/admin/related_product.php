<?php
include_once $g['path_module'].$module.'/var/var.php';
include_once $g['path_module'].$module.'/_main.php';
$sort	= $sort ? $sort : 'gid';
$orderby= $orderby ? $orderby : 'asc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 20;
$_WHERE = 'uid>0';

$R=getUidData($table[$module.'product'],$uid);
$joint = getArrayString($R['joint']); // 관련상품 
$_WHERE2=' and (';
foreach($joint['data'] as $val){
     $_WHERE2.='uid='.$val.' or ';
}
$_WHERE .= substr($_WHERE2,0,strlen($_WHERE2)-4).')';

$RCD = getDbArray($table[$module.'product'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$module.'product'],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);
?>
<div class="page-header">
	<h4>
		<small><?php echo number_format($NUM)?> 개 ( <?php echo $p?>/<?php echo $TPG.($TPG>1?'pages':'page')?> )</small>
		<a href="#" data-toggle="modal" data-target="#modal_window" data-role="search-related-product" class="pull-right btn btn-link"><i class="fa fa-plus"></i> 관련상품 등록</a>
	</h4>
</div>
<form name="listForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>">
	<input type="hidden" name="r" value="<?php echo $r?>">
	<input type="hidden" name="m" value="<?php echo $module?>">
	<input type="hidden" name="a" value="">
	<div class="table-responsive">
		<table class="table table-striped">          
			<thead>
				<tr>
					<th><label data-tooltip="tooltip" title="선택"><input type="checkbox" class="checkAll-post-user"></label></th>
					<th>번호</th>
					<th>사진</th>
					<th>상품명</th>
					<th>가격</th>
					<th>적립금</th>
					<th>재고</th>
					<th>조회</th>
					<th>판매</th>
					<th>문의</th>
					<th>평가</th>
					<th>날짜</th>
				</tr>
			</thead>
		     <tbody>
				<?php while($R=db_fetch_array($RCD)):?>
				<tr>
				      <td><input type="checkbox" name="product_members[]" value="<?php echo $R['uid']?>" class="rb-post-user" onclick="checkboxCheck();"/></td>
					<td><?php echo $NUM-((($p-1)*$recnum)+$_rec++)?></td>
					<td class="pic"><a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $module?>&amp;cat=<?php echo $R['category']?>&amp;uid=<?php echo $R['uid']?>" target="_blank" title="매장보기"><img src="<?php echo getPic($R,'s')?>" width="30" alt="" /></a></td>
					<td class="sbj"><a href="<?php echo str_replace('front=main','front=regis',$g['adm_href'])?>&amp;uid=<?php echo $R['uid']?>" title="상품등록정보"><?php echo $R['name']?></a></td>
					<td class="price"><?php echo $R['price_x']?'전화문의':number_format($R['price'])?></td>
					<td class="point"><?php echo number_format($R['point'])?></td>
					<td class="hit">
						<?php if($R['display']==1):?>
						<div class="pumjeol">[임품]</div>
						<?php else:?>
						<?php if($R['stock']&&$R['stock_num']<1):?>
						<div class="pumjeol">[품절]</div>
						<?php else:?>
						<?php echo $R['stock']?number_format($R['stock_num']):'-'?>
						<?php endif?>
						<?php endif?>
					</td>
					<td class="hit"><?php echo number_format($R['hit'])?></td>
					<td class="hit"><?php echo number_format($R['buy'])?></td>
					<td class="hit"><?php echo number_format($R['qna'])?></td>
					<td class="hit"><?php echo number_format($R['comment'])?></td>
					<td class="hit"><?php echo getDateFormat($R['d_regis'],'Y.m.d')?></td>
				</tr> 
		     <?php endwhile?> 
		</tbody>
	</table>
    <?php if(!$NUM):?>
    	<div class="rb-none">등록된 상품이 없습니다.</div>
	<?php endif?>
		<div class="rb-footer clearfix">
			<div class="pull-right">
				<ul class="pagination">
				<script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
				<?php //echo getPageLink(5,$p,$TPG,'')?>
				</ul>
			</div>	
			<div>
				<button type="button" onclick="chkFlag('product_members[]');checkboxCheck();" class="btn btn-default btn-sm">선택/해제 </button>
				<button type="button" onclick="actCheck('product_multi_delete');" class="btn btn-default btn-sm rb-action-btn" disabled>삭제</button>
			</div>
		</div>
	</form>
</div>
	
<script type="text/javascript">
//<![CDATA[

// 선택박스 체크 이벤트 핸들러
$(".checkAll-post-user").click(function(){
	$(".rb-post-user").prop("checked",$(".checkAll-post-user").prop("checked"));
	checkboxCheck();
});

// 회원정보 modal 호출하는 함수 : 위에서 지정한 회원 uid & mod 로 호출한다 .
$('[data-role="search-related-product"]').on('click',function() {
	modalSetting('modal_window','<?php echo getModalLink('&amp;m=admin&amp;module=catalog&amp;front=search_product&amp_mod=modal;')?>');
});

// 선택박스 체크시 액션버튼 활성화 함수
function checkboxCheck()
{
	var f = document.listForm;
    var l = document.getElementsByName('post_members[]');
    var n = l.length;
    var i;
	var j=0;
	for	(i = 0; i < n; i++)
	{
		if (l[i].checked == true) j++;
	}
	if (j) $('.rb-action-btn').prop("disabled",false);
	else $('.rb-action-btn').prop("disabled",true);
}

function actCheck(act)
{
	var f = document.listForm;
    var l = document.getElementsByName('product_members[]');
    var n = l.length;
	var j = 0;
    var i;
	var s = '';
    for (i = 0; i < n; i++)
	{
		if(l[i].checked == true)
		{
			j++;
			s += '['+l[i].value+']';
		}
	}
	if (!j)
	{
		alert('선택된 상품이 없습니다.      ');
		return false;
	}
	
	if (act == 'product_multi_delete')
	{
		if(confirm('정말로 삭제하시겠습니까?    '))
		{
			f.a.value = act;
			f.submit();
		}
	}
	return false;
}
//]]>
</script>
