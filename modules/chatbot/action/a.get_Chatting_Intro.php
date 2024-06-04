<?php
/* 채팅박스 초기화 */
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';

$pData = getEscString($_POST);

if(!trim($pData['botid'])) {
    echo "<script>alert('잘못된 접근입니다'); history.back();</script>"; exit;
}

$chatbot = new Chatbot();

$chatbot->botid = $bot_id = $pData['botid'];
$chatbot->cmod = $cmod = $pData['cmod'];//
$chatbot->roomToken = $roomToken = $pData['roomToken'];

$botData = $chatbot->getBotDataFromId($bot_id);
$result = array();

if($botData['intro_use']) {
    $_data = array();
    $_data['vendor'] = $botData['vendor'];
    $_data['bot'] = $botData['bot'];
    $aIntroData = $chatbot->getBotIntroData($_data);
    $aIntroProfile = $aIntroData['aIntroProfile'];
    $aIntroMenu = $aIntroData['aIntroMenu'];
    
    $chHtml = "";
    $chHtml .="<div id='intro_window' class='intro_window'>";
    $chHtml .=" <div class='intro_exit'>";
    $chHtml .="     <span class='intro_btn_exit' data-role='chat-exit'></span>";
    $chHtml .=" </div>";
    $chHtml .=" <div class='intro_ctitle'>";
    $chHtml .="     <div class='mtitle'>".nl2br(stripslashes($aIntroData['intro_greeting']))."</div>";
    $chHtml .="     <div class='stitle'>".nl2br(stripslashes($aIntroData['intro_sub_greeting']))."</div>";
    
    $chHtml .=" </div>";
    $chHtml .=" <div class='intro_ctitle2'>";
    $chHtml .="     <div class='ctitle2_msg'>응답시간 관련한 문구도 입력해보세요<br>응답시간 빠름 보통 몇 분 내에 응답합니다.!</div>";
    
    if($aIntroData['intro_profile']) {
        $chHtml .="     <div class='profile_wrap'>";
        foreach($aIntroProfile as $aProfile) {
            $chHtml .="     <span class='profile' style='background-image:url(".$aProfile['value'].");'></span>";
        }
        $chHtml .="     </div>";
    }
    $chHtml .="     <div class='hr'></div>";
    $chHtml .="     <a href='javascript:;' class='start' data-role='chat-start'>새 대화 시작</a>";
    $chHtml .=" </div>";
    
    if($aIntroData['intro_menu']) {
        $chHtml .=" <div class='intro_menu'>";
        foreach($aIntroMenu as $aMenu) {
            $chHtml .=" <div class='btn_menu'>";
            $chHtml .="     <a href='".$aMenu['url']."' target='_blank'>".stripslashes($aMenu['name'])."</a>";
            $chHtml .="     <i class='fa fa-angle-right' aria-hidden='true'></i>";
            $chHtml .=" </div>";            
        }
        $chHtml .=" </div>";
    }
    
    if($aIntroData['intro_channel']) {
        $chHtml .=" <div class='intro_sns_list'>";
        $chHtml .="     <a href='' target='_blank'><img src='/_core/skin/images/sns_kakao.png'></a>";
        $chHtml .="     <a href='' target='_blank'><img src='/_core/skin/images/sns_facebook.png'></a>";
        $chHtml .="     <a href='' target='_blank'><img src='/_core/skin/images/sns_line.png'></a>";
        $chHtml .="     <a href='' target='_blank'><img src='/_core/skin/images/sns_talk.png'></a>";
        $chHtml .=" </div>";
    }
    
    if($aIntroData['intro_logo'] && $aIntroData['intro_logo_url']) {
        $chHtml .=" <div class='intro_bottom'>";
        $chHtml .="     <img src='".$aIntroData['intro_logo_url']."'>";
        $chHtml .=" </div>";
        $chHtml .="</div>";
    }
    
    $result['chat_intro'] = $chHtml;    
}

echo json_encode($result);
exit;
?>