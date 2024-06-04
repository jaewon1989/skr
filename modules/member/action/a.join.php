<?php
if(!defined('__KIMS__')) exit;

include_once $g['dir_module'].'var/var.join.php';

$id			= trim($email);
$name		= trim($name);
$nic		= trim($nic);
$nic		= $nic ? $nic : $name;
$email		= trim($email);

// if (!$check_id || ($d['member']['form_nic'] && !$check_nic) || !$check_email)
// {
// 	getLink('','','정상적인 접근이 아닙니다.','');
// }

if(strstr(','.$d['member']['join_cutid'].',',','.$id.',') || getDbRows($table['s_mbrid'],"id='".$id."'"))
{
	getLink('','','사용할 수 없는 아이디입니다.','');
}
if(!$d['member']['join_rejoin'])
{
	if(is_file($g['path_tmp'].'out/'.$id.'.txt'))
	{
		getLink('','','사용할 수 없는 아이디입니다.','');
	}
}

if(getDbRows($table['s_mbrdata'],"email='".$email."'"))
{
	getLink('','','이미 존재하는 이메일입니다.','');
}
if($d['member']['form_nic'])
{
	if(strstr(','.$d['member']['join_cutnic'].',',','.$nic.',') || getDbRows($table['s_mbrdata'],"nic='".$nic."'"))
	{
		getLink('','','사용할 수 없는 닉네임입니다.','');
	}
}
//getDbInsert($table['s_mbrid'],'site,id,pw',"'$s','$id','".md5($pw1)."'"); 1.2 암호변경 방식을 아래방식으로 변경
getDbInsert($table['s_mbrid'],'site,id,pw',"'$s','$id','".getCrypt($pw1,$date['totime'])."'");
$memberuid  = getDbCnt($table['s_mbrid'],'max(uid)','');
$auth		= $d['member']['join_auth'];
$mygroup		= $d['member']['join_group'];
$level		= $d['member']['join_level'];
$comp		= $d['member']['form_comp'] && $comp ? 1 : 0;
$admin		= 0;
$name		= trim($name);
$photo		= '';
$home		= $home ? (strstr($home,'http://')?str_replace('http://','',$home):$home) : '';
$birth1		= $birth_1;
$birth2		= $birth_2.$birth_3;
$birthtype	= $birthtype ? $birthtype : 0;
$tel1		= $tel1_1 && $tel1_2 && $tel1_3 ? $tel1_1 .'-'. $tel1_2 .'-'. $tel1_3 : '';
//$tel2		= $tel2_1 && $tel2_2 && $tel2_3 ? $tel2_1 .'-'. $tel2_2 .'-'. $tel2_3 : '';

if(!$foreign)
{
	//$zip		= $zip_1.$zip_2; //우편번호 5 자리 적용 
	$addrx		= explode(' ',$addr1);
	$addr0		= $addr1 && $addr2 ? $addrx[0] : '';
   $addr0		= substr($addr0,0,6); // 신주소 및 기타 주소 API 의 경우 '서울특별시' 와 같은 형태로 입력되기 때문에 2 글자로 잘라서 저장(php 4.0 이상 가능 ) 2015.1.11 by kiere@naver.com	
	$addr1		= $addr1 && $addr2 ? $addr1 : '';
	$addr2		= trim($addr2);
}
else {
	$zip		= '';
	$addr0		= '해외';
	$addr1		= '';
	$addr2		= '';
}
$job		= trim($job);
$marr1		= $marr_1 && $marr_2 && $marr_3 ? $marr_1 : 0;
$marr2		= $marr_1 && $marr_2 && $marr_3 ? $marr_2.$marr_3 : 0;
$sms		= $tel2 && $sms ? 1 : 0;
$mailing	= $remail;
$smail		= 0;
$point		= $d['member']['join_point'];
$usepoint	= 0;
$money		= 0;
$cash		= 0;
$num_login	= 1;
$pw_q		= trim($pw_q);
$pw_a		= trim($pw_a);
$now_log	= 1;
$last_log	= $date['totime'];
$last_pw	= $date['totime'];
$is_paper	= 0;
$d_regis	= $date['totime'];
$sns		= '';
$addfield	= '';

