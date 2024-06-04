<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

include $g['path_module'].$m.'/includes/tree.func.php';
$subQue = getMenuCodeToSqlBlog($table[$m.'intent'],$cat,'uid');

if ($subQue)
{
	$DAT = getDbSelect($table[$m.'intent'],$subQue,'*');
	while($R=db_fetch_array($DAT))
	{
		getDbDelete($table[$m.'intent'],'uid='.$R['uid']);
	}
	
	if ($parent)
	{
		if (!getDbRows($table[$m.'intent'],'parent='.$parent))
		{
			getDbUpdate($table[$m.'intent'],'isson=0','uid='.$parent);
		}
	}
	db_query("OPTIMIZE TABLE ".$table[$m.'intent'],$DB_CONNECT); 
}

getLink('reload','parent.','','');
?>
