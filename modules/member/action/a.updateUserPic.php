<?php
if(!defined('__KIMS__')) exit;
// var_dump($_FILES);
// exit;

$result=array();
$result['error']=false;

if (!$my['uid'])
{
	$result['error']=true;
	$result['message']='정상적인 접근이 아닙니다.';
	exit;
	//getLink('','','정상적인 접근이 아닙니다.','');
}

if($agent=='iphone' || $agent=='ipad' || $agent=='desktop'){

    if($_FILES['setting_photo']['tmp_name']){
    	$tmpname	= $_FILES['setting_photo']['tmp_name'];
		$realname	= $_FILES['setting_photo']['name'];

		// $result['message']='ddd'.$realname;
		// exit;
		$fileExt	= strtolower(getExt($realname));
		$fileExt	= $fileExt == 'jpeg' ? 'jpg' : $fileExt;
		$photo		= $my['id'].'.'.$fileExt;
		$saveFile1	= $g['path_var'].'avatar/'.$photo;

		if (is_uploaded_file($tmpname))
		{
			if (is_file($saveFile1))
			{
				unlink($saveFile1);
			}
			move_uploaded_file($tmpname,$saveFile1);
			@chmod($saveFile1,0707);
			getDbUpdate($table['s_mbrdata'],"photo='".$photo."'",'memberuid='.$my['uid']);
		}

    }

}else{
    if($photo) {
		$site_dir = $_SERVER['DOCUMENT_ROOT'].'/'; // 서버 절대 주소 체크 
		$dir_file = str_replace('http://'.$_SERVER[HTTP_HOST].'/',$site_dir,$photo);
		$save_file = str_replace('files/temp/'.date('Ymd'),'_var/simbol',$dir_file);
		
		if(is_file($dir_file)) {
			@copy($dir_file,$save_file);
			//ResizeWidth($save_file,$save_file,180);
			// ResizeWidthHeight($saveFile2,$saveFile1,50,50);
			//@chmod($save_file,0707);
			// @chmod($saveFile2,0707);
			//if(!$_POST['isdirpath'])@unlink($dir_file);
			$photo = str_replace($site_dir,'http://'.$_SERVER[HTTP_HOST].'/',$save_file);
			$photo = str_replace('http://'.$_SERVER[HTTP_HOST].'/_var/simbol/','', $photo);
		}
		$_QVAL= "photo='$photo'";
	     
	          getDbUpdate($table['s_mbrdata'],$_QVAL,'memberuid='.$my['uid']); 
   
    }	
   
}
 $result['message']='사진이 업데이트 되었습니다.';

echo json_encode($result,true);
exit;
?>