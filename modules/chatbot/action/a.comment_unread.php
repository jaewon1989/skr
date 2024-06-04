<?php
if(!defined('__KIMS__')) exit;
if (!$my['uid']){
	echo '[RESULT:default-로그인을 먼저 해주세요.:RESULT]';
	exit;
} 
$R = getUidData($table['s_comment'],$uid);
if (!$R['uid']){
	echo '[RESULT:default-존재하지 않는 글입니다.:RESULT]';
	exit;
} 
$mbruid=$my['uid'];
$entry=$uid;
$d_regis=$date['totime'];

$QKEY="mbruid,entry,d_regis";
$QVAL="'$mbruid','$entry','$d_regis'";
getDbInsert($table[$m.'unread'],$QKEY,$QVAL);

echo '[RESULT:해당 게시물이 숨김처리 되었습니다.:RESULT]';
exit;
?>
