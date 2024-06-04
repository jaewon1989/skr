<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);
include_once $g['dir_module'].'lib/action.func.php';

foreach($comment_members as $val)
{
	$R = getUidData($table['s_comment'],$val);
	if (!$R['uid']) continue;

   // 댓글 삭제시 추가삭제 부분 처리함수 - 한줄의견,첨부파일,장소 삭제 및 피드테이블 적용 
   setCommentDel($R,$d);

	getDbDelete($table['s_comment'],'uid='.$R['uid']);
	getDbUpdate($table['s_numinfo'],'comment=comment-1',"date='".substr($R['d_regis'],0,8)."' and site=".$R['site']);


	if ($R['point']&&$R['mbruid'])
	{
		getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'".$R['mbruid']."','0','-".$R['point']."','댓글삭제(".getStrCut($R['subject'],15,'').")환원','".$date['totime']."'");
		getDbUpdate($table['s_mbrdata'],'point=point-'.$R['point'],'memberuid='.$R['mbruid']);
	}

}


getLink('reload','parent.','','');
?>
