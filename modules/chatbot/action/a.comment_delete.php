<?php
if(!defined('__KIMS__')) exit;

include_once $theme.'/_var.php';
include_once $g['path_module'].'feed/lib/action.func.php';

$STOP=0;

$R = getUidData($table['s_comment'],$uid);

if (!$R['uid']){
	echo '[RESULT:default-존재하지 않는 댓글입니다.:RESULT]';$STOP++;//getLink('','','존재하지 않는 댓글입니다.','');
}  

if ($my['uid'] != $R['mbruid'] && !$my['admin'])
{
	if ($pw)
	{
		if (md5($pw) != $R['pw']) {
		     echo '[RESULT:default-비밀번호가 일치하지 않습니다.:RESULT]';$STOP++;//getLink('','','비밀번호가 일치하지 않습니다.','');
	    }
	}
	else {
		echo '[RESULT:default-비밀번호를 입력해 주세요.:RESULT]';$STOP++;// getLink('','','비밀번호를 입력해 주세요.','');
	}
}
if ($d['comment']['onelinedel'])
{
	if($R['oneline'])
	{
		echo '[RESULT:default-한줄의견이 있는 글은 삭제할 수 없습니다.:RESULT]';$STOP++;//getLink('','','한줄의견이 있는 댓글은 삭제할 수 없습니다.','');
	}
}

if($STOP==0)
{
   // 댓글 삭제시 추가삭제 부분 처리함수 - 한줄의견,첨부파일,장소 삭제 및 피드테이블 적용 
   setCommentDel($R,$d);
   
	getDbDelete($table['s_comment'],'uid='.$R['uid']);
	getDbUpdate($table['s_numinfo'],'comment=comment-1',"date='".substr($R['d_regis'],0,8)."' and site=".$R['site']);
	if ($R['point']&&$R['mbruid'])
	{
		getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'".$R['mbruid']."','0','-".$R['point']."','댓글삭제(".getStrCut($R['subject'],15,'').")환원','".$date['totime']."'");
		getDbUpdate($table['s_mbrdata'],'point=point-'.$R['point'],'memberuid='.$R['mbruid']);
	}
  	echo '[RESULT:reload-글이 삭제되었습니다. :RESULT]';
}	
?>
