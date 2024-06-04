<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);
include_once $g['dir_module'].'var/var.php';

foreach ($product_members as $val)
{
	$R=getUidData($table[$m.'product'],$val);
	if (!$R['uid']) continue;

	getDbDelete($table[$m.'product'],'uid='.$R['uid']); //상품삭제
	getDbDelete($table[$m.'qna'],'product='.$R['uid']);//상품문의삭제
	getDbDelete($table[$m.'comment'],'product='.$R['uid']);//상품평가삭제
	getDbDelete($table[$m.'wish'],'product='.$R['uid']);//위시리스트삭제
	getDbUpdate($table[$m.'category'],'num=num-1','uid='.$R['category']);//카테고리갱신

	if ($R['upfiles'])
	{
		$UPFILES = getArrayString($R['upfiles']);

		foreach($UPFILES['data'] as $_val)
		{
			$U = getUidData($table[$m.'upload'],$_val);
			if ($U['uid'])
			{
				getDbDelete($table[$m.'upload'],'uid='.$U['uid']);
				if ($U['url']==$d['shop']['ftp_urlpath'])
				{
					$FTP_CONNECT = ftp_connect($d['shop']['ftp_host'],$d['shop']['ftp_port']); 
					$FTP_CRESULT = ftp_login($FTP_CONNECT,$d['shop']['ftp_user'],$d['shop']['ftp_pass']); 
					if (!$FTP_CONNECT) getLink('','','FTP서버 연결에 문제가 발생했습니다.','');
					if (!$FTP_CRESULT) getLink('','','FTP서버 아이디나 패스워드가 일치하지 않습니다.','');

					ftp_delete($FTP_CONNECT,$d['shop']['ftp_folder'].$U['folder'].'/'.$U['tmpname']);
					ftp_close($FTP_CONNECT);
				}
				else {
					unlink($g['dir_module'].'files/'.$U['folder'].'/'.$U['tmpname']);
				}
			}
		}
	}
	if ($R['ext'])
	{
		$fileFolder = substr($R['d_regis'],0,4).'/'.substr($R['d_regis'],4,2).'/'.substr($R['d_regis'],6,2).'/'.$R['uid'];
		@unlink($g['dir_module'].'files/'.$fileFolder.'_1.'.$R['ext']);
		@unlink($g['dir_module'].'files/'.$fileFolder.'_2.'.$R['ext']);
		@unlink($g['dir_module'].'files/'.$fileFolder.'_3.'.$R['ext']);
		@unlink($g['dir_module'].'files/'.$fileFolder.'_4.'.$R['ext']);
	}
}

getLink('reload','parent.','삭제되었습니다.','');
?>