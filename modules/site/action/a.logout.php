<?php
if(!defined('__KIMS__')) exit;

if ($my['uid'])
{
	getDbUpdate($table['s_mbrdata'],'now_log=0','memberuid='.$my['uid']);
	session_destroy();
}

$referer = $referer ? urldecode($referer) : $_SERVER['HTTP_REFERER'];
$referer = explode('&_admpnl_',$referer);
$referer = $referer[0];

if($GLOBALS['_cloud_'] !== true) {
    //getLink($referer,'top.','','');
    header("Location: ".$g['sid_sso_login']."/router/?continue=".$g['sid_send_url']); exit;
} else {
?>
<script>
    if(window.opener) {
        window.close();
    } else {
        location.href = "<?=$g['sid_sso_login'].'/router/?continue='.$g['sid_send_url']?>";
    }
</script>
<?
}
?>