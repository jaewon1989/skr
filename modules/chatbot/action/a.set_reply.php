<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'includes/base.class.php';  
include_once $g['dir_module'].'includes/module.class.php';
$chatbot = new Chatbot(); 

$R = getUidData($table[$m.'bot'],$bot);
if(!$R['uid']) getLink($g['s'].'/?r='.$r,'','잘못된 접근입니다  ','');	

$V = getUidData($table[$m.'vendor'],$R['vendor']);
$language    = $language?$language:'KOR';
$hidden     = $hidden ? intval($hidden) : 0;
$display	= $hidepost || $hidden ? 0 : 1;
$auth       = $auth?$auth:1; 
$induCat    = $induCat?$induCat:$V['induCat']; // 업종분류는 bot 마다 설정할 or 해당 업체의 업종으로 통일 
$vendor     = $R['vendor'];
$bot        = $R['uid'];

// showType 
$_showType = array();
foreach ($showType as $sType) {
   if($sType!='') $_showType[] = $sType;
}
$showType = json_encode($_showType);

if($uid){

    // reply 테이블 업데이트 
    $QVAL ="showType='$showType'";
    getDbUpdate($table[$m.'reply'],$QVAL,'uid='.$uid);

    $RM_QKEY = "r_uid,gid,display,vendor,bot,type,quesCat,entity,text,title,sub_title,link_url,link_target";

    // 텍스트 타입 
    if(isset($text_arr)){
        $type = 'T';
        foreach ($text_arr as $gid=>$text){
            if($text_uid[$gid]){
                $QVAL = "text='$text'";
                getDbUpdate($table[$m.'replyMulti'],$QVAL,'uid='.$text_uid[$gid]);
            }else{
               $QVAL = "'$r_uid','$gid','$display','$vendor','$bot','$type','$quesCat','$entity','$text','$title','$sub_title','$link_url','$link_target'";
               getDbInsert($table[$m.'replyMulti'],$RM_QKEY,$QVAL);     
            }
                         
        }
    }
    // 메뉴 타입 
    if(isset($menu_title)){
        $type = 'M';
        foreach ($menu_title as $gid=>$menu){
            if (!$menu) continue;
            $entity = $menu;
            $title = $menu;
            $link_url = $menu_link[$gid];
            $link_target = $menu_linkTarget[$gid];

            // 수정 
            if($menu_uid[$gid]){
                $QVAL = "display='$display',entity='$entity',title='$title',sub_title='$sub_title',link_url='$link_url',link_target='$link_target'";
                getDbUpdate($table[$m.'replyMulti'],$QVAL,'uid='.$menu_uid[$gid]);
            }else{
                $QVAL = "'$r_uid','$gid','$display','$vendor','$bot','$type','$quesCat','$entity','$text','$title','$sub_title','$link_url','$link_target'";
                getDbInsert($table[$m.'replyMulti'],$RM_QKEY,$QVAL);     
            }                         
        }
    }
    $r_uid= $uid;
   
}else{
    // reply 테이블 저장 
    $QKEY ="display,hidden,quesCat,vendor,bot,type,lang,content,showType";
    $QVAL ="'1','0','$quesCat','$vendor','$bot','M','$language','','$showType'";
    getDbInsert($table[$m.'reply'],$QKEY,$QVAL);

    $r_uid = getDbCnt($table[$m.'reply'],'max(uid)',"bot='".$bot."' and vendor='".$vendor."'");

    // replyMulti 테이블 저장 
    $RM_QKEY = "r_uid,gid,display,vendor,bot,type,quesCat,entity,text,title,sub_title,link_url,link_target";
    if(isset($text_arr)){
        $type = 'T';
        foreach ($text_arr as $gid=>$text){
            if (!$text) continue;
            $QVAL = "'$r_uid','$gid','$display','$vendor','$bot','$type','$quesCat','$entity','$text','$title','$sub_title','$link_url','$link_target'";
            getDbInsert($table[$m.'replyMulti'],$RM_QKEY,$QVAL);              
        }
    }
    if(isset($menu_title)){
        $type = 'M';
        foreach ($menu_title as $gid=>$menu){
            if (!$menu) continue;
            $entity = $menu;
            $title = $menu;
            $link_url = $menu_link[$gid];
            $link_target = $menu_linkTarget[$gid];
            $QVAL = "'$r_uid','$gid','$display','$vendor','$bot','$type','$quesCat','$entity','$text','$title','$sub_title','$link_url','$link_target'";
            getDbInsert($table[$m.'replyMulti'],$RM_QKEY,$QVAL);              
        }
    }
    
}

$link = $g['s'].'/?r='.$r.'&m='.$m.'&c='.$c.'&cat='.$cat.'&bot='.$bot;
getLink($link,'parent.','','');

?>
