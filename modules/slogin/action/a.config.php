<?php
if(!defined('__KIMS__')) exit;
checkAdmin(0);

$snsSet = array('t','f','g','n','k','i');
$_tmpdfile = $g['dir_module'].'var/var.php';

$fp = fopen($_tmpdfile,'w');
fwrite($fp, "<?php\n");
foreach ($snsSet as $val)
{
	fwrite($fp, "\$d[".$m."]['use_".$val."'] = \"".${'use_'.$val}."\";\n");
	fwrite($fp, "\$d[".$m."]['key_".$val."'] = \"".${'key_'.$val}."\";\n");
	fwrite($fp, "\$d[".$m."]['secret_".$val."'] = \"".${'secret_'.$val}."\";\n");
}
fwrite($fp, "?>");
fclose($fp);
@chmod($_tmpdfile,0707);
getLink('reload','parent.','','');
?>
