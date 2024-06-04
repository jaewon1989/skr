<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

include $g['path_module'].$m.'/includes/tree.func.php';
$subQue = getMenuCodeToSqlBlog($table[$m.'entityVal'],$uid,'uid');

if ($subQue)
{
	$DAT = getDbSelect($table[$m.'entityVal'],$subQue,'*');
	while($R=db_fetch_array($DAT))
	{
		getDbDelete($table[$m.'entityVal'],'uid='.$R['uid']);

	}
	
	if ($parent)
	{
		if (!getDbRows($table[$m.'entityVal'],'parent='.$parent))
		{
			getDbUpdate($table[$m.'entityVal'],'isson=0','uid='.$parent);
		}
	}
    
	db_query("OPTIMIZE TABLE ".$table[$m.'entityVal'],$DB_CONNECT);

}

getLink('reload','parent.','','');
?>
