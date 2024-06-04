<?php
class Callbot {
    public $mbruid;
    public $vendor;
    public $bot;
    public $botid;
    public $dialog;
    public $botName;
    public $botActive;
    public $bot_avatar;
    public $use_chatting;
    public $fromPhone;
    public $roomToken;
    public $accessToken;
    public $bargein;
    public $next_status;
    public $ttsSpeed; //-5 ~ 5
    public $r_data;
    public $aOutputs;
    public $intentMV;
    public $aAutoHangup;
    public $channel;
    public $cgroup;
    public $nExpireMin;
    public $humanMod;
    public $sameq;
    public $language;
    public $errorMsg;

    public $oDate;
    public $oNumber;

    public function __construct(){
        global $g, $chatbot;

        $this->bargein = false;
        $this->next_status = array('action'=>'recognize');
        $this->ttsSpeed = '0';
        $this->r_data = array();
        $this->aOutputs = array();
        $this->aAutoHangup = array('nokeyin'=>3, 'noinput'=>5);
        $this->channel = $chatbot->channel = 'call';
        $this->nExpireMin = 10; // 토큰 expire 타임(분)
        $this->sameq = false;
        $this->language = "ko-KR"; // en-US
        $this->oDate = new DateParse();
        $this->oNumber = new NumberParse();
    }

    public function removeExpireToken($data) {
        global $table, $m;

        if($data['del_type'] == "expire") {
            $_now = time();
            $del_q = "access_mod='callInput' and expire < '".$_now."'";
            $RCD = getDbArray($table[$m.'token'], $del_q, 'uid, roomToken, userId', 'uid', 'asc', '', 1);
            while ($R = db_fetch_array($RCD)) {
                $aSockData = array();
                $aSockData['fromPhone'] = $R['userId'];
                $aSockData['roomToken'] = $R['roomToken'];
                $aSockData['role'] = "disconnect";
                $this->sendWebSocket($aSockData);

                getDbDelete($table[$m.'token'], "uid='".$R['uid']."'");
            }
        } else {
            $bot = $data['bot'] ? $data['bot'] : $this->bot;
            $fromPhone = $data['userId'] ? $data['userId'] : $this->fromPhone;

            $R = getDbData($table[$m.'token'], "bot='".$bot."' and access_mod='callInput' and userId='".$fromPhone."'", "uid, roomToken, userId");
            if($R['uid']) {
                if(!isset($data['no_send']) || $data['no_send'] != true) {
                    $aSockData = array();
                    $aSockData['fromPhone'] = $R['userId'];
                    $aSockData['roomToken'] = $R['roomToken'];
                    $aSockData['role'] = "disconnect";
                    $this->sendWebSocket($aSockData);
                }
                getDbDelete($table[$m.'token'], "uid='".$R['uid']."'");
            }
        }
        return true;
    }

    public function checkToken($data) {
        global $table, $m;

        $expire = time()+(60*$this->nExpireMin); // 10분
        $_now = time();

        // 유효기간 지난것 삭제
        $data['del_type'] = "expire";
        $this->removeExpireToken($data);

        if($data['mode'] == 'create') {
            // 같은 폰 토큰 삭제
            $data['del_type'] = "phone";
            $this->removeExpireToken($data);

            $_QKEY = "bot, access_mod, access_token, roomToken, userId, expire, r_data";
            $_QVAL = "'".$data['bot']."','callInput','".$data['accessToken']."', '".$data['roomToken']."', '".$data['userId']."', '".$expire."', ''";
            getDbInsert($table[$m.'token'],$_QKEY,$_QVAL);

        } else if($data['mode'] == 'search') {
            $_wh = "A.access_mod='callInput' and A.access_token='".$data['accessToken']."'";
            $_table = $table[$m.'token']." A ";
            $_table .="left join ".$table[$m.'bot']." B on A.bot = B.uid ";
            $_table .="left join rb_s_mbrdata C on B.mbruid = C.memberuid ";
            $_table .="left join ".$table[$m.'botSettings']." D on A.bot = D.bot and D.name = 'use_chatting' ";
            $_table .="left join ".$table[$m.'botSettings']." E on A.bot = E.bot and E.name = 'stt_language' ";
            $R = getDbData($_table, $_wh, "A.*, B.active as botActive, B.vendor, B.name, B.mbruid, B.id as botid, B.error_msg, C.cgroup, D.value as use_chatting, E.value as stt_language");
            if($R['uid']) {
                getDbUpdate($table[$m.'token'], "expire='".$expire."'", "uid='".$R['uid']."'");
            }

            $_data = array();
            $_data['mbruid'] = $R['mbruid'];
            $_data['name'] = $R['name'];
            $_data['vendor'] = $R['vendor'];
            $_data['bot'] = $R['bot'];
            $_data['botid'] = $R['botid'];
            $_data['botActive'] = $R['botActive'];
            $_data['roomToken'] = $R['roomToken'];
            $_data['userId'] = $R['userId'];
            $_data['use_chatting'] = $R['use_chatting'];
            $_data['stt_language'] = ($R['stt_language'] ? $R['stt_language'] : "ko-KR");
            $_data['cgroup'] = $R['cgroup'];
            $_data['humanMod'] = $R['humanMod'];
            $_data['r_data'] = $R['r_data'] ? json_decode(preg_replace('/\r\n|\r|\n/',' ', stripslashes(getUnesc($R['r_data']))), true) : array();
            $_data['error_msg'] = $R['error_msg'];
            return $_data;
        }
    }

