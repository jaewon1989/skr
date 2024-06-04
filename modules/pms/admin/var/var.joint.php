<?php
function getCategoryShowSelect($table,$j,$parent,$depth,$uid,$hidden)
{
	global $cat,$g,$r,$smodule;
	static $j;

	$CD=getDbSelect($table,'depth='.($depth+1).' and parent='.$parent.($hidden ? ' and hidden=0':'').' order by gid asc','*');
	while($C=db_fetch_array($CD))
	{
		$j++;
		echo '<tr>';
		echo '<td>';
		if(!$depth) echo 'ㆍ';
		for($i=1;$i<$C['depth'];$i++) echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		if ($C['depth'] > 1) echo '<span class="subcat">ㄴ</span>';
		echo '<a href="'.$g['s'].'/?r='.$r.'&amp;m='.$smodule.'&amp;cat='.$C['uid'].'" target="_blank"'.($cat==$C['uid']?' class="b"':'').'>'.$C['name'].'</a>'.($C['num']?' <span class="num">('.$C['num'].')</span>':'');
		echo '</td>';
		echo '<td class="jright">';
		echo '<input type="button" value="상품" class="btngray" onclick="document.productform.cat.value=\''.$C['uid'].'\';document.productform.submit();"  /> ';
		echo '<input type="button" value="연결" class="btnblue" onclick="dropJoint(\''.$g['s'].'/?r='.$r.'&m='.$smodule.'&cat='.$C['uid'].'\');" />';
		echo '</td>';
		echo '</tr>';
		if ($C['isson']) getCategoryShowSelect($table,$j,$C['uid'],$C['depth'],$uid,$hidden);
	}
}
function getShopCategoryCodeToSql($table,$cat)
{
	static $sql;

	$R=getUidData($table,$cat);
	if ($R['uid']) $sql .= 'uid='.$R['uid'].' or ';
	if ($R['isson'])
	{
		$RDATA=getDbSelect($table,'parent='.$R['uid'],'uid');
		while($C=db_fetch_array($RDATA)) getShopCategoryCodeToSql($table,$C['uid']);
	}
	return substr($sql,0,strlen($sql)-4);
}
?>

<div id="mjointbox">

	<div class="title">
		<span class="b">연결할 매장이나 서비스를 선택해 주세요.</span><br />
		상품을 직접 연결하시려면 상품버튼을 클릭한 후 연결해 주세요.
	</div>


	<table class="cattable">	
		<tr>
			<td>ㆍ<span class="b">쇼핑몰 전체</span></td>
			<td class="jright">
			<input type="button" value="연결" class="btnblue" onclick="dropJoint('<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $smodule?>');" />
			</td>
		</tr>
		<tr>
			<td>
			<select id="brand_select">
			<option value="">&nbsp;+ 브랜드</option>
			<option value="">----------------------------------</option>
			<?php $_brandset=explode(',',implode('',file($g['path_module'].$smodule.'/var/set.brand.txt')))?>
			<?php foreach($_brandset as $_brand):if(!trim($_brand))continue?>
			<option value="<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $smodule?>&brand=<?php echo $_brand?>">ㆍ<?php echo $_brand?></option>
			<?php endforeach?>
			</select>			
			</td>
			<td class="jright">
			<input type="button" value="연결" class="btnblue" onclick="if(getId('brand_select').value==''){alert('브랜드를 선택해 주세요.');}else{dropJoint(getId('brand_select').value);}" />
			</td>
		</tr>
		<tr>
			<td>
			<select id="maker_select">
			<option value="">&nbsp;+ 제조사</option>
			<option value="">----------------------------------</option>
			<?php $_makerset=explode(',',implode('',file($g['path_module'].$smodule.'/var/set.maker.txt')))?>
			<?php foreach($_makerset as $_maker):if(!trim($_maker))continue?>
			<option value="<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $smodule?>&maker=<?php echo $_maker?>">ㆍ<?php echo $_maker?></option>
			<?php endforeach?>
			</select>			
			</td>
			<td class="jright">
			<input type="button" value="연결" class="btnblue" onclick="if(getId('maker_select').value==''){alert('제조사를 선택해 주세요.');}else{dropJoint(getId('maker_select').value);}" />
			</td>
		</tr>
		<tr>
			<td>
			<select id="event_select">
			<option value="">&nbsp;+ 이벤트</option>
			<option value="">----------------------------------</option>
			<?php $XCD = getDbArray($table[$smodule.'event'],'','*','gid','asc',0,1)?>
			<?php while($E=db_fetch_array($XCD)):?>
			<option value="<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $smodule?>&mod=event&cat=<?php echo $E['uid']?>">ㆍ<?php echo $E['name']?></option>
			<?php endwhile?>
			</select>				
			</td>
			<td class="jright">
			<input type="button" value="연결" class="btnblue" onclick="if(getId('event_select').value==''){alert('이벤트를 선택해 주세요.');}else{dropJoint(getId('event_select').value);}" />
			</td>
		</tr>
		<tr>
			<td>ㆍ장바구니</td>
			<td class="jright">
			<input type="button" value="연결" class="btnblue" onclick="dropJoint('<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $smodule?>&amp;mod=cart');" />
			</td>
		</tr>
		<tr>
			<td>ㆍ주문조회</td>
			<td class="jright">
			<input type="button" value="연결" class="btnblue" onclick="dropJoint('<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $smodule?>&amp;mod=myorder');" />
			</td>
		</tr>
		<tr>
			<td>ㆍ상품보관함</td>
			<td class="jright">
			<input type="button" value="연결" class="btnblue" onclick="dropJoint('<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $smodule?>&amp;mod=mywish');" />
			</td>
		</tr>
		<tr>
			<td>ㆍ적립금내역</td>
			<td class="jright">
			<input type="button" value="연결" class="btnblue" onclick="dropJoint('<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $smodule?>&amp;mod=mycash');" />
			</td>
		</tr>
		<tr>
			<td>ㆍ상품평가</td>
			<td class="jright">
			<input type="button" value="연결" class="btnblue" onclick="dropJoint('<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $smodule?>&amp;mod=reviewall');" />
			</td>
		</tr>
		<tr>
			<td>ㆍ상품문의</td>
			<td class="jright">
			<input type="button" value="연결" class="btnblue" onclick="dropJoint('<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $smodule?>&amp;mod=qnaall');" />
			</td>
		</tr>
	</table>
	<hr />
	<table class="cattable">
	<?php getCategoryShowSelect($table[$smodule.'category'],0,0,0,0,0)?>
	</table>


	<form name="productform" action="<?php echo $g['s']?>/" method="get">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="system" value="<?php echo $system?>" />
	<input type="hidden" name="iframe" value="<?php echo $iframe?>" />
	<input type="hidden" name="dropfield" value="<?php echo $dropfield?>" />
	<input type="hidden" name="smodule" value="<?php echo $smodule?>" />
	<input type="hidden" name="cmodule" value="<?php echo $cmodule?>" />
	<input type="hidden" name="p" value="<?php echo $p?>" />
	<input type="hidden" name="cat" value="<?php echo $cat?>" />
	</form>

