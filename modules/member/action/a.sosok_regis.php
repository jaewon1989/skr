<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

$i = 0;
foreach($sosokmembers as $val)
{
	getDbUpdate($table['s_mbrgroup'],'gid='.($i++).",name='".trim(${'name_'.$val})."'",'uid='.$val);
}

if ($name)
{
	getDbInsert($table['s_mbrgroup'],'gid,name,num',"'".$i."','".trim($name)."','0'");
}

getLink('reload','parent.','','');
?>