<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅 

$result=array();
$result['error']=false;
$mod=$_POST['mod'];
$search=$_POST['search'];
$nextPage=(int)$_POST['currentPage']+1;
$search_data=explode(',',$search);

$search=array();
foreach ($search_data as $data_arr) {
    $data=explode('-',$data_arr);
    $key=$data[0];
    $val=$data[1];
    $search[$key] = $val;
}

$feed = new feed();
$getUser = $feed->getUser($mbruid,$mod,$search,$nextPage);

$result['content'] = $getUser[0];
$result['query'] = $getUser[1];


echo json_encode($result);
exit;
?>
