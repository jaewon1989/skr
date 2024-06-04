<?php
// 카카오톡 플러스친구 관리자(https://center-pf.kakao.com) 에서 스마트 채팅
// API형에 http://bottalks.co.kr 주소 추가하시면 됩니다.

// 카카오톡은 URL 뒤에 /keyboard 와 /message를 사용합니다. (.htaccess 수정해서 새로 맵핑)
// keyboard 주소는 유저가 처음 채팅방에 들어와서 말을 했을때 띄울 메뉴 구성을 출력하게됩니다.
// message 주소는 유저가 추후 메시지를 보냈을때 처리하게되며, json 형태로 들어오게되므로 아래줄 $input에 담아서 처리하였습니다.

// 카카오톡 어플을 켜면 맨위에 검색창에 융규 입력하여 플러스친구 추가 후 채팅
// 기능은 "이미지" / "링크" / "컴포넌트" 등이 있습니다. 일반 입력시 텍스트내용과 유저 번호를 반환합니다.

session_start();
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

// 카카오톡 서버에서만 접근 가능 (보안처리)
/*
$ip_set = explode('.', $_SERVER['REMOTE_ADDR']);
if( substr($_SERVER['REMOTE_ADDR'], 0, 10) != '110.76.143' && ($ip_set[3] < 234 or $ip_set[3] > 236) ) {
	//echo 'Invalid Access'; exit();
};
*/

//#############################################################################################
// 중요!!! 카카오톡 오픈빌더 Skill URL 입력 형식 : https://chatbot.bottalks.co.kr/chatapi_kakao/봇ID
//#############################################################################################

class KakaoBot {
	public $webhookResponse; // json으로 넘어온 정보 담기
	public $webhookEventObject; // json으로 넘어온 정보 파싱
	public $botId;
	public $url_host;
	public $input_msg, $aOutputs, $aQuickReplies;

	public function __construct(){
		global $chatbot,$g;

		/*
		$shost = $_SERVER['HTTP_HOST'];
		if (strpos($shost, $g['chatbot_host']) !== false) {
			$aHost = explode(".", $shost);
			$this->botId = $aHost[0];
		} else {
			$url_arr = explode('/',$_SERVER['REQUEST_URI']);
			$this->botId = $url_arr[2];
		}
		*/
		$url_arr = explode('/',$_SERVER['REQUEST_URI']);
		$this->botId = $url_arr[2];

		$this->url_host = 'https://'.$this->botId.'.'.$g['chatbot_host'];
		$this->webhookResponse = file_get_contents('php://input');
		$this->webhookEventObject = json_decode($this->webhookResponse);
		$this->aOutputs = array();
		$this->aQuickReplies = array();
	}

	public function pushLog($data) {
		global $g;
		$data = "[".date("Y-m-d H:i:s")."] ".$data;
		//file_put_contents($_SERVER['DOCUMENT_ROOT']."/_tmp/cache/kakao.txt", $data."\n", FILE_APPEND);
	}

	// 유저가 입력타입 체크  : button, typing
	public function getInputType(){
		$webhook = $this->webhookEventObject;
		if($webhook->userRequest->event->name == 'welcome_event') {
		    $result = 'welcome_event';
		} else {
		    $result = $webhook->userRequest->user->type;
		}
		return $result;
	}

	// 유저가 채팅창에 입력해서 보내진 내용 텍스트
	public function getMessageText(){
		$webhook = $this->webhookEventObject;
		$result = $webhook->userRequest->utterance;
		$result = preg_replace('/\r\n|\r|\n/', '', trim($result));
		$this->input_msg = $result;
		return $result;
	}

	// 유저가 채팅창에 선택메뉴 선택시 보내진 코드
	public function getSelectedCode(){
		$webhook = $this->webhookEventObject;
		$result = $webhook->action->clientExtra->hMenu;
		return $result;
	}

