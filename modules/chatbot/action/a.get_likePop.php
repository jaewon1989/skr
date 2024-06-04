<?php
if(!defined('__KIMS__')) exit;

// 파라미터 세팅 
$type=$_GET['type']; 
$entry=$_GET['entry']; 


// class 패스 지정 및 인클루드   
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅

$feed = new feed();
$post=$feed->getUidData($feed->table('post'),$entry);

// 쪽지 박스 마크업 세팅 
$skin=new skin('like/like_box');
$likeList = $feed->getLike($type,$entry,1,'likeBox');
$TMPL['like_rows']=$likeList?$likeList:'더이상 좋아요가 없습니다.';
$TMPL['like_total']=$post['likes'];// 좋아요 합계
$likeBox = $skin->make();

// json 리턴 
$result=array();
$result['error']=false;
$result['content']=$likeBox;
echo json_encode($result);
exit;

?>
