<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

if (!$cat) getLink('./?m=admin&module='.$m.'&front=category&iframe=Y&uid='.$uid,'parent.','','');

include $g['path_module'].$m.'/includes/tree.func.php';
$subQue = getMenuCodeToSqlBlog($table[$m.'category'],$cat,'uid');

if ($subQue)
{
	$DAT = getDbSelect($table[$m.'category'],$subQue,'*');
	while($R=db_fetch_array($DAT))
	{
		getDbDelete($table[$m.'category'],'uid='.$R['uid']);
	}
	
	if ($parent)
	{
		if (!getDbRows($table[$m.'category'],'parent='.$parent))
		{
			getDbUpdate($table[$m.'category'],'isson=0','uid='.$parent);
		}
	}
	db_query("OPTIMIZE TABLE ".$table[$m.'category'],$DB_CONNECT); 
}

getLink('reload','parent.','','');
?>
