<?php
if(!defined('__KIMS__')) exit;

if (!$my['uid'])
{
	getLink('','','정상적인 접근이 아닙니다.','');
}

include_once $g['dir_module'].'var/var.join.php';

$memberuid	= $my['admin'] && $memberuid ? $memberuid : $my['uid'];
$name		= trim($name);
$nic		= trim($nic);
$nic		= $nic ? $nic : $name;

/*
 if (($d['member']['form_nic'] && !$check_nic) || !$check_email)
{
	getLink('','','정상적인 접근이 아닙니다.','');
} 

if(getDbRows($table['s_mbrdata'],"memberuid<>".$memberuid." and email='".$email."'"))
{
	getLink('','','이미 존재하는 이메일입니다.','');
}
if($d['member']['form_nic'])
{
	if(!$my['admin'])
	{
		if(strstr(','.$d['member']['join_cutnic'].',',','.$nic.',') || getDbRows($table['s_mbrdata'],"memberuid<>".$memberuid." and nic='".$nic."'"))
		{
			getLink('','','이미 존재하는 닉네임입니다.','');
		}
	}
}
*/
$home		= $home ? (strstr($home,'http://')?str_replace('http://','',$home):$home) : '';
$birth1		= $birth_1;
$birth2		= $birth_2.$birth_3;
$birthtype	= $birthtype ? $birthtype : 0;
$tel1		= $tel1_1 && $tel1_2 && $tel1_3 ? $tel1_1 .'-'. $tel1_2 .'-'. $tel1_3 : '';
$tel2		= $tel2_1 && $tel2_2 && $tel2_3 ? $tel2_1 .'-'. $tel2_2 .'-'. $tel2_3 : '';

if(!$foreign)
{
	$zip		= $zip_1.$zip_2;
	$addrx		= explode(' ',$addr1);
	$addr0		= $addr1 && $addr2 ? $addrx[0] : '';
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
$pw_q		= trim($pw_q);
$pw_a		= trim($pw_a);


$_QVAL = "comp='$comp',email='$email',name='$name',nic='$nic',home='$home',sex='$sex',birth1='$birth1',birth2='$birth2',birthtype='$birthtype',tel1='$tel1',tel2='$tel2',";
$_QVAL.= "zip='$zip',addr0='$addr0',addr1='$addr1',addr2='$addr2',job='$job',marr1='$marr1',marr2='$marr2',sms='$sms',mailing='$mailing',pw_q='$pw_q',pw_a='$pw_a',addfield='$addfield'";
getDbUpdate($table['s_mbrdata'],$_QVAL,'memberuid='.$memberuid);

$isCOMP = getDbData($table['s_mbrcomp'],'memberuid='.$memberuid,'*');

if ($comp)
{
	$comp_num	= $comp_num_1 && $comp_num_2 && $comp_num_3 ? $comp_num_1.$comp_num_2.$comp_num_3 : 0;
	//$comp_type	= $comp_type;
	$comp_name	= trim($comp_name);
	$comp_ceo	= trim($comp_ceo);
	$comp_upte	= trim($comp_upte);
	$comp_jongmok = trim($comp_jongmok);
	$comp_tel	= $comp_tel_1 && $comp_tel_2 ? $comp_tel_1 .'-'. $comp_tel_2 .($comp_tel_3 ? '-'.$comp_tel_3 : '') : '';
	$comp_fax	= $comp_fax_1 && $comp_fax_2 && $comp_fax_3 ? $comp_fax_1 .'-'. $comp_fax_2 .'-'. $comp_fax_3 : '';
	$comp_zip	= $comp_zip_1.$comp_zip_2;
	$comp_addr0	= $comp_addr1 && $comp_addr2 ? substr($comp_addr1,0,6) : '';
	$comp_addr1	= $comp_addr1 && $comp_addr2 ? $comp_addr1 : '';
	$comp_addr2	= trim($comp_addr2);
	$comp_part	= trim($comp_part); 
	$comp_level	= trim($comp_level);

	if ($isCOMP['memberuid'])
	{
		$_QVAL = "comp_num='$comp_num',comp_type='$comp_type',comp_name='$comp_name',comp_ceo='$comp_ceo',comp_upte='$comp_upte',comp_jongmok='$comp_jongmok',";
		$_QVAL.= "comp_tel='$comp_tel',comp_fax='$comp_fax',comp_zip='$comp_zip',comp_addr0='$comp_addr0',comp_addr1='$comp_addr1',comp_addr2='$comp_addr2',comp_part='$comp_part',comp_level='$comp_level'";
		getDbUpdate($table['s_mbrcomp'],$_QVAL,'memberuid='.$isCOMP['memberuid']);
	}
	else {

		$_QKEY = "memberuid,comp_num,comp_type,comp_name,comp_ceo,comp_upte,comp_jongmok,";
		$_QKEY.= "comp_tel,comp_fax,comp_zip,comp_addr0,comp_addr1,comp_addr2,comp_part,comp_level";
		$_QVAL = "'$memberuid','$comp_num','$comp_type','$comp_name','$comp_ceo','$comp_upte','$comp_jongmok',";
		$_QVAL.= "'$comp_tel','$comp_fax','$comp_zip','$comp_addr0','$comp_addr1','$comp_addr2','$comp_part','$comp_level'";
		getDbInsert($table['s_mbrcomp'],$_QKEY,$_QVAL);
	}
}

getLink('reload','parent.','변경되었습니다.','');
?>