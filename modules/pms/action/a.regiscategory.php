<?php
if(!defined('__KIMS__')) exit;
checkAdmin(0);
$codhead = trim($codhead);
$codfoot = trim($codfoot);
$recnum  = trim($recnum);
if ($cat && !$vtype)
{
	$R = getUidData($table[$m.'category'],$cat);
	$puthead=$imghead?1:0;
      $putfoot=$imgfoot?1:0;
	// for ($i = 0; $i < 2; $i++)
	// {
	// 	$tmpname	= $_FILES['img'.$imgset[$i]]['tmp_name'];
	// 	$realname	= $_FILES['img'.$imgset[$i]]['name'];
	// 	$fileExt	= strtolower(getExt($realname));
	// 	$fileExt	= $fileExt == 'jpeg' ? 'jpg' : $fileExt;
	// 	$userimg	= sprintf('%05d',$R['uid']).'_'.$imgset[$i].'.'.$fileExt;
	// 	$saveFile	= $g['dir_module'].'var/files/'.$userimg;
	// 	if (is_uploaded_file($tmpname))
	// 	{
	// 		if (!strstr('[gif][jpg][png][swf]',$fileExt))
	// 		{
	// 			getLink('','','헤더/풋터파일은 gif/jpg/png/swf 파일만 등록할 수 있습니다.','');
	// 		}
	// 		move_uploaded_file($tmpname,$saveFile);
	// 		@chmod($saveFile,0707);
	// 		${'img'.$imgset[$i]} = $userimg;
	// 	}
	// }
	$QVAL = "hidden='$hidden',reject='$reject',name='$name',";
	$QVAL.= "layout='$layout',skin='$skin',skin_mobile='$skin_mobile',imghead='$imghead',imgfoot='$imgfoot',puthead='$puthead',putfoot='$putfoot',recnum='$recnum',sosokmenu='$sosokmenu',review='$review',tags='$tags',featured_img='$featured_img'";
	getDbUpdate($table[$m.'category'],$QVAL,'uid='.$cat);
	$vfile = $g['dir_module'].'var/code/'.sprintf('%05d',$cat);
	if (trim($codhead))
	{
		$fp = fopen($vfile.'.header.php','w');
		fwrite($fp, trim(stripslashes($codhead)));
		fclose($fp);
		@chmod($vfile.'.header.php',0707);
	}
	else {
		if(is_file($vfile.'.header.php'))
		{
			unlink($vfile.'.header.php');
		}
	}
	if (trim($codfoot))
	{
		$fp = fopen($vfile.'.footer.php','w');
		fwrite($fp, trim(stripslashes($codfoot)));
		fclose($fp);
		@chmod($vfile.'.footer.php',0707);
	}
	else {
		if(is_file($vfile.'.footer.php'))
		{
			unlink($vfile.'.footer.php');
		}
	}
	if ($subcopy == 1)
	{
		include_once $g['dir_module'].'_main.php';
		$subQue = getShopCategoryCodeToSql($table[$m.'category'],$cat,'uid');
		if ($subQue)
		{
			getDbUpdate($table[$m.'category'],"hidden='".$hidden."',reject='".$reject."',layout='".$layout."',skin='".$skin."',skin_mobile='".$skin_mobile."'","uid <> ".$cat." and (".$subQue.")");
		}
	}

}
else {
	$MAXC = getDbCnt($table[$m.'category'],'max(gid)','depth='.($depth+1).' and parent='.$parent);
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
		$QKEY = "gid,isson,parent,depth,hidden,reject,name,layout,skin,skin_mobile,imghead,imgfoot,puthead,putfoot,recnum,num,sosokmenu,review,tags,featured_img";
		$QVAL = "'$gid','0','$parent','$xdepth','$hidden','$reject','$xname','$layout','$skin','$skin_mobile','$imghead','$imgfoot','$puthead','$putfoot','$recnum','0','$sosokmenu','$review','$tags','$featured_img'";
		getDbInsert($table[$m.'category'],$QKEY,$QVAL);
	}
	
	if ($parent)
	{
		getDbUpdate($table[$m.'category'],'isson=1','uid='.$parent);
	}
	db_query("OPTIMIZE TABLE ".$table[$m.'category'],$DB_CONNECT); 
}

getLink('reload','parent.','','');
?>