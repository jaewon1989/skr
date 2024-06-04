<?php
if(!defined('__KIMS__')) exit;

$mbruid = $_POST['mbruid'];
$d_regis = $date['totime'];
if($logType=='access'){
   $QKEY ="mbruid,d_regis";
   $QVAL ="'".$mbruid."','$d_regis'";
   getDbInsert('hcn_mv_accessLog',$QKEY,$QVAL);	
}


$result['content'] = '';

echo json_encode($result);	



exit;
?>
