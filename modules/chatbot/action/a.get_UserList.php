<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅 

$result=array();
$result['error']=false;
$page = $_POST['page']?$_POST['page']:1;

// best 페이지에서 넘어오는 값 
if($tabMod=='tab-man') $search=array('sex'=>'male');
else if($tabMod=='tab-women') $search=array('sex'=>'female');
else if($tabMod=='tab-video') $search=array('regisType'=>'video');

// search 페이지에서 넘어오는 값 
if(isset($_POST['search_array'])){
   $search_array=stripslashes($_POST['search_array']); // 넘어온 값에서 / 제거 
   $search=json_decode($search_array,true);		
}

$feed = new feed();
$getUser = $feed->getUser($mbruid,$mod,$search,$page);

$result['content'] = $getUser[0];
$result['query'] = $getUser[1];

echo json_encode($result);
exit;
?>
