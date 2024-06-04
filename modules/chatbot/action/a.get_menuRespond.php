<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';

$data = getEscString($_POST['data']);

if(!trim($data['bot'])) {
    echo "<script>alert('잘못된 접근입니다'); history.back();</script>"; exit;
}

$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$chatbot->vendor = $data['vendor'];
$chatbot->botuid = $data['bot'];
$chatbot->dialog = $data['dialog'];
$chatbot->botid = $data['botid'];
$chatbot->bottype = $data['bot_type'];
$chatbot->channel = $data['channel'];
$cmod = $chatbot->cmod = $data['cmod']; // vod or cs
$roomToken = $chatbot->roomToken = $data['roomToken'];

// aramjo context
$chatbot->getBotContext($data);

$data['botUid'] = $data['bot'];
$data['roomToken'] = $roomToken;
$data['cmod'] = $cmod;
$data['botActive'] = $data['botActive'];

if($data['jump']) $reply = $chatbot->getNodeRespond($data); // 특정 노드로 점프
else $reply = $chatbot->getMenuRespond($data); // array(res,res_type)

$result['content'] = $reply;
$result['context'] = $_SESSION['context'];

echo json_encode($result);
exit;
?>
