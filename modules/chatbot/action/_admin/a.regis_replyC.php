<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

$register = $my['uid']?$my['uid']:1;
$vendor = $vendor?$vendor:1;
$language = $language?$language:'KOR';
$reply = trim($reply);

$msg_array = $chatbot->getMopAndPattern($question);
$pattern = $msg_array['pat'];

if($uid)
{
	$R = getUidData($table[$m.'ruleC'],$uid);
	$QVAL = "quesCat='$quesCat',question='$question',pattern='$pattern',reply='$reply',lang='$language'";
	getDbUpdate($table[$m.'ruleC'],$QVAL,'uid='.$R['uid']);

}else{
   
    $QKEY="register,vendor,quesCat,question,pattern,reply,lang";
    $QVAL="'$register','$vendor','$quesCat','$question','$pattern','$reply','$language'";
    getDbInsert($table[$m.'ruleC'],$QKEY,$QVAL); 
    
} 
getLink('reload','parent.parent.','','');
?>
