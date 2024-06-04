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

if(!$hform) {
    echo "<script>alert('잘못된 접근입니다2'); history.back();</script>"; exit;
}

// 암호화 해독
if($pData['nonceVal']) {
    $nonceVal = $pData['nonceVal'];
    $encryption = new Encryption();
    $pData['r_data'] = $encryption->decrypt($pData['r_data'], $nonceVal);
}

$r_data = json_decode(stripslashes(htmlspecialchars_decode($pData['r_data'])), true);
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

switch($action) {
    // 유저 로그인
    case('user_login') :    
        if(!trim($r_data['user_id'])) {
            $result['error'] = true;
            $result['err_msg'] = "아이디를 입력해주세요.";
            echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
        }
        if(!trim($r_data['user_pw'])) {
            $result['error'] = true;
            $result['err_msg'] = "비밀번호를 입력해주세요.";
            echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
        }
        
        // 암호화 공개키 조회
        $encryptKey = $objGSITM->getGSITMEncryptKey();
        if(!$encryptKey) {
            $result['error'] = true;
            $result['err_msg'] = "암호화키 실패";
            echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
        }
        
        // JWT 토큰 조회
        $_data = array();
        $_data['bot'] = $bot;
        $_data['user_id'] = $r_data['user_id'];
        $_data['user_pw'] = $r_data['user_pw'];
        $_data['encryptKey'] = $encryptKey;        
        $aToken = $objGSITM->getGSITMAuthToken($_data);
        if(!$aToken['token']) {
            $result['error'] = true;
            $result['err_msg'] = "인증 토큰 실패";
            echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
        }
        
        $json_data = array();
        $json_data['is_login'] = true;
        $json_data['expire'] = $aToken['expire'];
        
        $result['error'] = false;        
        $result['json_data'] = $json_data;
        $result['func'] = "getWelcomeMsg";
    break;
    
    // 요청 분류값 응답
    case('get_reqCl') :    
        if(!isset($_SESSION['aGSITMCodes'])) {
            $objGSITM->getGSITMTypeCode();
        }

        $reqTySe = $step;
        $reqTyKey = array_search($reqTySe, array_column($_SESSION['aGSITMCodes'], 'reqTySe'));
        if(count($_SESSION['aGSITMCodes'][$reqTyKey]['aReqCl']) == 0) {
            $objGSITM->getGSITMTypeCode($reqTyKey);
        }
        
        $reqCl_codes = $servId_codes = "<option value=''>- ".($pData['reqType'] == "search" ? "전체" : "선택")." -</option>";
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
        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
    break;
    
    // 요청 분류의 양식 및 결재라인
    case('get_format') :
        if($pData['reqTySe'] && $pData['reqCl']) {
            if($pData['tmlNo']) {
                if(!isset($_SESSION['aGSITMTmlList'])) {
                    $_data = array('reqTySe' => $pData['reqTySe'], 'reqCl' => $pData['reqCl'], 'servId' => $pData['servId']);                    
                    $objGSITM->getGSITMDataFormat($_data);
                }
                $_key = array_search($pData['tmlNo'], array_column($_SESSION['aGSITMTmlList'], 'tmlNo'));
                $result['tmlCnt'] = $_key !== false ? $_SESSION['aGSITMTmlList'][$_key]['tmlCn'] : "";
            } else {
                $_data = array('reqTySe' => $pData['reqTySe'], 'reqCl' => $pData['reqCl'], 'servId' => $pData['servId']);
                $aDataFormat = $objGSITM->getGSITMDataFormat($_data);
                $result['systemList'] = $aDataFormat['systemList'];
                $result['tmlList'] = $aDataFormat['tmlList'];
                $result['tmlCnt'] = $aDataFormat['tmlCnt'];
                $result['apprLines'] = $aDataFormat['apprLines'];
            }        
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
    break;
    
    // 검색을 통한 리스트 조회
    case('get_search_list') :
        $_data['gsitm_listtype'] = $pData['listtype'];
        
        $_data['getParam'] = array();
        $_data['getParam']['fromDate'] = str_replace("-", "", $r_data['gsitm_fromDate']);
        $_data['getParam']['toDate'] = str_replace("-", "", $r_data['gsitm_toDate']);        
        
        if($pData['listtype'] == "service") {            
            $_data['getParam']['chkActivity'] = $r_data['gsitm_chkActivity'];        
            $_data['getParam']['sReqTySe'] = $r_data['gsitm_reqTySe'];
            $_data['getParam']['reqCl'] = $r_data['gsitm_reqCl'];
            $_data['getParam']['schCond'] = $r_data['gsitm_schCond'];
            $_data['getParam']['searchword'] = $r_data['gsitm_searchword'];
            $result['data'] = $objGSITM->getGSITMServiceList($_data);
        }
        
        if(preg_match("/^approval_/", $pData['listtype'])) {
            $_data['getParam']['searchAppStatusCod'] = $r_data['gsitm_searchAppStatusCod'];        
            $_data['getParam']['searchDateUse'] = $r_data['gsitm_searchDateUse'] == "true" ? true : false;
            $_data['getParam']['searchType'] = $r_data['gsitm_searchType'];
            $_data['getParam']['searchReqTySe'] = $r_data['gsitm_reqTySe'];
            $_data['getParam']['searchWord'] = $r_data['gsitm_searchword'];
            $result['data'] = $objGSITM->getGSITMServiceList($_data);
        }
        
        if(preg_match("/^circular_/", $pData['listtype'])) {
            $_data['getParam']['searchAppStatusCod'] = $r_data['gsitm_searchAppStatusCod'];
            $_data['getParam']['searchType'] = $r_data['gsitm_searchType'];
            $_data['getParam']['searchReqTySe'] = $r_data['gsitm_reqTySe'];
            $_data['getParam']['searchWord'] = $r_data['gsitm_searchword'];
            $result['data'] = $objGSITM->getGSITMServiceList($_data);
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
    break;
    
    // 리스트 more 버튼 요청
    case('get_more_list') :
        if($pData['page'] >= count($_SESSION['aGSITMSericeList'])) {
            if($pData['listtype'] != "notice") {
                $result['moreList'] = "";
                $result['page'] = $pData['page'];
            } else {
                if($pData['ntpage'] < $pData['ntpageall']) { 
                    $_data = array();
                    $_data['gsitm_listtype'] = $pData['listtype'];
                    $_data['getParam']['page'] = ($pData['ntpage']+1);
                    $result = $objGSITM->getGSITMServiceList($_data);
                    $result['moreList'] = $result['gsitm_req_list'];
                    $result['page'] = $result['gsitm_page'];
                    $result['pageall'] = $result['gsitm_pageall'];
                    $result['ntpage'] = $_data['getParam']['page'];
                }
            }
        } else {
            $_data = array();
            $_data['gsitm_listtype'] = $pData['listtype'];
            $_data['list_page'] = $pData['page'];
            $result['moreList'] = $objGSITM->getGSITMServiceListRows($_data);
            $result['page'] = ($pData['page']+1);
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
    break;
    
    // 상세보기 페이지 
    case('get_view_data') :
        $_data = array();
        
        $pData['listtype_org'] = $pData['listtype'];
        
        // 회람문서 보기는 결재문서 보기와 동일
        if(preg_match("/^circular_/", $pData['listtype'])) {
            $pData['listtype'] = str_replace("circular_", "approval_", $pData['listtype']);            
        }
        
        $_data['gsitm_listtype'] = $pData['listtype'];
        $_data['gsitm_listtype_org'] = $pData['listtype_org'];
        $_data['idx'] = $pData['idx'];
        
        if(preg_match("/^approval_/", $pData['listtype'])) {
            $_data['createCoId'] = $pData['coid'];
            $_data['createEmpId'] = $pData['empid'];
            $_data['apprDetailId'] = $pData['apprdetailid'];
        }
        if($pData['listtype'] == "notice") {
            $_data['bbsId'] = $pData['bbsid'];
        }
        $result['viewData'] = $objGSITM->getGSITMServiceViewPage($_data);
        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
    break;
    
    // 회람 사용자 폼
    case('get_circular_form') :
        $result['viewData'] = $objGSITM->getCircularForm();
        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
    break;
    
    // 회람 사용자 검색
    case('get_circular_usersearch') :
        $result['viewData'] = $objGSITM->getCircularSearchMember($pData['searchWord']);
        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
    break;
    
    // 회람 전송 처리
    case('get_circular_submit') : 
        $_data['postParam'] = array();
        $_data['postParam']['circularInfo'] = $pData['circularInfo'];
        $_data['postParam']['pApprId'] = $pData['pApprId'];
        $_data['postParam']['pCoId'] = $pData['pCoId'];
        $_data['postParam']['pCreateEmpId'] = $pData['pCreateEmpId'];
        $_data['postParam']['pCircularDesc'] = $pData['pCircularDesc'];
        $result = $objGSITM->getCircularSubmit($_data);
        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
    break;
    
    // 결재라인 변경 폼
    case('get_apprline_form') :
        if(isset($_POST['apprLines'])) $_data = $_POST['apprLines'];
        $result['viewData'] = $objGSITM->getApprLineForm($_data);
        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
    break;
    
    // 결재 승인 버튼들
    case('get_appr_doc_submit') :
        $_data['actionMode'] = $pData['actionMode'];
        
        $_data['postParam'] = array();
        
        // 회람 전송
        if($pData['actionMode'] == "circular") {
            $_data['postParam']['pCoId'] = $_SESSION['srinfo']['qusrCompId'];
            $_data['postParam']['pCreateEmpId'] = $_SESSION['srinfo']['createEmpId'];
            $_data['postParam']['pApprId'] = $_SESSION['srinfo']['srId'];
            $_data['postParam']['circularInfo'] = $pData['circularInfo'];
            $_data['postParam']['pCircularDesc'] = $pData['pCircularDesc'];
        }
        if($pData['actionMode'] == "confirm") {
            $_data['postParam']['pCoId'] = $_SESSION['srinfo']['qusrCompId'];
            $_data['postParam']['pCreateEmpId'] = $_SESSION['srinfo']['createEmpId'];
            $_data['postParam']['pApprDetailId'] = $_SESSION['srinfo']['apprDetailId'];
            $_data['postParam']['pApprEmpCoId'] = $_SESSION['srinfo']['apprCoId'];
            $_data['postParam']['pApprEmpId'] = $_SESSION['srinfo']['apprEmpId'];
            $_data['postParam']['pApprTy'] = $_SESSION['srinfo']['apprTy'];
            $_data['postParam']['pApprId'] = $_SESSION['srinfo']['srId'];
            $_data['postParam']['pDocId'] = $_SESSION['srinfo']['srId'];
        }
        if($pData['actionMode'] == "return") {
            $_data['postParam']['pCoId'] = $_SESSION['srinfo']['qusrCompId'];
            $_data['postParam']['pCreateEmpId'] = $_SESSION['srinfo']['createEmpId'];
            $_data['postParam']['pApprDetailId'] = $_SESSION['srinfo']['apprDetailId'];
            $_data['postParam']['pApprEmpCoId'] = $_SESSION['srinfo']['apprCoId'];
            $_data['postParam']['pApprEmpId'] = $_SESSION['srinfo']['apprEmpId'];
            $_data['postParam']['pApprTy'] = $_SESSION['srinfo']['apprTy'];
            $_data['postParam']['pApprId'] = $_SESSION['srinfo']['srId'];
            $_data['postParam']['pDocId'] = $_SESSION['srinfo']['srId'];
            $_data['postParam']['pApprReply'] = $pData['pApprReply'];
            $_data['postParam']['pReturnDtlId'] = 0;
            $_data['postParam']['pReturnEmpId'] = $_SESSION['srinfo']['createEmpId'];
            $_data['postParam']['pReturnDepId'] = $_SESSION['srinfo']['qusrDeptId'];
            $_data['postParam']['pCurStatus'] = 4;
        }
        if($pData['actionMode'] == "reject") {
            $_data['postParam']['pCoId'] = $_SESSION['srinfo']['qusrCompId'];
            $_data['postParam']['pCreateEmpId'] = $_SESSION['srinfo']['qusrId'];
            $_data['postParam']['pApprDetailId'] = $_SESSION['srinfo']['apprDetailId'];
            $_data['postParam']['pApprEmpCoId'] = $_SESSION['srinfo']['apprCoId'];
            $_data['postParam']['pApprEmpId'] = $_SESSION['srinfo']['apprEmpId'];
            $_data['postParam']['pApprTy'] = $_SESSION['srinfo']['apprTy'];
            $_data['postParam']['pApprId'] = $_SESSION['srinfo']['srId'];
            $_data['postParam']['pDocId'] = $_SESSION['srinfo']['srId'];
            $_data['postParam']['pApprReply'] = $pData['pApprReply'];
            $_data['postParam']['pCurStatus'] = 6;
        }
        if($pData['actionMode'] == "temp_delete") {
            $_data['postParam']['pCoId'] = $_SESSION['srinfo']['qusrCompId'];
            $_data['postParam']['pCreateEmpId'] = $_SESSION['srinfo']['createEmpId'];
            $_data['postParam']['pApprDetailId'] = $_SESSION['srinfo']['apprDetailId'];
            $_data['postParam']['pApprFlag'] = $_SESSION['srinfo']['apprFlag'];
            $_data['postParam']['pApprId'] = $_SESSION['srinfo']['srId'];
        }
        
        $result = $objGSITM->getApprDocSubmit($_data);
        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
    break;
    
    // 결재 수정 폼
    case('get_appr_modify') :
        $_data['gsitm_listtype'] = $pData['listtype'];
        $_data['idx'] = $pData['idx'];
        $_data['createCoId'] = $pData['coid'];
        $_data['createEmpId'] = $pData['empid'];
        
        $result['viewData'] = $objGSITM->getApprovalModifyPage($_data);
        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
    break;

    // 전송 프로세스
    case('request') :
        switch($step) {        
            case('gsitm_confirm') :
            case('gsitm_modify') :
                $_data['actionMode'] = $step;
                
                if($step == "gsitm_confirm") {
                    if(!trim($r_data['gsitm_reqTySe'])) {
                        $result['error'] = true;
                        $result['err_msg'] = "요청유형을 선택해주세요.";
                        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
                    }
                    if(!trim($r_data['gsitm_reqCl'])) {
                        $result['error'] = true;
                        $result['err_msg'] = "요청분류를 선택해주세요.";
                        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
                    }
                    if(trim($r_data['gsitm_reqTySe']) == "0223") {
                        if(!trim($r_data['gsitm_icdtOcrDt'])) {
                            $result['error'] = true;
                            $result['err_msg'] = "장애발생일시를 입력해주세요.";
                            echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
                        }
                    }
                }
                
                if(!trim($r_data['gsitm_chgCmplHopeDt'])) {
                    $result['error'] = true;
                    $result['err_msg'] = "완료희망일시를 입력해주세요.";
                    echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
                }
                if(!trim($r_data['gsitm_req_title'])) {
                    $result['error'] = true;
                    $result['err_msg'] = "제목을 입력해주세요.";
                    echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
                }
                if(!trim($r_data['gsitm_req_content'])) {
                    $result['error'] = true;
                    $result['err_msg'] = "내용을 입력해주세요.";
                    echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
                }
                
                // servId 값 없을 경우 SV00001411 로 전송
                $gsitm_servId = trim($r_data['gsitm_servId']) ? trim($r_data['gsitm_servId']) : $objGSITM->servId;
                
                // 결재라인 세팅
                if(isset($r_data['gsitm_apprLinesStr']) && is_array($r_data['gsitm_apprLinesStr'])) {
                    for ($i=0, $nCnt=count($r_data['gsitm_apprLinesStr']); $i<$nCnt; $i++) {
                        $_data['postParam']['apprLinesStr['.$i.']'] = stripslashes($r_data['gsitm_apprLinesStr'][$i]);
                    }
                }
                
                // 첨부파일 세팅            
                if(isset($_FILES['files'])) {
                    for ($i=0, $nCnt=count($_FILES['files']['name']); $i<$nCnt; $i++) {
                        $_data['postParam']['files['.$i.']'] = new curlfile($_FILES['files']['tmp_name'][$i], $_FILES['files']['type'][$i], $_FILES['files']['name'][$i]);
                    }
                }
                
                // 전송
                if($cmod != 'dialog' && $cmod != 'skin' && $cmod != 'LC' && $cmod != 'TS') {                    
                    $_data['postParam']['reqTySe'] = trim($r_data['gsitm_reqTySe']);
                    $_data['postParam']['reqCl'] = trim($r_data['gsitm_reqCl']);
                    $_data['postParam']['servId'] = $gsitm_servId;
                    $_data['postParam']['chgCmplHopeDt'] = trim($r_data['gsitm_chgCmplHopeDt']);
                    $_data['postParam']['srTitlNm'] = trim($r_data['gsitm_req_title']);
                    $_data['postParam']['srCn'] = trim($r_data['gsitm_req_content']);
                    $_data['postParam']['secuCn'] = trim($r_data['gsitm_req_secucontent']);
                    if(trim($r_data['gsitm_icdtOcrDt'])) {
                        $_data['postParam']['icdtOcrDt'] = trim($r_data['gsitm_icdtOcrDt']);
                    }
                    if($step == "gsitm_modify") {
                        $_data['postParam']['createCoId'] = trim($r_data['createCoId']);
                        $_data['postParam']['createEmpId'] = trim($r_data['createEmpId']);
                        $_data['postParam']['srId'] = trim($r_data['srId']);
                        $_data['postParam']['apprDetailId'] = trim($r_data['apprDetailId']);                        
                        $_data['postParam']['approvalStatus'] = trim($r_data['approvalStatus']);
                    }
                    
                    $_result = $objGSITM->getGSITMReqSend($_data);
                    if($step == "gsitm_confirm") {
                        $bot_msg = $_result['msg'];
                    } else {
                        $result['msg'] = $_result['msg'];
                        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
                    }
                        
                } else {
                    if($step == "gsitm_confirm") {
                        $bot_msg = "정상적으로 처리되었습니다";
                    } else {
                        $result['msg'] = "정상적으로 처리되었습니다";
                        echo json_encode($result, JSON_UNESCAPED_UNICODE); exit;
                    }
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
    break;
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
