<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

include $g['path_module'].$m.'/includes/tree.func.php';
$subQue = getMenuCodeToSqlBlog($table[$m.'entity'],$cat,'uid');

if ($subQue)
{
	$DAT = getDbSelect($table[$m.'entity'],$subQue,'*');
	while($R=db_fetch_array($DAT))
	{
		getDbDelete($table[$m.'entity'],'uid='.$R['uid']);
		// 엔터티 목록도 삭제
	    getDbDelete($table[$m.'entityVal'],'entity='.$R['uid']);
	}
	
	if ($parent)
	{
		if (!getDbRows($table[$m.'entity'],'parent='.$parent))
		{
			getDbUpdate($table[$m.'entity'],'isson=0','uid='.$parent);
		}
	}
    


	db_query("OPTIMIZE TABLE ".$table[$m.'entity'],$DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$m.'entityVal'],$DB_CONNECT); 
}

getLink('reload','parent.','','');
?>
