<?php
if(!defined('__KIMS__')) exit;



if ($cat && !$vtype)
{
	$R = getUidData($table[$m.'course'],$cat);

	$QVAL = "hidden='$hidden',reject='$reject',name='$name',summary='$summary',content='$content'";
	getDbUpdate($table[$m.'course'],$QVAL,'uid='.$cat);
	

}
else {
	$MAXC = getDbCnt($table[$m.'course'],'max(gid)','depth='.($depth+1).' and parent='.$parent.' and type='.$type);
	$sarr = explode(',' , trim($name));
	$slen = count($sarr);

	//if ($depth > ) getLink('','','매장분류는 최대 3단계까지 등록할 수 있습니다.','');
	for ($i = 0 ; $i < $slen; $i++)
	{
		if (!$sarr[$i]) continue;
		$gid	= $MAXC+1+$i;
		$xdepth	= $depth+1;
		$xname	= trim($sarr[$i]);
		$puthead=$imghead?1:0;
		$putfoot=$imgfoot?1:0;
		$QKEY = "gid,type,isson,parent,depth,hidden,reject,name,summary,content,upload";
		$QVAL = "'$gid','$type','0','$parent','$xdepth','$hidden','$reject','$xname','$summary','$content','$upload'";
		getDbInsert($table[$m.'course'],$QKEY,$QVAL);
	}
	
	if ($parent)
	{
		getDbUpdate($table[$m.'course'],'isson=1','uid='.$parent);
	}
	db_query("OPTIMIZE TABLE ".$table[$m.'course'],$DB_CONNECT); 
}

getLink('reload','parent.','','');
?>