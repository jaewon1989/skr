<?php
if(!defined('__KIMS__')) exit;
checkAdmin(0);
include_once $g['path_core'].'function/string.func.php';

$name		= trim($name);
$price		= trim($price);
$price1		= trim($price1);
$point		= trim($point);
$country	= trim($country);
$maker		= trim($maker);
$brand		= trim($brand);
$model		= trim($model);
$stock_num	= trim($stock_num);
$tags		= trim($tags);
$content	= trim($content);
$html		= $html ? $html : 'TEXT';
$comment	= 0;
$vote		= 0;
$qna		= 0;
$hit		= 0;
$wish		= 0;
$buy		= 0;
$d_regis	= $date['totime'];
$md			= 0;
$num1		= 1;
$num2		= 0;
$code		= substr($date['totime'],1,13);
$namekey	= getSiKey($name);
$d_make		= $date['today'];
$joint		= trim($joint);
$halin_event= '';
$halin_mbr	= '';


if($use_event)
{
	$halin_event = $halin_event1 ? trim($halin_event1).','.$year1.$month1.$day1.','.$year2.$month2.$day2: '';
	for ($i = 0; $i < $halin_mbr_num; $i++) $halin_mbr .= ${'halin_mbr_'.$i}.',';
}
if ($uid)
{
	$R=getUidData($table[$m.'product'],$uid);
	$upfolder = substr($R['d_regis'],0,8);
	if ($R['category'] != $category)
	{
		getDbUpdate($table[$m.'category'],'num=num+1','uid='.$category);
		getDbUpdate($table[$m.'category'],'num=num-1','uid='.$R['category']);
	}
	$QVAL = "display='$display',category='$category',name='$name',price='$price',price1='$price1',point='$point',price_x='$price_x',country='$country',maker='$maker',";
	$QVAL.= "brand='$brand',model='$model',stock='$stock',stock_num='$stock_num',addinfo='$addinfo',addoptions='$addoptions',icons='$icons',tags='$tags',";
	$QVAL.= "content='$content',html='$html',upload='$upload',vendor='$vendor',md='$md',";
	$QVAL.= "num1='$num1',num2='$num2',namekey='$namekey',d_make='$d_make',is_free='$is_free',is_cash='$is_cash',halin_event='$halin_event',halin_mbr='$halin_mbr',joint='$joint',review='$review'";
	getDbUpdate($table[$m.'product'],$QVAL,'uid='.$R['uid']);
	
}
else {
	$upfolder = $date['today'];
	$mingid = getDbCnt($table[$m.'product'],'min(gid)','');
	$gid = $mingid ? $mingid-1 : 100000000;
	$QKEY = "gid,display,category,name,price,price1,point,price_x,country,maker,brand,model,";
	$QKEY.= "stock,stock_num,addinfo,addoptions,icons,tags,content,html,ext,upload,comment,vote,qna,hit,wish,buy,d_regis,";
	$QKEY.= "vendor,md,num1,num2,code,namekey,d_make,is_free,is_cash,halin_event,halin_mbr,joint,featured_img,review";
	$QVAL = "'$gid','$display','$category','$name','$price','$price1','$point','$price_x','$country','$maker','$brand','$model',";
	$QVAL.= "'$stock','$stock_num','$addinfo','$addoptions','$icons','$tags','$content','$html','$ext','$upload','$comment','$vote','$qna','$hit','$wish','$buy','$d_regis',";
	$QVAL.= "'$vendor','$md','$num1','$num2','$code','$namekey','$d_make','$is_free','$is_cash','$halin_event','$halin_mbr','$joint','$featured_img','$review'";
	
	getDbInsert($table[$m.'product'],$QKEY,$QVAL);
	//getDbUpdate($table[$m.'category'],'num=num+1','uid='.$category);
	$R['uid']= getDbCnt($table[$m.'product'],'max(uid)','');	
}
    

