<?php
if(!defined('__KIMS__')) exit;

$site = $s;
$vendor = $vendor?$vendor:0;
$bot = $bot?$bot:0;
$regiMod = $regiMod?$regiMod:'admin'; // 등록모드 
$type = $regiMod=='admin'?'S':'V'; // 시스템 인텐트 여부 

if ($cat && !$vtype)
{
	$R = getUidData($table[$m.'entity'],$cat);
    
 	$QVAL = "hidden='$hidden',name='$name'";
	getDbUpdate($table[$m.'entity'],$QVAL,'uid='.$cat);
    
    // 엔터티 항목 값 업데이트 
	if($entityVal_uid){
        $i=0;
        foreach ($entityVal_uid as $uid) {
        	$name = $entityVal_name[$i];
        	$synonyms = $entityVal_synonyms[$i];
        	$QVAL ="name='$name',synonyms='$synonyms'";
            getDbUpdate($table[$m.'entityVal'],$QVAL,'uid='.$uid);

            $i++;
        }    

	}
}
else {
	if($regiMod =='admin') $_sql ="type='S'";
	else if($regiMod ='vendor') $_sql ="type='V' and vendor='".$vendor."' and bot='".$bot."'";

	$entity_array = array();

	$MAXC = getDbCnt($table[$m.'entity'],'max(gid)',$_sql);
	$sarr = explode(',' , trim($name));
	$slen = count($sarr);
	
	for ($i = 0 ; $i < $slen; $i++)
	{
		if (!$sarr[$i]) continue;
		$gid	= $MAXC+1+$i;
		$_xname	= trim($sarr[$i]);
		$xname_arr = explode('(',$_xname);
		$xname = $xname_arr[0];	
		$entity_array[]= $xname;
		$QKEY = "gid,type,site,vendor,bot,hidden,name,rp_sentence";
		$QVAL = "'$gid','$type','$s','$vendor','$bot','0','$xname','$rp_sentence'";
		getDbInsert($table[$m.'entity'],$QKEY,$QVAL);
        
        $entity = getDbCnt($table[$m.'entity'],'max(uid)','');

        $_sql.=' and entity='.$entity;

        $MAXC = getDbCnt($table[$m.'entityVal'],'max(gid)',$_sql);
        $entityVal = rtrim(trim($xname_arr[1]),')');
        $eval_arr = explode('|',$entityVal);
        $elen = count($eval_arr);
        for($j = 0; $j < $elen; $j++){
        	if (!$eval_arr[$j]) continue;
			$gid	= $MAXC+1+$j;
			$xname	= trim($eval_arr[$j]);
			$QKEY = "gid,type,site,vendor,bot,entity,hidden,name";
			$QVAL = "'$gid','$type','$s','$vendor','$bot','$entity','0','$xname'";
			getDbInsert($table[$m.'entityVal'],$QKEY,$QVAL);
        }
        db_query("OPTIMIZE TABLE ".$table[$m.'entityVal'],$DB_CONNECT);
	}
	
	db_query("OPTIMIZE TABLE ".$table[$m.'entity'],$DB_CONNECT); 
}
if($regiMod =='admin') getLink('reload','parent.','','');
else{
   echo json_encode($entity_array);
   exit;
} 
?>