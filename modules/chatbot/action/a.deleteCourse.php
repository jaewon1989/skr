<?php
if(!defined('__KIMS__')) exit;

if (!$cat) getLink('./?r='.$r.'&c=mybot/course&page=vendor/course&uid='.$uid,'parent.','','');

include $g['path_module'].$m.'/includes/tree.func.php';
$subQue = getMenuCodeToSqlBlog($table[$m.'course'],$cat,'uid');

if ($subQue)
{
	$DAT = getDbSelect($table[$m.'course'],$subQue,'*');
	while($R=db_fetch_array($DAT))
	{
		getDbDelete($table[$m.'course'],'uid='.$R['uid']);
	}
	
	if ($parent)
	{
		if (!getDbRows($table[$m.'course'],'parent='.$parent))
		{
			getDbUpdate($table[$m.'course'],'isson=0','uid='.$parent);
		}
	}
	db_query("OPTIMIZE TABLE ".$table[$m.'course'],$DB_CONNECT); 
}

getLink('reload','parent.','','');
?>
