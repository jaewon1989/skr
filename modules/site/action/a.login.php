<?php
if(!defined('__KIMS__')) exit;

$history = $__target ? '-1' : '';
$id	= trim($_POST['id']);
$pw	= trim($_POST['pw']);

if (!$id || !$pw) getLink('','',_LANG('a4001','site'),$history);

if (strpos($id,'@') && strpos($id,'.'))
{
	$M1 = getDbData($table['s_mbrdata'],"email='".$id."'",'*');
	$M	= getUidData($table['s_mbrid'],$M1['memberuid']);
}
else {
	$M = getDbData($table['s_mbrid'],"id='".$id."'",'*');
	$M1 = getDbData($table['s_mbrdata'],'memberuid='.$M['uid'],'*');
}

$maxLogFailCnt = 10;

if ('Y' === $M1['is_lock']) getLink('', '', _LANG('a4007', 'site'), $history);
if (!$M['uid'] || $M1['auth'] == 4) getLink('','',_LANG('a4002','site'),$history);
if ($M1['auth'] == 2) getLink('','',_LANG('a4003','site'),$history);
if ($M1['auth'] == 3) getLink('','',_LANG('a4004','site'),$history);
if (($M['pw'] != getCrypt($pw, $M1['d_regis']) && $M1['tmpcode'] != $pw) &&
    ($M['pw'] != getCryptByCCaaS($pw, $M1['d_regis']) && $M1['tmpcode'] != $pw)) {

    $errorMsg = _LANG('a4005', 'site');
    getDbUpdate($table['s_mbrdata'], "log_fail_cnt=log_fail_cnt+1", 'memberuid=' . $M['uid']);
    if (($maxLogFailCnt - 1) === (int)$M1['log_fail_cnt']) {
        $errorMsg = _LANG('a4007', 'site');
        getDbUpdate($table['s_mbrdata'], "is_lock='Y'", 'memberuid=' . $M['uid']);
    }
    getLink('', '', $errorMsg, $history);

}

if ($usertype == 'admin')
if (!$M1['admin']) getLink('','',_LANG('a4006','site'),$history);

getDbUpdate($table['s_mbrdata'],"tmpcode='',num_login=num_login+1,now_log=1,last_log='".$date['totime']."'",'memberuid='.$M['uid']);

if ($idpwsave == 'checked') setcookie('svshop', $id.'|'.$pw, time()+60*60*24*30, '/');
else setcookie('svshop', '', 0, '/');

$_SESSION['mbr_id'] = $M['id'];
$_SESSION['mbr_uid'] = $M['uid'];
$referer = $referer ? urldecode($referer) : $_SERVER['HTTP_REFERER'];
$referer = str_replace('&panel=Y','',$referer);
$referer = str_replace('&a=logout','',$referer);

if ($usertype == 'admin') getLink($g['s'].'/?r='.$r.'&panel=Y&pickmodule=dashboard','parent.parent.','','');
if ($M1['admin']) getLink($g['s'].'/?r='.$r.'&panel=Y&_admpnl_='.urlencode($referer),'parent.parent.','','');

if (strtotime($M1['last_pw']) < strtotime('-6 months')) {
    $_SESSION['is_pw_change'] = 'Y';
    getLink('/adm/memberadd', 'parent.parent.', _LANG('a4008', 'site'), '');
} else {
    $_SESSION['is_pw_change'] = 'N';
    getLink('/adm', 'parent.parent.', '', '');
}

?>