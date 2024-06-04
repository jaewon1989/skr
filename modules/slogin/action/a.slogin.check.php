<?php
if(!defined('__KIMS__')) exit;
$targetModule = 'slogin';
include $g['path_module'].$targetModule.'/var/var.php';

function getCURLData($url,$header)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	if(is_array($header)) curl_setopt($ch, CURLOPT_HTTPHEADER,$header); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt($ch, CURLOPT_COOKIE, '' );
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

	$curl_exec = curl_exec($ch);
	curl_close($ch);
	return $curl_exec;
}
$g['snskor'] = array(
	'f' => array('페이스북','facebook',1000,650,'#39589C'),
   	'k' => array('카카오톡','kakao',400,450,'#EBBA0F'),
	'n' => array('네이버','naver',400,450,'#23C10A'),
	'g' => array('구글','google',450,450,'#D94C35'),
	't' => array('트위터','twitter',810,550,'#1DA1F2'),
	'i' => array('인스타그램','instagram',500,350,'#3D6B92'),
);

//임시
if (is_array($_SESSION['SL']['naver']) || is_array($_SESSION['SL']['kakao']) || is_array($_SESSION['SL']['facebook']) || is_array($_SESSION['SL']['google']) || is_array($_SESSION['SL']['instagram']) || is_array($_SESSION['SL']['twitter']))
{
	$name		= '';
	$nic		= '';
	$email		= '';
	$id			= 'sns'.$g['time_split'][1].substr($g['time_split'][0],2,6);
	$pw			= $id;
	$auth		= 1;
	$mygroup	= 9999;
	$level		= 1;
	$comp		= 0;
	$adm_view	= '';
	$home		= '';
	$birth1		= 0;
	$birth2		= 0;
	$birthtype	= 0;
	$tel1		= '';
	$zip		= '';
	$addr0		= '';
	$addr1		= '';
	$addr2		= '';
	$job		= '';
	$marr1		= 0;
	$marr2		= 0;
	$sms		= 0;
	$mailing	= 0;
	$smail		= 0;
	$point		= 0;
	$usepoint	= 0;
	$money		= 0;
	$cash		= 0;
	$num_login	= 0;
	$pw_q		= '';
	$pw_a		= '';
	$now_log	= 0;
	$last_log	= '';
	$last_pw	= $date['totime'];
	$is_paper	= 0;
	$d_regis	= $date['totime'];
	$sns		= '';
	$noticeconf	= '';
	$num_notice	= 0;
	$addfield	= '';

	if (is_array($_SESSION['SL']['naver']))
	{
		$snsuid	= 'naver-'.$_SESSION['SL']['naver']['userinfo']['birthday'].$_SESSION['SL']['naver']['userinfo']['sex'].$_SESSION['SL']['naver']['userinfo']['age'];
		$name	= $_SESSION['SL']['naver']['userinfo']['name'];
		$nic	= $name;
		$email	= $_SESSION['SL']['naver']['userinfo']['email'];
		$sex	= $_SESSION['SL']['naver']['userinfo']['sex'];
		$birth2	= $_SESSION['SL']['naver']['userinfo']['birthday'];
		$_photo	= $_SESSION['SL']['naver']['userinfo']['photo'];
	}
	if (is_array($_SESSION['SL']['kakao']))
	{
		$snsuid	= 'kakao-'.$_SESSION['SL']['kakao']['userinfo']['uid'];
		$name	= $_SESSION['SL']['kakao']['userinfo']['name'];
		$nic	= $name;
		$birth2	= $_SESSION['SL']['kakao']['userinfo']['birthday'];
		$birthtype	= $_SESSION['SL']['kakao']['userinfo']['birthday_type'];
		$home	= $_SESSION['SL']['kakao']['userinfo']['link'];
		$_photo	= $_SESSION['SL']['kakao']['userinfo']['photo'];
	}
	if (is_array($_SESSION['SL']['facebook']))
	{
		$snsuid	= 'facebook-'.$_SESSION['SL']['facebook']['userinfo']['uid'];
		$name	= $_SESSION['SL']['facebook']['userinfo']['name'];
		$nic	= $name;
		$email	= $_SESSION['SL']['facebook']['userinfo']['email'];
		$sex	= $_SESSION['SL']['facebook']['userinfo']['sex'];
		$birth1	= substr($_SESSION['SL']['facebook']['userinfo']['birthday'],0,4);
		$birth2	= substr($_SESSION['SL']['facebook']['userinfo']['birthday'],6,2).substr($_SESSION['SL']['facebook']['userinfo']['birthday'],4,2);
		$home	= $_SESSION['SL']['facebook']['userinfo']['link'];
		$_photo	= $_SESSION['SL']['facebook']['userinfo']['photo'];
	}
	if (is_array($_SESSION['SL']['google']))
	{
		$snsuid	= 'google-'.$_SESSION['SL']['google']['userinfo']['uid'];
		$name	= $_SESSION['SL']['google']['userinfo']['name'];
		$nic	= $name;
		$email	= $_SESSION['SL']['google']['userinfo']['email'];
		$sex	= $_SESSION['SL']['google']['userinfo']['sex'];
		$home	= $_SESSION['SL']['google']['userinfo']['link'];
		$_photo	= $_SESSION['SL']['naver']['userinfo']['photo'];
	}
	if (is_array($_SESSION['SL']['instagram']))
	{
		$snsuid	= 'instagram-'.$_SESSION['SL']['instagram']['userinfo']['uid'];
		$name	= $_SESSION['SL']['instagram']['userinfo']['name'];
		$nic	= $name;
		$home	= $_SESSION['SL']['instagram']['userinfo']['link'];
		$_photo	= $_SESSION['SL']['instagram']['userinfo']['photo'];
	}
	if (is_array($_SESSION['SL']['twitter']))
	{
		$snsuid	= 'twitter-'.$_SESSION['SL']['twitter']['userinfo']['uid'];
		$name	= $_SESSION['SL']['twitter']['userinfo']['name'];
		$nic	= $name;
		$home	= $_SESSION['SL']['twitter']['userinfo']['link'];
		$_photo	= $_SESSION['SL']['twitter']['userinfo']['photo'];
	}

	//결과값 못 받은 경우
	if (!$name)
	{
		$_SESSION['SL'] = '';
		if($_isModal) getLink('reload','','','');
		else getLink('reload','','','');
	}

	//소셜 로그인 중복체크
	$isSNSlogin = getDbData($table['s_mbrdata'],"sns='".$snsuid."'",'memberuid');
	if ($isSNSlogin['memberuid'])
	{
		$M	= getUidData($table['s_mbrid'],$isSNSlogin['memberuid']);
		$_SESSION['mbr_uid'] = $M['uid'];
		$_SESSION['mbr_pw']  = $M['pw'];
		$_SESSION['SL'] = '';

		if($_isModal) getLink('reload','','','');
		else getLink('reload','','','');
	}

	// //중복 이메일이 존재할 경우
	// $isMember = getDbData($table['s_mbrdata'],"email='".$email."'",'memberuid');
	// if ($isMember['memberuid'])
	// {
	// 	$M	= getUidData($table['s_mbrid'],$isMember['memberuid']); 
	// 	$_SESSION['mbr_uid'] = $M['uid'];
	// 	$_SESSION['mbr_pw']  = $M['pw'];
	// 	$_SESSION['SL'] = '';

	// 	if($_isModal) getLink('reload','','','');
	// 	else getLink('reload','','','');
	// }

	$_pw = md5($pw);
	getDbInsert($table['s_mbrid'],'site,id,pw',"'$s','$id','".$_pw."'");
	$memberuid  = getDbCnt($table['s_mbrid'],'max(uid)','');

	//include $g['path_core'].'function/rss.func.php';
	//$_photodata = getUrlData($_photo,10);
	
	if ($_photo && strpos($_photo,'facebook'))
	{
        require_once $g['path_module'].$targetModule.'/lib/getFacebookPhoto.class.php';
	    $get_real_photo = new sfFacebookPhoto;
		$_realphoto = $get_real_photo->getRealUrl($_photo);
		$_photodata = getCURLData($_realphoto,'');
	}
	else {
		$_photodata = getCURLData($_photo,'');
	}
	
	if ($_photodata)
	{
		$fileExt	= 'jpg';
		$fp = fopen($g['path_var'].'avatar/snstmp.jpg','w');
		fwrite($fp,$_photodata);
		fclose($fp);

		$photo		= $id.'.'.$fileExt;
		$saveFile1	= $g['path_var'].'avatar/'.$photo;
		$saveFile2	= $g['path_var'].'avatar/150.'.$photo;

		include $g['path_core'].'function/thumb.func.php';

		ResizeWidth($g['path_var'].'avatar/snstmp.jpg',$saveFile2,300);
		ResizeWidthHeight($saveFile2,$saveFile1,150,150);
		@chmod($saveFile1,0707);
		@chmod($saveFile2,0707);
		unlink($g['path_var'].'avatar/snstmp.jpg');
	}

	$_QKEY = "memberuid,site,auth,mygroup,level,comp,admin,adm_view,";
	$_QKEY.= "email,name,nic,grade,photo,home,sex,birth1,birth2,birthtype,tel1,tel2,zip,";
	$_QKEY.= "addr0,addr1,addr2,job,marr1,marr2,sms,mailing,smail,point,usepoint,money,cash,num_login,pw_q,pw_a,now_log,last_log,last_pw,is_paper,d_regis,tmpcode,sns,noticeconf,num_notice,addfield";
	$_QVAL = "'$memberuid','$s','$auth','$mygroup','$level','$comp','$admin','$adm_view',";
	$_QVAL.= "'$email','$name','$nic','$grade','$photo','$home','$sex','$birth1','$birth2','$birthtype','$tel1','$tel2','$zip',";
	$_QVAL.= "'$addr0','$addr1','$addr2','$job','$marr1','$marr2','$sms','$mailing','$smail','$point','$usepoint','$money','$cash','$num_login','$pw_q','$pw_a','$now_log','$last_log','$last_pw','$is_paper','$d_regis','','$snsuid','$noticeconf','$num_notice','$addfield'";
	getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);
	getDbUpdate($table['s_mbrlevel'],'num=num+1','uid='.$level);
	getDbUpdate($table['s_mbrgroup'],'num=num+1','uid='.$sosok);

	$_SESSION['mbr_uid'] = $memberuid;
	$_SESSION['mbr_pw']  = $_pw;
	$_SESSION['SL'] = '';


	if($_isModal) getLink('reload','','','');
	else getLink('reload','','','');
}

