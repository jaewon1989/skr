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
            $code = $goods_code[$index];

            $is_goods = getDbData($table[$m.'goods'],'uid='.$guid,'uid');
            if($is_goods['uid']){
                $QVAL = "hidden='$hidden',name='$name',code='$code',link='$link',f_img='$f_img'";
                getDbUpdate($table[$m.'goods'],$QVAL,'uid='.$is_goods['uid']);
            }else{
                $bot = $R['uid'];
                $QKEY = "vendor,bot,induCat,hidden,name,link,f_img,code"; 
                $QVAL= "'$vendor','$bot','$induCat','$hidden','$name','$link','$f_img','$code'"; 
                getDbInsert($table[$m.'goods'],$QKEY,$QVAL);    
            }           
        }
    	
    }
}

getLink('reload','parent.','','');

?>
