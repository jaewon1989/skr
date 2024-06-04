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
$entry=$R['uid'];
$module='feed';
$category ='feed-'.$R['parent'];
$subject = addslashes($R['subject']);
$url     = getLinkFilter($g['s'].'/'.$R['id'].'/issues/'.$R['uid']);

getDbDelete($table['s_scrap'],"mbruid='".$mbruid."' and module='".$module."' and entry='".$entry."'");
echo '[RESULT:링크저장이 취소되었습니다. :RESULT]';
exit;  

?>
