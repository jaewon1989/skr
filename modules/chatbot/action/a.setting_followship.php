<?php
if(!defined('__KIMS__')) exit;
require_once $theme.'/function.php';

$result=array();
$result['error']=false;

if (!$my['uid'])
{
	$result['error']=true;
	$result['message']='정상적인 접근이 아닙니다.';
}

$type=$_POST['type'];
$ProfileUid=$_POST['ProfileUid'];

$ProfileUserInfo=getDbData($table['s_mbrdata'],'memberuid='.$ProfileUid,'*');
$ProfileUserName=$ProfileUserInfo['name'];

if($type=='unfollow'){
	$R = getDbData($table['s_follow'],'my_mbruid='.$ProfileUid.' and by_mbruid='.$my['uid'],'*');
	if ($R['uid'])  getDbDelete($table['s_follow'],'uid='.$R['uid']);	
	$result['message']=$ProfileUserName.'님과 팔로우가  취소되었습니다. ';
}
else if($type=='follow'){
   getDbInsert($table['s_follow'],'rel,my_mbruid,by_mbruid,category,d_regis',"'','".$ProfileUid."','".$my['uid']."','','".$date['totime']."'");
   
   $result['message']=$ProfileUserName.'님의 팔로우가 되었습니다. ';  
}
$result['btn_html']=getFollowBtn($my['uid'],$ProfileUid,$_mod,$Btnsize);
echo json_encode($result,true);
exit;
?>
