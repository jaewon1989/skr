<?php
if(!defined('__KIMS__')) exit;

// 파라미터 세팅 
$regisType=$_GET['regisType']; // 등록타입 (포토 or 동영상) 
$uid=$_GET['uid']; // 해당 포스트 uid (수정시)

// class 패스 지정 및 인클루드   
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅 

$feed = new feed();

// 수정 모드인 경우 
if($uid){
   $row = getUidData($feed->table('post'),$uid);
   $TMPL['content'] = $row['content'];
   $TMPL['video_play_icon'] ='<span class="dm-icon dm-icon-play"></span>';
   $TMPL['video_thumb'] = $feed->getLinkData($row['links'],'featured_img');
   $TMPL['video_url'] = $feed->getLinkData($row['links'],'pageUrl');
}else{
   $TMPL['video_thumb'] ='<img src="'.$g['img_layout'].'/06_upload/images/youtube_img.png" id="preview-default">';
}

// TMPL 값 세팅 
$TMPL['goPlayReview'] = YG_getDecrypt($_GET['goPlayReview']);
$TMPL['img_module_skin']=$g['img_module_skin'];
$TMPL['img_layout']=$g['img_layout'];

$TMPL['goods_rows']=$feed->getOrderGoods($my['uid'],$uid); 
$TMPL['regisType']=$regisType; 
$TMPL['uid']=$uid; 
$TMPL['mbruid']=$mbruid; 
$TMPL['subject']=$regisType=='video'?'동영상':'사진';

if($regisType=='photo'){
	$TMPL['photo_rows']=$feed->getUploadPhotoList($post_uid);
}

// 우측영역 마크업 세팅 (상품선택 및 노출설정 부분) 
$right=new skin('regis/right');
$TMPL['right'] = $right->make();

// 페이지 마크업 세팅 
$skin=new skin('regis/'.$regisType);
$regisMarkup = $skin->make();

// json 리턴 
$result=array();
$result['error']=false;
$result['content']=$regisMarkup;
echo json_encode($result);
exit;

?>
