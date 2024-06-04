<?php
class Disability {
    public $chatbot;
    public $callbot;
    public $module;

    public $vendor;
    public $bot;
    public $botid;
    public $dialog;
    public $msg;
    public $msg_type;
    public $roomToken;
    public $userId;
    public $last_chat;
    public $api_data;
    public $name_stt;
    public $cgroup;
    public $apiURL;

    public function __construct() {
        global $chatbot, $callbot;

        $this->chatbot = $chatbot;
        $this->callbot = $callbot;
        $this->module = $this->chatbot->module;
        $this->bot = $this->callbot->bot;
        $this->vendor = $this->callbot->vendor;
        $this->dialog = $this->callbot->dialog;
        $this->cgroup = $this->callbot->cgroup;
    }

    public function getDisabilityResponse($data){
        global $m, $table;

        $this->botid = $data['botId'];
        $this->msg = trim($data['msg']);
        $this->msg_type = trim($data['msg_type']);
        $this->roomToken = $data['roomToken'];
        $this->userId = $data['userId'];

        $result = $response = array();

        if($this->msg != "") {
            $userChat = array();
            $userChat['printType'] ='T';
            $userChat['userId'] = $this->userId;
            $userChat['content'] = $this->msg;
            $userLastChat = $this->chatbot->addChatLog($userChat);
            $this->last_chat = $userLastChat['last_chat'];

            $r_data = $data['r_data'];
            $r_data['last_chat'] = $this->last_chat;
            $r_data['msg_type'] = $data['msg_type'];

            // hangup 들어왔을 경우 처리
            if($r_data['msg_type'] == "hangup") {
                getDbUpdate($table[$m.'token'], "r_data=''", "bot='".$this->bot."' and access_mod='callInput' and access_token='".$this->callbot->accessToken."'");
                exit;
            } else if($r_data['msg_type'] == "noinput" || $r_data['msg_type'] == "sttfail") {
                $response['next_status'] = $r_data['next_status'];

                $r_data['content'] = str_replace("죄송합니다.", "", $r_data['content']);
                $r_data['content'] = str_replace("네. 알겠습니다.", "", $r_data['content']);
                $r_data['content'] = str_replace("확인 감사합니다.", "", $r_data['content']);
                $r_data['content'] = str_replace("장애 접수를 진행하겠습니다.", "", $r_data['content']);
                $r_data['content'] = str_replace("감사합니다.", "", $r_data['content']);
                $r_data['content'] = str_replace("네. ", "", $r_data['content']);
                $response['content'] = "죄송합니다. 다시 내용을 듣고 말씀해주세요. ".$r_data['content'];
                $response['r_data'] = $r_data;
                $response['type'] = "text";
                $response['bargein'] = false;
                $response['r_data']['intentName'] = "";

                $callbot->getCallbotBotChatLog($response['content']);
                $result[] = $response;
            } else {
                $result = $this->getCallbotRequest($r_data);
            }
        }
        return $result;
    }
    //-------------------------------------------------------------------------
    public function getCallbotRequest($r_data) {
        global $m, $table, $callbot;

        $result = $response = array();

        switch($r_data['step']) {
            case('start') :
                // 소속(부서)
                $r_data['upart'] = $this->msg;
                $r_data['step'] = 'ask_name';
                $response['content'] = "이름을 말씀해주세요.";
            break;

            case('ask_name') :
                // 소속(부서)
                $r_data['uname'] = $this->msg;
                $r_data['step'] = 'ask_phone';
                $response['content'] = "휴대폰 번호를 010 부터 말씀해주세요.";
            break;

            case('ask_phone') :
                // 소속(부서)
                $r_data['uphone'] = $this->msg;
                $r_data['step'] = 'ask_content';
                $response['content'] = "장애 증상을 구체적으로 말씀해주세요.";
            break;

            case('ask_content') :
                // 소속(부서)
                $r_data['ucontent'] = $this->msg;
                $r_data['step'] = 'finish';

                $d_regis = date("YmdHis");
                $QKEY = "vendor, bot, roomToken, category, name, phone, content, addval, status, d_regis";
                $QVAL = "'".$this->vendor."', '".$this->bot."', '".$this->roomToken."', 'disability', '".$r_data['uname']."', '".$r_data['uphone']."', '".$r_data['ucontent']."', '".$r_data['upart']."', 'ready', '$d_regis'";
                getDbInsert('rb_chatbot_reserve', $QKEY, $QVAL);

                $response['content'] = "감사합니다. 정상적으로 접수되었습니다.";
            break;
        }

        $next_status = array('action'=>'recognize');
        $r_data['next_status'] = $next_status;
        $r_data['content'] = $response['content'];

        $response['r_data'] = $r_data;
        $response['next_status'] = $next_status;
        $response['type'] = "text";
        $response['bargein'] = false;

        // 봇응답 로그 기록
        $callbot->getCallbotBotChatLog($response['content']);

        $result[] = $response;
        return $result;
    }

    //-------------------------------------------------------------------------

