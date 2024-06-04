<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

foreach($members as $val) getDbDelete($table['s_referer'],'uid='.$val);

getLink('reload','parent.','','');
?>