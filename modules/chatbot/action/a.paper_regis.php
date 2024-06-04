<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅 

$feed = new feed();

$result=array();
$result['error']=false;

if (!$_SESSION['wcode']){
	$result['error']='정상적인 접근이 아닙니다.';
	echo json_encode($result);
	exit;
}else{
	$by_mbruid		= $my['uid'];
	$id			= $my['id'];
	$name		= $my['uid'] ? $my['name'] : trim($name);
	$nic		= $my['uid'] ? $my['nic'] : $name;
	$pw			= $pw ? md5($pw) : ''; 
	$subject	= $my['admin'] ? trim($subject) : htmlspecialchars(trim($subject));
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
	$dispaly_in_review	= $dispaly_in_review? intval($dispaly_in_review) : 0;
	$mbr_country = $FM['country_code']?$FM['country_code']:'KOR'; // 등록자 국가 코드 
	$mbr_sex = $FM['sex']=='male'?1:2; // 등록자 성별 

	$hashTag = $feed->gethashtags($content); // 해시태그 붙은 단어 추출
    $tag = trim($hashTag);

    // 첨부/업로드 여부값 세팅 
	if($photos){
	   $is_photo=1;
	   $type ='text';	
	}	
   
	if ($uid)
	{
		$R = getUidData($table[$m.'paper'],$uid);
	}
	else 
	{
	   
	    foreach ($rsv_member as $uid_id) {
            $user_arr = explode('^^',$uid_id);
            $my_mbruid = $user_arr[0]; // 수신자 uid 

            $QKEY='type,my_mbruid,by_mbruid,content,html,d_regis,is_photo';
            $QVAL="'".$type."','".$my_mbruid."','".$by_mbruid."','".$content."','".$html."','".$date['totime']."','$is_photo'";
            getDbInsert($table[$m.'paper'],$QKEY,$QVAL); 
            
            $LASTUID = getDbCnt($table[$m.'paper'],'max(uid)','');

	        // 사진 처리
	        $photo_num=count($_FILES['photos']['tmp_name']);
	        $inputFileName='photos'; // <input type="file" name="photos[]"/> 
	        $post = $LASTUID; // 해당 피드 PK
	        $module = 'paper'; // 
			if($photo_num>0){
			   include $g['dir_module'].'action/a.photo_upload.php';	
			}
        }
	
	} 
	// 신규등록 
	$NOWUID = $LASTUID ? $LASTUID : $R['uid'];

} 
$result['content'] = $feed->getPaper($target,1,$position);
echo json_encode($result);
exit;
?>
