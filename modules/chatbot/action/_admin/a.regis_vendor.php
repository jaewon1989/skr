<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 

$mbruid		= $mbruid;
$name	= trim($name);
$service	= trim($service);
$intro	= trim($intro);
$logo	= trim($logo);
$html		= $html ? $html : 'TEXT';
$d_regis	= $date['totime'];
$comment	= 0;
$likes		= 0;
$report		= 0;
$hidden		= $hidden ? intval($hidden) : 0;
$display	= $hidepost || $hidden ? 0 : 1;
$auth       = 1;  
$type       =1; // 일반 /프리미엄 

if ($uid)
{

	$R = getUidData($table[$m.'vendor'],$uid);
    $QVAL = "mbruid='$mbruid',name='$name',service='$service',intro='$intro',tel='$tel',tel2='$tel2',email='$email',induCat='$induCat',logo='$logo'";
	getDbUpdate($table[$m.'vendor'],$QVAL,'uid='.$R['uid']);
}
else 
{	    
	$mingid = getDbCnt($table[$m.'vendor'],'min(gid)','');
	$gid = $mingid ? $mingid-1 : 1000000000;
 	$QKEY = "auth,gid,display,hidden,type,mbruid,induCat,id,name,service,intro,tel,tel2,email,logo,upload,d_regis";
 	$QVAL = "'$auth','$gid','$display','$hidden','$type','$mbruid','$induCat','$id','$name','$service','$intro','$tel','$tel2','$email','$logo','$upload','$d_regis'";

	getDbInsert($table[$m.'vendor'],$QKEY,$QVAL);
	
	$LASTUID = getDbCnt($table[$m.'vendor'],'max(uid)','');
  
} 
// 신규등록 
$NOWUID = $LASTUID ? $LASTUID : $R['uid'];

getLink('reload','parent.parent.','','');


?>
