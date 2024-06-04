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
// $chatbot->init(); // keyword 데이타 파일로 세팅

$chatbot->botid = $bot_id = $pData['botid'];
$chatbot->theme_name = $pData['theme_name']; // post 로 넘어온 값
$chatbot->cmod = $pData['cmod'];//
$chatbot->roomToken = $pData['roomToken'];

$cmod = $pData['cmod'];
$roomToken = $pData['roomToken'];

$userUid = $pData['mbruid'];
$chatbox->contaxt['mbruid'] = $userUid;

$botData = $chatbot->getBotDataFromId($bot_id);
$_SESSION['S_UseChatGPT'] = $botData['use_chatgpt'] == 'on' ? true : false;

$data= array();
$data['vendor'] = $botData['vendor'];
$data['bot'] = $botData['bot'];
$data['dialog'] = $botData['dialog'];
$data['cmod'] = $cmod;
$data['roomToken'] = $roomToken;
$data['userUid'] = $userUid;

// bot data 파싱
$botData['bot_service'] = $botData['chatTop'] == 'title' ? $botData['bot_service'] : "<span class='logo'><img src='".$botData['chatLogo']."' /></span>";

foreach ($botData as $key=>$value) {
   $TMPL[$key] = $value;
}

if($_POST['showTimer']){
	$chatbot->showTimer = true;
    $TMPL['showTimer'] = '';
}
else{
    $chatbot->showTimer = false;
    $TMPL['showTimer'] = 'style="display:none;"';
}

// 챗 로그 출력
$TMPL['chat_rows'];

$TMPL['bot_id'] = $bot_id;
$TMPL['user_avatar_src'] = $chatbot->getUserAvatar($my['uid'],'src');

// 메세지 페이지에서 호출한 경우가 아닌 때만 저장한다.
if($chatbox_mod!='message' && $chatbox_mod!='mybot'){
   if($mod!='changeBot' && $cmod != 'dialog' && $cmod != 'skin' && $cmod != 'LC' && $botData['botActive']!=1){
       // counter 등록
       $chatbot->regisBotCount($bot_id);
   }

   // back 버튼 출력
   $TMPL['back_button_hidden'] = '';
}else{
   $TMPL['back_button_hidden'] = ' cb-hidden';
}

// input box 스타일
if($cmod =='monitering'){
    $TMPL['monitering_inputStyle'] = $botData['bottype'] == 'call' ? 'style="display:none;"' : '';
    $TMPL['default_inputStyle'] ='style="display:none;"';
}else{
    $TMPL['monitering_inputStyle'] ='style="display:none;"';
    $TMPL['default_inputStyle'] ='';
}

$TMPL['roomToken'] = $roomToken;

//이모티콘
$TMPL['emoticon_list'] = $chatbot->getEmoticonList($bot_id); // 이모티콘 리스트

// 언어 변환 버튼
$btn_lang = new skin('chat/btn_lang');
$TMPL['btn_lang'] = $btn_lang->make();

//$TMPL['learning_data'] = $movie->getLearningWordForm($cat);

// 챗봇 박스 출력
$chatbox = new skin('chat/chat_box');
$chat_box = $chatbox->make();

$noti_box = '<div class="bg-wrap" id="bgWrap"></div>';
/*$noti_box .= '<div class="commonpanel bottom" id="commonLayerPanel">';
$noti_box .= '<div class="panel-header"></div>';
$noti_box .= '<div class="panel-contents"><span class="commonLayer-icon">아이콘</span><dl><dt>알림</dt><dd></dd></dl><p></p>';
$noti_box .= '<ul class="flex-center">';
$noti_box .= '<li><button id="popup-action" onclick="location.reload();">처음으로</button></li>';
$noti_box .= '</ul>';
$noti_box .= '</div>';
$noti_box .= '</div>';*/

$chat_box .= $noti_box;

// 2024.05.20 spikecow
// 링크 새창 대신 iframe floating layer 영역으로 노출 추가
$layer_box = '<div class="floatingpanel layer" id="floatingLayerPanel">';
$layer_box .= '<div class="panel-header"></div>';
$layer_box .= '<div class="panel-contents">';
$layer_box .= '<div class="layer-url"><button class="btn-sclose" onclick="chatbot.hideFloatLayer();"><span class="none">닫기</span></button><!--button class="btn-pre"><span class="none">이전</span></button--><input type="text"><!--button class="btn-next""><span class="none">다음</span></button--></div>';
$layer_box .= '<iframe src=""></iframe>';
$layer_box .= '</div>';
$layer_box .= '</div>';

$chat_box .= $layer_box;

// 2024.05.23 spikecow
// 상단 퀵 메뉴 layer
$quick_box = '<div class="quickpanel top" id="quickLayerPanel">';
$quick_box .= '<div class="panel-header"></div>';
$quick_box .= '<div class="panel-contents"><dl><dt>퀵메뉴 영역</dt><dd></dd></dl><p></p>';
$quick_box .= '<ul class="flex-center">';
$quick_box .= '<li><!--button id="popup-action" onclick="location.reload();">새로고침</button--></li>';
$quick_box .= '</ul>';
$quick_box .= '</div>';
$quick_box .= '</div>';

//$chat_box .= $quick_box;

$result['chat_box'] = $chat_box;
$result['userGroup'] = $my['mygroup']?$my['mygroup']:0;
$result['userLevel'] = $my['level']?$my['level']:0;
$result['vendor'] = $botData['vendor'];
$result['botUid'] = $botData['bot_uid'];
$result['botActive'] = $botData['botActive'];
$result['dialog'] = $botData['dialog'];
$result['bot_avatar_src'] = $botData['bot_avatar_src'];
$result['mbruid'] = $userUid;
$result['cgroup'] = $botData['cgroup'];
$result['showTimer'] = $pData['showTimer'];
$result['cmod'] = $pData['cmod'];

echo json_encode($result);
exit;
?>