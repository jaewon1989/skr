<?php
if(!defined('__KIMS__')) exit;

// 파라미터 세팅 
$regisType=$_GET['regisType']; // 등록타입 (포토 or 동영상) 
$uid=$_GET['uid']; // 해당 포스트 uid (수정시)


// class 패스 지정 및 인클루드   
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅

if($uid){
	$R=getUidData($table[$m.'post'],$uid);
} 

$feed = new feed();

// view Data 추출 
$viewData=$feed->getFeedView($uid);
foreach ($viewData as $key => $value) {
	$TMPL[$key]=$value;
}

// 페이지 마크업 세팅 
$skin=new skin('feed/view');
$viewMarkup = $skin->make();

// json 리턴 
$result=array();
$result['error']=false;
$result['content']=$viewMarkup;
$result['meta_title']=$TMPL['meta_title'];
$result['meta_url']=$TMPL['meta_url'];
$result['meta_description']=$TMPL['meta_description'];
$result['meta_image']=$TMPL['meta_image'];

echo json_encode($result);
exit;

?>