	// 메시지 보낸 유저의 식별 아이디
	public function getUserId(){
		$webhook = $this->webhookEventObject;
		$result = $webhook->userRequest->user->id;
		return $result;
	}

	public function pushText($data) {
		$this->aOutputs[] = array(
			"simpleText"=>array("text"=>str_ireplace("<br>", "\n", $data))
		);
	}

	public function pushCard($data) {
		$aCardItem = array();
		foreach ($data as $key => $jsonData) {
			$aCard = json_decode($jsonData);
			$img_url = str_replace("http://chatbot.bottalks.co.kr", "", $aCard->img_url);

			$aCardItem[] = array(
				"title"=>$aCard->title,
				"description"=>$aCard->summary,
				"thumbnail"=>array(
					"imageUrl"=>$this->url_host.stripslashes($img_url)
				),
				"buttons"=>array(
					array("action"=>"webLink", "label"=>"이동하기", "webLinkUrl"=>$aCard->res_link)
				)
			);
		}

		$this->aOutputs[] = array(
			"carousel"=>array("type"=>"basicCard", "items"=>$aCardItem)
		);
	}

	public function pushLink($type, $content) {
		$title = $type == "tel" ? "버튼을 클릭하여 전화연결해주세요." : "버튼을 클릭하여 이동해주세요.";
		$action = $type == "tel" ? "phone" : "webLink";
		$label = $type == "tel" ? "전화연결" : "이동하기";
		$linkField = $type == "tel" ? "phoneNumber" : "webLinkUrl";
		$this->aOutputs[] = array(
			"basicCard"=>array(
				"title"=>$title,
				"buttons"=>array(
					array("action"=>$action, "label"=>$label, $linkField=>$content)
				)
			)
		);
	}

	public function pushImage($data) {
		$aImgItem = array();
		if (is_array($data)) {
			foreach ($data as $key => $jsonData) {
				$aImg = json_decode($jsonData);
				$img_url = str_replace("http://chatbot.bottalks.co.kr", "", $aImg->img_url);

				$aImgItem[] = array(
					"thumbnail"=>array(
					    "imageUrl"=>$this->url_host.stripslashes($img_url),
					    "link"=>array("web"=>$this->url_host.stripslashes($img_url))
					)
				);
			}
		} else {
		    $aImgItem[] = array(
				"thumbnail"=>array(
				    "imageUrl"=>$this->url_host.stripslashes($data),
				    "link"=>array("web"=>$this->url_host.stripslashes($data))
				)
			);
		}
		if(count($aImgItem) > 1) {
			$this->aOutputs[] = array(
				"carousel"=>array("type"=>"basicCard", "items"=>$aImgItem)
			);
		}else{
			$this->aOutputs[] = array(
				"basicCard"=>$aImgItem[0]
			);
		}
	}

	public function pushHMenu($data) {
		foreach ($data as $key => $jsonData) {
			$menu = json_decode($jsonData);
			$menuText = $menu->link ? $menu->link : $menu->title;
			$this->aQuickReplies[] = array("label"=>$menu->title, "action"=>"message", "messageText"=>$menu->title, "extra"=>array("hMenu"=>$menu->uid));
		}
	}

	public function pushMenuRespond($_data) {
		global $chatbot;

		$result = $_data['result'];
		$data = $_data['data'];

		foreach ($result as $item) {
			for($i=0, $nCnt=count($item); $i<$nCnt; $i++){
				$type = $item[$i]['type'];
				$content = $item[$i]['content'];
				if($type=='text' || $type=='form'){
					$this->pushText($content);
				}else if($type=='img'){
					$this->pushImage($content);
				}else if($type=='link' || $type=='tel'){
					$this->pushLink($type, $content);
				}else if($type=='node'){
					$data['node'] = $content;
					$this->getNodeRespond($data);
			    }
			}
		}
	}

