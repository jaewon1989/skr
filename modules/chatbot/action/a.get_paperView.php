<?php
if(!defined('__KIMS__')) exit;

// 파라미터 세팅 
$register=$_GET['register']; // 해당 포스트 글 작성자 uid

// class 패스 지정 및 인클루드   
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅

$feed = new feed();
$registerData=$feed->getProfile($register);
$TMPL['img_layout']=$g['img_layout'];
$TMPL['register_name']=$registerData['user_name'];
$TMPL['register']=$register;

$TMPL['msg_rows'] = $feed->getPaper($register,1,'msgBox');

// 폼 전송 세팅 
if (!$_SESSION['upsescode']) $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
$sescode = $_SESSION['upsescode'];
$sess_Code =$sescode.'_'.$my['uid']; // 코드- 회원 uid  
$_SESSION['wcode']=$date['totime'];
$TMPL['gs'] = $g['s'];
$TMPL['url_layout'] = $g['url_layout'];
$TMPL['pcode'] = $date['totime'];
$TMPL['wcode'] = $_SESSION['wcode'];
$TMPL['module'] = $m;
$TMPL['sess_Code'] = $sess_Code;
$TMPL['saveDir'] = $g['path_file'].$m.'/paper/';// <!-- 포토 업로드 폴더 -->
$TMPL['rsv_member'] =$register.'^^'.$registerData['userid'];

// 쪽지 박스 마크업 세팅 
$skin=new skin('paper/msg_box');
$paperBox = $skin->make();

// json 리턴 
$result=array();
$result['error']=false;
$result['content']=$paperBox;
echo json_encode($result);
exit;

?>
