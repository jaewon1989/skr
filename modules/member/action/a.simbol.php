<?php
if(!defined('__KIMS__')) exit;

if (!$my['uid'])
{
	getLink('','','정상적인 접근이 아닙니다.','');
}

$tmpname	= $_FILES['upfile']['tmp_name'];
$realname	= $_FILES['upfile']['name'];
$fileExt	= strtolower(getExt($realname));
$fileExt	= $fileExt == 'jpeg' ? 'jpg' : $fileExt;
$photo		= $my['id'].'.'.$fileExt;
$saveFile1	= $g['path_var'].'simbol/'.$photo;
$saveFile2	= $g['path_var'].'simbol/180.'.$photo;

if (is_uploaded_file($tmpname))
{
	if (!strstr('[gif][jpg][png]',$fileExt))
	{
		getLink('','','gif/jpg/png 파일만 등록할 수 있습니다.','');
	}
	if (is_file($saveFile1))
	{
		unlink($saveFile1);
	}
	if (is_file($saveFile2))
	{
		unlink($saveFile2);
	}

	$wh = getimagesize($tmpname);
	if ($wh[0] < 180 || $wh[1] < 180)
	{
		getLink('','','가로/세로 180픽셀 이상이어야 합니다.','');
	}

	include_once $g['path_core'].'function/thumb.func.php';

	move_uploaded_file($tmpname,$saveFile2);
	ResizeWidth($saveFile2,$saveFile2,180);
	ResizeWidthHeight($saveFile2,$saveFile1,50,50);
	@chmod($saveFile1,0707);
	@chmod($saveFile2,0707);

	getDbUpdate($table['s_mbrdata'],"photo='".$photo."'",'memberuid='.$my['uid']);
}


getLink('reload','parent.','','');
?>