<?php
// sns 아바타 이미지 세팅 함수 
function setAvatar($picdata,$id,$type)
{
      global $g;
	 include_once $g['path_core'].'function/thumb.func.php';
	   
	$pic = $g['path_var'].'avatar/'.$id.'.jpg';
	 
	 $fp = fopen($pic,'w');
	 fwrite($fp,$picdata);
	 fclose($fp);
	 
	 //ResizeWidth($pic,$pic,300);
	 //ResizeWidthHeight($pic,$pic,300,300);
	 
	 @chmod($pic);
	 $photo = $id.'.jpg';
    return $photo;
}
// s_mbrdata 필드명 추출함수
function getMbrDataKey()
{
     $_QKEY = "memberuid,site,auth,mygroup,level,comp,admin,adm_view,email,name,nic,grade,photo,home,sex,birth1,birth2,birthtype,tel1,tel2,zip,";
     $_QKEY.= "addr0,addr1,addr2,job,marr1,marr2,sms,mailing,smail,point,usepoint,money,cash,num_login,pw_q,pw_a,now_log,last_log,last_pw,is_paper,d_regis,tmpcode,sns,addfield";
   
      return $_QKEY;
}
// sns 회원가입 프로세스 함수
function setSnsMember($_QKEY,$_QVAL,$memberuid,$vendor,$sns_id)
{
      global $g,$table,$date;
    
      include $g['path_module'].'member/var/var.join.php';
   
	 getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);
	 getDbUpdate($table['s_mbrlevel'],'num=num+1','uid='.$d['member']['join_level']);
	 getDbUpdate($table['s_mbrgroup'],'num=num+1','uid='.$d['member']['join_group']);
	 getDbUpdate($table['s_numinfo'],'login=login+1,mbrjoin=mbrjoin+1',"date='".$date['today']."' and site=".$s);
	 if($d['member']['join_point']) getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'$memberuid','0','".$d['member']['join_point']."','".$d['member']['join_pointmsg']."','".$date['totime']."'");
	 getDbInsert($table['s_mbrsns'],'memberuid,'.$vendor,"'".$memberuid."','".$sns_id."'");
	 $_SESSION['mbr_uid'] = $memberuid;
	 $_SESSION['mbr_pw']  = '';

	 // 로그인 유지기능 추가 
	   setAccessToken($memberuid,''); // sys.function.php 파일 함수  참조
    
}
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
?>
