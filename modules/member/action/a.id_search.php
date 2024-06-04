<?php
if(!defined('__KIMS__')) exit;

$name		= trim($name);
$email		= trim($email);

if (!$name || !$email) getLink('','','정상적인 접근이 아닙니다.','');

include_once $g['dir_module'].'var/var.join.php';

if ($d['member']['login_emailid'])
{
	$R = getDbData($table['s_mbrid'],"id='".$email."'",'*');
	if (!$R['uid'])
	{
		getLink('','','입력하신 정보로 일치하는 회원데이터가 없습니다.','');
	}
	$M = getDbData($table['s_mbrdata'],'memberuid='.$R['uid'],'*');
}
else {
	$M = getDbData($table['s_mbrdata'],"email='".$email."'",'*');
	if (!$M['email'])
	{
		getLink('','','입력하신 정보로 일치하는 회원데이터가 없습니다.','');
	}
	$R = getUidData($table['s_mbrid'],$M['memberuid']);
}

if ($M['name'] != $name)
{
	getLink('','','입력하신 정보로 일치하는 회원데이터가 없습니다.','');
}

if ($d['member']['login_emailid'])
{
	getLink('','','회원님의 이메일은 ['.$M['email'].']입니다.','');
}
else {
	getLink('','','회원님의 아이디는 ['.$R['id'].']입니다.','');
}
?>