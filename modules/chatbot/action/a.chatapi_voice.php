<?php
// REST API 챗봇 응답
/*
클라우드 : https://봇아이디.chatbot.bottalks.co.kr/chatapi_voice
일반접속 : https://챗봇주소/chatapi_voice/봇아이디

발화 전송 :
1. 환영 : {"type":"say_hello", "msg":"hi", "tts":"naver", "speaker":"njooahn"}  or {"type":"init"}
2. 대화 : {"type":"text", "msg":"발화문", "roomtoken":"sadfds456", "tts":"naver", "speaker":"njooahn"}
3. 버튼 : {"type":"hmenu", "msg': "병원소개", "hmenu_uid": 12346};
4. 로그 : {"type":"hmenu_log" or "card_log", "title": "병원안내", "msg": "병원안내"}
* tts 파라미터 존재할 경우 tts 벤더 api 이용
*/
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");

if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

class VoiceBot {
	public $botId;
	public $url_host;
	public $webhookRequest;
	public $aRequest;
	public $aOutputs;
	public $msg_type;
	public $roomToken;
	public $ttsVendor;
	public $ttsSpeaker;
	public $ttsSaveDir;

	public function __construct(){
		global $chatbot,$g;

		$shost = $_SERVER['HTTP_HOST'];
		if ($shost != "sys.chatbot.bottalks.co.kr" && strpos($shost, "chatbot.bottalks.co.kr") !== false) {
			$aHost = explode(".", $shost);
			$this->botId = $aHost[0];
		} else {
			$url_arr = explode('/',$_SERVER['REQUEST_URI']);
			$this->botId = $url_arr[2];
		}
		$this->url_host = $g['url_host'];
		$this->aOutputs = array();
		$this->ttsSaveDir = $g['path_tmp']."tts";
		$this->ttsSpeaker = "njooahn";

		$this->webhookRequest = file_get_contents('php://input'); // {"msg": "안녕"}
		$this->aRequest = json_decode($this->webhookRequest, true);

		if($this->aRequest['type'] == "init" || $this->aRequest['type'] == "say_hello" || $this->aRequest['msg'] == "say_hello") {
		    $this->msg_type = "say_hello";
            $this->aRequest['msg'] = "hi";
        } else {
            $this->msg_type = $this->aRequest['type'] ? $this->aRequest['type'] : "text";
        }

        if(!isset($this->aRequest['roomtoken']) || !$this->aRequest['roomtoken']) {
            $this->roomToken = $this->getRoomToken();
        } else {
            $this->roomToken = $this->aRequest['roomtoken'];
        }

		if(isset($this->aRequest['tts']) && $this->aRequest['tts']) {
		    $this->ttsVendor = $this->aRequest['tts'];
		    $this->ttsSpeaker = $this->aRequest['speaker'] ? $this->aRequest['speaker'] : $this->ttsSpeaker;
		}
	}

	public function getRoomToken() {
	    return substr(md5(uniqid(mt_rand())), 0, 20);
	}

	public function getProcessInput($data){
        global $chatbot, $g;

        if($data['msg_type'] == "hmenu") {
            $reply = $chatbot->getMenuRespond($data);
        } else {
            $reply = $chatbot->getApiResponse($data);
        }
        $response = getBotApiResponseContent($reply);
        if($response[0]['type'] == "node" && $response[0]['content']) {
            $data['node'] = $response[0]['content'];
            $reply = $chatbot->getApiResponse($data);
            $response = getBotApiResponseContent($reply);
        }

        $ttsText = "";
        $_response = array();
        foreach($response as $idx=>$resItem) {
            if($resItem['type'] == "text" || $resItem['type'] == "form") {
                $_temp = explode("[code]", $resItem['content']);
                if(trim($_temp[1])) {
                    $resItem['code'] = $_temp[1];
                    $resItem['content'] = $_temp[0];
                }
                $ttsText .=$resItem['content'];
            } else if($resItem['type'] == "img") {
                $_content = $_size = "";
                if(is_array($resItem['content'])) {
                    for($i=0, $nCnt=count($resItem['content']); $i<$nCnt; $i++) {
                        $_content = str_replace($g['url_host'], "", $resItem['content'][$i]['img_url']);
                        if(file_exists($_SERVER['DOCUMENT_ROOT'].$_content)) {
                            $_size = getimagesize($_SERVER['DOCUMENT_ROOT'].$_content);
                            $resItem['content'][$i]['size'] = $_size[0]."x".$_size[1];
                        }
                    }
                } else {
                    $_content = str_replace("./files/", "/files/", $resItem['content']);
                    if(file_exists($_SERVER['DOCUMENT_ROOT'].$_content)) {
                        $_size = getimagesize($_SERVER['DOCUMENT_ROOT'].$_content);
                        $resItem['content'] = $_content;
                        $resItem['size'] = $_size[0]."x".$_size[1];
                    }
                }
            }
            $_response[] = $resItem;
        }

        // tts용 응답 텍스트
        if($this->ttsVendor) {
            if($this->ttsVendor == "naver" && $ttsText != "") {
                $ttsAudio = $this->getNaverTTSVoice($ttsText);
                if($ttsAudio) $_response['ttsAudio'] = $ttsAudio;
            }
        }
        $response = $this->getReplaceTypeToText($_response);
        return $response;
    }

