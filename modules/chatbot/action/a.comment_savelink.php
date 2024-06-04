<?php
if(!defined('__KIMS__')) exit;
if (!$my['uid']){
   echo '[RESULT:로그인을 먼저 해주세요.:RESULT]';
   exit;
} 
$R = getUidData($table['s_comment'],$uid);

if(!$R['uid']){
   echo '[RESULT:삭제되었거나 존재하지 않는 게시물입니다.:RESULT]';
   exit;
} 

$mbruid  = $my['uid'];
$module='feed';
$entry=$R['uid'];
$category ='feed-'.$R['parent'];
$subject = addslashes($R['subject']);
$url     = getLinkFilter($g['s'].'/'.$R['id'].'/issues/'.$R['uid']);
$d_regis = $date['totime'];

if (getDbRows($table['s_scrap'],"mbruid='".$mbruid."' and  module='".$module."' and entry='".$entry."'")){
   echo '[RESULT:이미 링크저장되었습니다. :RESULT]';
   exit;
}else{
  $_QKEY = 'mbruid,module,moduleid,entry,category,subject,url,d_regis';
  $_QVAL = "'$mbruid','$module','$moduleid','$entry','$category','$subject','$url','$d_regis'";
   getDbInsert($table['s_scrap'],$_QKEY,$_QVAL);
   echo '[RESULT:링크저장되었습니다. :RESULT]';
   exit;  
}

?>
