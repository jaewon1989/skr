<?php
if(!defined('__KIMS__')) exit;
checkAdmin(0);
include_once $g['dir_module'].'_main.php';
$sort	= $sort ? $sort : 'gid';
$orderby= $orderby ? $orderby : 'asc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 20;
$p=$P?$P:1;

$_WHERE = 'uid>0';
$_WHERE2=' and (';
$joint=getArrayString($productArray);
$_WHERE2=' and (';
foreach($joint['data'] as $val){
     $_WHERE2.='uid='.$val.' or ';
}
$_WHERE .= substr($_WHERE2,0,strlen($_WHERE2)-4).')';	

$RCD = getDbArray($table[$m.'product'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$m.'product'],$_WHERE);
$list='';
while($R=db_fetch_array($RCD)){
   $list_num=$NUM-((($p-1)*$recnum)+$_rec++);
   $g['adm_href']=$g['s'].'/?r='.$r.'&amp;m=admin&amp;module='.$m.'&amp;front=';
   $list.='
     	<tr>
	      <td><input type="checkbox" name="selected_product[]" value="['.$R['uid'].']" class="rb-post-user" onclick="checkboxCheck()"/></td>
		<td>'.$list_num.'</td>
		<td class="pic"><a href="'.$g['s'].'/?r='.$r.'&amp;m='.$m.'&amp;cat='.$R['category'].'&amp;uid='.$R['uid'].'" target="_blank" title="매장보기"><img src="'.getPic($R,'s').'" width="30" alt="" /></a></td>
		<td class="sbj"><a href="'.$g['adm_href'].'regis&amp;uid='.$R['uid'].'" title="상품등록정보">'.$R['name'].'</a></td>
		<td class="price">'.($R['price_x']?'전화문의':number_format($R['price'])).'</td>
		<td class="point">'.number_format($R['point']).'</td>
		<td class="hit">';
			 if($R['display']==1)  $list.='<div class="pumjeol">[임품]</div>';
			 else{
			    	 if($R['stock']&&$R['stock_num']<1)  $list.='<div class="pumjeol">[품절]</div>';
			       else  $list.=$R['stock']?number_format($R['stock_num']):'-';
			 }
		 $list.='	
		</td>
		<td class="hit">'.number_format($R['hit']).'</td>
		<td class="hit">'.number_format($R['buy']).'</td>
		<td class="hit">'.number_format($R['qna']).'</td>
		<td class="hit">'.number_format($R['comment']).'</td>
		<td class="hit">'.getDateFormat($R['d_regis'],'Y.m.d').'</td>
	</tr>';
}


$result=array();
$result['error']=false;

$result['list']=$list;
 
echo json_encode($result,true);
exit;
?>