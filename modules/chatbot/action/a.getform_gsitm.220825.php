<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
require_once $g['path_core'].'function/encryption.php';
require_once $g['dir_module'].'includes/gsitm.class.php';

$pData = getEscString($_POST['pData']);

if(!trim($pData['bot'])) {
    echo "<script>alert('잘못된 접근입니다'); history.back();</script>"; exit;
}

$aWeek = array("일", "월", "화", "수", "목", "금", "토");
$dToday = date("Y-m-d");
$dNowTime = date("H:i");

$chatbot = new Chatbot();
$objGSITM = new GSITM($chatbot);

$result=array();
$result['error']=false;
$result['log'] = false;

$chatbot->vendor = $vendor = $pData['vendor'];
$chatbot->botuid = $bot = $pData['bot'];
$chatbot->dialog = $dialog = $pData['dialog'];
$chatbot->botid = $botid = $pData['botid'];
$chatbot->cmod = $cmod = $pData['cmod']; // vod or cs 
$chatbot->roomToken = $roomToken = $pData['roomToken'];
$chatbot->bottype = $bottype = $pData['bot_type'];
$chatbot->channel = $channel = $pData['channel'];

// aramjo context
$chatbot->getBotContext($pData);
//-----------------------------------------------
$category = trim($pData['category']);
$hform = trim($pData['hform']);
$action = trim($pData['action']);
$step = trim($pData['step']);
$last_chat = trim($pData['last_chat']);

if(!$hform || !$step) {
    echo "<script>alert('잘못된 접근입니다2'); history.back();</script>"; exit;
}

// 암호화 해독
if($pData['nonceVal']) {
    $nonceVal = $pData['nonceVal'];
    $encryption = new Encryption();
    $pData['r_data'] = $encryption->decrypt($pData['r_data'], $nonceVal);
}

$r_data = json_decode($pData['r_data'], true);
$botData = $chatbot->getBotDataFromId($botid);

$TMPL = array();
$TMPL['bot_avatar_src'] = $botData['bot_avatar_src'];
$TMPL['bot_name'] = $botData['bot_name'];
$TMPL['date'] = (date('a') == 'am' ? '오전 ':'오후 ').date('g').':'.date('i');
$TMPL['category_type'] = $category;
$TMPL['hform_type'] = $hform;
$TMPL['action'] = $action;
$TMPL['last_chat'] = $last_chat;

// api 조회용
$_data = array();
$_data['vendor'] = $vendor;
$_data['bot'] = $bot;
$_data['getParam'] = array();
$_data['postParam'] = array();

// 요청 분류값 응답
if($action == "get_reqCl") {
    if(!isset($_SESSION['aGSITMCodes'])) {
        $objGSITM->getGSITMTypeCode();
    }

    $reqTySe = $step;    
    $reqTyKey = array_search($reqTySe, array_column($_SESSION['aGSITMCodes'], 'reqTySe'));
    if(count($_SESSION['aGSITMCodes'][$reqTyKey]['aReqCl']) == 0) {
        $objGSITM->getGSITMTypeCode($reqTyKey);
    }
    
    $reqCl_codes = $servId_codes = "<option value=''>- 선택 -</option>";
    $aReqCl = $_SESSION['aGSITMCodes'][$reqTyKey]['aReqCl'];
    foreach($aReqCl as $aCl) {
        $reqCl_codes .="<option value='".$aCl['reqCl']."' servId='".$aCl['servId']."'>".$aCl['reqClNm']."</option>";
        
        if($aCl['servId']) {
            $servId_codes .="<option value='".$aCl['servId']."'>".$aCl['servNm']."</option>";
        }
    }
    $result['error'] = false;
    $result['reqCls'] = $reqCl_codes;
    $result['servIds'] = $servId_codes;
    echo json_encode($result); exit;
}

// 전송 프로세스
if($action == "request") {
    switch($step) {        
        case('gsitm_confirm') :
            if(!trim($r_data['gsitm_reqTySe'])) {
                $result['error'] = true;
                $result['err_msg'] = "요청유형을 선택해주세요.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['gsitm_reqCl'])) {
                $result['error'] = true;
                $result['err_msg'] = "요청분류를 선택해주세요.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['gsitm_req_title'])) {
                $result['error'] = true;
                $result['err_msg'] = "제목을 입력해주세요.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['gsitm_req_content'])) {
                $result['error'] = true;
                $result['err_msg'] = "내용을 입력해주세요.";
                echo json_encode($result); exit;
            }
            
            // servId 값 없을 경우 SV00001411 로 전송
            $gsitm_servId = trim($r_data['gsitm_servId']) ? trim($r_data['gsitm_servId']) : $objGSITM->servId;            
            
            // 전송
            if($cmod != 'dialog' && $cmod != 'skin' && $cmod != 'LC' && $cmod != 'TS') {
                $_data['servId'] = $gsitm_servId;
                $_data['reqTySe'] = trim($r_data['gsitm_reqTySe']);
                $_data['reqCl'] = trim($r_data['gsitm_reqCl']);
                $_data['srTitlNm'] = trim($r_data['gsitm_req_title']);
                $_data['srCn'] = trim($r_data['gsitm_req_content']);
                $_data['secuCn'] = "";
                
                $bot_msg = $objGSITM->getGSITMReqSend($_data);
            } else {
                $bot_msg = "정상적으로 처리되었습니다";
            }
            
            $TMPL['response'] = "<span>".nl2br($bot_msg)."</span>";
            
            $skinFile = $hform.'_'.$action.'_result';
            $skin = new skin($skinFile);
            $content = $skin->make('lib');
            $result['msg'] = $content;
            $result['log'] = true;
            $result['finish'] = true;
        break;
        
        case('cancel') :
            // 모든 예약 process cancel
            $bot_msg = "취소되었습니다.";
            $TMPL['response'] = "<span>".nl2br($bot_msg)."</span>";
            
            $skinFile = $hform.'_cancel';
            $skin = new skin($skinFile);
            $content = $skin->make('lib');
            $result['msg'] = $content;
            $result['log'] = true;
        break;
    }
}

if($last_chat && $result['msg']) {
    $botChat = array();
    $botChat['content'] = array(array("hform", $result['msg']));
    $botChat['last_chat'] = $last_chat; // 사용자 chat uid
    $botChat['same_chat'] = 1;
    $chatbot->addBotChatLog($botChat);
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit;
?>
