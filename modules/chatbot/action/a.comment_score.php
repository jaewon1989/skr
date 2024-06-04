<?php
if(!defined('__KIMS__')) exit;
if (!$my['uid']) echo '[RESULT:로그인을 먼저 해주세요.:RESULT]';
$R = getUidData($table['s_comment'],$uid);
if (!$R['uid']) exit;
$score_limit = 1; //점수한계치(이 점수보다 높은 갚을 임의로 보낼 경우 제한)
$score = $score ? $score : 1;
if ($score > $score_limit) $score = $score_limit;
if (!strstr($_SESSION['module_feed_score'],'['.$R['uid'].']'))
{
	if ($value == 'like')
	{
		getDbUpdate($table['s_comment'],'likes=likes+'.$score,'uid='.$R['uid']);
		echo '<script>parent.getId("_likes_'.$uid.'").innerHTML="'.($R['likes']+$score).'";</script>';;
	}
	else {
		getDbUpdate($table['s_comment'],'unlikes=unlikes+'.$score,'uid='.$R['uid']);
		echo '<script>parent.getId("_unlikes_'.$uid.'").innerHTML="'.($R['unlikes']+$score).'";</script>';;
	}
	$_SESSION['module_feed_score'] .= '['.$R['uid'].']';
   echo '[RESULT:ok:RESULT]'; 
}
else {
   echo	'[RESULT:이미 평가하신 댓글입니다.:RESULT]'; 
}
exit;
?>
