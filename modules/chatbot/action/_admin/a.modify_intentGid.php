<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

$i = 0;
foreach($categorymembers as $val) getDbUpdate($table[$m.'intent'],'gid='.($i++),'uid='.$val);

getLink('reload','parent.','','');
?>
