<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

$badword = trim($badword);
$badword = str_replace("\r\n","",$badword);
$badword = str_replace("\n","",$badword);


$fdset = array('skin_main','skin_mobile','skin_total','rss','restr','replydel','commentdel','badword','badword_action','badword_escape','singo_del','singo_del_num','singo_del_act','recnum','sbjcut','newtime');

$gfile= $g['dir_module'].'var/var.php';
$fp = fopen($gfile,'w');
fwrite($fp, "<?php\n");
foreach ($fdset as $val)
{
	fwrite($fp, "\$d['bbs']['".$val."'] = \"".trim(${$val})."\";\n");
}
fwrite($fp, "?>");
fclose($fp);
@chmod($gfile,0707);



getLink('reload','parent.','','');
?>