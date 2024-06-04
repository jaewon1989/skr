<?php
if(!defined('__KIMS__')) exit;

$site = $s;
$vendor = $vendor?$vendor:0;
$bot = $bot?$bot:0;
$regiMod = $regiMod?$regiMod:'admin'; // 등록모드 
$type = $regiMod=='admin'?'S':'V'; // 시스템 인텐트 여부 

if ($cat && !$vtype)
{
	$R = getUidData($table[$m.'intent'],$cat);
    
 	$QVAL = "hidden='$hidden',name='$name',rp_sentence='$rp_sentence'";
	getDbUpdate($table[$m.'intent'],$QVAL,'uid='.$cat);
}
else {
	if($regiMod =='admin') $_sql ="type='S'";
	else if($regiMod ='vendor') $_sql ="type='V' and vendor='".$vendor."' and bot='".$bot."'";

	$intent_array = array();

	$MAXC = getDbCnt($table[$m.'intent'],'max(gid)',$_sql);
	$sarr = explode(',' , trim($name));
	$slen = count($sarr);
	
	for ($i = 0 ; $i < $slen; $i++)
	{
		if (!$sarr[$i]) continue;
		$gid	= $MAXC+1+$i;
		$xname	= trim($sarr[$i]);
		$intent_array[]= $xname;
		$QKEY = "gid,type,site,vendor,bot,hidden,name,rp_sentence";
		$QVAL = "'$gid','$type','$s','$vendor','$bot','0','$xname','$rp_sentence'";
		getDbInsert($table[$m.'intent'],$QKEY,$QVAL);
	}
	
	db_query("OPTIMIZE TABLE ".$table[$m.'intent'],$DB_CONNECT); 
}
if($regiMod =='admin') getLink('reload','parent.','','');
else{
   echo json_encode($intent_array);
   exit;
} 
?>