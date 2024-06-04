<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'includes/base.class.php';  
include_once $g['dir_module'].'includes/module.class.php';
$chatbot = new Chatbot(); 
$chatbot->botuid = $botuid;

$ACD = getDbSelect($table['s_mbrid'],'site=2','uid'); // 현대 챗봇에 접속한 유저 
// while ($R=db_fetch_array($ACD)){
// 	$chatbot->regisAdMessage($R['uid'],$my['uid'],$vendor,$message);
// }

$chatbot->regisAdMessage(0,$my['uid'],$vendor,$message);

if($send_mod=='mobile'){
   $result=array();
   $result['message'] = '광고 메세지가 등록되었습니다.';
   echo json_encode($result,true);
   exit; 
}
else getLink('reload','parent.','광고 메세지가 등록되었습니다.','');

?>
