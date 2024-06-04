<?php
if(!defined('__KIMS__')) exit;
checkAdmin(0);
if ($act == 'ingam_delete')
{
	unlink($g['dir_module'].'var/ingam.gif');
	getLink('reload','parent.','','');
}
// print_r($_POST);
// exit;
$fdset = array('layout_desktop','layout_mobile','layout_desktop_admin','layout_mobile_admin','skin_desktop','skin_mobile','upjong','campus','helloword','starword','badword','tabooword','searchword','recommendword','trendword','feelingword');

$gfile= $g['dir_module'].'var/var.php';
include_once $gfile;
foreach ($fdset as $val) $d['chatbot'][$val] = trim(${$val});
$fp = fopen($gfile,'w');
fwrite($fp, "<?php\n");
foreach ($d['chatbot'] as $key => $val)
{
	fwrite($fp, "\$d['chatbot']['".$key."'] = \"".$val."\";\n");
}
fwrite($fp, "?>");
fclose($fp);
@chmod($gfile,0707);

getLink('reload','parent.','','');
?>