    public function getLogInput($data) {
        global $chatbot;

        $B = $chatbot->getBotDataFromId($data['botId']);

        $_data = array();
        $_data['bot'] = $B['bot'];
        $_data['botUid'] = $chatbot->botuid = $B['bot'];
        $_data['vendor'] = $chatbot->vendor = $B['vendor'];
        $_data['botActive'] = $chatbot->botActive = $B['botActive'];

        $_data['botid'] = $chatbot->botid = $data['botId'];
        $_data['roomToken'] = $chatbot->roomToken = $data['roomToken'];
        $_data['userId'] = $chatbot->userId = $data['userId'];

        $_data['chatType'] = $data['chatType'] ? $data['chatType'] : '';
        $_data['printType'] = $data['msg_type'] == "hmenu_log" ? "B" : "C";
        $_data['content'] = $data['msg'];
        $chatbot->addChatLog($_data);
    }

    public function getReplaceTypeToText($array) {
        if (is_array($array)) {
            $newArr = array();
            foreach ($array as $k => $v) {
                $value = ($k == 'type' && $v == 'form' || $v == 'tel') ? 'text' : $v;
                $newArr[$k] = is_array($v) ? $this->getReplaceTypeToText($v) : $value;
            }
            return $newArr;
        }
        return $array;
    }

    public function getOldFileRemove($botid) {
        if(!$this->botId) return false;

        $nowTime = time();
        if(is_dir($this->ttsSaveDir)) {
            $oDir = dir($this->ttsSaveDir);
            while ($fileName = $oDir->read()) {
                if ($fileName != "." && $fileName != "..") {
                    if(strpos($fileName, "tts_voice_".$this->botId) !== false) {
                        $nFileTime = filemtime($this->ttsSaveDir."/".$fileName);
                        if($nFileTime < $nowTime) @unlink($this->ttsSaveDir."/".$fileName);
                    }
                }
            }
        }
    }

    public function getNaverTTSVoice($str) {
        global $g;

        $this->getOldFileRemove();
        if(!trim($str)) return "";

        $ttsAudio = "";
        $apiURL = "https://naveropenapi.apigw.ntruss.com/tts-premium/v1/tts";

        $aHeader = array();
        $aHeader[] = "X-NCP-APIGW-API-KEY-ID: ym5yndqra0";
        $aHeader[] = "X-NCP-APIGW-API-KEY: w6agO1gxdjlZUoKwALhskbPLJtHZRSBTKFE1D1dg";
        $aHeader[] = "Content-Type: application/x-www-form-urlencoded";

        $_data = array();
        $_data['postParam'] = array();
        $_data['postParam']['speaker'] = $this->ttsSpeaker; //nminsang, nsinu, njinho, njihun, njooahn, nseonghoon,
        $_data['postParam']['text'] = $str;
        $_data['postParam']['volume'] = 0; // -5 ~ 5
        $_data['postParam']['speed'] = 0; // -5 ~ 5
        $_data['postParam']['pitch'] = 0; // -5 ~ 5
        $_data['postParam']['format'] = "mp3"; // mp3, wav
        //$_data['getParam']['sampling-rate'] = "24000"; // (wav시에만 8000, 16000, 24000, 48000);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_data['postParam']));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.75 Safari/537.36");
        $response = curl_exec($ch);
        $aResInfo = curl_getinfo($ch);
        curl_close($ch);
        if($aResInfo['http_code'] == 200) {
            $tts_file = "tts_voice_".$this->botId."_".date("ymdHis").".mp3";
            $_file = file_put_contents($this->ttsSaveDir."/".$tts_file, $response);
            if($_file !== false) {
                $ttsAudio = $g['url_host']."/_tmp/tts/".$tts_file;
            }
        }
        return $ttsAudio;
    }
}

$bot = new VoiceBot();

$data = array();
$data['botId'] = $bot->botId;//'vdCEhUke2L1G5x6';
$data['msg_type'] = $bot->msg_type;
$data['msg'] = trim($bot->aRequest['msg']);
$data['title'] = isset($bot->aRequest['title']) ? trim($bot->aRequest['title']) : '';
$data['uid'] = isset($bot->aRequest['hmenu_uid']) ? trim($bot->aRequest['hmenu_uid']) : '';
$data['roomToken'] = $bot->roomToken;
$data['userId'] = $userId;
$data['chatType'] = 'V';
$data['channel'] = 'voice';
$data['api'] = true;

if($data['msg']) {
    // 한국야쿠르트 음성봇 관련
    if($bot->botId == "f2adb5e364a9cc5") {
        if($bot->msg_type == "say_hello") {
            $response = array("type"=>"init", "msg"=>"", "roomtoken"=>$bot->roomToken);
        } else {
            $response = $bot->getProcessInput($data);
            $response = count($response) == 1 ? $response[0] : $response;
            $response = array_key_replace($response, array('content'=>'msg'));
            $response['roomtoken'] = $bot->roomToken;
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);

    } else {

        if(strpos($data['msg_type'], "_log") !== false) {
            $bot->getLogInput($data);
        } else {
            $response = $bot->getProcessInput($data);
            $result = array();
            $result['response'] = $response;
            $result['roomtoken'] = $bot->roomToken;

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }
    }
}
exit;
?>