	public function getNodeRespond($data){
		global $g, $chatbot;
		$result = $chatbot->getApiResponse($data);

		//$_log = json_encode($result, JSON_UNESCAPED_UNICODE);
		//file_put_contents($_SERVER['DOCUMENT_ROOT']."/_tmp/cache/kakao.txt", $_log."\n", FILE_APPEND);

		foreach ($result as $resItem) {
			$type = $resItem['type'];
			$content = $resItem['content'];
			if($type=='text'){
				$this->pushText($content);
			}else if($type=='if'){
				$_data = array();
				$_data['data'] = $data;
				$_data['result'] = $content;
				$this->pushMenuRespond($_data);

			}else if($type=='card'){
				$this->pushCard($content);

			}else if($type=='img'){
				$this->pushImage($content);

			}else if($type=='hMenu'){
				$this->pushHMenu($content);
			}
		}
	}

	public function pushResult() {
		$aResponse = array();
		$aResponse['version'] = "2.0";
		$aResponse['template'] = array();
		if (count($this->aOutputs) == 0) {
			$this->pushText($this->input_msg);
		}
		$aResponse['template']['outputs'] = $this->aOutputs;

		if (count($this->aQuickReplies) > 0) {
			$aResponse['template']['quickReplies'] = $this->aQuickReplies;
		}

		$this->pushLog(json_encode($aResponse, JSON_UNESCAPED_UNICODE));

		echo json_encode($aResponse, JSON_UNESCAPED_UNICODE);
	}
}

$receive_msg = $hMenuUid = "";

// 라인 봇 객체 생성
$bot = new KakaoBot();

// 로그 기록 --------------------
$json_str = file_get_contents('php://input');
$bot->pushLog($json_str);
//--------------------------------

$inputType = $bot->getInputType();
$receive_msg = preg_replace('/\r\n|\r|\n/', '', $bot->getMessageText());
$hMenuUid = $bot->getSelectedCode();
$userId = $bot->getUserId();

$data = array();
$data['botId'] = $bot->botId;//'vdCEhUke2L1G5x6';
$data['msg'] = $receive_msg;
$data['userId'] = $userId;
$data['chatType'] = 'K';
$data['channel'] = 'kakao';
$data['api'] = true;

if($inputType == 'welcome_event' || $receive_msg == '대화시작하기') {
    $data['msg'] = 'hi';
    $data['msg_type'] = 'say_hello';
}

if ($hMenuUid) {
	$data['uid'] = $hMenuUid;
	$result = $chatbot->getMenuRespond($data);
	if($result[0]){
		$_data = array();
		$_data['data'] = $data;
		$_data['result'] = $result;
		$bot->pushMenuRespond($_data);
	} else {
		$bot->getNodeRespond($data); // content 없는 경우 그냥 입력한 것으로 간주
	}
	$bot->pushResult();
} else {
	if ($receive_msg) {
		if(!preg_match("/^http/i", $receive_msg)) {
			$bot->getNodeRespond($data);
		}
		$bot->pushResult();
	}
}
exit;


