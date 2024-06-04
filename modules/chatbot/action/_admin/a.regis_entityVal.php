<?php
if(!defined('__KIMS__')) exit;

$site = $s;
$vendor = $vendor?$vendor:0;
$bot = $bot?$bot:0;
$regiMod = $regiMod?$regiMod:'admin'; // 등록모드 
$type = $regiMod=='admin'?'S':'V'; // 시스템 인텐트 여부 

if($regiMod =='admin') $_sql ="type='S'";
else if($regiMod ='vendor') $_sql ="type='V' and vendor='".$vendor."' and bot='".$bot."'";

$result_array = array();

$_sql.=' and entity='.$entity;

$MAXC = getDbCnt($table[$m.'entityVal'],'max(gid)',$_sql);
$eval_arr = explode(',',$entityVal);
$elen = count($eval_arr);
for($i = 0; $i < $elen; $i++){
	if (!$eval_arr[$i]) continue;
	$gid	= $MAXC+1+$i;
	$xname	= trim($eval_arr[$i]);
	$result_array [] = $xname;
	$QKEY = "gid,type,site,vendor,bot,entity,hidden,name";
	$QVAL = "'$gid','$type','$s','$vendor','$bot','$entity','0','$xname'";
	getDbInsert($table[$m.'entityVal'],$QKEY,$QVAL);
}
db_query("OPTIMIZE TABLE ".$table[$m.'entityVal'],$DB_CONNECT);


if($regiMod =='admin') getLink('reload','parent.','','');
else{
   echo json_encode($result_array);
   exit;
} 
?>