<?php
if(!defined('__KIMS__')) exit;

$val=iconv('utf-8','utf-8',$val)==$val?$val:iconv('euc-kr','utf-8',$val);

// 쿼리
if($depth=='sido'){ 
  $adq=db_query("select gugun from ".$table[$m.'zipcode']." where sido='".$val."' group by gugun",$DB_CONNECT);
  $data='gugun';  
  $fname='+ 구/군 선택';
}else if($depth=='gugun'){
  $adq=db_query("select dong from ".$table[$m.'zipcode']." where gugun='".$val."' group by dong",$DB_CONNECT);
  $data='dong';
  $fname='+ 동 선택'; 
} 
$result=array();
$content='<option value="">'.$fname.'</option>';
while($Addr=db_fetch_array($adq)){
   $content.='<option value="'.$Addr[$data].'">'.$Addr[$data].'</option>';	
}
$result['content'] = $content;
$result['depth'] = $depth;
echo json_encode($result);
exit;
?>