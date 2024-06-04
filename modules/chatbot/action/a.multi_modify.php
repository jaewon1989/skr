<?php
if(!defined('__KIMS__')) exit;
checkAdmin(0);

foreach ($object_members as $val)
{
	$R = getUidData($table[$m.'object'],$val);
	if (!$R['uid']) continue;
	getDbUpdate($table[$m.'object'],"name='".trim(${'name_'.$R['uid']})."'",'uid='.$R['uid']);
}

getLink('reload','parent.','수정되었습니다.','');
?>
