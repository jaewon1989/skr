<?php
if(!defined('__KIMS__')) exit;

if ($_SESSION['_pwemail_'] > 2) exit;
$_SESSION['_pwemail_'] = $_SESSION['_pwemail_'] + 1;

include $g['path_core'].'function/email.func.php';

$tmpPw = rand(0,999999);
$content = '<h4>'.$tmpPw.'</h4><br><b>'._LANG('ad001','admin').'</b>';

$firstadmin = getDbData($table['s_mbrdata'],'memberuid=1','name,email');
$tmpUpdate = getDbUpdate($table['s_mbrdata'],"tmpcode='".$tmpPw."'",'memberuid=1');

$to = $firstadmin['email'].'|'.$firstadmin['name'];
$from = $d['admin']['sysmail'];

$result = getSendMail($to,$from,'['.$_HS['name'].']'._LANG('ad000','admin'),$content,'HTML');

if ($result)
{
	getLink('reload','parent.',_LANG('ad002','admin'),'');
}
else {
	getLink('','',_LANG('ad003','admin'),'');
}
?>