<?php
if(!defined('__KIMS__')) exit;

include $g['path_core'].'function/thumb.func.php';
include $g['path_module'].'blog/var/var.php';

$sessArr	= explode('_',$sess_Code);
$tmpcode	= $sessArr[0];
$mbruid		= $sessArr[1];
$blog		= $sessArr[2];
$fserver	= $d['blog']['up_use_fileserver'];
$url		= $fserver ? $d['blog']['ftp_urlpath'] : $g['url_root'].'/modules/'.$m.'/files/';
$name		= strtolower($_FILES['file']['name']);
$size		= $_FILES['file']['size'];
$width		= 0;
$height		= 0;
$caption	= trim($caption);
$down		= 0;
$d_regis	= $date['totime'];
$d_update	= '';
$fileExt	= getExt($name);
$fileExt	= $fileExt == 'jpeg' ? 'jpg' : $fileExt;
$type		= getFileType($fileExt);
$tmpname	= md5($name).substr($date['totime'],8,14);
$tmpname	= $type == 2 ? $tmpname.'.'.$fileExt : $tmpname;
$hidden		= $type == 2 ? 1 : 0;

if ($d['blog']['up_ext_cut'] && strstr($d['blog']['up_ext_cut'],$fileExt))
{
    $code='200';
    $msg='정상적인 접근이 아닙니다.';
    $result=array($code,$msg);  
    echo json_encode($result);
    exit;
} 

$savePath1	= $saveDir.substr($date['today'],0,4);
$savePath2	= $savePath1.'/'.substr($date['today'],4,2);
$savePath3	= $savePath2.'/'.substr($date['today'],6,2);
$folder		= substr($date['today'],0,4).'/'.substr($date['today'],4,2).'/'.substr($date['today'],6,2);

if ($fserver)
{
	$FTP_CONNECT = ftp_connect($d['blog']['ftp_host'],$d['blog']['ftp_port']); 
	$FTP_CRESULT = ftp_login($FTP_CONNECT,$d['blog']['ftp_user'],$d['blog']['ftp_pass']); 
	if ($d['blog']['ftp_pasv']) ftp_pasv($FTP_CONNECT, true);
	if (!$FTP_CONNECT) exit;
	if (!$FTP_CRESULT) exit;

	ftp_chdir($FTP_CONNECT,$d['blog']['ftp_folder']);

	for ($i = 1; $i < 4; $i++)
	{
		ftp_mkdir($FTP_CONNECT,$d['blog']['ftp_folder'].str_replace('./files/','',${'savePath'.$i}));
	}

	if ($Overwrite == 'true' || !is_file($saveFile))
	{
		if ($type == 2)
		{
			$thumbname = md5($tmpname).'.'.$fileExt;
			$thumbFile = $g['path_tmp'].'backup/'.$thumbname;
			ResizeWidth($_FILES['file']['tmp_name'],$thumbFile,150);
			$IM = getimagesize($_FILES['file']['tmp_name']);
			$width = $IM[0];
			$height= $IM[1];
			ftp_put($FTP_CONNECT,$d['blog']['ftp_folder'].$folder.'/'.$thumbname,$thumbFile,FTP_BINARY);
			unlink($thumbFile);
		}
	}
	ftp_put($FTP_CONNECT,$d['blog']['ftp_folder'].$folder.'/'.$tmpname,$_FILES['file']['tmp_name'],FTP_BINARY);
	ftp_close($FTP_CONNECT);
}
else {

	for ($i = 1; $i < 4; $i++)
	{
		if (!is_dir(${'savePath'.$i}))
		{
			mkdir(${'savePath'.$i},0707);
			@chmod(${'savePath'.$i},0707);
		}
	}

	$saveFile = $savePath3.'/'.$tmpname;

	if ($Overwrite == 'true' || !is_file($saveFile))
	{
		move_uploaded_file($_FILES['file']['tmp_name'], $saveFile);
		if ($type == 2)
		{
			$thumbname = md5($tmpname).'.'.$fileExt;
			$thumbFile = $savePath3.'/'.$thumbname;
			ResizeWidth($saveFile,$thumbFile,150);
			@chmod($thumbFile,0707);
			$IM = getimagesize($saveFile);
			$width = $IM[0];
			$height= $IM[1];
		}
		@chmod($saveFile,0707);
	}

}

$mingid = getDbCnt($table[$m.'upload'],'min(gid)','');
$gid = $mingid ? $mingid - 1 : 100000000;

$QKEY = "gid,hidden,tmpcode,blog,parent,mbruid,type,ext,fserver,url,folder,name,tmpname,thumbname,size,width,height,caption,down,d_regis,d_update";
$QVAL = "'$gid','$hidden','$tmpcode','$blog','0','$mbruid','$type','$fileExt','$fserver','$url','$folder','$name','$tmpname','$thumbname','$size','$width','$height','$caption','$down','$d_regis','$d_update'";
getDbInsert($table[$m.'upload'],$QKEY,$QVAL);

if ($gid == 100000000) db_query("OPTIMIZE TABLE ".$table[$m.'upload'],$DB_CONNECT); 

if ($upType == 'normal')
{
	getLink($g['s'].'/?r='.$r.'&m='.$m.'&blog='.$blog.'&upload=Y&mod='.$mod.'&gparam='.$gparam.($cupload?'&cupload='.$cupload:''),'','','');
}
           

$lastuid= getDbCnt($table[$m.'upload'],'max(uid)','');
$sourcePath='./modules/'.$m.str_replace('..','',$savePath3); // 소스에 보여주는 패스트 -- 상대경로를  제거한다.  
$code='100';
$src=$saveFile;
$result=array($code,$src,$lastuid); // 이미지 path 및 이미지 uid 값 
echo json_encode($result);// 최종적으로 에디터에 넘어가는 값 
exit;
?>
