<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'includes/base.class.php';  
include_once $g['dir_module'].'includes/module.class.php';
$chatbot = new Chatbot(); 

$R = getUidData($table[$m.'bot'],$uid);
if(!$R['uid']) getLink($g['s'].'/?r='.$r,'','잘못된 접근입니다  ','');	

$V = getUidData($table[$m.'vendor'],$R['vendor']);
$name	 = $name?trim($name):$R['name'];
$ranStr = $chatbot->getRanString(15);
$language    = $language?$language:'KOR';
$html		= $html ? $html : 'TEXT';
$d_regis	= $date['totime'];
$likes		= 0;
$report		= 0;
$hidden		= $hidden ? intval($hidden) : 0;
$display	= $hidepost || $hidden ? 0 : 1;
$auth       = $auth?$auth:1; 
$induCat    = $induCat?$induCat:$V['induCat']; // 업종분류는 bot 마다 설정할 수 도 있고 아니면 해당 업체의 업종으로 통일 
$vendor     = $R['vendor'];
$bot        = $R['uid'];

// 첫 페이지에서 수정할 경우 
if($step==1){
    $QVAL = "auth='$auth',induCat='$induCat',hidden='$hidden',display='$display',name='$name',service='$service',intro='$intro',website='$website',boturl='$boturl',lang='$language',content='$content',d_modify='$d_regis',avatar='$avatar',upload='$upload'";

    getDbUpdate($table[$m.'bot'],$QVAL,'uid='.$R['uid']);

    // 업체내용 업데이트 
    $logo=$logo?$logo:$avatar;
    $QVAL = "name='$name',induCat='$induCat',logo='$logo',upload='$upload'";
    getDbUpdate($table[$m.'vendor'],$QVAL,'uid='.$R['vendor']);  
}


// 업체 답변 적용
if($reply_arr){
    $i=0;
    foreach ($reply_arr as $reply) {
        if($reply!=''){
            $quesCat = $quesCat_arr[$i];
            $is_vendor_reply = getDbRows($table[$m.'reply'],"vendor='".$vendor."' and bot='".$bot."' and quesCat='".$quesCat."'");
            if($is_vendor_reply){
                getDbUpdate($table[$m.'reply'],"content='".$reply."'","vendor='".$vendor."' and bot='".$bot."' and quesCat='".$quesCat."'");
            }else{

                $QKEY ="display,hidden,quesCat,vendor,bot,type,lang,content";
                $QVAL ="'1','0','$quesCat','$vendor','$bot','A','$language','$reply'";
                getDbInsert($table[$m.'reply'],$QKEY,$QVAL);
            }  
        }
        $i++;  
    }
} 

// 추천 상품 업데이트 
if($goods_name){
	// //기존 상품 uid 가 삭제된 경우  
 //    $_orign_goods_uid = getDbArray($table[$m.'goods'],'vendor='.$R['vendor'].' and bot='.$R['uid'],'*','uid','asc',0,1);
 //    while($_ogu=db_fetch_array($_orign_goods_uid)){
 //    	if(!strstr($goods_uid,$_ogu['uid'])){
 //    		getDbDelete($table[$m.'goods'],'uid='.$_ogu['uid']);
 //    	}
 //    }

    // 업데이트 및 신규 추가  
    foreach ($goods_name as $index=>$name) {
        if($name){
            $link = $goods_link[$index];
            $guid = $goods_uid[$index];
            $hidden = $goods_hidden[$index];
            $f_img = $goods_f_img[$index];

            $is_goods = getDbData($table[$m.'goods'],'uid='.$guid,'uid');
            if($is_goods['uid']){
                $QVAL = "hidden='$hidden',name='$name',link='$link',f_img='$f_img'";
                getDbUpdate($table[$m.'goods'],$QVAL,'uid='.$is_goods['uid']);
            }else{
                $bot = $R['uid'];
                $QKEY = "vendor,bot,induCat,hidden,name,link,f_img"; 
                $QVAL= "'$vendor','$bot','$induCat','$hidden','$name','$link','$f_img'"; 
                getDbInsert($table[$m.'goods'],$QKEY,$QVAL);    
            }           
        }
    	
    }
}

// 봇 기준 추가질문/답변 업데이트 
if($vqa_reply){

    foreach ($vqa_reply as $index=>$reply) {
        if($reply){
            $q_content = $vqa_question[$index]; // 질문 내용
            $q_uid = $vqa_q_uid[$index]; // 질문 uid
            $r_content = $reply; // 답변 내용 
            $r_uid = $vqa_r_uid[$index];// 답변 uid 

            $is_q = getDbData($table[$m.'question'],'uid='.$q_uid,'uid');
            if($is_q['uid']){
                $q_uid = $is_q['uid'];
                $msg_array = $chatbot->getMopAndPattern($q_content);
                $pattern = $msg_array['pat'];
                $morpheme = $msg_array['mop'];
                $q_up = "pattern='".$pattern."',content='".$q_content."',morpheme='".$morpheme."'";
                getDbUpdate($table[$m.'question'],$q_up,'uid='.$q_uid);
                getDbUpdate($table[$m.'reply'],"content='".$r_content."'",'uid='.$r_uid);
                
                $chatbot->getUpdateRule($vendor,$bot,$r_uid,$q_uid,$morpheme,$pattern,$r_type,$r_content); // rule 업데이트 

            }else{
                $bot = $R['uid'];
                $r_type ='A';
                $chatbot->getInsertQA($vendor,$bot,$q_content,$r_content,$r_type); // 질/답 입력 및 rule 업데이트               
            }           
        }        
    }  
}

if($mod=='edit') $next_link = $g['s'].'/?r='.$r.'&m='.$m.'&page=build/step2&uid='.$uid;
else $next_link = $g['s'].'/?r='.$r.'&m='.$m.'&page='.$next_page.'&uid='.$uid;
getLink($next_link,'parent.','','');

?>
