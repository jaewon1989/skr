<?php
if(!defined('__KIMS__')) exit;

// 쿼리
$adq=db_query("select uid,subject from ".$table[$m.'company']." where auth=1 and display=1 and hidden=0 and mbruid='".$val."' group by subject",$DB_CONNECT);
$result=array();
$content='<option value="">+ 업체를 선택해주세요</option>';
while($R=db_fetch_array($adq)){
   $content.='<option value="'.$R['uid'].'">'.$R['subject'].'</option>';	
}
$result['content'] = $content;
$result['depth'] = $depth;
echo json_encode($result);
exit;
?>