$_addarray	= file($g['path_module'].$m.'/var/add_field.txt');
foreach($_addarray as $_key)
{
	$_val = explode('|',trim($_key));
	if ($_val[2] == 'checkbox')
	{
		$addfield .= $_val[0].'^^^';
		if (is_array(${'add_'.$_val[0]}))
		{
			foreach(${'add_'.$_val[0]} as $_skey)
			{
				$addfield .= '['.$_skey.']';
			}
		}
		$addfield .= '|||';
	}
	else {
		$addfield .= $_val[0].'^^^'.trim(${'add_'.$_val[0]}).'|||';
	}
}

$_QKEY = "memberuid,site,auth,mygroup,level,comp,admin,adm_view,";
$_QKEY.= "email,name,nic,grade,photo,home,sex,birth1,birth2,birthtype,tel1,tel2,zip,";
$_QKEY.= "addr0,addr1,addr2,job,marr1,marr2,sms,mailing,smail,point,usepoint,money,cash,num_login,pw_q,pw_a,now_log,last_log,last_pw,is_paper,d_regis,tmpcode,sns,addfield,age";
$_QVAL = "'$memberuid','$s','$auth','$mygroup','$level','$comp','$admin','',";
$_QVAL.= "'$email','$name','$nic','','$photo','$home','$sex','$birth1','$birth2','$birthtype','$tel1','$tel2','$zip',";
$_QVAL.= "'$addr0','$addr1','$addr2','$job','$marr1','$marr2','$sms','$mailing','$smail','$point','$usepoint','$money','$cash','$num_login','$pw_q','$pw_a','$now_log','$last_log','$last_pw','$is_paper','$d_regis','','$sns','$addfield','$age'";
getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);
getDbUpdate($table['s_mbrlevel'],'num=num+1','uid='.$level);
getDbUpdate($table['s_mbrgroup'],'num=num+1','uid='.$mygroup);
getDbUpdate($table['s_numinfo'],'login=login+1,mbrjoin=mbrjoin+1',"date='".$date['today']."' and site=".$s);

if($_FILES['profile_photo']['tmp_name']){
	$tmpname	= $_FILES['profile_photo']['tmp_name'];
	$realname	= $_FILES['profile_photo']['name'];
	// $result['message']='ddd'.$realname;
	// exit;
	$fileExt	= strtolower(getExt($realname));
	$fileExt	= $fileExt == 'jpeg' ? 'jpg' : $fileExt;
	$photo		= $id.'.'.$fileExt;
	$saveFile1	= $g['path_var'].'avatar/'.$photo;
	$saveFile2	= $g['path_var'].'avatar/180.'.$photo;
	if (is_uploaded_file($tmpname))
	{
		if (is_file($saveFile1))
		{
			unlink($saveFile1);
		}
		if (is_file($saveFile2))
		{
			unlink($saveFile2);
		}
		$wh = getimagesize($tmpname);
		include_once $g['path_core'].'function/thumb.func.php';
		move_uploaded_file($tmpname,$saveFile2);
		ResizeWidth($saveFile2,$saveFile2,300);
		ResizeWidthHeight($saveFile2,$saveFile1,200,200);
		@chmod($saveFile1,0707);
		@chmod($saveFile2,0707);
		getDbUpdate($table['s_mbrdata'],"photo='".$photo."'",'memberuid='.$memberuid);
	} 
}

