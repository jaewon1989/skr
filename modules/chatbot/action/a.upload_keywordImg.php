<?php
if(!defined('__KIMS__')) exit;

include $g['dir_module'].'var/var.php';

// 업로드 디렉토리 없는 경우 추가 
if(!is_dir($saveDir)){
   mkdir($saveDir,0707);
 @chmod($saveDir,0707);
}
if(!$sescode){
    $code='200';
    $msg='정상적인 접근이 아닙니다.';
    $result=array($code,$msg);  
    echo json_encode($result);
    exit;
} 
$tmpcode = $sescode;
$mbruid  = $my['uid'];
$fserver = $d['mediaset']['up_use_fileserver'];
$url  = $fserver ? $d['mediaset']['ftp_urlpath'] : str_replace('.','',$saveDir);
$name  = strtolower($_FILES['file']['name']);
$size  = $_FILES['file']['size'];
$width  = 0;
$height  = 0;
$caption = trim($caption);
$down  = 0;
$d_regis = $date['totime'];
$d_update = '';
$fileExt = getExt($name);
$fileExt = $fileExt == 'jpeg' ? 'jpg' : $fileExt;
$type  = getFileType($fileExt);
$tmpname = md5($name).substr($date['totime'],8,14);
$tmpname = $type == 2 ? $tmpname.'.'.$fileExt : $tmpname;
$hidden  = $type == 2 ? 1 : 0;

if ($d['mediaset']['up_ext_cut'] && strstr($d['mediaset']['up_ext_cut'],$fileExt))
{
    $code='200';
    $msg='정상적인 접근이 아닙니다.';
    $result=array($code,$msg);  
    echo json_encode($result);
    exit;
} 


$savePath1 = $saveDir.substr($date['today'],0,4);
$savePath2 = $savePath1.'/'.substr($date['today'],4,2);
$savePath3 = $savePath2.'/'.substr($date['today'],6,2);
$folder  = substr($date['today'],0,4).'/'.substr($date['today'],4,2).'/'.substr($date['today'],6,2); 

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
           $IM = getimagesize($saveFile);
           $width = $IM[0];
           $height= $IM[1];
    }
    @chmod($saveFile,0707);
}


$mingid = getDbCnt($table[$m.'upload'],'min(gid)','');
$gid = $mingid ? $mingid - 1 : 100000000;

$QKEY = "gid,hidden,tmpcode,mbruid,type,ext,fserver,url,folder,name,tmpname,thumbname,size,width,height,caption,d_regis";
$QVAL = "'$gid','$hidden','$tmpcode','$mbruid','$type','$fileExt','$fserver','$url','$folder','$name','$tmpname','$thumbname','$size','$width','$height','$caption','$d_regis'";
getDbInsert($table[$m.'upload'],$QKEY,$QVAL);

if ($gid == 100000000) db_query("OPTIMIZE TABLE ".$table[$m.'upload'],$DB_CONNECT); 

$lastuid= getDbCnt($table[$m.'upload'],'max(uid)','');
$_LU=getUidData($table[$m.'upload'],$lastuid);

$sourcePath=$_LU['url'].$_LU['folder'].'/'.$_LU['tmpname']; 
$code='100';
$src=$saveFile;
$file_name = $_LU['name'];
$result=array($code,$sourcePath,$lastuid,$file_name); // 이미지 path 및 이미지 uid 값 
echo json_encode($result);// 최종적으로 에디터에 넘어가는 값 
exit;
?>
