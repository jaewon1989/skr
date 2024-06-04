<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'includes/base.class.php';  
include_once $g['dir_module'].'includes/module.class.php';
$chatbot = new Chatbot(); 
$chatbot->botuid = $botuid;

$ACD = getDbSelect($table[$m.'added'],'vendor='.$vendor.' and botuid='.$botuid,'mbruid'); // 해당 bot 을 추가한 사용자 데이타 
$total = 0;
while ($R=db_fetch_array($ACD)){
	$chatbot->regisAdMessage($R['mbruid'],$my['uid'],$vendor,$message);
    $total++;
}
if($send_mod=='mobile'){
   $result=array();
   $result['message'] = '총 '.$total.' 명에게 메세지가 전송되었습니다.';
   echo json_encode($result,true);
   exit; 
}
else getLink('reload','parent.','총 '.$total.' 명에게 메세지가 전송되었습니다.','');

?>
