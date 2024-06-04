<?php
class ReserveBasic {
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

    public function __construct() {
        $this->name_stt = true; // 예약자 이름을 자연어로 처리할지 여부
    }

    public function getReserveResponse($data){
        global $m, $table, $chatbot, $callbot;

        $this->bot = $callbot->bot;
        $this->vendor = $callbot->vendor;
        $this->dialog = $callbot->dialog;
        $this->cgroup = $callbot->cgroup;
        $this->botid = $data['botId'];
        $this->msg = trim($data['msg']);
        $this->msg_type = trim($data['msg_type']);
        $this->roomToken = $data['roomToken'];
        $this->userId = $data['userId'];

        // api 조회용
        $this->api_data = array();
        $this->api_data['vendor'] = $this->vendor;
        $this->api_data['bot'] = $this->bot;
        $this->api_data['getParam'] = array();
        $this->api_data['postParam'] = array();

        $result = array();

        if($this->msg != "") {
            $userChat = array();
            $userChat['printType'] ='T';
            $userChat['userId'] = $this->userId;
            $userChat['content'] = $this->msg;
            $userLastChat = $chatbot->addChatLog($userChat);
            $this->last_chat = $userLastChat['last_chat'];

            $r_data = $data['r_data'];
            $r_data['last_chat'] = $this->last_chat;
            $r_data['msg_type'] = $data['msg_type'];

            $result = $this->getReserveRequest($r_data);
        }

        return $result;
    }

    public function getReserveARSLink($r_data) {
        global $g, $m, $table, $chatbot;

        // 예약 링크 주소 문자 전송
        if(strpos($_SERVER['HTTP_HOST'], $chatbot->botid) !== false) {
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

    public function getReserveErrorForword($r_data, $msg="") {
        global $m, $table, $chatbot;
        /*
        $next_status = array('action'=>'routing', 'value'=>'0256567878');
        $r_data['step'] = 'finish';
        $response['next_status'] = $next_status;
        $response['content'] = $msg == "" ? "예약이 원활하게 진행되지 못해 죄송합니다. 상담원을 연결해드리겠습니다. 잠시만 대기해주세요." : $msg;
        */
        $next_status = array('action'=>'hangup');
        $r_data['step'] = 'finish';
        $response['next_status'] = $next_status;
        $response['content'] = $msg == "" ? "예약이 원활하게 진행되지 못해 죄송합니다. 고객센터로 연락 부탁드립니다." : $msg;

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
	    if($r_data['step'] == "ask_new") {
	        $aYesAdd = array("그럴", "예약", "처음", "진행");
	    }
	    if($r_data['step'] == "ask_modify") {
	        if($r_data['action'] == "modify") {
	            $aYesAdd = array("그럴", "변경", "바꿀", "바꿔", "수정");
	        }
	        if($r_data['action'] == "cancel") {
	            $aYesAdd = array("취소");
	        }
	    }
	    if($r_data['step'] == "ask_cancel") {
	        $aYesAdd = array("그럴", "취소");
	    }
	    if($r_data['step'] == "ask_reserve" || $r_data['step'] == "ask_date") {
	        $aYesAdd = array("그럴", "그렇게", "예약");
	    }
	    if($r_data['step'] == "confirm") {
	        if($r_data['action'] == "request") {
	            $aYesAdd = array("예약", "진행", "그렇게");
	            if($r_data['finish_modify'] == true) {
	                array_push($aYesAdd, "변경", "바꿔", "수정");
	            }
	        }
	        if($r_data['action'] == "modify" && $r_data['finish_modify'] == true) {
	            $aYesAdd = array("변경", "바꿔", "수정");
	        }
	        if($r_data['action'] == "cancel") {
	            $aYesAdd = array("취소");
	        }
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
	public function getNearestTimes($sys_time, $times, $type='date') {
        $timeDiff = $type == "date" ? 2678400 : 100000;
        $nCntTimes = count($times);
        $result = array();
        foreach($times as $key=>$time) {
            $curDiff = abs(strtotime($time)-strtotime($sys_time));
            if($curDiff < $timeDiff){
                $timeDiff = $curDiff;
                $cKey = $key;
            }
        }
        $cKey = isset($cKey) && $cKey ? $cKey : 0;
        for($i=0; $i<2; $i++){
            $_cKey = $cKey++;
            if($_cKey >= $nCntTimes){
                $_cKey = ($_cKey-2);
                $result[] = $times[$_cKey];
            } else {
                $result[] = $times[$_cKey];
            }
        }
        sort($result);
        return $result;
    }
    public function getFinishReset($r_data) {
        $r_data['sys_fast'] = false;
        $r_data['intentName'] = "";
        $r_data['temp_sys_date'] = "";
        $r_data['temp_sys_week'] = "";
        $r_data['temp_sys_time'] = "";
        $r_data['uname_temp'] = "";
        return $r_data;
    }

}
?>