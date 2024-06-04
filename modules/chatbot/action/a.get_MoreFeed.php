<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅 

// 리스트 타입 
$list_type = $_REQUEST['type'];

$result=array();
$result['error']=false;
$mod=$_POST['mod'];
$nextPage=(int)$_POST['currentPage']+1;

$search_array=stripslashes($_POST['search_array']); // 넘어온 값에서 / 제거 
$search=json_decode($search_array,true);

$feed = new feed();
$is_mobile = $feed->is_mobile();

if($is_mobile) $feed->recnum = $d['sns']['feed_m_'.$list_type.'_recnum'];
else $feed->recnum = $d['sns']['per_page'];

if($userid){
  $profile = $feed->getProfileFromId($userid);
  $mbruid = $profile['member_seq'];
}
else $mbruid='';


$feed_rows = $feed->getFeed($mbruid,$type,$mod,$search,$nextPage);  

$TMPL['rows']=$feed_rows[0];
$skin=new skin('feed/default');
$result['content']=$skin->make();

echo json_encode($result);
exit;
?>
