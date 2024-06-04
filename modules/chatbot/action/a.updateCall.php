<?php
if(!defined('__KIMS__')) exit;

// 업데이트 콜(hit)
getDbUpdate($table[$m.'post'],'hit=hit+1','uid='.$uid); 

$result['content'] = 'OK';
echo json_encode($result);
exit;
?>