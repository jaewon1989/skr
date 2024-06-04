<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$botid = $_POST['botid'];

$botData = $chatbot->getBotDataFromId($botid);
foreach ($botData as $key=>$value) {
    $TMPL[$key] = $value;    
}
$TMPL['date'] = $date['year'].'-'.substr($date['month'],4,2).'-'.substr($date['today'],6,2);
$TMPL['response'] = '<span>'.$chatbot->getSayHello($botid).'</span>';

// 챗봇 박스 출력 
$chatbox = new skin('chat/bot_msg');
$html =$chatbox->make();

// 최종 답변 리턴 
$result['content'] = $html;
echo json_encode($result);
exit;
?>