<?php if($cat):?>
<br />
<br />
<br />
<?php
$sort	= $sort ? $sort : 'gid';
$orderby= $orderby && strstr('[asc][desc]',$orderby) ? $orderby : 'asc';
$recnum	= 10;

$_WHERE = 'display<2';
if ($cat) $_WHERE.= ' and ('.getShopCategoryCodeToSql($table[$smodule.'category'],$cat).')';
if ($where && $keyword)
{
	$_WHERE .= getSearchSql($where,$keyword,$ikeyword,'or');
}
$NUM = getDbRows($table[$smodule.'product'],$_WHERE);
$RCD = getDbArray($table[$smodule.'product'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$TPG = getTotalPage($NUM,$recnum);
?>

<table class="product">
<tbody>
<?php while($R=db_fetch_array($RCD)):?>
<?php $upfile1 = $g['path_module'].$smodule.'/files/'.substr($R['d_regis'],0,4).'/'.substr($R['d_regis'],4,2).'/'.substr($R['d_regis'],6,2).'/'.$R['uid'].'_4.'.$R['ext']?>
<?php $upfile2 = $g['url_root'].'/modules/'.$smodule.'/files/'.substr($R['d_regis'],0,4).'/'.substr($R['d_regis'],4,2).'/'.substr($R['d_regis'],6,2).'/'.$R['uid'].'_4.'.$R['ext']?>
<?php $upfile3 = $g['img_core'].'/blank.gif'?>
<tr>
<td class="pic"><a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $smodule?>&amp;cat=<?php echo $R['category']?>&amp;uid=<?php echo $R['uid']?>" target="_blank" title="매장보기"><img src="<?php echo is_file($upfile1)?$upfile2:$upfile3?>" alt="" /></a></td>
<td class="sbj">
<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $smodule?>&amp;cat=<?php echo $R['categoryt']?>&amp;uid=<?php echo $R['uid']?>" target="_blank"><?php echo $R['name']?></a><br />
<span class="price"><?php echo $R['price_x']?'전화문의':number_format($R['price'])?></span>
</td>
<td class="jointb">
<input type="button" value="연결" class="btnblue" onclick="dropJoint('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $smodule?>&cat=<?php echo $R['uid']?>&uid=<?php echo $R['uid']?>');" />
</td>
</tr> 
<?php endwhile?> 
</tbody>
</table>


<?php if(!$NUM):?>
<div class="noneproduct">등록된 상품이 없습니다.</div> 
<?php endif?>


<div class="pagebox01">
<script type="text/javascript">getPageLink(10,<?php echo $p?>,<?php echo $TPG?>,'<?php echo $g['img_core']?>/page/default');</script>
</div>


<?php endif?>



	<br />
	<br />
	<br />

</div>

<style type="text/css">
#mjointbox {}
#mjointbox .title {border-bottom:#dfdfdf dashed 1px;padding:0 0 10px 0;margin:0 0 20px 0;line-height:150%;}
#mjointbox .cattable {width:100%;_width:99.6%;}
#mjointbox .subcat {color:#c0c0c0;}
#mjointbox .jright {text-align:right;}
#mjointbox .num {color:#ff0000;font-family:arial;}

#mjointbox .product {width:100%;_width:99.6%;border-spacing:0px;border-collapse:collapse;}
#mjointbox .pic {padding:4px;width:60px;}
#mjointbox .pic img {width:55px;background:#efefef;}
#mjointbox .sbj {text-align:left;padding:5px 0 3px 10px;letter-spacing:-1px;}
#mjointbox .sbj a {font-size:12px;font-family:gothic,gulim;color:#222222;text-decoration:none;line-height:160%;}
#mjointbox .sbj a:hover {text-decoration:underline;}
#mjointbox .price {font-weight:bold;font-size:11px;color:#ff0000;}
#mjointbox .noneproduct {text-align:center;padding:20px;margin:20px 0 0 0;border-top:#dfdfdf solid 1px;color:#c0c0c0;}
#mjointbox .jointb {text-align:right;}

#mjointbox .pagebox01 {padding:20px 0 20px 0;text-align:center;border-top:#dfdfdf solid 1px;}

</style>