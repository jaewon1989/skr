<?php

include $g['path_core'].'function/thumb.func.php';

// 업로드 디렉토리 없는 경우 추가 
if(!is_dir($saveDir)){
   mkdir($saveDir,0707);
   @chmod($saveDir,0707);
}

$tmpcode	= $sescode;
$album      = $album?$album:1;     
$fserver	= $d[$m]['up_use_fileserver'];
$url		= $fserver ? $d[$m]['ftp_urlpath'] : $g['url_root'];
$hidden		= $type == 2 ? 1 : 0;
$savePath1	= $saveDir.substr($date['today'],0,4);
$savePath2	= $savePath1.'/'.substr($date['today'],4,2);
$savePath3	= $savePath2.'/'.substr($date['today'],6,2);
$folder		= $savePath3;//saveDir.substr($date['today'],0,4).'/'.substr($date['today'],4,2).'/'.substr($date['today'],6,2);
$width		= 0;
$height		= 0;
$caption	= trim($caption);
$down		= 0;
$d_regis	= $date['totime'];


for ($i = 1; $i < 4; $i++)
{
	if (!is_dir(${'savePath'.$i}))
	{
		mkdir(${'savePath'.$i},0707);
		@chmod(${'savePath'.$i},0707);
	}
}

for($i=0;$i<$photo_num;$i++){

	if (!$_FILES['photos']['tmp_name'][$i]) continue;
	$name	= strtolower($_FILES['photos']['name'][$i]);
	$fileExt	= getExt($name);
	$fileExt	= $fileExt == 'jpeg' ? 'jpg' : $fileExt;
	$type	    = getFileType($fileExt);
	$tmpname	= md5($name).substr($date['totime'],8,14);
	$tmpname	= $type == 2 ? $tmpname.'.'.$fileExt : $tmpname;
	$saveFile   = $savePath3.'/'.$tmpname;
	$size		= $_FILES['photos']['size'][$i];
	$ext = $fileExt;

	if (!is_file($saveFile))
	{
		move_uploaded_file($_FILES['photos']['tmp_name'][$i], $saveFile);
	
		if ($type == 2)
		{
			$thumbname = md5($tmpname).'.'.$fileExt;
			$thumbFile = $savePath3.'/'.$thumbname;
			ResizeWidth($saveFile,$thumbFile,200);
			@chmod($thumbFile,0707);
			$IM = getimagesize($saveFile);
			$width = $IM[0];
			$height= $IM[1];
		}
	    
		@chmod($thumbFile,0707); // 썸네일 
		@chmod($saveFile,0707); // 원본 
	}

	$mingid = getDbCnt($table[$m.'photo'],'min(gid)','');
	$gid = $mingid ? $mingid - 1 : 100000000;
	$folder=str_replace('.','',$folder);

	$QKEY = "gid,module,album,hidden,tmpcode,post,mbruid,type,ext,fserver,url,folder,name,tmpname,thumbname,size,width,height,d_regis";
	$QVAL = "'$gid','$module','$album','$hidden','$tmpcode','$post','$mbruid','$type','$ext','$fserver','$url','$folder','$name','$tmpname','$thumbname','$size','$width','$height','$d_regis'";
	getDbInsert($table[$m.'photo'],$QKEY,$QVAL);

	if ($gid == 100000000) db_query("OPTIMIZE TABLE ".$table[$m.'photo'],$DB_CONNECT); 
}	

?>
