<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

$_data = getEscString($_POST['data']);
 
$chatbot->vendor = $_data['vendor'];
$chatbot->botid = $_data['botid'];
$chatbot->cmod = $_data['cmod']; // vod or cs 
$chatbot->roomToken = $_data['roomToken'];
$chatbot->mbruid = $_data['mbruid'];

$_data['content'] = $_data['msg'];

if($_data['title'] && $_data['printType'] && $_data['content']) {
    $chatbot->addChatLog($_data);
}
exit;
?>
