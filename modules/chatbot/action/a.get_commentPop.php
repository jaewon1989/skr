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

$commentList = $feed->getComment($type,$entry,1,'commentBox');
$TMPL['uid'] = $entry;
$TMPL['register'] = $post['mbruid'];
$TMPL['comment_rows']=$commentList[0]?$commentList[0]:'더이상 댓글이 없습니다.';
$TMPL['comment_total']=$post['comment'];// 댓글 합계
$skin=new skin('comment/comment_box');
$commentBox = $skin->make();

// json 리턴 
$result=array();
$result['error']=false;
$result['content']=$commentBox;
$result['query'] = $commentList[1];
echo json_encode($result);
exit;

?>
