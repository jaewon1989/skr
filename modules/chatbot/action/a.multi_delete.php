<?php
if(!defined('__KIMS__')) exit;
checkAdmin(0);

foreach ($object_members as $val) {
	getDbDelete($table[$m.'object'],'uid='.$val);
}

getLink('reload','parent.','','');
?>
