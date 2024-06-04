<?php

use NexusSpace\NexusService;

require_once $g['dir_include'].'NexusService.php';
require_once $g['dir_include'].'CsChatService.php';

class Cschat {

	public $chatbot;
	public $botId;
	public $vendor;
	public $bot;
	public $botActive;
	public $cgroup;
	public $roomToken;
	public $secretKey;
	public $socketHost;
	public $socketPort;
	public $aChatAPI;
	public $chatAPIUrl;
    // 2024.03.13 spikecow
    private $csChatService;

	public function __construct() {
        $this->chatbot = $GLOBALS['chatbot'];
        $this->socketHost = $GLOBALS['g']['call_socket_host'];
        $this->socketPort = $GLOBALS['g']['call_socket_port'];

        $this->aChatAPI = $this->chatbot->csChatAPIs;

        // 2024.03.13 spikecow
        $nexusService = new NexusService();
        $this->csChatService = new CsChatService($nexusService);

        $_log = "[".date("Y-m-d H:i:s")."] ".getRemoteIP()." req_mode: ".$_REQUEST['mode']." ".trim(file_get_contents('php://input'));
        $this->setRawLog($_log);

        if(isset($_REQUEST['botid']) && trim($_REQUEST['botid'])) {
            $this->botId = $_REQUEST['botid'];
        } else {
            $body = trim(file_get_contents('php://input'));
            $aData = json_decode($body, true);
            $this->botId = $aData['botid'];
        }

        if(!isset($this->botId) || !trim($this->botId)) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>'Not exists botid.']);
        }

        // bot 기본사항 로딩
        $chQuery = "Select A.value as secretkey, B.uid as bot, B.active, B.bottype, B.vendor, C.cgroup From rb_chatbot_channelSettings A ";
        $chQuery .="left join rb_chatbot_bot B on A.botid = B.id ";
        $chQuery .="left join rb_s_mbrdata C on B.mbruid = C.memberuid ";
        $chQuery .="Where A.botid = '".$this->botId."' and A.name='client_secret'";
        $R = db_fetch_assoc(db_query($chQuery, $GLOBALS['DB_CONNECT']));
        if(!$R['secretkey'] || !$R['bot']) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>'Invalid botid.']);
        }
        $this->vendor = $R['vendor'];
        $this->bot = $R['bot'];
        $this->bottype = $R['bottype'];
        $this->botActive = $R['active'];
        $this->cgroup = $R['cgroup'];
        $this->secretKey = $R['secretkey'];

        // 채팅상담 정보 로딩
        $chQuery = "Select A.name, A.value From rb_chatbot_botSettings A ";
        $chQuery .="Where A.vendor = '".$this->vendor."' and A.bot = '".$this->bot."' and (name = 'use_cschat' or name = 'cschat_api') ";
        $RCD = db_query($chQuery, $GLOBALS['DB_CONNECT']);
        while($R=db_fetch_assoc($RCD)){
            $this->{$R['name']} = $R['value'];
        }

        if($this->use_cschat != 'on') {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>'Unabled chatting.']);
        }
        if(!array_key_exists($this->cschat_api, $this->aChatAPI)) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>'Unabled chat api.']);
        }

        $this->chatAPIUrl = $this->aChatAPI[$this->cschat_api]['url'];
    }

	// 헤더 체크 (키 체크)
	public function getCheckHeader() {
        $headers = apache_request_headers();

        if(!isset($headers['X-Bottalks-Key']) || !$headers['X-Bottalks-Key']) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>'Invalid key.']);
        }
        if($headers['X-Bottalks-Key'] != $this->secretKey) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>'Invalid key.']);
        }
    }

    // 대화 시작, 대화 종료 구분 값에 따른 DTO 변경 대응
    private function chatModeDivision($path) {
        $roomToken = trim($_POST['roomToken']);

        if(!$roomToken) {
            $aResult = ['result'=>false, 'msg'=>'중요변수가 존재하지 않습니다'];
            $this->getResultAPIJSON(200, $aResult);
        }

        // 회사 ID SSO 정보에 포함
        $user_name = trim($_POST['user_name']) ? $_POST['user_name'] : "";
        $user_phone = trim($_POST['user_phone']) ? $_POST['user_phone'] : "";
        $user_id = trim($_POST['user_id']) ? $_POST['user_id'] : "";

        $userInfo = ['name'=>$user_name, 'phone'=>$user_phone, 'userid'=>$user_id];

        // 챗봇 로그 검색 및 전송
        $aChatbotLog = [];
        $_data = ['vendor'=>$this->vendor, 'bot'=>$this->bot, 'roomToken'=>$roomToken];
        $aChatbotLog = getChatbotLogToJson($_data);

        if (!$aChatbotLog || empty($aChatbotLog)) {
            $aChatbotLog = [new stdClass()];
        }

        // 2024.03.13 spikecow
        //curl --> guzzle Interface 로 변경
        $nexusAssignData =
            [
                'endPoint'=> $this->chatAPIUrl,
                'path'=> $path,
                'secretKey'=> $this->secretKey,
                'mode'=>'chat_open',
                'botId'=>$this->botId,
                'roomToken'=>$roomToken,
                'userInfo'=>$userInfo,
                'messages'=>$aChatbotLog
            ];

        return $nexusAssignData;
    }

    // 대화로그 및 채팅상담 파라미터 전송
    public function getCSChatOpen() {
        // 로그 및 파라미터 전송
        /*$aPostData = [];
        $aPostData['path'] = "reference";
        $aPostData['data'] = [
            'mode'=>'chat_open', 'botid'=>$this->botId, 'roomToken'=>$roomToken, 'secretKey'=>$this->secretKey, 'messages'=>$aChatbotLog,
            'tenant'=>$tenant, 'userInfo'=>$userInfo
        ];
        $aPostData['data'] = json_encode($aPostData['data'], JSON_UNESCAPED_UNICODE);*/

        //$aResponse = $this->sendCSChatCurl($aPostData);
        $nexusAssignData = $this->chatModeDivision("reference");
        $response = $this->csChatService->nexusAssignAPI($nexusAssignData);

        if($response['result']) {
            $_data = ['vendor'=>$this->vendor, 'botid'=>$this->botId, 'bot'=>$this->bot, 'roomToken'=>trim($_POST['roomToken']), 'speaker'=>"user", 'content'=>"chat_open"];
            $this->addCSLog($_data);
        }
        $this->getResultAPIJSON(200, $response['result']);
    }

    public function getForceChatEnd() {
        $nexusAssignData = $this->chatModeDivision("chat_end");
        $response = $this->csChatService->nexusAssignAPI($nexusAssignData);

        if($response['result']){
            $_data = ['vendor'=>$this->vendor, 'botid'=>$this->botId, 'bot'=>$this->bot, 'roomToken'=>trim($_POST['roomToken']), 'speaker'=>"user", 'content'=>"chat_open"];
            $this->addCSLog($_data);
        }
        $this->getResultAPIJSON(200, $response['result']);
    }

    // 채팅서버 연결 처리
    public function getCheckCSConnect() {
        $this->getCheckHeader();

        $body = trim(file_get_contents('php://input'));
        if(!$body) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>'Not found data.']);
        }

        $aData = json_decode($body, true);
        $_json = getJSONError(json_last_error());
        if($_json !== true) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>$_json]);
        }

        if(!isset($aData['roomToken']) || !trim($aData['roomToken'])) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>'Not exists roomToken.']);
        }

        // 챗봇 로그 기록
        $_data = ['vendor'=>$this->vendor, 'botid'=>$this->botId, 'bot'=>$this->bot, 'roomToken'=>$aData['roomToken'], 'speaker'=>"cs", 'content'=>"cs_connect"];
        $this->addCSLog($_data);

        $aData['action'] = "cs_connect";

        // 클라이언트 전송
        $result = $this->sendWebSocket($aData);
        if($result != true) {
            $this->getResultAPIJSON(500, ['result'=>false, 'msg'=>$result]);
        }

        $this->getResultAPIJSON(200, ['result'=>true]);
    }

    // 사용자 발화 전송
    public function getCSChatUserMsg() {
        $roomToken = trim($_POST['roomToken']);
        if(!$roomToken) {
            $aResult = ['result'=>false, 'msg'=>'중요변수가 존재하지 않습니다'];
            $this->getResultAPIJSON(200, $aResult);
        }

        $msg = trim($_POST['msg']);
        $csId = trim($_POST['cs_id']);
        if($msg) {
            // 챗봇 로그 기록
            $_data = ['vendor'=>$this->vendor, 'botid'=>$this->botId, 'bot'=>$this->bot, 'roomToken'=>$roomToken, 'speaker'=>"user", 'content'=>$msg];
            $msgId = $this->addCSLog($_data);

            /*$aPostData = [];
            $aPostData['path'] = "message";
            $aPostData['data'] = [
                'mode'=>'user_msg', 'botid'=>$this->botId, 'roomToken'=>$roomToken, 'messages'=>['content'=>$msg], 'time'=>time(), 'messageId'=>$msgId
            ];
            if($csId) $aPostData['data']['cs_id'] = $csId;
            $aPostData['data'] = json_encode($aPostData['data'], JSON_UNESCAPED_UNICODE);

            $aResponse = $this->sendCSChatCurl($aPostData);*/

            // 2024.03.15 spikecow
            //curl --> guzzle Interface 로 변경
            $nexusAssignData =
                [
                    'endPoint'=> $this->chatAPIUrl,
                    'path'=> 'message',
                    'secretKey'=> $this->secretKey,
                    'mode'=>'user_msg',
                    'roomToken'=>$roomToken,
                    'messages'=>['content'=>$msg],
                    'time'=>time(),
                    'messageId'=>$msgId
                ];

            $aResponse = $this->csChatService->nexusAssignAPI($nexusAssignData);

            // 전송 결과 확인
            $_result = ['result'=>false, 'msg'=>''];
            if(!$aResponse['result']) {
                $_result['msg'] = isset($aResponse['msg']) && $aResponse['msg'] ? $aResponse['msg'] : "채팅상담 접속 실패";
            } else {
                $_result['result'] = true;
            }
            $this->getResultAPIJSON(200, $_result);
        }
    }

    // 상담사 채팅 응답 처리
    public function getCheckCSResponse() {
        $this->getCheckHeader();

        $body = trim(file_get_contents('php://input'));
        if(!$body) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>'Not found data.']);
        }

        $aData = json_decode($body, true);
        $_json = getJSONError(json_last_error());
        if($_json !== true) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>$_json]);
        }

        if(!isset($aData['roomToken']) || !trim($aData['roomToken'])) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>'Not exists roomToken.']);
        }
        if(!isset($aData['messages'])) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>'Not exists messages.']);
        }

        // 챗봇 로그 기록
        $_data = ['vendor'=>$this->vendor, 'botid'=>$this->botId, 'bot'=>$this->bot, 'roomToken'=>$aData['roomToken'], 'speaker'=>"cs"];
        if(is_array($aData['messages'])) {
            foreach($aData['messages'] as $_message) {
                $_data['content'] = $_message;
                $this->addCSLog($_data);
            }
        } else {
            $_data['content'] = $aData['messages'];
            $this->addCSLog($_data);
        }

        $aData['action'] = "cs_chat";

        // 클라이언트 전송
        $result = $this->sendWebSocket($aData);
        if($result != true) {
            $this->getResultAPIJSON(500, ['result'=>false, 'msg'=>$result]);
        }

        $this->getResultAPIJSON(200, ['result'=>true]);
    }

    // 종료 요청 전송
    public function getCSChatEnd() {
        $roomToken = trim($_POST['roomToken']);
        if(!$roomToken) {
            $aResult = ['result'=>false, 'msg'=>'중요변수가 존재하지 않습니다'];
            $this->getResultAPIJSON(200, $aResult);
        }

        /*$aPostData = [];
        $aPostData['path'] = "expired_session";
        $aPostData['data'] = ['mode'=>'client', 'botid'=>$this->botId, 'roomToken'=>$roomToken];
        if($csId) $aPostData['data']['cs_id'] = $csId;
        $aPostData['data'] = json_encode($aPostData['data'], JSON_UNESCAPED_UNICODE);

        $aResponse = $this->sendCSChatCurl($aPostData);*/

        $nexusAssignData =
            [
                'endPoint'=> $this->chatAPIUrl,
                'path'=> 'expired_session',
                'secretKey'=> $this->secretKey,
                'mode'=>'client',
                'roomToken'=>$roomToken,
                'botId'=>$this->botId
            ];

        $aResponse = $this->csChatService->nexusAssignAPI($nexusAssignData);


        // 챗봇 로그 기록
        $_data = ['vendor'=>$this->vendor, 'botid'=>$this->botId, 'bot'=>$this->bot, 'roomToken'=>$roomToken, 'speaker'=>"user", 'content'=>"chat_end"];
        $this->addCSLog($_data);

        // 전송 결과 확인 (무조건 ok 응답)
        /*
        $_result = ['result'=>false, 'msg'=>''];
        if(!$aResponse['result']) {
            $_result['msg'] = isset($aResponse['msg']) && $aResponse['msg'] ? $aResponse['msg'] : "채팅상담 종료 실패";
        } else {
            $_result['result'] = true;
        }
        */
        $_result['result'] = true;
        $this->getResultAPIJSON(200, $_result);
    }

    // 상담사 채팅 종료 처리
    public function getCheckCSEnd() {
        $this->getCheckHeader();

        $body = trim(file_get_contents('php://input'));
        if(!$body) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>'Not found data.']);
        }

        $aData = json_decode($body, true);
        $_json = getJSONError(json_last_error());
        if($_json !== true) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>$_json]);
        }

        if(!isset($aData['roomToken']) || !trim($aData['roomToken'])) {
            $this->getResultAPIJSON(400, ['result'=>false, 'msg'=>'Not exists roomToken.']);
        }

        // 챗봇 로그 기록
        $_data = ['vendor'=>$this->vendor, 'botid'=>$this->botId, 'bot'=>$this->bot, 'roomToken'=>$aData['roomToken'], 'speaker'=>"cs", 'content'=>"cs_end"];
        $this->addCSLog($_data);

        $aData['action'] = "cs_end";

        // 클라이언트 전송
        $result = $this->sendWebSocket($aData);
        if($result != true) {
            $this->getResultAPIJSON(500, ['result'=>false, 'msg'=>$result]);
        }

        $this->getResultAPIJSON(200, ['result'=>true]);
    }

    public function sendCSChatCurl($postData) {
        if(!isset($postData['path']) || !$postData['path']) exit;
        if(!isset($postData['data']) || !$postData['data']) exit;

        $headers = [];
        $headers[] = "Content-Type: application/json; charset=utf-8";
        if($postData['path'] != "reference") {
            $headers[] = "X-Bottalks-Key: ".$this->secretKey;
        }

        $_log = "[".date("Y-m-d H:i:s")."] request ".getRemoteIP()." url: ".$this->chatAPIUrl."/".$postData['path']." data: ".$postData['data'];
        $this->setRawLog($_log);

        // 로그 전송
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->chatAPIUrl."/".$postData['path']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData['data']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.75 Safari/537.36");
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $_log = "[".date("Y-m-d H:i:s")."] response ".getRemoteIP()." url: ".$this->chatAPIUrl."/".$postData['path']." code: ".$http_code." data: ".$response;
        $this->setRawLog($_log);

        if($http_code == 200 && $response) {
            $aData = json_decode($response, true);
            if($aData['code'] == "200") {
                $aResponse = ['result'=>true];
            } else {
                $aResponse = ['result'=>false, 'msg'=>$aData['message']];
            }
            $aResponse = ['result'=>true];
        } else {
            $aResponse = ['result'=>false, 'msg'=>'chat server not connected.'];
        }
        return $aResponse;
    }

    public function sendWebSocket($data) {
        $data['type'] = "cs";
        $data['role'] = "cs_chat";
        $data['botid'] = isset($data['botid']) && $data['botid'] ? $data['botid'] : $this->botId;
        $_data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $host = $this->socketHost;
        $port = $this->socketPort;
        $path = "/chatbot_".$this->botId;

        if($fp = fsockopen($host, $port, $errno, $errstr, 30)) {
            $_out = "POST ".$path." HTTP/1.1\r\n";
            $_out .="Host: ".$host."\r\n";
            $_out .="Content-Type: application/json\r\n";
            $_out .="Content-Length: ". strlen($_data) ."\r\n";
            $_out .="Connection: Close\r\n\r\n";
            $_out .=$_data;
            fwrite($fp, $_out);
        } else {
            return $errstr." (".$errno.")";
        }
        fclose($fp);
        return true;
    }

    public function getResultAPIJSON($error=200, $aArray=array()) {
        http_response_code($error);
        $json = json_encode($aArray, JSON_UNESCAPED_UNICODE);

        header_remove("Access-Control-Allow-Origin");
        header_remove("Access-Control-Allow-Methods");
        header_remove("Access-Control-Allow-Headers");
        header_remove("Set-Cookie");
        header_remove("Expires");
        header_remove("X-XSS-Protection");
        header_remove("X-Content-Type-Options");
        header_remove("X-Frame-Options");
        header('Content-Type: application/json; charset=utf-8');
        echo $json;
        exit;
    }

    public function addCSLog($data) {
        global $date;

        $ip     = $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $d_regis = $date['totime'] ? $date['totime'] : date('YmdHis');
        $msgid = "";

        if($data['speaker'] == "user") {
            if($data['content'] != "chat_open" && $data['content'] != "chat_end") {
                /*$_wh = "vendor=".$data['vendor']." and bot=".$data['bot']." and speaker='user' and left(d_regis, 8) = '".date('Ymd')."' and (content <> 'chat_open' and content <> 'chat_end')";
                $cnt = getDbCnt('rb_chatbot_csLog','count(*)', $_wh);
                $msgid = "BOT".date('Ymd').sprintf("%05d", ($cnt+1));*/
                $msgid = uniqid();
            }

            $QKEY = "vendor, bot, roomToken, msgid, speaker, userName, userId, userUid, content, ip, d_regis";
            $QVAL = "'".$data['vendor']."','".$data['bot']."','".$data['roomToken']."','$msgid','user','','','0','".$data['content']."','$ip','$d_regis'";
        } else {
            $puid = getDbCnt('rb_chatbot_csLog','max(uid)', "vendor=".$data['vendor']." and bot=".$data['bot']." and roomToken='".$data['roomToken']."' and speaker='user'");

            $QKEY = "vendor, bot, roomToken, puid, speaker, userName, userId, userUid, content, ip, d_regis";
            $QVAL = "'".$data['vendor']."','".$data['bot']."','".$data['roomToken']."','$puid','cs','','','0','".$data['content']."','$ip','$d_regis'";
        }
        getDbInsert('rb_chatbot_csLog',$QKEY,$QVAL);
        return $msgid;
    }

    public function setRawLog($log) {
        $logFile = $_SERVER['DOCUMENT_ROOT']."/_tmp/log/cschat_log_".date("Y-m-d").".txt";
        file_put_contents($logFile, $log."\n", FILE_APPEND);
    }
}
?>