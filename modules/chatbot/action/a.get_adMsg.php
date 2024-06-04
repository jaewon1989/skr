<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$botid = $_POST['botid'];
$data = array(
   "botid"=>$_POST['botid'],
   "mbruid"=>$_POST['mbruid'],
   "type"=>"read",
   "mod"=>"chatbox"
);

$result['content'] = $chatbot->getAdMessage($data); // 광고 메세지 가져오기  
echo json_encode($result);
exit;
?>
