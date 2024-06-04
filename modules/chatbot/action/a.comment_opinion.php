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

$mbruid=$my['uid'];
$module=$m;
$entry=$entry;
$d_regis=$date['totime'];
 
if($type=='like'){
  $opinion='like';  
  $QKEY="mbruid,module,entry,opinion,d_regis";
  $QVAL="'$mbruid','$module','$entry','$opinion','$d_regis'";
  getDbInsert($table['s_opinion'],$QKEY,$QVAL);

  // s_comment likes 필드 업데이트
  getDbUpdate($table['s_comment'],'likes=likes+1','uid='.$entry); 

}else if($type=='cancellike'){
   getDbDelete($table['s_opinion'],"mbruid='".$mbruid."' and module='".$m."' and opinion='like' and entry='".$entry."'");  
    // s_comment likes 필드 업데이트
    getDbUpdate($table['s_comment'],'likes=likes-1','uid='.$entry);  
}

$result['num']=getLikeData($m,$entry,'num');
$result['html']=getLikeData($m,$entry,'html');

echo json_encode($result,true);
exit;
?>
