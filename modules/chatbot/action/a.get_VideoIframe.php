<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅 

$feed=new feed();
$_table = $feed->table('post');
$row = $feed->getUidData($_table,$post);
$videoIframe = $feed->getLinkData($row['links'],'videoIframe');  
$result=str_replace('display: none','display: block',$videoIframe);

echo $result;
exit;
?>    