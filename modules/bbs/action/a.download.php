<?php
if(!defined('__KIMS__')) exit;
include_once $g['path_module'].'mediaset/var/var.php';
$R=getUidData($table['s_upload'],$uid);
if (!$R['uid']) getLink('','','정상적인 요청이 아닙니다.','');
$filename = getUTFtoKR($R['name']);
$filetmpname = getUTFtoKR($R['tmpname']);
if ($R['url']==$d['upload']['ftp_urlpath'])
{
	$filepath = $d['upload']['ftp_urlpath'].$R['folder'].'/'.$filetmpname;
	$filesize = $R['size'];
}
else {
	$filepath = '.'.$R['url'].$R['folder'].'/'.$filetmpname;
	$filesize = filesize($filepath);
}
if (!strstr($_SERVER['HTTP_REFERER'],'module='.$m) && !$my['admin'])
{

	//동기화
	$cyncArr = getArrayString($R['sync']); //[blog][60][uid,down][rb_blog_data][1][m:blog,bid:3,uid:60]
	$fdexp = explode(',',$cyncArr['data'][2]);
	if($fdexp[0]&&$fdexp[1]&&$cyncArr['data'][3])
	{
		// if ($cyncArr['data'][0] == 'blog' && $cyncArr['data'][1])
		// {
		// 	$AT = getUidData($table[$cyncArr['data'][0].'data'],$cyncArr['data'][1]);
		// 	include_once $g['path_module'].$cyncArr['data'][0].'/var/var.'.$AT['bbsid'].'.php';
		// 	$B['var'] = $d['bbs'];
		// 	if ($B['var']['perm_l_view'] > $my['level'] || strstr($B['var']['perm_g_view'],'['.$my['sosok'].']'))
		// 	{
		// 		getLink('','','다운로드 권한이 없습니다.','-1');
		// 	}
		// }
		$cyncQue = $fdexp[1].'='.$fdexp[1].'+1';
		getDbUpdate($cyncArr['data'][3],$cyncQue,$fdexp[0].'='.$cyncArr['data'][1]);
	}
	getDbUpdate($table['s_upload'],'down=down+1','uid='.$R['uid']);
	getDbUpdate($table['s_numinfo'],'download=download+1',"date='".$date['today']."' and site=".$s);
}
header("Content-Type: application/octet-stream"); 
header("Content-Length: " .$filesize); 
header('Content-Disposition: attachment; filename="'.$filename.'"'); 
header("Cache-Control: private, must-revalidate"); 
header("Pragma: no-cache");
header("Expires: 0");
if ($R['url']==$d['upload']['ftp_urlpath'])
{
	$FTP_CONNECT = ftp_connect($d['upload']['ftp_host'],$d['upload']['ftp_port']); 
	$FTP_CRESULT = ftp_login($FTP_CONNECT,$d['upload']['ftp_user'],$d['upload']['ftp_pass']); 
	if (!$FTP_CONNECT) getLink('','','FTP서버 연결에 문제가 발생했습니다.','');
	if (!$FTP_CRESULT) getLink('','','FTP서버 아이디나 패스워드가 일치하지 않습니다.','');
	
	$filepath = $g['path_tmp'].'session/'.$filetmpname;
	ftp_get($FTP_CONNECT,$filepath,$d['upload']['ftp_folder'].$R['folder'].'/'.$filetmpname,FTP_BINARY);
	ftp_close($FTP_CONNECT);
	$fp = fopen($filepath, 'rb');
	if (!fpassthru($fp)) fclose($fp);
	unlink($filepath);
}
else {
	$fp = fopen($filepath, 'rb');
	if (!fpassthru($fp)) fclose($fp);
}
exit;
?>