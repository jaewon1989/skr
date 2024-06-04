<?php
if(!defined('__KIMS__')) exit;

$param_arr=explode('@',$_GET['params']);
$themeUrl=$param_arr[0];
$saveDir=$param_arr[1];
$path_core=$param_arr[2];
$path_module=$param_arr[3];
$sess_Code=$param_arr[4];

// 업로드 디렉토리 없는 경우 추가 
if(!is_dir($saveDir)){
   mkdir($saveDir,0707);
 @chmod($saveDir,0707);
}

include_once $path_module.'mediaset/var/var.php';
include $path_core.'function/thumb.func.php';

$sessArr  = explode('_',$sess_Code);
$tmpcode  = $sessArr[0];
$mbruid   = $sessArr[1];
$parent   = $sessArr[2];
$fserver  = $d['mediaset']['up_use_fileserver'];
$url    = $fserver ? $d['mediaset']['ftp_urlpath'] : str_replace('.','',$saveDir);
$name   = strtolower($_FILES['files']['name']);
$size   = $_FILES['files']['size'];
$width    = 0;
$height   = 0;
$caption  = trim($caption);
$down   = 0;
$d_regis  = $date['totime'];
$d_update = '';
$fileExt  = getExt($name);
$fileExt  = $fileExt == 'jpeg' ? 'jpg' : $fileExt;
$type   = getFileType($fileExt);
$tmpname  = md5($name).substr($date['totime'],8,14);
$tmpname  = $type == 2 ? $tmpname.'.'.$fileExt : $tmpname;
$hidden   = $type == 2 ? 1 : 0;



if ($d['mediaset']['up_ext_cut'] && strstr($d['mediaset']['up_ext_cut'],$fileExt))
{
    $code='200';
    $msg='정상적인 접근이 아닙니다.';
    $result=array($code,$msg);  
    echo json_encode($result);
    exit;
} 

$savePath1  = $saveDir.substr($date['today'],0,4);
$savePath2  = $savePath1.'/'.substr($date['today'],4,2);
$savePath3  = $savePath2.'/'.substr($date['today'],6,2);
$folder   = substr($date['today'],0,4).'/'.substr($date['today'],4,2).'/'.substr($date['today'],6,2);
if(isset($_FILES["files"]))
{
   if ($fserver)
    {
      $FTP_CONNECT = ftp_connect($d['mediaset']['ftp_host'],$d['mediaset']['ftp_port']); 
      $FTP_CRESULT = ftp_login($FTP_CONNECT,$d['mediaset']['ftp_user'],$d['mediaset']['ftp_pass']); 
      if ($d['mediaset']['ftp_pasv']) ftp_pasv($FTP_CONNECT, true);
      if (!$FTP_CONNECT) exit;
      if (!$FTP_CRESULT) exit;

      ftp_chdir($FTP_CONNECT,$d['mediaset']['ftp_folder']);

      for ($i = 1; $i < 4; $i++)
      {
        ftp_mkdir($FTP_CONNECT,$d['mediaset']['ftp_folder'].str_replace('./files/','',${'savePath'.$i}));
      }

      if ($Overwrite == 'true' || !is_file($saveFile))
      {
        if ($type == 2)
        {
           $IM = getimagesize($_FILES['files']['tmp_name']);
          $width = $IM[0];
          $height= $IM[1];
        }
      }
      ftp_put($FTP_CONNECT,$d['mediaset']['ftp_folder'].$folder.'/'.$tmpname,$_FILES['files']['tmp_name'],FTP_BINARY);
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
        move_uploaded_file($_FILES['files']['tmp_name'], $saveFile);
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
    // DB 저장 
    $mingid = getDbCnt($table['s_upload'],'min(gid)','');
    $gid = $mingid ? $mingid - 1 : 100000000;

    $QKEY = "gid,pid,hidden,tmpcode,parent,mbruid,type,ext,fserver,url,folder,name,tmpname,thumbname,size,width,height,caption,down,d_regis,d_update";
    $QVAL = "'$gid','$gid','$hidden','$tmpcode','','$mbruid','$type','$fileExt','$fserver','$url','$folder','$name','$tmpname','$thumbname','$size','$width','$height','$caption','$down','$d_regis','$d_update'";
    getDbInsert($table['s_upload'],$QKEY,$QVAL);

    if ($gid == 100000000) db_query("OPTIMIZE TABLE ".$table['s_upload'],$DB_CONNECT); 

    $lastuid= getDbCnt($table['s_upload'],'max(uid)','');
    $R=getUidData($table['s_upload'],$lastuid);
    $src=$R['url'].$R['folder'].'/'.$R['thumbname'];
    $btnDelimg=$themeUrl.'/img/btn-file-del.png';    
    $html="";
    $html .='<div class="file-wrapper">';
    $html .='<input type="hidden" name="upfiles[]" value="['.$R['uid'].']"/>';
    $html .='<div class="img-preview"><img src="'.$src.'" alt="'.$name.'" ></div>';
    $html .='<img class="btn-file-del hide" src="'.$btnDelimg.'" id="'.$R['uid'].'">';
    $html .='</div>';

    echo $html; 

}  
exit;
?>
