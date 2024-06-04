<?php
class GSITM {
    public $module;
    public $chatbot;
    
    public $apiHost;
    public $apiAuthKey;
    public $coId;
    public $cltUseAt;
    public $useAt;
    public $reqCod;
    public $servId;
    public $userId;
    public $aApprovalStatus;
    public $jwtKey;
    
    public function __construct($chatbot) {        
        $this->chatbot = $chatbot;
        $this->module = $this->chatbot->module;
        
        $this->apiHost = "http://3.35.193.57/open-api/chatbot/v1";
        $this->apiAuthKey = array("oapi-accessKey: 5ff96cf5-1c85-472a-a96a-72c88426a431", "oapi-secretKey: MldTd3lfSEc5Ky5PMFFIZjVtNVVJNWNja1w9MjxI");
        $this->cltUseAt = "Y";
        $this->useAt = "Y";
        $this->reqCod = "2";
        $this->servId = "SV00001411";
        $this->coId = "05";
        $this->userId = "it1457";
        $this->aApprovalStatus = array(2=>'진행중', 4=>'반려', 5=>'승인', 6=>'기각');
    }
    
    // 로그인 JWT 설정
    public function setGSITMJWTKey() {
        global $g, $table, $TMPL;
        
        $bot = $this->chatbot->botuid;
        $_dbToken = getDbData($table[$this->module.'token'], "bot='".$bot."' and access_mod='gsitm_jwt'", "*");
        if($_dbToken['r_data']) {
            $aToken = json_decode($_dbToken['r_data'], true);
            
            $this->apiAuthKey[] = "Authorization: Bearer ".$aToken['token'];
            $this->coId = $aToken['domain'];
            $this->userId = $aToken['userId'];
        }
    }
    
