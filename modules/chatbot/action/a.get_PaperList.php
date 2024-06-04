<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅 

$result=array();
$result['error']=false;
$page = $_POST['page']?$_POST['page']:1;

$search_array=stripslashes($_POST['search_array']); // 넘어온 값에서 / 제거 
$search=json_decode($search_array,true);

$feed = new feed();
$is_mobile = $feed->is_mobile();

$getPaper = $feed->getMyPaper($my['uid'],$page,$position); // mod 에 다라서 query 만 다르게 한다. 

$result['content'] = $getPaper;
$result['query'] = '';

echo json_encode($result);
exit;
?>
