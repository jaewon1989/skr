<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$_data = $_POST['data'];
// if(isset($_POST['context']) && $_POST['context']!=null){
// 	$chatbot->updateContext($_POST['context']);
// }

$chatbot->vendor = $_data['vendor'];
$chatbot->botuid = $_data['bot'];
$chatbot->dialog = $_data['dialog'];
$chatbot->botid = $_data['botid'];
$cmod = $chatbot->cmod = $_data['cmod']; // vod or cs
$roomToken = $chatbot->roomToken = $_data['roomToken'];


//$botData = $chatbot->getBotDataFromId($_data['botid']);
$data['vendor'] = $_data['vendor'];
$data['botUid'] = $_data['bot'];
$data['bot'] = $_data['bot'];
$data['dialog'] = $_data['dialog'];
$data['roomToken'] = $roomToken;
$data['cmod'] = $cmod;
$data['clean_input'] = $chatbot->verifyUserInput($_data['msg']);
$data['botActive'] = $_data['botActive'];
$data['jump'] = $_data['node'];
$data['api'] = true;

$reply = $chatbot->getNodeRespond($data); // 특정 노드로 점프

$result['content'] = $reply;
$result['context'] = $_SESSION['context'];

echo json_encode($result);
exit;
?>
