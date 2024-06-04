<?php
if(!defined('__KIMS__')) exit;
checkAdmin(0);
$makerfile = $g['dir_module'].'var/set.maker.txt';
$brandfile = $g['dir_module'].'var/set.brand.txt';
$fp = fopen($makerfile,'w');
fwrite($fp,trim($maker));
fclose($fp);
@chmod($makerfile,0707);
$fp = fopen($brandfile,'w');
fwrite($fp,trim($brand));
fclose($fp);
@chmod($brandfile,0707);
getLink('reload','parent.','','');
?>