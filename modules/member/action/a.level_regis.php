<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

for ($i = 1; $i < 101; $i++)
{
	$name = ${'name_'.$i};
	$login = ${'login_'.$i};
	$post = ${'post_'.$i};
	$comment = ${'comment_'.$i};
	getDbUpdate($table['s_mbrlevel'],"gid=0,name='$name',login='$login',post='$post',comment='$comment'",'uid='.$i);
}
getDbUpdate($table['s_mbrlevel'],'gid=1','uid='.$num);

getLink('reload','parent.','','');
?>