    public function getCheckHeader($data) {
        global $table, $m, $g, $_db_bot, $DB, $DB_CONNECT, $chatbot;

        $result = array();
        $result["result"] = true;

        if($data['mode'] == "auth") {
            $aHost = explode(".", $_SERVER['HTTP_HOST']);

            if (!isset($data['headers']['X-Bottalks-Bot-Id']) || !$data['headers']['X-Bottalks-Bot-Id']) {
                $result['result'] = false;
                $result['message'] = "Invalid bot id.";
            }
            if (!isset($data['headers']['X-Bottalks-Role']) || $data['headers']['X-Bottalks-Role'] != "callbot") {
                $result['result'] = false;
                $result['message'] = "Invalid role.";
            }
            if ($result['result'] == false) {
                $result['code'] = 400;
            } else {
                // dev쪽으로 db 정보 문의
                $botID = $data['headers']['X-Bottalks-Bot-Id'];

                if($GLOBALS['_cloud_'] === true) {
                    $apiURL = $g['front_host'].'/api/v1/account_info/'.$botID;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $apiURL);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($ch);
                    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close ($ch);

                    if($httpCode == 200 && $response) {
                        $dbinfo = json_decode($response, true);
                        if (!$dbinfo['mbruid']) {
                            $result['result'] = false;
                            $result['message'] = "Invalid botId.";
                            $result['code'] = 401;
                            return $result;
                        } else {
                            $_SESSION['mbr_db'] = "bot_user".$dbinfo['mbruid'];
                        }
                    } else {
                        $result['result'] = false;
                        $result['message'] = "Invalid botId.";
                        $result['code'] = 401;
                        return $result;
                    }
                }

                require $g['path_var'].'db.info.php';
                $DB_CONNECT = isConnectedToDB($DB);

                // botId 로 관련 데이타 추출
                $B = $chatbot->getBotDataFromId($botID);
                if(!$B['bot']) {
                    $result['result'] = false;
                    $result['message'] = "Invalid botId.";
                    $result['code'] = 401;
                } else {
                    $accessToken = "";
                    while (strlen($accessToken) < 64) $accessToken .= chr(mt_rand(0, 255));
                    $accessToken = bin2hex($accessToken).md5(uniqid(mt_rand()));
                    if($GLOBALS['_cloud_'] === true) {
                        $accessToken .="-".$dbinfo['mbruid'];
                    }
                    $roomToken = substr(str_shuffle(md5(uniqid(mt_rand()))),0,20);
                    $_data = array('mode'=>'create', 'bot'=>$B['bot'], 'accessToken'=>$accessToken, 'roomToken'=>$roomToken, 'userId'=>$data['userId']);
                    $this->checkToken($_data);

                    $this->mbruid = $chatbot->mbruid = $B['mbruid'];
                    $this->vendor = $chatbot->vendor = $B['vendor'];
                    $this->bot = $chatbot->botuid = $B['bot'];
                    $this->botid = $chatbot->botid = $botID;
                    $this->dialog = $chatbot->dialog = $B['dialog'];
                    $this->botActive = $chatbot->botActive = $B['botActive'];
                    $this->fromPhone = $chatbot->fromPhone = $data['userId'];
                    $this->accessToken = $chatbot->accessToken = $accessToken;
                    $this->roomToken = $chatbot->roomToken = $roomToken;
                    $this->cgroup = $chatbot->cgroup = $B['cgroup'];
                    $this->botName = $B['name'];
                    $this->use_chatting = $B['use_chatting'];
                    $this->language = ($B['stt_language'] ? $B['stt_language'] : "ko-KR");
                    $this->errorMsg = $B['error_msg'];

                    $_R = getUidData($table[$m.'bot'], $this->bot);
                    $this->bot_avatar = $chatbot->getBotAvatarSrc($_R);
                }
            }
            return $result;

        } else if($data['mode'] == "chatbot") {
            if (!isset($data['headers']['X-Bottalks-Token']) || !$data['headers']['X-Bottalks-Token']) {
                $result['result'] = false;
                $result['message'] = "Invalid Access Token.";
                $result['code'] = 401;
            } else {
                $accessToken = $data['headers']['X-Bottalks-Token'];

                if($GLOBALS['_cloud_'] === true) {
                    $aToken = explode("-", $accessToken);
                    $mbruid = end($aToken);
                    $_SESSION['mbr_db'] = "bot_user".$mbruid;
                }

                require $g['path_var'].'db.info.php';
                $DB_CONNECT = isConnectedToDB($DB);

                $_data = array('mode'=>'search', 'accessToken'=>$accessToken);
                $_tdata = $this->checkToken($_data);
                if(!$_tdata['roomToken']) {
                    $result['result'] = false;
                    $result["message"] = "Invalid Access Token.";
                    $result['code'] = 401;
                } else {
                    $d_wh = "vendor=".$_tdata['vendor']." and bot=".$_tdata['bot']." and gid=0 and active=1 and type='D'";
                    $D = getDbData($table[$m.'dialog'],$d_wh,'uid');

                    $this->mbruid = $chatbot->mbruid = $_tdata['mbruid'];
                    $this->vendor = $chatbot->vendor = $_tdata['vendor'];
                    $this->bot = $chatbot->botuid = $_tdata['bot'];
                    $this->botid = $chatbot->botid = $_tdata['botid'];
                    $this->dialog = $chatbot->dialog = $D['uid'];
                    $this->botActive = $chatbot->botActive = $_tdata['botActive'];
                    $this->fromPhone = $chatbot->fromPhone = $_tdata['userId'];
                    $this->accessToken = $chatbot->accessToken = $accessToken;
                    $this->roomToken = $chatbot->roomToken = $_tdata['roomToken'];
                    $this->cgroup = $chatbot->cgroup = $_tdata['cgroup'];
                    $this->botName = $_tdata['name'];
                    $this->use_chatting = $_tdata['use_chatting'];
                    $this->language = ($_tdata['stt_language'] ? $_tdata['stt_language'] : "ko-KR");
                    $this->humanMod = $_tdata['humanMod'];
                    $this->r_data = $_tdata['r_data'];
                    $this->errorMsg = $_tdata['error_msg'];

                    $_R = getUidData($table[$m.'bot'], $this->bot);
                    $this->bot_avatar = $chatbot->getBotAvatarSrc($_R);
                }
            }
            return $result;
        }
        return $result;
    }

    public function pushText($data) {
        $data = preg_replace('/<br>|<br \/>|<br\/>/',' ',$data);
        $data = preg_replace('/\r\n|\r|\n/',' ', $data);
        $this->aOutputs[] = addslashes($data);
    }

    public function pushMenuRespond($_data) {
        global $chatbot;

        $result = $_data['result'];
        $data = $_data['data'];

        foreach ($result as $item) {
            for($i=0, $nCnt=count($item); $i<$nCnt; $i++){
                $type = $item[$i]['type'];
                $content = $item[$i]['content'];
                $this->bargein = isset($item[$i]['bargein']) && $item[$i]['bargein'] ? $item[$i]['bargein'] : $this->bargein;
                $this->next_status = isset($item[$i]['next_status']) && $item[$i]['next_status'] ? $item[$i]['next_status'] : $this->next_status;
                $this->r_data = isset($item[$i]['r_data']) && $item[$i]['r_data'] ? $item[$i]['r_data'] : $this->r_data;
                if($type=='text' || $type=='form'){
                    $this->pushText($content);
                }else if($type=='node'){
                    $data['node'] = $content;
                    $this->getNodeRespond($data);
                }
            }
        }
    }

    public function getCallRespond($data) {
        global $g, $chatbot;

        $_data = $result = $_response = array();

        $result['result'] = false;
        $_response['type'] = "text";
        $_response['bargein'] = false;

        if($data['msg'] == "nokeyin") {
            $userChat = array('printType'=>'T', 'userId'=>$this->fromPhone, 'content'=>$data['msg']);
            $userLastChat = $chatbot->addChatLog($userChat);

            // keyin 데이터값이 넘어오지 않았을 경우
            $this->r_data['no_keyin_cnt'] = !isset($data['r_data']['no_keyin_cnt']) ? 2 : ($data['r_data']['no_keyin_cnt']+1);
            if($this->r_data['no_keyin_cnt'] >= $this->aAutoHangup['nokeyin']) {
                $result['result'] = true;
                $_response['content'] = "죄송합니다. 응답이 없어 통화를 종료합니다.";
                $_response['next_status'] = array('action'=>'hangup');
            } else {
                $result['result'] = true;
                $_response['content'] = $data['r_data']['content'];
                $_response['next_status'] = $data['r_data']['next_status'];
            }

        } else {
            $_data['vendor'] = $this->vendor;
            $_data['bot'] = $this->bot;
            $_data['bReserve'] = true;
            $_data['clean_input'] = $data['msg'];
            if(isset($data['r_data']['able_weeks']) && count($data['r_data']['able_weeks']) > 0) {
                $_data['able_weeks'] = $data['r_data']['able_weeks'];
            }

            $result['temp_sys_date'] = $result['temp_sys_week'] = $result['temp_sys_time'] = "";

            $aResultDate = $this->oDate->getDateParse($_data);
            if(isset($aResultDate['data']) && count($aResultDate['data']) > 0) {
                $DT = $aResultDate['data'][0];
                // 220401
                $result['temp_sys_date'] = (int)$DT['month'] && (int)$DT['day'] ? $DT['year']."-".$DT['month']."-".$DT['day'] : "";
                $result['temp_sys_week'] = $DT['weekday'] ? $DT['weekday'] : "";
                $result['temp_sys_time'] = (int)$DT['hour'] ? $DT['hour'].":".$DT['minute'] : "";
            }

            $IS = $chatbot->getSimilarityIntent($_data);
            if($IS['intentName']) {
                $_intentName = $IS['intentName'];
                $_intentScore = $IS['score'];
            } else {
                $I = $chatbot->getSentenceIntent($_data);
                if($I['intentName']) {
                    $_intentName = $I['intentName'];
                    $_intentScore = $I['score'];
                }
            }
            if($_intentName && $_intentScore >= $chatbot->intentMV) {
                $result['intentName'] = $_intentName;

                if($result['intentName'] == "시스템-반복") {
                    $userChat = array('printType'=>'T', 'userId'=>$this->fromPhone, 'content'=>$data['msg']);
                    $userLastChat = $chatbot->addChatLog($userChat);

                    $result['result'] = true;
                    $result['is_repeat'] = true;
                    $_response['content'] = $data['r_data']['content'];
                    $_response['next_status'] = $data['r_data']['next_status'];
                }

                if($result['intentName'] == "시스템-통화종료" && (!isset($data['r_data']['step']) || $data['r_data']['step'] == "finish")) {
                    $userChat = array('printType'=>'T', 'userId'=>$this->fromPhone, 'content'=>$data['msg']);
                    $userLastChat = $chatbot->addChatLog($userChat);

                    $result['result'] = true;
                    $_response['content'] = "감사합니다. 좋은 하루 보내세요.";
                    $_response['next_status'] = array("action"=>"hangup");
                }

                // 가능 시간대 확인
                if($result['intentName'] == "시스템-시간문의" && (!(int)$DT['month'] && !(int)$DT['day'])) {
                    preg_match_all("/오전|오후|이전|전|앞|이후|후|뒤/iu", $data['msg'], $_match);
                    $_before = (in_array("이전", $_match[0]) || in_array("전", $_match[0]) || in_array("앞", $_match[0])) ? "before" : "";
                    $_after = (in_array("이후", $_match[0]) || in_array("후", $_match[0]) || in_array("뒤", $_match[0])) ? "after" : "";
                    if(in_array("오전", $_match[0]) || in_array("오후", $_match[0])) {
                        if(in_array("오전", $_match[0])) {
                            if((int)$DT['hour'] == 0 && $_before == "" && $_after == "") {
                                $result['time_range'] = array('time'=>"12:00", 'order'=>'before');
                            } else {
                                $_order = $_before ? $_before : $_after;
                                $result['time_range'] = array('time'=>$result['temp_sys_time'], 'order'=>$_order);
                            }
                        }
                        if(in_array("오후", $_match[0])) {
                            if($_before == "" && $_after == "") {
                                $result['time_range'] = array('time'=>"12:00", 'order'=>'after');
                            } else {
                                $_order = $_before ? $_before : $_after;
                                $result['time_range'] = array('time'=>$result['temp_sys_time'], 'order'=>$_order);
                            }
                        }
                    } else {
                        $_order = $_before ? $_before : $_after;
                        $result['time_range'] = array('time'=>$result['temp_sys_time'], 'order'=>$_order);
                    }
                }
            }
        }

        // 시나리오 진입하지 않고 현재 단계에서 응답 내보낼 경우, 응답 로그 저장
        if($userLastChat['last_chat'] && $_response['content']) {
            $botChat = array();
            $botChat['content'] = array(array("hform", $_response['content']));
            $botChat['last_chat'] = $userLastChat['last_chat'];
            $chatbot->addBotChatLog($botChat);
        }

        $result['data'][] = $_response;
        return $result;
    }

    public function getNodeRespond($data){
        global $g, $chatbot, $reserve, $table, $m;

        $data['roomToken'] = $this->roomToken;
        $data['botId'] = $this->botid;
        $data['userId'] = $this->fromPhone;
        $data['accessToken'] = $this->accessToken;
        $data['r_data'] = $this->r_data;
        $data['r_data']['uphone'] = $this->fromPhone; // 발신폰번호
        $data['channel'] = $this->channel;
        $data['noMatchExceededCount'] = !empty($data['r_data']['noMatchExceededCount']) ? $data['r_data']['noMatchExceededCount'] : 0;

        $chatbot->lastNodeId = !empty($chatbot->lastNodeId) ? $chatbot->lastNodeId : $data['r_data']['lastNodeId'];
        $chatbot->lastNodeName = !empty($chatbot->lastNodeName) ? $chatbot->lastNodeName : $data['r_data']['lastNodeName'];
        $unknown = $res_end = $bargein = $is_repeat = $is_scenario = false;
        $_callRespond = array();

        // 직전 발화문과 동일한 문장 체크
        if(isset($this->r_data['last_msg']) && $this->r_data['last_msg']) {
            if(getEtcStrReplace($this->r_data['last_msg']) == getEtcStrReplace($data['msg'])) {
                $sameq = true;
            }
        }

        // humanMod일 경우 로그 등록 후 bot 응답 무시
        if($this->humanMod == "on") {
            $this->getCallbotAddChatLog($data['msg']);

            $response["result"] = true;
            $response['message'] = "_human_mode_";
            $response['next_status'] = $this->next_status;
            $response['barge_in'] = $this->bargein;
            $this->echoResponse(200, $response);
            exit;
        }

        // 반복 요청 등 전화상 문장 확인하기 (반복 의도문은 미리 학습되어 있어야함)
        if($data['msg_type'] != 'say_hello') {
            $_callRespond = $this->getCallRespond($data);
        }
        if(isset($data['r_data']['able_weeks'])) {
            unset($data['r_data']['able_weeks'], $this->r_data['able_weeks']);
        }

        if(isset($_callRespond['result']) && $_callRespond['result']) {
            $is_repeat = $_callRespond['is_repeat'] ? $_callRespond['is_repeat'] : $is_repeat;
            $result = $_callRespond['data'];
        } else {
            if(isset($data['r_data'])) {
                if($data['r_data']['form'] == 'reserve' && $data['r_data']['action']) {
                    $data['r_data']['intentName'] = $_callRespond['intentName'];
                    $data['r_data']['temp_sys_date'] = $_callRespond['temp_sys_date'];
                    $data['r_data']['temp_sys_week'] = $_callRespond['temp_sys_week'];
                    $data['r_data']['temp_sys_time'] = $_callRespond['temp_sys_time'];
                    if(isset($_callRespond['time_range']) && is_array($_callRespond['time_range'])) {
                        $data['r_data']['time_range'] = $_callRespond['time_range'];
                    }

                    // 예약 시나리오 완료되지 않은 상황일 경우
                    if($data['r_data']['step'] != "finish") {
                        $is_scenario = true;

                    // 예약 시나리오 완료된 상황에서 변경이 있을 경우
                    } else {
                        $is_user = $data['r_data']['uname'] && $data['r_data']['uphone'] ? true : false;
                        if($data['r_data']['action'] == "request" || $data['r_data']['action'] == "modify") {
                            if($is_user && $data['r_data']['intentName'] == "시스템-날짜시간변경") {
                                $is_scenario = true;
                                $data['r_data']['step'] = "ask_finish_modify";
                            }
                            if($is_user && preg_match("/확인|조회|검색/u", $data['msg'])) {
                                $is_scenario = true;
                                $data['r_data']['step'] = "ask_finish_search";
                            }
                        }
                        if($data['r_data']['action'] == "cancel") {
                            if($is_user && preg_match("/새로|신규|다시/u", $data['msg'])) {
                                $is_scenario = true;
                                $data['r_data']['step'] = "ask_finish_new";
                            }
                        }
                    }
                } else if($data['r_data']['form'] == 'jusobot') {
                    if($data['r_data']['step'] != "finish") $is_scenario = true;
                }
            }

            //---------- 온톨로지, 시나리오 응답 처리 분기 ------------//
            if($is_scenario) {
                if($data['r_data']['form'] == 'reserve') {
                    require_once $g['dir_include'].'reserve.'.$data['r_data']['category'].'.class.php';
                    $reserve = new Reserve();
                    $result = $reserve->getReserveResponse($data);
                } else if($data['r_data']['form'] == 'jusobot') {
                    require_once $g['dir_include'].'jusobot.class.php';
                    $jusobot = new Jusobot();
                    $result = $jusobot->getJusobotResponse($data);
                }
            } else {
                $result = $chatbot->getApiResponse($data);
            }
        }

        foreach ($result as $resItem) {
            $type = $resItem['type'];
            $content = $resItem['content'];
            $unknown = isset($resItem['unknown']) && $resItem['unknown'] ? $resItem['unknown'] : $unknown;
            $res_end = isset($resItem['res_end']) && $resItem['res_end'] ? $resItem['res_end'] : $res_end;

            if($type=='text'){
                $this->bargein = isset($resItem['bargein']) && $resItem['bargein'] ? $resItem['bargein'] : $this->bargein;
                $this->next_status = isset($resItem['next_status']) && $resItem['next_status'] ? $resItem['next_status'] : $this->next_status;
                $this->ttsSpeed = isset($resItem['ttsSpeed']) && $resItem['ttsSpeed'] ? $resItem['ttsSpeed'] : $this->ttsSpeed;
                $this->r_data = isset($resItem['r_data']) && $resItem['r_data'] ? $resItem['r_data'] : $this->r_data;
                $this->pushText($content);
            }else if($type=='if'){
                $_data = array();
                $_data['data'] = $data;
                $_data['result'] = $content;
                $this->pushMenuRespond($_data);
            }
        }

        $response = $r_data_set = "";
        if (count($this->aOutputs) == 0) {
            $response .="죄송합니다. 문의하신 내용의 답변을 찾을 수가 없습니다.";
        } else {
            foreach($this->aOutputs as $_out) {
                $response .=$_out." ";
            }
        }

        // ARS일 경우 hangup 처리
        if($this->next_status['action'] == 'ars') {
            $ars_link = $this->next_status['value'];
            $this->next_status = array('action'=>'hangup');
        }

        if($this->next_status['action'] == 'keyin') {
            $this->next_status['value'] = (int)$this->next_status['value'];
        }

        //인사말이 아닐 경우 lastNodeId가 변경될 가능성이 있으므로 최신 lastNodeId에 맞는 데이터 세팅
        if ($data['msg_type'] != 'say_hello') {
            $data['nodeSettingInfo'] = $chatbot->getNodeSettingInfo($this->bot, $chatbot->lastNodeId);
        }

        if(!$is_repeat) {
            if(isset($this->r_data['action'])) {
                if($data['msg'] != "sttfail" && $data['msg'] != "noinput") {
                    $this->r_data['last_msg'] = $data['msg'];
                }
                $r_data = $this->next_status['action'] == "hangup" ? "" : json_encode(getEsc($this->r_data), JSON_UNESCAPED_UNICODE);
                $r_data_set = "r_data='".$r_data."'";
            } else {
                // 시나리오 아닌 경우 next_status값만 저장
                $_r_data = array();

                //테스트용
                $_r_data['msg_type'] = $data['msg_type'];
                $_r_data['ttsSpeed'] = $this->ttsSpeed;

                $_r_data['lastNodeId'] = $chatbot->lastNodeId;
                $_r_data['lastNodeName'] = $chatbot->lastNodeName;
                $_r_data['content'] = $response;
                $_r_data['bargein'] = $this->bargein;
                $_r_data['next_status'] = $this->next_status;
                $_r_data['noMatchExceededCount'] = 0;
                if ($data['r_data']['lastNodeId'] === $chatbot->lastNodeId) {
                    if ('nomatch' === $data['msg_type']) {
                        $_r_data['noMatchExceededCount'] = isset($data['r_data']['noMatchExceededCount'])
                            ? ($data['r_data']['noMatchExceededCount'] + 1) : 0;
                    }
                }

                if($data['msg'] !== "sttfail" && $data['msg'] !== "noinput") {
                    $_r_data['last_msg'] = $data['msg'];
                }
                $r_data = json_encode(getEsc($_r_data), JSON_UNESCAPED_UNICODE);
                $r_data_set = "r_data='".$r_data."'";
            }
            getDbUpdate($table[$m.'token'], $r_data_set, "bot='".$this->bot."' and access_mod='callInput' and access_token='".$this->accessToken."'");
        }

        $_result = array();
        $_result['response'] = trim($response);
        $_result['next_status'] = $res_end ? array('action'=>'hangup') : $this->next_status;
        $_result['bargein'] = $this->bargein;
        $_result['r_data'] = $this->r_data;
        $_result['unknown'] = isset($unknown) && $unknown ? true : false;
        $_result['sameq'] = $this->sameq;
        $_result['language'] = $this->language;
        $_result['timeout'] = !empty($data['nodeSettingInfo']['nodeTimeout']) ? $data['nodeSettingInfo']['nodeTimeout'] : $data['nodeSettingInfo']['defaultTimeout'];
        $_result['timeoutMsg'] = !empty($data['nodeSettingInfo']['nodeTimeoutMsg']) ? $data['nodeSettingInfo']['nodeTimeoutMsg'] : $data['nodeSettingInfo']['defaultTimeoutMsg'];
        $_result['ttsSpeed'] = $this->ttsSpeed;

        if(isset($ars_link) && $ars_link) {
            $_result['ars_link'] = $ars_link;
        }

        return $_result;
    }

    public function getCallResCounter($data) {
        global $table, $m, $g, $DB_CONNECT, $chatbot;

        $_data = array();
        $startTime = $data['cstarttime'];
        $endTime = $data['cendtime'];

        if($startTime == 0 && $endTime == 0) {
            // 통화시간 없을 경우 마지막 응답 시간 + 2
            $_table = "rb_chatbot_chatLog A left join rb_chatbot_botChatLog B on A.vendor = B.vendor and A.bot=B.bot and A.roomToken=B.roomToken";
            $_wh = "A.vendor='".$this->vendor."' and A.bot='".$this->bot."' and A.roomToken='".$this->roomToken."' Group by A.vendor, A.bot, A.roomToken";
            $R = getDbData($_table, $_wh, "min(A.d_regis) as startTime, max(B.d_regis) as endTime");
            if($R['startTime'] && $R['endTime']) {
                $ctime = ((strtotime($R['endTime']) - strtotime($R['startTime'])) + 2);
            }
            $startTime = $startTime ? $startTime : $R['startTime'];
            $endTime = $endTime ? $endTime : $R['endTime'];
        } else {
            $ctime = (strtotime($endTime)-strtotime($startTime)); // 통화시간(초)
        }

        $_data['ctime'] = $ctime;
        $_data['cstarttime'] = $startTime;
        $_data['cendtime'] = $endTime;

        $callSec = $g['sys_callsec'] ? $g['sys_callsec'] : 10;
        $callCount = $g['sys_callcount'] ? $g['sys_callcount'] : 2;
        if($ctime <= $callSec) $_data['rcount'] = $callCount;
        else $_data['rcount'] = (ceil($ctime/$callSec) * $callCount);

        // 콜봇 응답 카운트 시간은 최초 접속 시간으로 설정
        $_data['d_regis'] = $startTime;
        $chatbot->setBotResCounter($_data);
    }

    public function setLogWrite($log) {
        $t = microtime(true);
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
        $dTime = $d->format("Y-m-d H:i:s.u");

        $log = $dTime." ".$_SERVER['REMOTE_ADDR']." ".$log;
        //file_put_contents($_SERVER['DOCUMENT_ROOT']."/_tmp/cache/callbot_".date("Y-m-d").".txt", $log."\r\n", FILE_APPEND);
    }

    public function echoResponse($status_code, $response) {
        $app = \Slim\Slim::getInstance();
        $app->status($status_code);
        if(is_array($response)){
            $app->contentType('application/json');
            $json_encode = json_encode($response,JSON_UNESCAPED_UNICODE);
            $json_encode = stripcslashes($json_encode);
            $json_encode = str_replace("[\"",'[',$json_encode);
            $json_encode = str_replace("}\"",'}',$json_encode);
            $json_encode = str_replace("\"{",'{',$json_encode);
            $response = stripcslashes($json_encode);
        }else{
            $app->contentType('text/html');
        }

        $this->setLogWrite($response);
        echo $response;
    }

    public function sendARSLink($msg) {
        global $g, $table, $m, $chatbot;

        if($this->fromPhone) {
            $R = getDbData($table['s_mbrcomp'], "memberuid='".$this->mbruid."'", 'comp_name');
            $subject = $R['comp_name'] ? $R['comp_name'] : $this->botName;

            $chByteMsg = iconv("utf-8", "euc-kr", str_replace("\n", "", $msg));
            $nByte = strlen($chByteMsg);
            $chSType = $nByte > 80 ? "L" : "S";
            $smsResult = getSendSMS_Cafe24("get_send_sms", $this->fromPhone, $msg, $subject, $chSType);
            if($smsResult['bResult']) {
                $_data = array();
                $_data[($chSType == "L" ? 'lms' : 'sms')] = 1;
                $_data['d_regis'] = date("YmdHis");
                $chatbot->setBotResCounter($_data);
            }
        }
    }

    public function sendWebSocket($data) {
        global $g, $table, $m, $chatbot;

        if($this->use_chatting == "on") {
            $_data = array();
            $_data['type'] = "user";
            $_data['bottype'] = "call";
            $_data['vendor'] = $this->vendor;
            $_data['botid'] = $this->botid;
            $_data['roomToken'] = $data['roomToken'] ? $data['roomToken'] : $this->roomToken;
            $_data['phone'] = $data['fromPhone'] ? $data['fromPhone'] : $this->fromPhone;
            $_data['role'] = $data['role'];
            $_data['date'] = date('y.m.d').' '.(date('a') == 'am' ? '오전 ':'오후 ').date('g').':'.date('i');
            $_data['bot_avatar'] = $this->bot_avatar;
            if($data['sender']) $_data['sender'] = $data['sender'];
            if($data['msg']) $_data['msg'] = $data['msg'];
            if($data['log']) $_data['log'] = $data['log'];
            if($data['sockid']) $_data['sockid'] = $data['sockid'];

            $_data = json_encode($_data, JSON_UNESCAPED_UNICODE);

            $host = $GLOBALS['g']['call_socket_host'];
            $port = $GLOBALS['g']['call_socket_port'];
            $path = "/chatbot_".$this->botid;

            if($fp = fsockopen($host, $port, $errno, $errstr, 2)) {
                $_out = "POST ".$path." HTTP/1.1\r\n";
                $_out .="Host: ".$host."\r\n";
                $_out .="Content-Type: application/json\r\n";
                $_out .="Content-Length: ". strlen($_data) ."\r\n";
                $_out .="Connection: Close\r\n\r\n";
                $_out .=$_data;
                fwrite($fp, $_out);
            } else {
                return array('status' => 'err', 'error' => "$errstr ($errno)");
            }
            fclose($fp);
        }
    }

    public function getCallbotAddChatLog($content) {
        global $m, $table, $chatbot;

        $data = array();
        $data['vendor'] = $this->vendor;
        $data['bot'] = $this->bot;
        $data['botid'] = $this->botid;
        $data['roomToken'] = $this->roomToken;
        $data['botActive'] = $this->botActive;
        $data['bot_type'] = 'call';
        $data['content'] = $content;
        $chatbot->addChatLog($data);
    }

    public function getCallbotBotChatLog($content) {
        global $m, $table, $chatbot;

        $botChat = array();
        $botChat['content'] = array(array("hform", $content));
        $botChat['last_chat'] = $this->last_chat;
        $chatbot->addBotChatLog($botChat);
    }

    public function sendHumanModResponse($data) {
        $tokens = getDbData("rb_chatbot_token", "access_mod='callInput' and bot='".$data['bot']."' and roomToken='".$data['roomToken']."'", "access_token, userId");

        if(isset($tokens['access_token']) && $tokens['access_token']) {
            $headers = [];
            $headers[] = "Content-Type: application/json; charset=utf-8";
            $headers[] = "X-Bottalks-Token: ".$tokens['access_token'];

            $postData = [];
            $postData['fromPhone'] = $tokens['userId'];
            $postData['roomToken'] = $data['roomToken'];
            $postData['message'] = $data['content'];
            $postData['next_status'] = ['action'=>'recognize'];
            $postData['barge_in'] = false;
            $postData = json_encode($postData, JSON_UNESCAPED_UNICODE);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $data['cti_chatapi_host']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if($http_code == 200) {}
        }
    }
}
?>