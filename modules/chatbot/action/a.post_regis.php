<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅 

$Ad = new Ad();

$result=array();
$result['error']=false;

if (!$_SESSION['wcode']){
	$result['error']='정상적인 접근이 아닙니다.';
	echo json_encode($result);
	exit;
}else{
	$register   = $my['uid'];
	$mbruid		= $mbruid;
    $M = getDbData($table['s_mbrdata'].' left join '.$table['s_mbrid'].' on memberuid=uid','memberuid='.$mbruid,'*');
	$id			= $M['id'];
	$name		= $M['name'];
	$nic		= $M['nic'];
	$content	= trim($content);
	$subject	= $subject ? $subject : getStrCut(str_replace('&amp;',' ',strip_tags($content)),35,'..');
	$html		= $html ? $html : 'TEXT';
	$d_regis	= $date['totime'];
	$d_modify	= '';
	$d_comment	= '';
	$ip			= $_SERVER['REMOTE_ADDR'];
	$agent		= $_SERVER['HTTP_USER_AGENT'];
	$adddata	= trim($adddata);
	$hit		= 0;
	$down		= 0;
	$comment	= 0;
	$likes		= 0;
	$unlikes		= 0;
	$report		= 0;
	$point		= $d['post']['give_point'];
	$hidden		= $hidden ? intval($hidden) : 0;
	$notice		= $notice ? intval($notice) : 0;
	$display	= $hidepost || $hidden ? 0 : 1;
	$user_display = $user_display?$user_display:1;
	$get_ip_info = json_decode(trim(file_get_contents("http://www.geognos.com/api/en/countries/info/{$_SERVER['REMOTE_ADDR']}.json")),true);
    $country_code_from_ip = $get_ip_info['Results']['CountryCodes']['iso3'];
	$mbr_country = $country_code_from_ip?$country_code_from_ip:($FM['country_code']?$FM['country_code']:'KOR'); // 등록자 국가 코드 
	$mbr_sex = $FM['sex']=='male'?1:2; // 등록자 성별 

	$hashTag = $Ad->gethashtags($content); // 해시태그 붙은 단어 추출
    $tag = trim($hashTag);
    
    // 광고 기간 
    $d_start = str_replace('/','',$ad_start).'000000';
    $d_end = str_replace('/','',$ad_end).'000000';

    // 광고 지역 
    $sido = trim($sido);
    $gugun = trim($gugun);
    $dong = trim($dong);   

    // 승인 결정 
    if($regis_mod=='admin') $auth=1; // 관리자 모드 일때만 기본세팅 --> 승인 
    else $auth=0;  

	if ($uid)
	{
		$R = getUidData($table[$m.'post'],$uid);
		if (!$R['uid']){
			$result['error']='존재하지 않는 포스트 입니다.';
	        echo json_encode($result);
	        exit;
		} 

		$QVAL = "company='$company',subject='$subject',content='$content',links='$links',area_depth='$area_depth',category='$category',sido='$sido',gugun='$gugun',dong='$dong',d_start='$d_start',d_end='$d_end',d_modify='$d_regis'";
		
		getDbUpdate($table[$m.'post'],$QVAL,'uid='.$R['uid']);
        
        // 웹(모바일/데스크탑) 에서 업로드한 파일 처리  
        if($platform=='web'){
            $photo_num=count($_FILES['photos']['tmp_name']);
	        $inputFileName='photos'; // <input type="file" name="photos[]"/> 
	        $post = $uid; // 해당 피드 PK
	        $module = $m; // 모듈명 
			if($photo_num>0){
			    include $g['dir_module'].'action/a.photo_upload.php';	
			} 	 
        } 

        // 변경된 파일이 있는 경우 기존 파일 지운다. 
        if($del_photos){
        	foreach ($del_photos as $uid) {
        	   $row = $Ad->getUidData($Ad->table('photo'),$uid);
        	   $Ad->deleteFile($row);
        	}
        }

	}
	else 
	{	    
		$mingid = getDbCnt($table[$m.'post'],'min(gid)','');
		$gid = $mingid ? $mingid-1 : 1000000000;
     
 		$QKEY = "auth,gid,company,ad_type,display,user_display,hidden,notice,name,nic,register,mbruid,id,area_depth,category,sido,gugun,dong,subject,content,html,";
		$QKEY.= "hit,down,comment,likes,unlikes,report,point,d_start,d_end,d_regis,d_modify,upload,ip,agent,sync,sns,adddata,links,is_onair";

		$QVAL = "'$auth','$gid','$company','$ad_type','$display','$user_display','$hidden','$notice','$name','$nic','$register','$mbruid','$id','$area_depth','$category','$sido','$gugun','$dong','$subject','$content','$html',";
		$QVAL.= "'$hit','$down','$comment','$likes','$unlikes','$report','$point','$d_start','$d_end','$d_regis','$d_modify','$upload','$ip','$agent','$sync','$sns','$adddata','$links','$is_onair'";

		getDbInsert($table[$m.'post'],$QKEY,$QVAL);
		
		$LASTUID = getDbCnt($table[$m.'post'],'max(uid)','');

        // 웹(모바일/데스크탑) 에서 업로드한 파일 처리  
        if($platform=='web'){
            $photo_num=count($_FILES['photos']['tmp_name']);
	        $inputFileName='photos'; // <input type="file" name="photos[]"/> 
	        $post = $LASTUID; // 해당 피드 PK
	        $module = $m; // 
			if($photo_num>0){
			   include $g['dir_module'].'action/a.photo_upload.php';	
			} 	 
        }

        // 모바일 App 에서 업로드한 파일 처리 
		if($photos && $platform !='web'){
		   $module = $m;	
		   foreach($photos as $uid) {
	  	   		getDbUpdate($table[$m.'photo'],"module='".$module."',mbruid='".$mbruid."',post='".$LASTUID."'",'uid='.$uid);
		   }	
		}  
	} 
	// 신규등록 
	$NOWUID = $LASTUID ? $LASTUID : $R['uid'];
    


	// 태그 등록 함수 실행 
	if ($tag || $R['tag']) $Ad->RegisPostTag($tag,$R,'post',$moduleid,$reply,$NOWUID);
} 

// 어드민 등록시 
if($regis_mod=='admin'){
	$adm_link = $g['s'].'/?m=admin&module='.$m.'&front=main';
	getLink($adm_link,'parent.parent.','','');
}


?>
