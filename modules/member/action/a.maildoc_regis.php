<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

if ($newdoc)
{
	if (is_file($g['dir_module'].'doc/'.$newdoc.'.txt'))
	{
		getLink('','','이미 존재하는 양식명칭입니다.    ','');
	}
	else {
		$type = $newdoc;
	}
}
$vfile = $g['dir_module'].'doc/'.$type.'.txt';

$fp = fopen($vfile,'w');
fwrite($fp, trim(stripslashes($content)));
fclose($fp);
@chmod($vfile,0707);

if ($newdoc)
{
	getLink($g['s'].'/?r='.$r.'&m=admin&module='.$m.'&front=maildoc&type='.$newdoc,'parent.','등록되었습니다. ','');
}
else {
	getLink('reload','parent.','수정되었습니다. ','');
}
?>