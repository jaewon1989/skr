<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

$fp = fopen($g['dir_module'].'theme/'.$theme.'/_var.php','w');
fwrite($fp,trim(stripslashes($theme_var)));
fclose($fp);
@chmod($g['dir_module'].'theme/'.$theme.'/_var.php',0707);

getLink('reload','parent.','','');
?>