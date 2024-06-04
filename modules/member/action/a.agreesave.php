<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

for ($i = 1; $i < 6; $i++)
{
	$mfile = $g['path_module'].$m.'/var/agree'.$i.'.txt';
	$fp = fopen($mfile,'w');
	fwrite($fp,trim(stripslashes(${'agree'.$i})));
	fclose($fp);
	@chmod($mfile,0707);
}

$_SESSION['_join_menu'] = 5;
$_SESSION['_join_tab'] = $_join_tab;

getLink('reload','parent.','','');
?>