<?php
if(!defined('__KIMS__')) exit;

$result=array();
$result['error']=false;

if (!$my['admin']){
	$result['error']=true;
	$result['msg']='관리자 권한이 필요합니다.';
}else{
  if($act=='notice') getDbUpdate($table['s_comment'],'notice=1','uid='.$uid);
  else if($act=='unnotice') getDbUpdate($table['s_comment'],'notice=0','uid='.$uid);
  else if($act=='recommend') getDbUpdate($table['s_comment'],'is_recommended=1','uid='.$uid);
  else if($act=='unrecommend') getDbUpdate($table['s_comment'],'is_recommended=0','uid='.$uid);
  $result['msg']='OK';
} 
echo json_encode($result);
exit;
?>
