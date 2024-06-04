<?php
if(!defined('__KIMS__')) exit;

$U = getUidData($table['s_upload'],$uid);
 if ($U['uid'])
 {
     getDbUpdate($table['s_numinfo'],'upload=upload-1',"date='".substr($U['d_regis'],0,8)."' and site=".$U['site']);
     getDbDelete($table['s_upload'],'uid='.$U['uid']);

     if ($U['url']==$d['mediaset']['ftp_urlpath'])
     {
         $FTP_CONNECT = ftp_connect($d['mediaset']['ftp_host'],$d['mediaset']['ftp_port']); 
         $FTP_CRESULT = ftp_login($FTP_CONNECT,$d['mediaset']['ftp_user'],$d['mediaset']['ftp_pass']); 
         if (!$FTP_CONNECT) getLink('','','FTP서버 연결에 문제가 발생했습니다.','');
         if (!$FTP_CRESULT) getLink('','','FTP서버 아이디나 패스워드가 일치하지 않습니다.','');

         ftp_delete($FTP_CONNECT,$d['mediaset']['ftp_folder'].$U['folder'].'/'.$U['tmpname']);
         if($U['type']==2) ftp_delete($FTP_CONNECT,$d['mediaset']['ftp_folder'].$U['folder'].'/'.$U['thumbname']);
         ftp_close($FTP_CONNECT);
     }
     else {
        unlink('.'.$U['url'].$U['folder'].'/'.$U['tmpname']);
        if($U['type']==2) unlink('.'.$U['url'].$U['folder'].'/'.$U['thumbname']);
     }
 }
 echo 'ok';
exit;
?>