/*
// 텍스트
$aOutputs = array();
$aOutputs[] = array(
	"simpleText"=>array(
		"text"=>"안녕하세요"
	)
);
$aResponse = array();
$aResponse['version'] = "2.0";
$aResponse['template'] = array();
$aResponse['template']['outputs'] = $aOutputs;

$aOutputs = array();
$aOutputs[] = array("label"=>"테스트1", "action"=>"message", "messageText"=>"테스트1의 텍스트");
$aOutputs[] = array("label"=>"테스트2", "action"=>"message", "messageText"=>"테스트2의 텍스트");
$aOutputs[] = array("label"=>"테스트3", "action"=>"message", "messageText"=>"테스트3의 텍스트");
$aResponse['template']['quickReplies'] = $aOutputs;
echo json_encode($aResponse, JSON_UNESCAPED_UNICODE);
exit;

// 이미지
$aOutputs = array();
$aOutputs[] = array(
	"simpleImage"=>array(
		"imageUrl"=>$g['url_root']."/files/chatbot/58/2019/01/03/4e18c1ebd27574349173ac116bd1478c145117.png", "altText"=>"테스트 이미지"
	)
);

// 한장짜리 기본카드
$aOutputs = array();
$aOutputs[] = array(
	"basicCard"=>array(
		"title"=>"병원소개",
		"description"=>"저희 병원을 소개해드립니다.",
		"thumbnail"=>array(
			"imageUrl"=>$g['url_root']."/files/chatbot/58/2019/01/03/4e18c1ebd27574349173ac116bd1478c145117.png", "fixedRatio"=>false
		),
		"buttons"=>array(
			array("action"=>"webLink", "label"=>"이동하기", "webLinkUrl"=>"http://sb.its-me.co.kr/mobile/sub/intro/intro.php")
		)
	)
);

// 케로셀 카드
$aCardItem = array();
$aCardItem[] = array(
	"title"=>"병원소개",
	"description"=>"저희 병원을 소개해드립니다.",
	"thumbnail"=>array(
		"imageUrl"=>$g['url_root']."/files/chatbot/58/2019/01/03/4e18c1ebd27574349173ac116bd1478c145117.png", "fixedRatio"=>false
	),
	"buttons"=>array(
		array("action"=>"webLink", "label"=>"이동하기", "webLinkUrl"=>"http://sb.its-me.co.kr/mobile/sub/intro/intro.php")
	)
);
$aCardItem[] = array(
	"title"=>"의료진 소개",
	"description"=>"의료진을 소개해드립니다.",
	"thumbnail"=>array(
		"imageUrl"=>$g['url_root']."/files/chatbot/58/2019/01/03/73bb3bf21a1f25fd1e640f4d9da52535145148.png", "fixedRatio"=>false
	),
	"buttons"=>array(
		array("action"=>"webLink", "label"=>"이동하기", "webLinkUrl"=>"http://sb.its-me.co.kr/mobile/sub/intro/team.php")
	)
);
$aCardItem[] = array(
	"title"=>"진행중인 이벤트",
	"description"=>"진행중인 이벤트를 알려드립니다.",
	"thumbnail"=>array(
		"imageUrl"=>$g['url_root']."/files/chatbot/58/2019/01/03/95fd557c10c8066b3d7082b00285cb2c152355.png", "fixedRatio"=>false
	),
	"buttons"=>array(
		array("action"=>"webLink", "label"=>"이동하기", "webLinkUrl"=>"http://sb.its-me.co.kr/mobile/reserv/location.php")
	)
);

$aOutputs = array();
$aOutputs[] = array(
	"simpleText"=>array(
		"text"=>"안녕하세요"
	)
);
$aOutputs[] = array(
	"carousel"=>array(
		"type"=>"basicCard",
		"header"=>array("title"=>"제목입니다"),
		"items"=>$aCardItem
	)
);

$aResponse = array();
$aResponse['version'] = "2.0";
$aResponse['template'] = array();
$aResponse['template']['outputs'] = $aOutputs;
echo json_encode($aResponse, JSON_UNESCAPED_UNICODE);
exit;

// 퀵 replies
$aOutputs = array();
$aOutputs[] = array("label"=>"테스트1", "action"=>"message", "messageText"=>"테스트1의 텍스트");
$aOutputs[] = array("label"=>"테스트2", "action"=>"message", "messageText"=>"테스트2의 텍스트");
$aOutputs[] = array("label"=>"테스트3", "action"=>"message", "messageText"=>"테스트3의 텍스트");

$aResponse = array();
$aResponse['version'] = "2.0";
$aResponse['template'] = array();
$aResponse['template']['quickReplies'] = $aOutputs;

echo json_encode($aResponse, JSON_UNESCAPED_UNICODE);
exit;
*/
?>