    public function getCallbotARSLink($r_data) {
        global $g, $m, $table;

        // 예약 링크 주소 문자 전송
        if(strpos($_SERVER['HTTP_HOST'], $this->chatbot->botid) !== false) {
            $ars_link = $g['url_host'].'?user_input=rsv_'.$r_data['action'];
        } else {
            $ars_link = $g['url_host'].'/R2'.$this->botid.'?user_input=rsv_'.$r_data['action'];
        }

        $response = array();
        $next_status = array('action'=>'ars', 'value'=>$ars_link);
        $response['next_status'] = $next_status;
        $response['content'] = "문자로 보이는 ARS 예약 링크를 보내드렸습니다. 문자의 링크를 눌러 예약을 진행해주세요.";

        $r_data['step'] = 'finish';
        $r_data['next_status'] = $next_status;
        $r_data['content'] = $response['content'];
        $response['r_data'] = $this->getFinishReset($r_data);
        return $response;
    }

    public function getForwordToCSR($r_data, $msg="") {
        $next_status = array('action'=>'hangup');
        $msg = $msg == "" ? "죄송합니다. 고객센터로 연락 부탁드립니다." : $msg;

        $r_data['step'] = 'finish';
        $r_data['next_status'] = $next_status;
        $r_data['content'] = $msg;

        $response['next_status'] = $next_status;
        $response['content'] = $msg;
        $response['r_data'] = $this->getFinishReset($r_data);
        return $response;
    }

    public function getDateKorean($dRegDate, $type="", $week="") {
		$aDate = explode("-", $dRegDate);
		$_date = $type == "no_year" ? (int)$aDate[1]."월 ".(int)$aDate[2]."일" : $aDate[0]."년 ".(int)$aDate[1]."월 ".(int)$aDate[2]."일";
		if($week == "with_week") {
		    $_date .=" ".$this->getWeekKorean($dRegDate)."요일";
		}
		return $_date;
	}
	public function getTimeKorean($dTime) {
		$time = date('a g i', strtotime($dTime));
        $time = str_replace("pm", "오후", str_replace("am", "오전", $time));
        $_time = explode(" ", $time);
        $time = $_time[0]." ".$_time[1]."시".((int)$_time[2] > 0 ? " ".(int)$_time[2]."분" : "");
		return $time;
	}
	public function getWeekKorean($dRegDate) {
		$aWeek = array("일", "월", "화", "수", "목", "금", "토");
		return $aWeek[date("w", strtotime($dRegDate))];
	}
	public function getYesorNo($msg, $r_data) {
	    $result = false;
	    $aYes = array("네", "넵", "예", "옙", "응", "그래", "맞아", "맞어", "어", "맞습", "yes", "예스"); //"해줘", "해주세",

	    if($r_data['step'] == "ask_new") $aYesAdd = array("그럴", "예약", "처음", "진행");
	    if($r_data['step'] == "ask_modify") {
	        if($r_data['action'] == "modify") $aYesAdd = array("그럴", "변경", "바꿀", "바꿔", "수정");
	        if($r_data['action'] == "cancel") $aYesAdd = array("취소");
	    }
	    if($r_data['step'] == "ask_cancel") $aYesAdd = array("그럴", "취소");
	    if($r_data['step'] == "ask_reserve" || $r_data['step'] == "ask_date") $aYesAdd = array("그럴", "그렇게", "예약");
	    if($r_data['step'] == "confirm") {
	        if($r_data['action'] == "request") {
	            $aYesAdd = array("예약", "진행", "그렇게");
	            if($r_data['finish_modify'] == true) array_push($aYesAdd, "변경", "바꿔", "수정");
	        }
	        if($r_data['action'] == "modify" && $r_data['finish_modify'] == true) $aYesAdd = array("변경", "바꿔", "수정");
	        if($r_data['action'] == "cancel") $aYesAdd = array("취소");
	    }
	    $aYes = isset($aYesAdd) && count($aYesAdd) > 0 ? array_merge($aYes, $aYesAdd) : $aYes;
	    preg_match_all("/".implode("|", $aYes)."/iu", $msg, $matches);

	    // 한글자 엔터티(예, 응..)와 발화문 비교 (예술, 공예, 어제.. => no match), (그치만 응, 응응 => match)
	    $_matches = array();
	    if(count($matches[0]) > 0) {
	        foreach($matches[0] as $match) {
	            // 매칭된 엔터티가 한글자일 경우 발화문의 매칭 글자 앞뒤로 공백있는지 여부 확인
	            if(mb_strlen($match) == 1) {
	                $_pos = mb_strpos($msg, $match);
	                if($_pos == 0) {
	                    $_posNext = mb_substr($msg, ($_pos+1), 1);
	                    if($_posNext == ' ' || $_posNext == $_match) $_matches[] = $match;
	                } else if($_pos == (mb_strlen($msg)-1)) {
	                    $_posPrev = mb_substr($msg, ($_pos-1), 1);
	                    if($_posPrev == ' ') $_matches[] = $match;
	                } else {
	                    $_posPrev = mb_substr($msg, ($_pos-1), 1);
	                    $_posNext = mb_substr($msg, ($_pos+1), 1);
	                    if($_posPrev == ' ' && ($_posNext == ' ' || $_posNext == $match)) $_matches[] = $match;
	                }
	            } else {
	                $_matches[] = $match;
	            }
	        }
	        $_matches = array_unique($_matches);
	        $result = count($_matches) > 0 ? true : false;
	    }
	    return $result;
	}
}
?>