// $slogin['naver'] = socialLogin('naver',$d['sociallogin']['key_n'],$d['sociallogin']['secret_n'],$g['url_root'].'/?r='.$r.'&m='.$targetModule.'&a=slogin&sloginReturn=naver',false);
// $slogin['kakao'] = socialLogin('kakao',$d['sociallogin']['key_k'],$d['sociallogin']['secret_k'],$g['url_root'].'/?r='.$r.'&m='.$targetModule.'&a=slogin&sloginReturn=kakao',false);
// $slogin['facebook'] = socialLogin('facebook',$d['sociallogin']['key_f'],$d['sociallogin']['secret_f'],$g['url_root'].'/?r='.$r.'&m='.$targetModule.'&a=slogin&sloginReturn=facebook',false);
// $slogin['google'] = socialLogin('google',$d['sociallogin']['key_g'],$d['sociallogin']['secret_g'],$g['url_root'].'/?r='.$r.'&m='.$targetModule.'&a=slogin&sloginReturn=google',false);
// $slogin['instagram'] = socialLogin('instagram',$d['sociallogin']['key_i'],$d['sociallogin']['secret_i'],$g['url_root'].'/?r='.$r.'&m='.$targetModule.'&a=slogin&sloginReturn=instagram',false);
// $slogin['twitter'] = socialLogin('twitter',$d['sociallogin']['key_t'],$d['sociallogin']['secret_t'],$g['url_root'].'/?r='.$r.'&m='.$targetModule.'&a=slogin&sloginReturn=twitter',false);
?>