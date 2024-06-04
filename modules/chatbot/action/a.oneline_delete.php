<?php
if(!defined('__KIMS__')) exit;

if (!$my['uid']) echo '[RESULT:default-정상적인 접근이 아닙니다.:RESULT]';

$R = getUidData($table['s_oneline'],$uid);

if (!$R['uid']) echo '[RESULT:default-존재하지 않는 한줄 의견입니다.:RESULT]';
if ($R['id']!=$my['id']&&!$my['admin']) echo '[RESULT:default-삭제권한이 없습니다.:RESULT]';

$C = getUidData($table['s_comment'],$R['parent']);

// 한줄의견2 가 있는 경우 삭제 
if($R['is_child']){
   $OCD2= getDbSelect($table['s_oneline'],'parent='.$R['uid'].' and depth=1','*');
    while($O2=db_fetch_array($OCD2)){
       getDbDelete($table['s_oneline'],'uid='.$O2['uid']);
    }
}

getDbDelete($table['s_oneline'],'uid='.$R['uid']);
getDbUpdate($table['s_comment'],'oneline=oneline-1','uid='.$C['uid']);
getDbUpdate($table['s_numinfo'],'oneline=oneline-1',"date='".substr($R['d_regis'],0,8)."' and site=".$R['site']);

if ($R['point']&&$R['mbruid'])
{
    getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'".$R['mbruid']."','0','-".$R['point']."','한줄의견삭제(".getStrCut(str_replace('&amp;',' ',strip_tags($R['content'])),15,'').")환원','".$date['totime']."'");
    getDbUpdate($table['s_mbrdata'],'point=point-'.$R['point'],'memberuid='.$R['mbruid']);
}

//동기화
//댓글의 부모글에 한줄의견 수량 추가  
$sync_arr=explode('|',$C['sync']);
$feed_table=$sync_arr[0];
$feed_uid=$sync_arr[1]; 
getDbUpdate($feed_table,'oneline=oneline-1','uid='.$feed_uid);

echo '[RESULT:reload-삭제되었습니다.:RESULT]';
exit;
?>
