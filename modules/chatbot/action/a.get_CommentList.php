<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅 

$feed=new feed();

$result=array();
$result['error']=false;
$getComment = $feed->getComment('feed',$parent,1,$position);
$result['content'] = $getComment[0];
$result['query'] = $getComment[1];

echo json_encode($result);
exit;
?>