    // 로그인 암호화키 조회
    public function getGSITMEncryptKey() {
        $_data = array();
        $_data['api_url'] = $this->apiHost."/common/encryptKey";
        $_data['headers'] = $this->apiAuthKey;
        $aAPIResult = $this->chatbot->getReserveAPI($_data);
        if($aAPIResult['code'] == "0000" && isset($aAPIResult['data']) && $aAPIResult['data']) {
            return $aAPIResult['data'];
        } else {
            return false;
        }
    }
    // 로그인 JWT 가져오기
    public function getGSITMAuthToken($data) {
        global $g, $table, $TMPL;
        
        $result = array();
        $user_id = $data['user_id'];
        $user_pw = $data['user_pw'];
        $encryptKey = $data['encryptKey'];
        
        $encID = $this->getGSITMRSAEncrypt($user_id, $encryptKey);
        $encPW = $this->getGSITMRSAEncrypt($user_pw, $encryptKey);
        if(!$encID || !$encPW) {
            $result['message'] = "암호화 실패";
        } else {
            $_data = array();
            $_data['api_url'] = $this->apiHost."/common/authorize";
            $_data['headers'] = $this->apiAuthKey;
            $_data['headers'][] = "id: ".$encID;
            $_data['headers'][] = "pw: ".$encPW;
            $_data['method'] = "post";
            $_data['postParam'] = "";
            $aAPIResult = $this->chatbot->getReserveAPI($_data);
            if($aAPIResult['code'] == "0000" && isset($aAPIResult['data'])) {
                $token = $aAPIResult['data']['token'];
                $aToken = explode('.', $token);
                $aJWTData = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', $aToken[1]))), true);
                
                $result['token'] = $token;
                $result['expire'] = $aJWTData['exp'];
                $result['domain'] = $aJWTData['loginDomain'];
                $result['userId'] = $aJWTData['sub'];
                $r_data = json_encode($result, JSON_UNESCAPED_UNICODE);
                
                // 로그인 JWT 정보 등록
                $_dbToken = getDbData($table[$this->module.'token'], "bot='".$data['bot']."' and access_mod='gsitm_jwt'", "*");
                if($_dbToken['uid']) {
                    getDbUpdate($table[$this->module.'token'], "expire='".$result['expire']."', r_data='".$r_data."'", "uid='".$_dbToken['uid']."'");
                } else {                
                    $_QKEY = "bot, access_mod, access_token, roomToken, userId, expire, r_data";
                    $_QVAL = "'".$data['bot']."', 'gsitm_jwt', '', '', '', '".$result['expire']."', '".$r_data."'";
                    getDbInsert($table[$this->module.'token'],$_QKEY,$_QVAL);
                }
                
            } else {
                $result['message'] = $aAPIResult['message'];
            }            
        }
        return $result;
    }
    public function getGSITMRSAEncrypt($plainText, $publicKey) {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'https://www.devglan.com/online-tools/rsa-encrypt',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "textToEncrypt": "'.$plainText.'",
                "publicKey": "'.$publicKey.'",
                "keyType": "publicKeyForEncryption",
                "cipherType": "RSA"
            }',
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        $aKey = json_decode($response, true);
        if($aKey['encryptedOutput']) return $aKey['encryptedOutput'];
        else return false;
    }
    
    // 요청분류 목록 조회
    public function getGSITMTypeCode($_reqTyKey=-1) {        
        $_data = array();
        $_data['api_url'] = $this->apiHost."/common/reqCl";
        $_data['headers'] = $this->apiAuthKey;
        
        if($_reqTyKey == -1) {
            $aCodes = array();
            $aCodes[] = array('reqTySe'=>'0215', 'reqTySeNm'=>'단순요청', 'aReqCl'=>array());
            $aCodes[] = array('reqTySe'=>'0223', 'reqTySeNm'=>'장애요청', 'aReqCl'=>array());
            $aCodes[] = array('reqTySe'=>'0179', 'reqTySeNm'=>'변경요청', 'aReqCl'=>array());            
            $aCodes[] = array('reqTySe'=>'1045', 'reqTySeNm'=>'문제/개선', 'aReqCl'=>array());
        
            // reqCl 전체 데이터 가져오기            
            $aAPIResult = $this->chatbot->getReserveAPI($_data);
            if($aAPIResult['code'] == "0000" && isset($aAPIResult['data'])) {
                foreach($aAPIResult['data'] as $_code) {
                    $reqTySe = $_code['reqTySe'];
                    $reqTyKey = array_search($reqTySe, array_column($aCodes, 'reqTySe'));
                    if($reqTyKey !== false) $aCodes[$reqTyKey]['aReqCl'][] = $_code;
                }
            }
            $_SESSION['aGSITMCodes'] = $aCodes;
            
        } else {
            $reqTySe = $_SESSION['aGSITMCodes'][$_reqTyKey]['reqTySe'];
            if($reqTySe) {
                $_data['getParam']['reqTySe'] = $reqTySe;
                $aAPIResult = $this->chatbot->getReserveAPI($_data);
                if($aAPIResult['code'] == "0000" && isset($aAPIResult['data'])) {
                    $_SESSION['aGSITMCodes'][$_reqTyKey]['aReqCl'] = $aAPIResult['data'];
                }
            }
        }        
    }
    
    // 데이터 양식, 결재라인
    public function getGSITMDataFormat($data) {
        global $g, $table, $TMPL;
        
        $aCheck = array_filter($this->apiAuthKey, function($val) {
            return preg_match("/Authorization:/", $val);
        });
        if(count($aCheck) == 0) {
            // JWT Token 설정
            $this->setGSITMJWTKey();
        }
        
        $_data = array();
        $_data['api_url'] = $this->apiHost."/sr/dataFormat";
        $_data['headers'] = $this->apiAuthKey;
        $_data['getParam'] = array();
        $_data['getParam']['reqTySe'] = $data['reqTySe'];
        $_data['getParam']['reqCl'] = $data['reqCl'];
        $_data['getParam']['servId'] = $data['servId'];        
        
        $_result = array();
        $aAPIResult = $this->chatbot->getReserveAPI($_data);
        if($aAPIResult['code'] == "0000" && isset($aAPIResult['data'])) {
            $aData = $aAPIResult['data'];
            $_SESSION['aGSITMTmlList'] = $aData['tmlList'];
            
            $systemList = $tmlList = "<option value=''>- 선택 -</option>";
            
            // 요청서비스 (servId)
            if(isset($aData['systemList'])) {
                $aSystemList = $aData['systemList'];                
                foreach($aSystemList as $aSystem) {                    
                    $systemList .="<option value='".$aSystem['servId']."' ".($aSystem['servId'] == $data['servId'] ? "selected" : "").">".$aSystem['servPathNm']."</option>";
                }
                $_result['systemList'] = $systemList;
            }
            
            // 템플릿 양식
            if(isset($aData['tmlList'])) {
                $aTmlList = $aData['tmlList'];
                foreach($aTmlList as $aTml) {
                    if($aTml['dfltAt'] == "Y") $_result['tmlCnt'] = $aTml['tmlCn'];
                    $tmlList .="<option value='".$aTml['tmlNo']."' ".($aTml['dfltAt'] == "Y" ? "selected" : "").">".$aTml['tmlTitlNm']."</option>";
                }
                $_result['tmlList'] = $tmlList;
            }
            
            // 결제 라인
            if(isset($aData['apprLines'])) {
                /*
                if(count($aData['apprLines']) == 0) {
                    $_apprLines = '[{"userNm": "권지은","apprTy": "A","titleNm": "팀원","posiNm": "매니저","deptId": "TR1T02","coId": "05","srOrd": 1,"deptNm": "SolutionLab","userId": "IT1451"},{"userNm": "박수훈","apprTy": "A","titleNm": "팀장","posiNm": "매니저","deptId": "TR1T02","coId": "05","srOrd": 11,"deptNm": "SolutionLab","userId": "IT0234"}]';
                    $aData['apprLines'] = json_decode($_apprLines, true);
                }
                */
                $apprLines = "";                
                $aApprLines = $aData['apprLines'];
                foreach($aApprLines as $idx=>$aAppr) {
                    $aAppr['apprTyNm'] = $aAppr['apprTy'] == "A" ? "결재" : ($aAppr['apprTy'] == "C" ? "합의" : "확인");
                    $aAppr['apprTyNm'] = $idx == 0 ? "작성자" : $aAppr['apprTyNm'];
                    $_apprInfo = json_encode($aAppr, JSON_UNESCAPED_UNICODE);
                    $apprLines .="<li>";
                    $apprLines .="  <div>";
                    $apprLines .="      <span>".$aAppr['userNm']."/".$aAppr['titleNm']." [".$aAppr['apprTyNm']."]</span>";
                    $apprLines .="      <span class='gsitm_apprLines dispnone'>".$_apprInfo."</span>";
                    $apprLines .="  </div>";                    
                    $apprLines .="</li>";
                }                
                $_result['apprLines'] = $apprLines;
            }
        }
        return $_result;
    }
    
    // 연동 API 인텐트 걸렸을 경우 (시작 지점)
    public function getGSITMFormRespond($data) {
        global $g, $table, $TMPL;
        
        $aGSITMMatch = array();
        
        if($data['api']) {
            $bot_msg = '요청 상세 내용을 입력해주세요.';
        } else {
            $bot_msg = '아래의 항목을 입력해주세요.';
            $TMPL['response'] = '<span>'.nl2br($bot_msg).'</span>';
        }
        
        $TMPL['gsitm_start'] = "gsitm_start";
        
        // 의도명으로 요청유형 위치 검색
        if(!isset($data['intentName']) || !$data['intentName']) {
            $data['intentName'] = "단순요청";            
        }
        $reqTyKey = array_search($data['intentName'], array_column($_SESSION['aGSITMCodes'], 'reqTySeNm'));
        $aGSITMMatch['reqTySe'] = $_SESSION['aGSITMCodes'][$reqTyKey]['reqTySe'];
        $aGSITMMatch['reqTySeNm'] = $_SESSION['aGSITMCodes'][$reqTyKey]['reqTySeNm'];
        
        $reqTySe_codes = "<option value=''>- 선택 -</option>";
        foreach($_SESSION['aGSITMCodes'] as $aReqTySe) {
            $selected = ($aGSITMMatch['reqTySe'] && $aReqTySe['reqTySe'] == $aGSITMMatch['reqTySe']) ? "selected" : "";
            $reqTySe_codes .="<option value='".$aReqTySe['reqTySe']."' ".$selected.">".$aReqTySe['reqTySeNm']."</option>";
        }
        $TMPL['gsitm_reqTySe_codes'] = $reqTySe_codes;
        
        // 요청 분류 API 선택
        if($reqTyKey !== false && $aGSITMMatch['reqTySe']) {
            // 요청 분류가 없다면 reqTySe값의 분류값 가져오기
            if(count($_SESSION['aGSITMCodes'][$reqTyKey]['aReqCl']) == 0) {
                $this->getGSITMTypeCode($reqTyKey);
            }
            $aReqCl = $_SESSION['aGSITMCodes'][$reqTyKey]['aReqCl'];
            
            $reqCl_codes = $selected = $entityDataFilter = "";
            
            // 문장에서 검출된 entityData 설정하기
            $aEntityDataFilter = array();
            if(isset($data['entityData']) && count($data['entityData']) > 0) {
                foreach ($data['entityData'] as $idx=>$aEntity){
                    //if($idx >= 1) break;
                    if(!in_array($aEntity[2], $aEntityDataFilter)) $aEntityDataFilter[] = $aEntity[2];
                    if(!in_array($aEntity[3], $aEntityDataFilter)) $aEntityDataFilter[] = $aEntity[3];
                }
                $entityDataFilter = implode("|", $aEntityDataFilter);
            }
            
            $matchCnt = 0;
            $reqCl_codes .="<option value=''>- 선택 -</option>";
            foreach($aReqCl as $aCl) {
                preg_match_all('/('.$entityDataFilter.')/iu',$aCl['reqClNm'],$match);                
                if(count($match[0]) > 0 && count($match[0]) > $matchCnt) {
                    $matchCnt = count($match[0]);
                    $aGSITMMatch['reqCl'] = $aCl['reqCl'];
                    $aGSITMMatch['reqClNm'] = $aCl['reqClNm'];
                    $aGSITMMatch['servId'] = $aCl['servId'];
                }
                $selected = $aGSITMMatch['reqCl'] == $aCl['reqCl'] ? "selected" : "";                
                $reqCl_codes .="<option value='".$aCl['reqCl']."' ".$selected." servId='".$aCl['servId']."'>".$aCl['reqClNm']."</option>";
            }
            $TMPL['gsitm_reqCl_codes'] = $reqCl_codes;            
            $TMPL['gsitm_servId'] = $aGSITMMatch['servId'];
            
            // 요청 양식, 결재라인 가져오기
            $aDataFormat = $this->getGSITMDataFormat($aGSITMMatch);
            $TMPL['gsitm_servId_codes'] = $aDataFormat['systemList'];
            $TMPL['gsitm_tmlNos'] = $aDataFormat['tmlList'];
            $TMPL['gsitm_reqCl_tml'] = $aDataFormat['tmlCnt'];
            $TMPL['gsitm_apprLinesStr'] = $aDataFormat['apprLines'];
            
            // 완료희망 날짜 미리 셋팅
            $finish_date = date("Y-m-d", strtotime(date("Y-m-d")." +1 weeks"));
            
            $finish_hours = $finish_minutes = "";
            for($i=1; $i<=24; $i++) {
                $_val = sprintf("%02d", $i);
                $_selected = date("H") == $_val ? "selected" : "";
                $finish_hours .="<option value='".$_val."' ".$_selected.">".$_val."</option>";
            }
            for($i=0; $i<=59; $i++) {
                $_val = sprintf("%02d", $i);
                $_selected = date("i") == $_val ? "selected" : "";
                $finish_minutes .="<option value='".$_val."' ".$_selected.">".$_val."</option>";
            }
            
            $TMPL['gsitm_ocrdate_class'] = $aGSITMMatch['reqTySe'] == "0223" ? "" : "ocrdate_none";
            $TMPL['gsitm_ocr_date'] = date("Y-m-d");
            
            $TMPL['gsitm_finish_date'] = $finish_date;
            $TMPL['gsitm_finish_hours'] = $finish_hours;
            $TMPL['gsitm_finish_minutes'] = $finish_minutes;
            
            // chatbot_token에 reqTySe, reqCl값 저장
            if($data['api']) {
                getDbDelete($table[$this->module.'token'], "bot='".$data['bot']."' and access_mod='gsitm_reqTySe' and expire < '".time()."'");
                
                $r_data = json_encode($aGSITMMatch, JSON_UNESCAPED_UNICODE);
                $expire = time()+(60*5); // 5분
                $aToken = getDbData($table[$this->module.'token'], "bot='".$data['bot']."' and access_mod='gsitm_reqTySe' and roomToken='".$data['roomToken']."'", "*");
                if($aToken['uid']) {
                    getDbUpdate($table[$this->module.'token'], "expire='".$expire."', r_data='".$r_data."'", "uid='".$aToken['uid']."'");
                } else {
                    $_QKEY = "bot, access_mod, access_token, roomToken, userId, expire, r_data";
                    $_QVAL = "'".$data['bot']."', 'gsitm_reqTySe', '', '".$data['roomToken']."', '".$data['userId']."', '".$expire."', '".$r_data."'";
                    getDbInsert($table[$this->module.'token'],$_QKEY,$_QVAL);
                }
            }
        }
        
        return $bot_msg;
    }
    
    // 채널(카톡) API 진입시 token에서 코드값 존재 여부 확인 및 요청 전송
    public function getGSITMCheckTokenSendResponse($data) {
        global $g, $table;
        
        $bot_msg = "";
        if($data['channel'] == 'kakao') {
            $aToken = getDbData($table[$this->module.'token'], "bot='".$data['bot']."' and access_mod='gsitm_reqTySe' and roomToken='".$data['roomToken']."'", "*");
            if($aToken['r_data']) {
                $userChat = array();
                $userChat['printType'] ='T';
                $userChat['chatType'] = $data['chatType'];
                $userChat['userId'] = $data['userId'];
                $userChat['content'] = $data['clean_input'];
                $userChat['input_mop'] = '';
                $userLastChat = $this->chatbot->addChatLog($userChat);
            
                $r_data = json_decode($aToken['r_data'], true);
                $r_data['servId'] = trim($r_data['servId']) ? trim($r_data['servId']) : $this->servId;
                
                $_data = array();
                $_data['vendor'] = $data['vendor'];
                $_data['bot'] = $data['bot'];
                
                $_data['servId'] = $r_data['servId'];
                $_data['reqTySe'] = $r_data['reqTySe'];
                $_data['reqCl'] = $r_data['reqCl'];
                $_data['srTitlNm'] = '[카카오톡 요청] : '.$r_data['reqClNm'] ? $r_data['reqClNm'] : $r_data['reqTySeNm'];
                $_data['srCn'] = trim($data['clean_input']);
                $_data['secuCn'] = "";
                
                $bot_msg = $this->getGSITMReqSend($_data);
                
                $botChat = array();
                $botChat['printType'] ='T';
                $botChat['content'] = array(array("text",$bot_msg));
                $botChat['last_chat'] = $userLastChat['last_chat'];
                $this->chatbot->addBotChatLog($botChat);
                
                getDbDelete($table[$this->module.'token'], "uid='".$aToken['uid']."'");
            }
        }
        return $bot_msg;
    }
    
    // 요청 실전송
    public function getGSITMReqSend($data) {
        $result = array();
        
        // JWT Token 설정
        $this->setGSITMJWTKey();
        
        if($data['actionMode'] == "gsitm_confirm") {
            $data['api_url'] = $this->apiHost."/sr/service";
            
            if(!$data['postParam']['coId']) $data['postParam']['coId'] = $this->coId;
            if(!$data['postParam']['reqCod']) $data['postParam']['reqCod'] = $this->reqCod;
        } else {
            $data['api_url'] = $this->apiHost."/approval/modify";
        }
        
        $data['headers'] = $this->apiAuthKey;        
        $data['method'] = "post";
        $data['contentType'] = "formData";
        $apiResult = $this->chatbot->getReserveAPI($data);
        if($apiResult['code'] == "0000" || $apiResult['code'] == "9999") {
            $result['result'] = true;
            $result['msg'] = $apiResult['message'];
        } else {
            $result['result'] = false;
            $result['msg'] = $apiResult['message'] ? $apiResult['message'] : "요청에 실패하였습니다.";
        }        
        return $result;
    }
    
    // 서비스 요청 현황
    public function getGSITMServiceList($data) {
        global $g, $table, $TMPL;
        
        $aAPIPath = array();
        $aAPIPath['service'] = $this->apiHost."/sr/services"; // 요청 목록
        
        // 결재문서함
        $aAPIPath['approval_write'] = $this->apiHost."/approval/write-docs"; // 작성한 문서함
        $aAPIPath['approval_approval'] = $this->apiHost."/approval/approval-docs"; // 결재할 문서함
        $aAPIPath['approval_approved'] = $this->apiHost."/approval/approved-docs"; // 결재한 문서함
        $aAPIPath['approval_temp'] = $this->apiHost."/approval/temporaryList"; // 임시저장함
        
        // 회람문서함
        $aAPIPath['circular_send'] = $this->apiHost."/approval-box/circular/sendList"; // 회람 전송함
        $aAPIPath['circular_receive'] = $this->apiHost."/approval-box/circular/receiveList"; // 회람 수신함
        
        // 공지사항
        $aAPIPath['notice'] = $this->apiHost."/bbs";
        
        // 결재문서함 구분, 회람 문서함 구분
        if($data['gsitm_listtype'] == "approval" || $data['gsitm_listtype'] == "circular") {            
            $aEntityDataFilter = array();
            if(isset($data['entityData']) && count($data['entityData']) > 0) {
                foreach ($data['entityData'] as $idx=>$aEntity){
                    if(!in_array($aEntity[3], $aEntityDataFilter)) $aEntityDataFilter[] = $aEntity[3];
                }
            }
            if($data['gsitm_listtype'] == "approval") {
                if(in_array("작성한문서", $aEntityDataFilter)) {
                    $data['gsitm_listtype'] = "approval_write";
                    $data['gsitm_boxname'] = "작성한 문서";
                } else if(in_array("결재할문서", $aEntityDataFilter)) {
                    $data['gsitm_listtype'] = "approval_approval";
                    $data['gsitm_boxname'] = "결재할 문서";
                } else if(in_array("결재한문서", $aEntityDataFilter)) {
                    $data['gsitm_listtype'] = "approval_approved";
                    $data['gsitm_boxname'] = "결재한 문서";
                } else if(in_array("임시저장", $aEntityDataFilter)) {
                    $data['gsitm_listtype'] = "approval_temp";
                    $data['gsitm_boxname'] = "임시 저장";
                }
                // 결재함 셀렉트 박스
                $TMPL['gsitm_listtype_select'] = "";
                $TMPL['gsitm_listtype_select'] .="<option value='approval_write' ".($data['gsitm_listtype']=="approval_write" ? "selected" : "").">작성한 문서</option>";
                $TMPL['gsitm_listtype_select'] .="<option value='approval_approval' ".($data['gsitm_listtype']=="approval_approval" ? "selected" : "").">결재할 문서</option>";
                $TMPL['gsitm_listtype_select'] .="<option value='approval_approved' ".($data['gsitm_listtype']=="approval_approved" ? "selected" : "").">결재한 문서</option>";
                $TMPL['gsitm_listtype_select'] .="<option value='approval_temp' ".($data['gsitm_listtype']=="approval_temp" ? "selected" : "").">임시저장 문서</option>";
            }
            if($data['gsitm_listtype'] == "circular") {
                if(in_array("전송문서", $aEntityDataFilter)) {
                    $data['gsitm_listtype'] = "circular_send";
                    $data['gsitm_boxname'] = "회람 전송 문서";
                } else if(in_array("수신문서", $aEntityDataFilter)) {
                    $data['gsitm_listtype'] = "circular_receive";
                    $data['gsitm_boxname'] = "회람 수신 문서";
                }
                // 회람함 셀렉트 박스
                $TMPL['gsitm_listtype_select'] = "";
                $TMPL['gsitm_listtype_select'] .="<option value='circular_send' ".($data['gsitm_listtype']=="circular_send" ? "selected" : "").">회람 전송 문서</option>";
                $TMPL['gsitm_listtype_select'] .="<option value='circular_receive' ".($data['gsitm_listtype']=="circular_receive" ? "selected" : "").">회람 수신 문서</option>";
            }
        }
        
        
        $_apiData = array();
        
        // JWT Token 설정
        $this->setGSITMJWTKey();        
        $_apiData['headers'] = $this->apiAuthKey;
        $_apiData['api_url'] = $aAPIPath[$data['gsitm_listtype']];
        
        // 챗봇엔진에서 호출되었을 때
        if(!isset($data['getParam'])) {
            // 검색기간
            $fromDate = date("Y-m-d", strtotime(date("Ymd")." -1 month"));
            $toDate = date("Y-m-d");
            
            $TMPL['gsitm_fromDate'] = $fromDate;
            $TMPL['gsitm_toDate'] = $toDate;
            
            // 요청유형
            $reqTySe_codes = "";
            foreach($_SESSION['aGSITMCodes'] as $aReqTySe) {
                $reqTySe_codes .="<option value='".$aReqTySe['reqTySe']."'>".$aReqTySe['reqTySeNm']."</option>";
            }
            $TMPL['gsitm_reqTySe_codes'] = $reqTySe_codes;
        
            // 서비스 요청 목록
            if($data['gsitm_listtype'] == "service") {
                // 요청분류
                $reqCl_codes = "";
                foreach($_SESSION['aGSITMCodes'] as $aReqTySe) {
                    foreach($aReqTySe['aReqCl'] as $aCl) {
                        $reqCl_codes .="<option value='".$aCl['reqCl']."' servId='".$aCl['servId']."'>".$aCl['reqClNm']."</option>";
                    }
                }
                $TMPL['gsitm_reqCl_codes'] = $reqCl_codes;
                
                $_apiData['getParam'] = array();
                $_apiData['getParam']['chkActivity'] = "req,appl";
                $_apiData['getParam']['fromDate'] = str_replace("-", "", $fromDate);
                $_apiData['getParam']['toDate'] = str_replace("-", "", $toDate);
            }
            
            // 결재문서함
            if(preg_match("/^approval_/", $data['gsitm_listtype'])) {
                $TMPL['gsitm_searchDateUse'] = $data['gsitm_listtype'] != "approval_approval" ? "checked" : "";
                
                $_apiData['getParam'] = array();
                $_apiData['getParam']['searchAppStatusCod'] = "1";
                $_apiData['getParam']['searchDateUse'] = $data['gsitm_listtype'] != "approval_approval" ? true : false;
                $_apiData['getParam']['fromDate'] = str_replace("-", "", $fromDate);
                $_apiData['getParam']['toDate'] = str_replace("-", "", $toDate);
                $_apiData['getParam']['searchType'] = "0";
            }
            
            // 회람문서함
            if(preg_match("/^circular_/", $data['gsitm_listtype'])) {                
                $_apiData['getParam'] = array();
                $_apiData['getParam']['searchAppStatusCod'] = "1";
                $_apiData['getParam']['fromDate'] = str_replace("-", "", $fromDate);
                $_apiData['getParam']['toDate'] = str_replace("-", "", $toDate);
                $_apiData['getParam']['searchType'] = "0";
            }
            
            // 서비스 요청 목록
            if($data['gsitm_listtype'] == "notice") {
                $_apiData['getParam'] = array();
                $_apiData['getParam']['page'] = 1;
            }
            
        } else {
            $_apiData['getParam'] = $data['getParam'];
        }
        $aAPIResult = $this->chatbot->getReserveAPI($_apiData);
        
        if($data['gsitm_listtype'] != "notice" && $aAPIResult['code'] != "0000") {
            $bot_msg = '데이터를 가져오지 못했습니다.';
        } else {
            if($data['gsitm_listtype'] == "service") {
                $bot_msg = '요청 현황 목록입니다.';
            } else if(preg_match("/^approval_/", $data['gsitm_listtype'])) {
                $bot_msg = '결재문서함 > '.$data['gsitm_boxname'].' 목록입니다.<br>원하시는 문서 유형을 선택하여 확인해보세요.';
            } else if(preg_match("/^circular_/", $data['gsitm_listtype'])) {
                $bot_msg = '회람문서함 > '.$data['gsitm_boxname'].' 목록입니다.<br>원하시는 문서 유형을 선택하여 확인해보세요.';
            } if($data['gsitm_listtype'] == "notice") {
                $noticePage = $aAPIResult[0]['pagingDto']['currentPage'];
                $noticeTotalPage = $aAPIResult[0]['pagingDto']['totalPages'];
                $bot_msg = '공지사항 목록입니다.<br>원하시는 공지를 확인해보세요.';
            }

            $aAPIData = $data['gsitm_listtype'] == "notice" ? $aAPIResult : $aAPIResult['data'];
            $_SESSION['aGSITMSericeList'] = array_chunk($aAPIData, 5);            

            $_data = array();
            $_data['gsitm_listtype'] = $data['gsitm_listtype'];
            $_data['list_page'] = 0;
            $_list = $this->getGSITMServiceListRows($_data);
            if($data['gsitm_form_start']) {
                $TMPL['gsitm_req_list'] = $_list;
                $TMPL['gsitm_listtype'] = $_data['gsitm_listtype'];
                $TMPL['gsitm_pageall'] = count($_SESSION['aGSITMSericeList']);
                $TMPL['gsitm_page'] = 1;
                $TMPL['gsitm_ntpageall'] = $noticeTotalPage;
                $TMPL['gsitm_ntpage'] = $noticePage;
                $TMPL['gsitm_more_class'] = count($_SESSION['aGSITMSericeList']) < 2 ? "dispnone" : "";
                $TMPL['response'] = '<span>'.nl2br($bot_msg).'</span>';                    
            } else {
                $_result = array();
                $_result['gsitm_req_list'] = $_list;
                $_result['gsitm_listtype'] = $_data['gsitm_listtype'];
                $_result['gsitm_pageall'] = count($_SESSION['aGSITMSericeList']);
                $_result['gsitm_page'] = 1;
                $_result['gsitm_ntpageall'] = $noticeTotalPage;
                $_result['gsitm_ntpage'] = $noticePage;
            }
        }
        return $data['gsitm_form_start'] ? $bot_msg : $_result;
    }
    
    public function getGSITMServiceListRows($data) {
        $nPage = ($data['list_page'] == "" || $data['list_page'] < 1) ? 0 : $data['list_page'];
        $_list = $_SESSION['aGSITMSericeList'][$nPage];
        $html = "";
        
        // 서비스요청
        if($data['gsitm_listtype'] == "service") {
            foreach($_list as $index => $aItem) {
                if(preg_match("/종료/", $aItem['stateName'])) $stateClass = "completed";
                else if($aItem['stateName'] == "요청") $stateClass = "requested";
                else $stateClass = "handled";
                
                $html .="<li>";
                $html .="    <a class='cardbox cardlink' data-listtype='".$data['gsitm_listtype']."' data-idx='".$aItem['srId']."'>";
                $html .="        <div class='statewrap'>";
                $html .="           <div class='state ".$stateClass."'>".$aItem['stateName']."</div>";
                $html .="           <div class='state'>".$aItem['reqTyNm']."</div>";
                $html .="        </div>";
                $html .="        <div class='title'>".$aItem['srTitlNm']."</div>";
                $html .="        <div class='date'>";
                $html .="            <span>".$aItem['reqDt']."</span>";
                $html .="            <span>".$aItem['srId']."</span>";
                $html .="        </div>";
                $html .="    </a>";
                $html .="</li>";
            }
        }
        // 결재문서
        if(preg_match("/^approval_/", $data['gsitm_listtype'])) {
            foreach($_list as $index => $aItem) {
                $_status = isset($aItem['apprStatusCod']) ? $aItem['apprStatusCod'] : $aItem['approvalStatus'];
                
                if(preg_match("/승인/", $_status)) $stateClass = "completed";
                else $stateClass = "handled";
                
                $html .="<li>";
                $html .="    <a class='cardbox cardlink' data-listtype='".$data['gsitm_listtype']."' data-coid='".$aItem['coId']."' data-empid='".$aItem['createEmpId']."' data-idx='".$aItem['srId']."' data-apprdetailid='".$aItem['apprDetailId']."'>";
                $html .="        <div class='statewrap'>";
                $html .="           <div class='state ".$stateClass."'>".$_status."</div>";
                $html .="           <div class='state'>".$aItem['reqTyNm']."</div>";
                $html .="        </div>";
                $html .="        <div class='title'>".$aItem['srTitlNm']."</div>";
                $html .="        <div class='date'>";
                $html .="            <span>".$aItem['qusrNm']."</span>";
                $html .="            <span>".$aItem['reqDt']."</span>";                
                $html .="        </div>";
                $html .="    </a>";
                $html .="</li>";
            }
        }
        // 회람문서
        if(preg_match("/^circular_/", $data['gsitm_listtype'])) {
            foreach($_list as $index => $aItem) {
                if(preg_match("/승인/", $aItem['apprStatusCod'])) $stateClass = "completed";
                else $stateClass = "handled";
                
                $html .="<li>";
                $html .="    <a class='cardbox cardlink' data-listtype='".$data['gsitm_listtype']."' data-coid='".$aItem['coId']."' data-empid='".$aItem['createEmpId']."' data-idx='".$aItem['srId']."'>";
                $html .="        <div class='statewrap'>";
                $html .="           <div class='state ".$stateClass."'>".$aItem['apprStatusCod']."</div>";
                $html .="           <div class='state'>".$aItem['reqTyNm']."</div>";
                $html .="        </div>";
                $html .="        <div class='title'>".$aItem['srTitlNm']."</div>";
                $html .="        <div class='date'>";
                $html .="            <span>".$aItem['qusrNm']."</span>";
                $html .="            <span>".$aItem['reqDt']."</span>";                
                $html .="        </div>";
                $html .="    </a>";
                $html .="</li>";
            }
        }
        // 공지사항
        if($data['gsitm_listtype'] == "notice") {
            foreach($_list as $index => $aItem) {                
                $html .="<li>";
                $html .="    <a class='cardbox cardlink' data-listtype='".$data['gsitm_listtype']."' data-bbsid='".$aItem['bbsId']."' data-idx='".$aItem['nttId']."'>";
                $html .="        <div class='title'>".$aItem['nttSj']."</div>";
                $html .="        <div class='date'>";
                $html .="            <span>".$aItem['ntceBgnde']."</span>";
                $html .="            <span>조회수 : ".$aItem['rdcnt']."</span>";
                $html .="        </div>";
                $html .="    </a>";
                $html .="</li>";
            }
        }
        return $html;
    }
    
    // 상세 보기 페이지 로딩
    public function getGSITMServiceViewPage($data) {        
        // JWT Token 설정
        $this->setGSITMJWTKey();
        
        $_data = array();
            
        if($data['gsitm_listtype'] == "service") {
            $_data['api_url'] = $this->apiHost."/sr/service/".$data['idx'];
        } else if($data['gsitm_listtype'] == "notice") {
            $_data['api_url'] = $this->apiHost."/bbs/".$data['idx'];
            
            $_data['getParam'] = array();
            $_data['getParam']['bbsId'] = $data['bbsId'];
        } else if(preg_match("/^approval_/", $data['gsitm_listtype'])) {
            $_data['api_url'] = $this->apiHost."/approval/doc/".$data['idx'];
            
            $_data['getParam'] = array();
            $_data['getParam']['createCoId'] = $data['createCoId'];
            $_data['getParam']['createEmpId'] = $data['createEmpId'];
        }
        $_data['headers'] = $this->apiAuthKey;
        $aAPIResult = $this->chatbot->getReserveAPI($_data);
        
        $html = "";
        // 공지사항 보기
        if($data['gsitm_listtype'] == "notice") {
            $aNotice = $aAPIResult[0];
            
            $html .="<div class='pop_header'>";
            $html .="    <div class='doc_title'>".$aNotice['nttSj']."</div>";
            $html .="</div>";
            $html .="<div class='pop_container'>";
            $html .="    <div class='pop_content'>";
            $html .="       <div class='section'>";
            $html .="           <div class='itemwrap'>";
            $html .="               <div class='input_area no_label'>";
            $html .="                   <label class='input_label'>작성자</label>";
            $html .="                   <div>".$aNotice['ntcrNm']."</div>";
            $html .="               </div>";
            $html .="               <div class='input_area no_label'>";
            $html .="                   <label class='input_label'>조회수</label>";
            $html .="                   <div>".$aNotice['rdcnt']."</div>";
            $html .="               </div>";
            $html .="           </div>";
            $html .="           <div class='itemwrap'>";
            $html .="              <div class='input_area no_label'>";
            $html .="                   <label class='input_label'>내용</label>";
            $html .="                   <div>".$aNotice['nttCn']."</div>";
            $html .="               </div>";
            $html .="           </div>";
            $html .="           <div class='itemwrap'>";
            $html .="               <div class='input_area no_label'>";
            $html .="                   <label class='input_label'>게시 기간</label>";
            $html .="                   <div>".$aNotice['ntceBgnde']." ~ ".$aNotice['ntceEndde']."</div>";
            $html .="               </div>";
            $html .="           </div>";
            $html .="       <div class='section mt30'>";
            $html .="           <h1>파일첨부목록</h1>";
            $html .="           <div class='itemwrap'>";
            $html .="               <ul class='ul_files'>";
            /*
            $html .="                   <li>";
            $html .="                       <div class='filename'>sdfsdfdsaf.png</div>";
            $html .="                       <button type='button'>다운로드</button>";
            $html .="                   </li>";
            */
            $html .="               </ul>";
            $html .="           </div>";
            $html .="       </div>";
            $html .="   </div>";
            $html .="</div>";
            
        }
        
        if($data['gsitm_listtype'] != "notice" && $aAPIResult['code'] == "0000" && isset($aAPIResult['data'])) {
            // 서비스 요청 보기
            if($data['gsitm_listtype'] == "service") {
                $aBaseInfo = $aAPIResult['data']['baseInfo'];
                
                if(preg_match("/종료/", $aBaseInfo['hdlStsNm'])) $stateClass = "completed";
                else if($aBaseInfo['hdlStsNm'] == "요청") $stateClass = "requested";
                else $stateClass = "handled";
                
                $html .="<div class='pop_header'>";
                $html .="    <div class='des'>";
                $html .="        <span>".$aBaseInfo['srId']."</span>";
                $html .="        <span class='state ".$stateClass."'>".$aBaseInfo['hdlStsNm']."</span>";
                $html .="    </div>";
                $html .="    <div class='doc_title'>".$aBaseInfo['srTitlNm']."</div>";
                $html .="</div>";
                $html .="<div class='pop_container'>";
                $html .="    <div class='pop_content'>";
                /*
                $html .="        <ul class='approvallines mb20'>";
                $html .="            <li>";
                $html .="                <div>";
                $html .="                    <span>오범석</span>";
                $html .="                    <span class='state'>[<em>승인</em>]</span>";
                $html .="                </div>";
                $html .="            </li>";
                $html .="        </ul>";
                */                
                $html .="       <div class='section'>";
                $html .="           <h1>요청내용</h1>";
                $html .="           <div class='itemwrap'>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>접수자</label>";
                $html .="                   <div>".$aBaseInfo['eusrNm']." ".$aBaseInfo['eusrPosiNm']."</div>";
                $html .="               </div>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>처리자</label>";
                $html .="                   <div>".$aBaseInfo['hdlNm']." ".$aBaseInfo['hdlPosiNm']."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="           <div class='itemwrap'>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>요청일</label>";
                $html .="                   <div>".$aBaseInfo['reqDt']."</div>";
                $html .="               </div>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>완료일</label>";
                $html .="                   <div>".$aBaseInfo['cmplDt']."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="           <div class='itemwrap'>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>완료희망일시</label>";
                $html .="                   <div>".$aBaseInfo['chgCmplHopeDtStr']."</div>";
                $html .="               </div>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>최종합의일시 </label>";
                $html .="                   <div>".$aBaseInfo['chgCmplAgreDtStr']."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="           <div class='itemwrap'>";
                $html .="              <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>요청서비스 </label>";
                $html .="                   <div>".$aBaseInfo['servPathNm']."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="           <div class='itemwrap'>";
                $html .="              <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>내용</label>";
                $html .="                   <div>".nl2br($aBaseInfo['srCn'])."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="           <div class='itemwrap'>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>보안 내용</label>";
                $html .="                   <div>".nl2br($aBaseInfo['secuCn'])."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="           <div class='itemwrap'>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>고객요청 답변내용</label>";
                $html .="                  <div>".nl2br($aBaseInfo['ntcCn'])."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="       </div>";
                $html .="       <div class='section mt30'>";
                $html .="           <h1>고객요청</h1>";
                $html .="           <div class='itemwrap'>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>고객요청 답변내용</label>";
                $html .="                  <div>".nl2br($aBaseInfo['ntcCn'])."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="       </div>";
                $html .="       <div class='section mt30'>";
                $html .="           <h1>반려의견</h1>";
                $html .="           <div class='itemwrap'>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>반려의견</label>";
                $html .="                  <div>".nl2br($aBaseInfo['ntcCn'])."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="       </div>";
                $html .="       <div class='section mt30'>";
                $html .="           <h1>파일첨부목록</h1>";
                $html .="           <div class='itemwrap'>";
                $html .="               <ul class='ul_files'>";
                /*
                $html .="                   <li>";
                $html .="                       <div class='filename'>sdfsdfdsaf.png</div>";
                $html .="                       <button type='button'>다운로드</button>";
                $html .="                   </li>";
                */
                $html .="               </ul>";
                $html .="           </div>";
                $html .="       </div>";
                $html .="   </div>";
                $html .="</div>";
            }
            
            // 결재문서 보기
            if(preg_match("/^approval_/", $data['gsitm_listtype'])) {
                $apprDto = $aAPIResult['data']['apprDto'];
                $apprLines = $aAPIResult['data']['apprLines'];
                $srInfo = $aAPIResult['data']['srInfo'];                
                
                // 자신의 결재정보
                $myApprInfo = array();
                foreach($apprLines as $idx=>$line) {
                    // 사용자 ID가 자신의 ID이고 결재상태가 2(진행중)일 때
                    if($line['userId'] == strtoupper($this->userId) && $line['admitStatus'] == 2) {
                        $myApprInfo = $line;
                        break;
                    }
                }
                
                // 문서종류 설정
                if($data['gsitm_listtype_org'] == "approval_approval") $apprFlag = "A";
                else if($data['gsitm_listtype_org'] == "approval_approved") $apprFlag = "B";
                else if($data['gsitm_listtype_org'] == "approval_write") $apprFlag = "C";
                else if($data['gsitm_listtype_org'] == "approval_temp") $apprFlag = "D";
                
                // srInfo 값 세션 저장
                $_SESSION['srinfo'] = $srInfo;
                $_SESSION['srinfo']['apprFlag'] = $apprFlag;
                $_SESSION['srinfo']['createEmpId'] = $srInfo['createEmpId'] ? $srInfo['createEmpId'] : $apprDto['createEmpId'];
                $_SESSION['srinfo']['apprDetailId'] = $srInfo['apprDetailId'] ? $srInfo['apprDetailId'] : $myApprInfo['apprDetailId'];
                $_SESSION['srinfo']['apprCoId'] = $srInfo['apprCoId'] ? $srInfo['apprCoId'] : $myApprInfo['coId'];
                $_SESSION['srinfo']['apprEmpId'] = $srInfo['apprEmpId'] ? $srInfo['apprEmpId'] : $myApprInfo['userId'];
                $_SESSION['srinfo']['apprTy'] = $srInfo['apprTy'] ? $srInfo['apprTy'] : $myApprInfo['apprTy'];
                
                // 반려 상태 설정
                $srInfo['approvalStatus'] = ($srInfo['approvalStatus'] == 2 && $srInfo['returnCnt'] > 0) ? 4 : $srInfo['approvalStatus'];
                
                $apprStatus = $this->aApprovalStatus[$srInfo['approvalStatus']];
                if($srInfo['approvalStatus'] == 5) $stateClass = "completed";
                else $stateClass = "handled";
                
                // 문서번호
                $docId = $apprDto['createCoId']."-".$apprDto['createEmpId']."-".$srInfo['srId'];
                
                $html .="<div class='pop_header'>";
                $html .="    <input type='hidden' name='appr_listtype' value='".$data['gsitm_listtype_org']."'>";
                $html .="    <input type='hidden' name='appr_srId' value='".$srInfo['srId']."'>";
                $html .="    <input type='hidden' name='appr_createCoId' value='".$apprDto['createCoId']."'>";
                $html .="    <input type='hidden' name='appr_createEmpId' value='".$apprDto['createEmpId']."'>";
                $html .="    <div class='des'>";
                $html .="        <span>".$docId."</span>";
                if($data['gsitm_listtype'] != "approval_temp") {
                    $html .="    <span class='state ".$stateClass."'>".$apprStatus."</span>";
                }
                $html .="    </div>";
                $html .="    <div class='doc_title'>".$srInfo['srTitlNm']."</div>";
                $html .="</div>";
                $html .="<div class='pop_container'>";
                $html .="    <div class='pop_content'>";
                $html .="        <ul class='approvallines mb20'>";
                foreach($apprLines as $aAppr) {
                    $html .="        <li>";
                    $html .="            <div>";
                    $html .="                <span>".$aAppr['userNm']."</span>";
                    $html .="                <span class='state'>[<em>".$aAppr['admitStatusNm']."</em>]</span>";
                    $html .="            </div>";
                    $html .="        </li>";
                }
                $html .="        </ul>";
                $html .="       <div class='section'>";
                $html .="           <h1>요청내용</h1>";
                $html .="           <div class='itemwrap'>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>요청일</label>";
                $html .="                   <div>".$srInfo['reqDt']."</div>";
                $html .="               </div>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>완료일</label>";
                $html .="                   <div>".$srInfo['cmplDt']."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="           <div class='itemwrap'>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>완료희망일시</label>";
                $html .="                   <div>".date("Y-m-d H:i", strtotime($srInfo['chgCmplHopeDt']))."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="           <div class='itemwrap'>";
                $html .="              <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>요청유형</label>";
                $html .="                   <div>".$srInfo['reqTyNm']."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="           <div class='itemwrap'>";
                $html .="              <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>요청분류</label>";
                $html .="                   <div>".$srInfo['reqClNm']."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="           <div class='itemwrap'>";
                $html .="              <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>요청서비스</label>";
                $html .="                   <div>".$srInfo['servPathNm']."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="           <div class='itemwrap'>";
                $html .="              <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>내용</label>";
                $html .="                   <div>".nl2br($srInfo['srCn'])."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="           <div class='itemwrap'>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>보안 내용</label>";
                $html .="                   <div>".nl2br($srInfo['secuCn'])."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="           <div class='itemwrap'>";
                $html .="               <div class='input_area no_label'>";
                $html .="                   <label class='input_label'>의견</label>";
                $html .="                  <div>".nl2br($srInfo['ntcCn'])."</div>";
                $html .="               </div>";
                $html .="           </div>";
                $html .="       </div>";
                $html .="       <div class='section mt30'>";
                $html .="           <h1>파일첨부목록</h1>";
                $html .="           <div class='itemwrap'>";
                $html .="               <ul class='ul_files'>";
                /*
                $html .="                   <li>";
                $html .="                       <div class='filename'>sdfsdfdsaf.png</div>";
                $html .="                       <button type='button'>다운로드</button>";
                $html .="                   </li>";
                */
                $html .="               </ul>";
                $html .="           </div>";
                $html .="       </div>";
                $html .="       <div class='btn_wrap mt30'>";
                
                if($data['gsitm_listtype_org'] != "approval_temp") {
                    $html .="       <button type='button' class='btn btn_appr_funcmode' data-mode='circular' onclick='getApprViewFunc($(this))'>회람</button>";
                }
                
                // 결재 기능 버튼
                if(preg_match("/^approval_/", $data['gsitm_listtype_org'])) {
                    // 결재할 문서 버튼
                    if($data['gsitm_listtype_org'] == "approval_approval") {
                        if($srInfo['approvalStatus'] == 2) {
                            $html .="<button type='button' class='btn btn_appr_funcmode' data-mode='confirm' onclick='getApprViewFunc($(this))'>승인</button>";
                            $html .="<button type='button' class='btn btn_appr_funcmode' data-mode='return' onclick='getApprViewFunc($(this))'>반려</button>";
                            $html .="<button type='button' class='btn btn_appr_funcmode' data-mode='reject' onclick='getApprViewFunc($(this))'>기각</button>";                            
                        }
                        //$html .="    <button type='button' class='btn btn_appr_funcmode' data-mode='modify' onclick='getApprViewFunc($(this))'>수정</button>";
                    }
                }
                if($data['gsitm_listtype_org'] == "approval_temp") {
                    //$html .="        <button type='button' class='btn btn_appr_funcmode' data-mode='modify' onclick='getApprViewFunc($(this))'>수정</button>";
                    $html .="        <button type='button' class='btn btn_appr_funcmode' data-mode='temp_delete' onclick='getApprViewFunc($(this))'>삭제</button>";
                }
                $html .="       </div>";
                $html .="   </div>";
                $html .="</div>";
            }
        }
        return $html;
    }
    
    // 회람 대상자 검색
    public function getCircularSearchMember($searchWord) {
        // JWT Token 설정
        $this->setGSITMJWTKey();
        
        $_data = array();
        $_data['api_url'] = $this->apiHost."/users/search";
        $_data['getParam'] = array("userName"=>$searchWord);
        $_data['headers'] = $this->apiAuthKey;        
        $aAPIResult = $this->chatbot->getReserveAPI($_data);
        $html = "";
        if($aAPIResult['code'] == "0000" && isset($aAPIResult['data'])) {
            $aAPIData = $aAPIResult['data'];
            foreach($aAPIData as $aItem) {
                $html .="<tr data-userid='".$aItem['userId']."' data-usernm='".$aItem['userNm']."' data-coid='".$aItem['coId']."' data-deptid='".$aItem['deptId']."' data-deptnm='".$aItem['deptNm']."' data-posicod='".$aItem['posiCod']."' data-posinm='".$aItem['posiNm']."' data-titlenm='".$aItem['titleNm']."' data-apprty='' data-apprtynm=''>";
                $html .="   <td>".$aItem['userNm']."</td>";
                $html .="   <td>".$aItem['titleNm']."</td>";
                $html .="   <td class='nowrap'>".$aItem['deptNm']."</td>";
                $html .="</tr>";
            }
        }
        return $html;
    }
    
    // 회람 전송 폼
    public function getCircularForm() {
        $html = '
            <div class="pop_header">
                <div class="doc_title">회람사용자 선택</div>
            </div>
            <div class="pop_container">
                <div class="pop_content bot_form">
                    <div class="circular_comment">
                        <div class="label">의견</div>
                        <div class="comment">
                            <textarea name="gsitm_circular_desc"></textarea>
                        </div>
                    </div>
                    
                    <fieldset class="form_field mt20">
                        <div class="input_area no_label">
                            <label class="input_label">사용자 검색</label>
                        </div>
                        <form id="frmSearchMember" onsubmit="return getCircularSearchMember()">
                            <div class="input_area no_label input_text flex mt5">
                                <div>
                                    <input type="text" name="gsitm_circular_find" maxlength="50">
                                </div>
                                <button type="submit" class="btn_search_right">이름검색</button>
                            </div>
                        </form>
                
                        <div class="tableWrap mt10 scroll">
                            <div class="tableHeaderWrap">
                                <table class="tableHeader">
                                    <colgroup>
                                        <col width="30%" /><col width="25%" /><col width="45%" />
                                    </colgroup>
                                    <tr>
                                        <th>성명</th>
                                        <th>직책</th>
                                        <th>부서</th>
                                    </tr>
                                </table>
                            </div>
                            <div class="tableListWrap">
                                <table class="tableList circularMemberList">
                                    <colgroup>
                                        <col width="30%" /><col width="25%" /><col width="45%" />
                                    </colgroup>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
            
                        <div class="input_area no_label mt15">
                            <button type="button" class="btn_circular_targetadd btn_black w100">결재목록 추가 ▼</button>
                        </div>
            
                        <div class="tableWrap mt15">
                            <div class="tableHeaderWrap">
                                <table class="tableHeader">
                                    <colgroup>
                                        <col width="25%" /><col width="25%" /><col width="40%" /><col width="10%" />
                                    </colgroup>
                                    <tr>
                                        <th>성명</th>
                                        <th>직책</th>
                                        <th>부서</th>
                                        <th></th>
                                    </tr>
                                </table>
                            </div>
                            <div class="tableListWrap">
                                <table class="tableList circularTargetList">
                                    <colgroup>
                                        <col width="25%" /><col width="25%" /><col width="40%" /><col width="10%" />
                                    </colgroup>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
            
                        <div class="input_area no_label acenter mt10">
                            <button type="button" class="btn_circular_submit btn_black w30">확인</button>
                        </div>
                    </fieldset>
                </div>
            </div>';
        return $html;        
    }
    
    public function getApprLineForm($data) {
        $_prevApprLine = "";
        if(is_array($data)) {
            foreach($data as $idx=>$aAppr) {
                $_aAppr = json_decode(stripslashes($aAppr), true);
                if($_aAppr['apprTyNm'] == "작성자") continue;
                
                $_prevApprLine .="<tr data-userid='".$_aAppr['userId']."' data-usernm='".$_aAppr['userNm']."' data-coid='".$_aAppr['coId']."' data-deptid='".$_aAppr['deptId']."' data-deptnm='".$_aAppr['deptNm']."' data-posicod='".$_aAppr['posiCod']."' data-posinm='".$_aAppr['posiNm']."' data-titlenm='".$_aAppr['titleNm']."' data-apprty='".$_aAppr['apprTy']."' data-apprtynm='".$_aAppr['apprTyNm']."'>";
                $_prevApprLine .="  <td>".$_aAppr['userNm']."</td>";
                $_prevApprLine .="  <td>".$_aAppr['titleNm']."</td>";
                $_prevApprLine .="  <td class='nowrap'>".$_aAppr['deptNm']."</td>";
                $_prevApprLine .="  <td>".$_aAppr['apprTyNm']."</td>";
                $_prevApprLine .="</tr>";
            }
        }
        
        $html ='
            <div class="pop_header">
                <div class="doc_title">결재라인 변경</div>
            </div>
            <div class="pop_container">
                <div class="pop_content bot_form">
                    <fieldset class="form_field">
                        <div class="input_area no_label">
                            <label class="input_label">사용자 검색</label>
                        </div>
                        <form id="frmSearchMember" onsubmit="return getCircularSearchMember()">
                            <div class="input_area no_label input_text flex mt5">
                                <div>
                                    <input type="text" name="gsitm_circular_find" maxlength="50">
                                </div>
                                <button type="submit" class="btn_search_right">이름검색</button>
                            </div>
                        </form>
                        <div class="tableWrap mt10 scroll">
                            <div class="tableHeaderWrap">
                                <table class="tableHeader">
                                    <colgroup>
                                        <col width="30%" /><col width="25%" /><col width="45%" />
                                    </colgroup>
                                    <tr>
                                        <th>성명</th>
                                        <th>직책</th>
                                        <th>부서</th>
                                    </tr>
                                </table>
                            </div>
                            <div class="tableListWrap">
                                <table class="tableList circularMemberList">
                                    <colgroup>
                                        <col width="30%" /><col width="25%" /><col width="45%" />
                                    </colgroup>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="input_area no_label acenter mt15">
                            <button type="button" class="btn_apprline_targetadd btn_black w25" data-type="A">결재</button>
                            <button type="button" class="btn_apprline_targetadd btn_black w25" data-type="C">합의</button>
                            <button type="button" class="btn_apprline_targetadd btn_black w25" data-type="S">확인</button>
                        </div>
                        
                        <div class="input_area no_label appr_list_wrap mt20">
                            <label class="input_label">결재 목록</label>
                            <span class="btn_appr_wrap">
                                <button type="button" class="btn btn_apprline_func" data-type="delete">삭제</button>
                                <button type="button" class="btn btn_apprline_func" data-type="up"><i class="fa fa-chevron-up" aria-hidden="true"></i></button>
                                <button type="button" class="btn btn_apprline_func" data-type="down"><i class="fa fa-chevron-down" aria-hidden="true"></i></button>
                            </span>
                        </div>
                        <div class="tableWrap mt10">
                            <div class="tableHeaderWrap">
                                <table class="tableHeader">
                                    <colgroup>
                                        <col width="20%" /><col width="20%" /><col width="40%" /><col width="20%" />
                                    </colgroup>
                                    <tr>
                                        <th>성명</th>
                                        <th>직책</th>
                                        <th>부서</th>
                                        <th>유형</th>
                                    </tr>
                                </table>
                            </div>
                            <div class="tableListWrap">
                                <table class="tableList circularTargetList apprlineTargetList">
                                    <colgroup>
                                        <col width="20%" /><col width="20%" /><col width="40%" /><col width="20%" />
                                    </colgroup>
                                    <tbody>'.$_prevApprLine.'</tbody>
                                </table>
                            </div>
                        </div>
                        <div class="input_area no_label acenter mt10">
                            <button type="button" class="btn_apprline_submit btn_black w30">확인</button>
                        </div>
                    </fieldset>
                </div>
            </div>';
        return $html;
    }
    
    // 결재 수정 폼
    public function getApprovalModifyPage($data) {
        // JWT Token 설정
        $this->setGSITMJWTKey();
        
        $_data = array();
        $_data['api_url'] = $this->apiHost."/approval/doc/".$data['idx'];
        
        $_data['getParam'] = array();
        $_data['getParam']['createCoId'] = $data['createCoId'];
        $_data['getParam']['createEmpId'] = $data['createEmpId'];
        $_data['headers'] = $this->apiAuthKey;
        $aAPIResult = $this->chatbot->getReserveAPI($_data);
        
        $html = "";
        if($aAPIResult['code'] == "0000" && isset($aAPIResult['data'])) {
            $apprDto = $aAPIResult['data']['apprDto'];
            $apprLines = $aAPIResult['data']['apprLines'];
            $srInfo = $aAPIResult['data']['srInfo'];
            
            // 요청 양식 가져오기
            $_formatData = array();
            $_formatData['reqCl'] = $srInfo['reqCl'];
            $_formatData['reqTySe'] = $srInfo['reqTySe'];
            $_formatData['servId'] = $srInfo['servId'];
            $aDataFormat = $this->getGSITMDataFormat($_formatData);
            $gsitm_tmlNos = $aDataFormat['tmlList'];
            
            // 결재라인
            $_apprLines = "";
            foreach($apprLines as $idx=>$aAppr) {
                $aAppr['apprTyNm'] = $aAppr['apprTy'] == "A" ? "결재" : ($aAppr['apprTy'] == "C" ? "합의" : "확인");
                $aAppr['apprTyNm'] = $idx == 0 ? "작성자" : $aAppr['apprTyNm'];
                $_apprInfo = json_encode($aAppr, JSON_UNESCAPED_UNICODE);
                $_apprLines .="<li>";
                $_apprLines .="  <div>";
                $_apprLines .="      <span>".$aAppr['userNm']."/".$aAppr['titleNm']." [".$aAppr['apprTyNm']."]</span>";
                $_apprLines .="      <span class='gsitm_apprLines dispnone'>".$_apprInfo."</span>";
                $_apprLines .="  </div>";                    
                $_apprLines .="</li>";
            }
            
            // 완료희망일시
            $finish_hours = $finish_minutes = "";
            for($i=1; $i<=24; $i++) {
                $_val = sprintf("%02d", $i);
                $_selected = $srInfo['chgCmplHopeHh'] == $_val ? "selected" : "";
                $finish_hours .="<option value='".$_val."' ".$_selected.">".$_val."</option>";
            }
            for($i=0; $i<=59; $i++) {
                $_val = sprintf("%02d", $i);
                $_selected = $srInfo['chgCmplHopeMm'] == $_val ? "selected" : "";
                $finish_minutes .="<option value='".$_val."' ".$_selected.">".$_val."</option>";
            }
            
            if($srInfo['icdtOcrDt']) {
                $icdtOcrDt = $srInfo['icdtOcrDt']."0000";
            }
                
            $html .="<div class='pop_container'>";
            $html .="    <form id='apprModify'>";            
            $html .="    <div class='bot_form gsitm'>";
            $html .="        <fieldset class='form_field'>";
            $html .="           <input type='hidden' name='srId' value='".$srInfo['srId']."' />";
            $html .="           <input type='hidden' name='createCoId' value='".$apprDto['createCoId']."' />";
            $html .="           <input type='hidden' name='createEmpId' value='".$apprDto['createEmpId']."' />";
            $html .="           <input type='hidden' name='apprDetailId' value='".$srInfo['apprDetailId']."' />";
            $html .="           <input type='hidden' name='approvalStatus' value='".$srInfo['approvalStatus']."' />";
            $html .="           <input type='hidden' name='gsitm_ocr_date' value='".$icdtOcrDt."' />";
            $html .="            <div class='input_area no_label approvallines_area'>";
            $html .="                <label class='input_label'>결재순서</label>";
            $html .="                <ul class='approvallines'>";
            $html .="                    ".$_apprLines." ";
            $html .="                </ul>";
            $html .="            </div>";
            $html .="            <div class='input_area no_label'>";
            $html .="                <label class='input_label'>요청 유형</label>";
            $html .="                <div>".$srInfo['reqTyNm']."</div>";
            $html .="                <input type='hidden' name='gsitm_reqTySe' value='".$srInfo['reqTySe']."' />";
            $html .="            </div>";
            $html .="            <div class='input_area no_label'>";
            $html .="                <label class='input_label'>요청 분류 <span class='red'>*</span></label>";
            $html .="                <div>".$srInfo['reqClNm']."</div>";
            $html .="                <input type='hidden' name='gsitm_reqCl' value='".$srInfo['reqCl']."' />";
            $html .="            </div>";
            $html .="            <div class='input_area no_label'>";
            $html .="                <label class='input_label'>요청 서비스</label>";
            $html .="                <div>".$srInfo['servPathNm']."</div>";
            $html .="                <input type='hidden' name='gsitm_servId' value='".$srInfo['servId']."' />";
            $html .="            </div>";
            $html .="            <div class='input_area no_label'>";
            $html .="                <label class='input_label'>템플릿</label>";
            $html .="                <div class='selectwrap'>";
            $html .="                    <select id='gsitm_tmlNo' name='gsitm_tmlNo'>";
            $html .="                        ".$gsitm_tmlNos." ";
            $html .="                    </select>";
            $html .="                </div>";
            $html .="            </div>";
            $html .="            <div class='input_area no_label'>";
            $html .="                <label class='input_label'>완료희망일시</label>";
            $html .="                <div class='input_datewrap'>";
            $html .="                    <input type='text' id='gsitm_req_date' name='gsitm_req_date' class='input_date' value='".$srInfo['chgCmplHopeDd']."'>";
            $html .="                </div>";
            $html .="                <div class='fr input_timewrap'>";
            $html .="                    <div class='selectwrap w48'>";
            $html .="                        <select id='gsitm_req_hour' name='gsitm_req_hour'>";
            $html .="                            ".$finish_hours." ";
            $html .="                        </select>";
            $html .="                    </div>";
            $html .="                    <div class='selectwrap fr w48'>";
            $html .="                        <select id='gsitm_req_min' name='gsitm_req_min'>";
            $html .="                            ".$finish_minutes." ";
            $html .="                        </select>";
            $html .="                    </div>";
            $html .="                </div>";
            $html .="            </div>";
            $html .="            <div class='input_area no_label input_text mt15'>";
            $html .="                <label class='input_label'>제목 <span class='red'>*</span></label>";
            $html .="                <div>";
            $html .="                    <input type='text' id='gsitm_req_title' name='gsitm_req_title' maxlength='50' value='".stripslashes($srInfo['srTitlNm'])."' />";
            $html .="                </div>";
            $html .="            </div>";
            $html .="            <div class='input_area no_label input_text mt15'>";
            $html .="                <label class='input_label'>내용 <span class='red'>*</span></label>";
            $html .="                <div>";
            $html .="                    <textarea id='gsitm_req_content' name='gsitm_req_content' rows='8' placeholder='요청사항을 입력해주세요.'>".stripslashes($srInfo['srCn'])."</textarea>";
            $html .="                </div>";
            $html .="            </div>";
            $html .="            <div class='input_area no_label input_text mt15'>";
            $html .="                <label class='input_label'>보안내용</label>";
            $html .="                <div>";
            $html .="                    <textarea id='gsitm_req_secucontent' name='gsitm_req_secucontent' rows='4' placeholder='보안 내용을 입력해주세요.'>".stripslashes($srInfo['secuCn'])."</textarea>";
            $html .="                </div>";
            $html .="            </div>";
            $html .="            <div class='input_area no_label input_text input_filezone mt15'>";
            $html .="                <label class='input_label'>첨부파일</label>";
            $html .="                <div class='no_box'>";
            $html .="                    <button type='button' class='btn_file btn_files'>";
            $html .="                        파일선택";
            $html .="                        <input type='file' id='blFile' name='blFile' id='btn_file_input' multiple='multiple' />";
            $html .="                    </button>";
            $html .="                    <span class='gray'>*최대 20MB까지 첨부 가능</span>";
            $html .="                </div>";
            $html .="                <ul class='ul_files'></ul>";
            $html .="            </div>";
            $html .="            <div class='input_area no_label input_text mt20'>";
            $html .="                <button type='button' class='btn_change_apprline btn_black'>결재라인 변경</button>";
            $html .="            </div>";
            $html .="        </fieldset>";
            $html .="        <div class='acenter mt30'>";
            $html .="            <button type='button' class='btn_black gsitm_modal_close w30'>취소</button>";
            $html .="            <button type='button' class='btn_black submit_modify w30' data-action='request' data-step='gsitm_modify'>저장</button>";
            $html .="        </div>";
            $html .="    </div>";
            $html .="    </form>";
            $html .="</div>";
        }
        return $html;
    }
    
    // 회람 전송
    public function getCircularSubmit($data) {
        $result = array();
        
        // JWT Token 설정
        $this->setGSITMJWTKey();
        
        $data['api_url'] = $this->apiHost."/approval/circulation";
        $data['headers'] = $this->apiAuthKey;
        $data['postParam'] = $data['postParam'];  
        $apiResult = $this->chatbot->getReserveAPI($data);
        
        $result['error'] = $apiResult['code'] == "0000" ? false : true;
        $result['msg'] = $apiResult['message'];        
        return $result;
    }
    
    // 결재문서 승인 관련
    public function getApprDocSubmit($data) {
        $aAPIPath['confirm'] = $this->apiHost."/approval/approve"; // 승인
        $aAPIPath['return'] = $this->apiHost."/approval/return"; // 반려
        $aAPIPath['reject'] = $this->apiHost."/approval/reject"; // 기각
        $aAPIPath['modify'] = $this->apiHost."/approval/modify"; // 수정
        $aAPIPath['circular'] = $this->apiHost."/approval/circulation"; // 회람
        $aAPIPath['temp_delete'] = $this->apiHost."/approval/temporaryDelete"; // 임시저장 삭제
        
        $result = array();
        
        // JWT Token 설정
        $this->setGSITMJWTKey();
        
        $data['api_url'] = $aAPIPath[$data['actionMode']];
        $data['headers'] = $this->apiAuthKey;
        $data['postParam'] = $data['postParam'];
        $apiResult = $this->chatbot->getReserveAPI($data);
        
        $result['error'] = $apiResult['code'] == "0000" ? false : true;
        if($data['actionMode'] == "confirm" || $data['actionMode'] == "return" || $data['actionMode'] == "reject") {
            $_mode = $data['actionMode'] == "confirm" ? "승인" : ($data['actionMode'] == "return" ? "반려" : "기각");
            if($apiResult['code'] == "0000") {
                $result['msg'] = $_mode." 처리가 완료되었습니다.<br>목록으로 이동합니다.";
            } else {
                $result['msg'] = $apiResult['message'] ? $apiResult['message'] : "처리 실패!";
            }
        } else {
            $result['msg'] = $apiResult['message'];
        }
        return $result;
    }
    
}
?>