// 스샷이 있는 경우 
if(isset($_FILES["shot_photo"]['tmp_name'])){

	if (!$_SESSION['upsescode']) $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
	$sescode = $_SESSION['upsescode'];
	$sess_Code =$sescode.'_'.$my['uid']; // 코드- 회원 uid  
    


	// 업로드 디렉토리 없는 경우 추가 
	if(!is_dir($saveDir)){
	   mkdir($saveDir,0707);
	 @chmod($saveDir,0707);
	}

	include $g['path_module'].'attach/theme/'.$theme.'/main.func.php';
	include $g['path_module'].'mediaset/var/var.php';
	include $g['path_core'].'function/thumb.func.php';



	$sessArr  = explode('_',$sess_Code);
	$tmpcode  = $sessArr[0];
	$mbruid   = $my['uid'];
	$fserver  = $d['mediaset']['up_use_fileserver'];
	$url    = $fserver ? $d['mediaset']['ftp_urlpath'] : str_replace('.','',$saveDir);
	$name   = strtolower($_FILES['shot_photo']['name']);
	$size   = $_FILES['shot_photo']['size'];
	$width    = 0;
	$height   = 0;
	$caption  = trim($caption);
	$down   = 0;
	$d_regis  = $date['totime'];
	$d_update = '';
	$fileExt  = getExt($name);
	$fileExt  = $fileExt == 'jpeg' ? 'jpg' : $fileExt;
	$type   = getFileType($fileExt);
	$tmpname  = md5($name).substr($date['totime'],8,14);
	$tmpname  = $type == 2 ? $tmpname.'.'.$fileExt : $tmpname;
	$hidden   = $type == 2 ? 1 : 0;

	if ($d['mediaset']['up_ext_cut'] && strstr($d['mediaset']['up_ext_cut'],$fileExt))
	{
	       $code='200';
	       $msg='정상적인 접근이 아닙니다.';
	       $result=array($code,$msg);  
	       echo json_encode($result);
	       exit;
	} 

	$savePath1  = $saveDir.substr($date['today'],0,4);
	$savePath2  = $savePath1.'/'.substr($date['today'],4,2);
	$savePath3  = $savePath2.'/'.substr($date['today'],6,2);
	$folder   = substr($date['today'],0,4).'/'.substr($date['today'],4,2).'/'.substr($date['today'],6,2);
	
    for ($i = 1; $i < 4; $i++)
    {
          if (!is_dir(${'savePath'.$i}))
         {
               mkdir(${'savePath'.$i},0707);
               @chmod(${'savePath'.$i},0707);
         }
    }

    $saveFile = $savePath3.'/'.$tmpname;

    if ($Overwrite == 'true' || !is_file($saveFile))
    {
            move_uploaded_file($_FILES['shot_photo']['tmp_name'], $saveFile);
            if ($type == 2)
            {
                  // $thumbname = md5($tmpname).'.'.$fileExt;
                  // $thumbFile = $savePath3.'/'.$thumbname;
                  // ResizeWidth($saveFile,$thumbFile,150);
                  // @chmod($thumbFile,0707);
                  $IM = getimagesize($saveFile);
                  $width = $IM[0];
                  $height= $IM[1];
            }
           @chmod($saveFile,0707);
    }
	
	if($_FILES['shot_photo']['name']){

       	// DB 저장 
		$mingid = getDbCnt($table[$m.'upload'],'min(gid)','');
		$gid = $mingid ? $mingid - 1 : 100000000;
	   
		$QKEY = "gid,pid,parent,hidden,tmpcode,mbruid,fileonly,type,ext,fserver,url,folder,name,tmpname,thumbname,size,width,height,caption,down,d_regis,d_update";
		$QVAL = "'$gid','$gid','$parent','$hidden','$tmpcode','$mbruid','1','$type','$fileExt','$fserver','$url','$folder','$name','$tmpname','$thumbname','$size','$width','$height','$caption','$down','$d_regis','$d_update'";
		getDbInsert($table[$m.'upload'],$QKEY,$QVAL);

		if ($gid == 100000000) db_query("OPTIMIZE TABLE ".$table[$m.'upload'],$DB_CONNECT); 
		$lastImgUid=getDbCnt($table[$m.'upload'],'max(uid)',''); 


		// 이미지 부모  uid  값 등록 
		$img_parent=$R['uid'];
		getDbUpdate($table[$m.'upload'],"parent='pms".$img_parent."'",'uid='.$lastImgUid);
		getDbUpdate($table[$m.'product'],"featured_img='".$lastImgUid."'",'uid='.$R['uid']);
	}

}




if ($uid)
{
	getLink('reload','parent.','','');
}
else {
	$backLink=$g['s'].'/?r='.$r.'&m=admin&module=pms&front=main';
	getLink($backLink,'parent.','','');
}
?>