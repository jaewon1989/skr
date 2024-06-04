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

if($type=='unfriend'){
	$R = getDbData($table['s_friend'],'(my_mbruid='.$ProfileUid.' and by_mbruid='.$my['uid'].') or (my_mbruid='.$my['uid'].' and by_mbruid='.$ProfileUid.')','*');
	if ($R['uid']) getDbDelete($table['s_friend'],'uid='.$R['uid']);
	$result['message']=$ProfileUserName.'님과 친구관계가 끊어졌습니다. ';
}
else if($type=='addfriend'){
       getDbInsert($table['s_friend'],'rel,my_mbruid,by_mbruid,category,d_regis',"'','".$ProfileUid."','".$my['uid']."','','".$date['totime']."'");
       $result['message']=$ProfileUserName.'님에게 친구요청이 전송되었습니다. ';  
}else if($type=='cancelrequest'){
	 getDbDelete($table['s_friend'],'my_mbruid='.$ProfileUid.' and by_mbruid='.$my['uid']);
	 $result['message']=$ProfileUserName.'님에게 요청한 친구신청이 취소되었습니다. ';
}else if($type=='confirmfriend'){
	getDbUpdate($table['s_friend'],'rel=1','my_mbruid='.$my['uid'].' and by_mbruid='.$ProfileUid);
      $result['message']=$ProfileUserName.'님과 친구가 되었습니다. ';
}
$result['btn_html']=getFriendBtn($my['uid'],$ProfileUid,$_mod,$Btnsize);
echo json_encode($result,true);
exit;
?>
