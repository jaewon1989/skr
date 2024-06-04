<?php
if(!defined('__KIMS__')) exit;
checkAdmin(0);
if ($act == 'ingam_delete')
{
	unlink($g['dir_module'].'var/ingam.gif');
	getLink('reload','parent.','','');
}
$fdset['theme'] = array('PB_path_desktop','PB_path_mobile','layout','layout_m','skin_main','skin_mobile','thumb1','thumb2','thumb3','thumb4','sosokmenu');
$fdset['server'] = array('use_fileserver','ftp_host','ftp_port','ftp_user','ftp_pass','ftp_folder','ftp_urlpath');
$fdset['config'] = array('m_company','m_ceoname','m_saupja','m_upte','m_jongmok','m_zip','m_addr1','m_addr2','m_tongsin','m_shopmail','m_shoptel','m_shopfax');
$fdset['bank'] = array('bank');
$fdset['card'] = array('pgcomp','pgid','pgkey','pgtest','useescr');
$fdset['pay'] = array('card','virt','ziro','phone','card_mobile','virt_mobile','ziro_mobile','phone_mobile','ptype','pay_order','point1','point2','point3','pjeolsa','jeolsa');
$fdset['tack'] = array('tacktype','freeprice','tackprice','usestack','stack','chackbulstr','bundletack');
$fdset['cancel'] = array('cancel','cancel_stock1','cancel_stock2','cancel_stock3','pointgive','pointstep','cancel_point','fixdate','send_order_1','send_order_2','send_order_email');
$fdset['qna'] = array('f_qna_update','f_qna_show','f_cment_auth','f_cment_update','f_cment_show','f_cment_length');
$fdset['tax'] = array('taxtype','tack','point','per','use_cash','use_tax');
$gfile= $g['dir_module'].'var/var.php';
include_once $gfile;
foreach ($fdset[$type] as $val) $d['shop'][$val] = trim(${$val});
$fp = fopen($gfile,'w');
fwrite($fp, "<?php\n");
foreach ($d['shop'] as $key => $val)
{
	fwrite($fp, "\$d['shop']['".$key."'] = \"".$val."\";\n");
}
fwrite($fp, "?>");
fclose($fp);
@chmod($gfile,0707);
if ($type == 'tack')
{
	$cfile = $g['dir_module'].'/var/data.tack.txt';
	$fp = fopen($cfile,'w');
	foreach($members as $val)
	{
		fwrite($fp,$val."\n");
	}
	fclose($fp);
	@chmod($cfile,0707);
}
if ($type == 'tax')
{
	$tmpname	= $_FILES['upfile']['tmp_name'];
	$realname	= $_FILES['upfile']['name'];
	$fileExt	= strtolower(getExt($realname));
	$saveFile	= $g['dir_module'].'var/ingam.gif';
	if (is_uploaded_file($tmpname))
	{
		if (!strstr('[gif]',$fileExt))
		{
			getLink('','','gif 파일만 등록할 수 있습니다.','');
		}
		move_uploaded_file($tmpname,$saveFile);
	}
}
getLink('reload','parent.','','');
?>