if ($comp)
{
	$comp_num	= $comp_num_1 && $comp_num_2 && $comp_num_3 ? $comp_num_1.$comp_num_2.$comp_num_3 : 0;
	//$comp_type	= $comp_type;
	$comp_name	= trim($comp_name);
	$comp_ceo	= trim($comp_ceo);
	$comp_condition	= trim($comp_condition);
	$comp_item = trim($comp_item);
	$comp_tel	= $comp_tel_1 && $comp_tel_2 ? $comp_tel_1 .'-'. $comp_tel_2 .($comp_tel_3 ? '-'.$comp_tel_3 : '') : '';
	$comp_fax	= $comp_fax_1 && $comp_fax_2 && $comp_fax_3 ? $comp_fax_1 .'-'. $comp_fax_2 .'-'. $comp_fax_3 : '';
	//$comp_zip	= $comp_zip_1.$comp_zip_2; 우편번호 5 자리 적용 
	$comp_addr0	= $comp_addr1 && $comp_addr2 ? substr($comp_addr1,0,6) : '';
	$comp_addr1	= $comp_addr1 && $comp_addr2 ? $comp_addr1 : '';
	$comp_addr2	= trim($comp_addr2);
	$comp_part	= trim($comp_part); 
	$comp_level	= trim($comp_level);

	$_QKEY = "memberuid,comp_num,comp_type,comp_name,comp_ceo,comp_condition,comp_item,";
	$_QKEY.= "comp_tel,comp_fax,comp_zip,comp_addr0,comp_addr1,comp_addr2,comp_part,comp_level";
	$_QVAL = "'$memberuid','$comp_num','$comp_type','$comp_name','$comp_ceo','$comp_condition','$comp_item',";
	$_QVAL.= "'$comp_tel','$comp_fax','$comp_zip','$comp_addr0','$comp_addr1','$comp_addr2','$comp_part','$comp_level'";
	getDbInsert($table['s_mbrcomp'],$_QKEY,$_QVAL);
}
if ($point)
{
	getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'$memberuid','0','$point','".$d['member']['join_pointmsg']."','$d_regis'");	
}

//이메일인증
if ($auth == 3 && $d['member']['join_email'])
{
	include_once $g['path_core'].'function/email.func.php';
	
	$content = implode('',file($g['dir_module'].'doc/_auth.txt'));
	$content = str_replace('{NAME}',$name,$content);
	$content = str_replace('{NICK}',$nic,$content);
	$content = str_replace('{ID}',$id,$content);
	$content = str_replace('{EMAIL}',$email,$content);
	$content.= '<a href="'.$g['url_root'].'/?r='.$r.'&m=member&a=email_auth&tmpuid='.$memberuid.'&tmpcode='.$d_regis.'" style="font-weight:bold;font-size:20px;">[인증요청]</a>';

	getSendMail($email.'|'.$name, $d['member']['join_email'].'|'.$_HS['name'], '['.$_HS['name'].']회원가입 인증요청 메일입니다.', $content, 'HTML');
}

if ($auth == 1)
{
	include_once $g['path_core'].'function/email.func.php';

	if ($d['member']['join_email_send']&&$d['member']['join_email'])
	{
		$content = implode('',file($g['dir_module'].'doc/_join.txt'));
		$content = str_replace('{NAME}',$name,$content);
		$content = str_replace('{NICK}',$nic,$content);
		$content = str_replace('{ID}',$id,$content);
		$content = str_replace('{EMAIL}',$email,$content);
		getSendMail($email.'|'.$name, $d['member']['join_email'].'|'.$_HS['name'], '['.$_HS['name'].']회원가입을 축하드립니다.', $content, 'HTML');
	}

	$_SESSION['mbr_uid'] = $memberuid;
   $_SESSION['mbr_pw']  = getCrypt($pw1,$d_regis);
	getLink(RW(0),'parent.','축하합니다. 회원가입 승인되었습니다.','');
}
if ($auth == 2)
{
	getLink(RW(0),'parent.','회원가입 신청서가 접수되었습니다. 관리자 승인후 이용하실 수 있습니다.','');
}
if ($auth == 3)
{
	getLink(RW(0),'parent.','회원가입 인증메일이 발송되었습니다. 이메일('.$email.')확인 후 인증해 주세요.','');
}
?>