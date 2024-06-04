<?php 
$bot = trim(mysqli_real_escape_string($DB_CONNECT, $_GET['bot']));
$type = trim(mysqli_real_escape_string($DB_CONNECT, $_GET['type']));
$m ='chatbot';

include $g['path_module'].$m.'/includes/base.class.php';
include $g['path_module'].$m.'/includes/module.class.php';
$chatbot=new Chatbot();

$B = getDbData($table[$m.'bot'],'uid='.$bot,'id,type,boturl');

$bot_url = $chatbot->getChatUrl($B);

if($type=='type01') $type_img ='geturl_01.png';
else if($type=='type02') $type_img ='geturl_02.png';
else if($type=='type03') $type_img ='geturl_03.png';
$wdg_img = '/layouts/chatbot-desktop/_images/'.$type_img;

?>
<a href="<?php echo $bot_url?>" target="_blank">
	<img src="<?php echo $wdg_img?>" alt="bottalks-widget-<?php echo $type?>"/>
</a>