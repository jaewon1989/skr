<?php
if(!defined('__KIMS__')) exit;

$display = 1;
$hidden = $hidden?$hidden:0;
$language = $language?$language:'KOR';
$content = trim($content);

if($uid)
{
	$R = getUidData($table[$m.'reply'],$uid);
	$QVAL = "display='$display',hidden='$hidden',quesCat='$quesCat',type='$type',lang='$language',content='$content'";
	getDbUpdate($table[$m.'reply'],$QVAL,'uid='.$R['uid']);

}else{
   
    $QKEY="display,hidden,induCat,quesCat,vendor,type,lang,content";
    $QVAL="'$display','$hidden','$induCat','$quesCat','$vendor','$type','$language','$content'";
    getDbInsert($table[$m.'reply'],$QKEY,$QVAL); 
    
} 
getLink('reload','parent.parent.','','');
?>
