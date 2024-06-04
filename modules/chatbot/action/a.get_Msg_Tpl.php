<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

$resutl = array();
$result['bot'] = $chatbot->getHtmlOnly('chat/bot_msg');
$result['user'] = $chatbot->getHtmlOnly('chat/user_msg');

echo json_encode($result,true);
exit;
?>