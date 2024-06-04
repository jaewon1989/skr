<?php

if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

// 테스트 주소 : talk.naver.com/WC6NHE
// /chatapi_navertalk 와 맵핑

// 네이버 서버에서만 접근 가능 (보안처리)
$remoteAddr = $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
$ip_set = explode('.', $remoteAddr);
if( substr($remoteAddr, 0, 10) != '117.52.141' && ($ip_set[3] < 192 && $ip_set[3] > 222) ) {
	echo 'Invalid Access'; exit();
};

// 로그를 납깁니다. (작업시 임시 파일)
$myfile = fopen($g['dir_module']."includes/sns_log/navertalk_log.txt", "a") or die("Unable to open file!");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str);
fwrite($myfile, $json_str."\r\n");
fclose($myfile);

class NaverTalkBot {
	private $talkAuthorization;
	private $webhookResponse;
	private $webhookEventObject;
	private $apiEvent;
	public $url_host;
	public $botId;

	public function __construct(){
		global $chatbot,$g;

		$shost = $_SERVER['HTTP_HOST'];
		if (strpos($shost, $g['chatbot_host']) !== false) {
			$aHost = explode(".", $shost);
			$botId = $aHost[0];
		} else {
			$url_arr = explode('/',$_SERVER['REQUEST_URI']);
			$botId = $url_arr[2];
		}

		$data = array();
		$data['botId'] = $botId;
		$data['channel'] = "ntok";
		$data['name_array'] = array("auth_code");
		$data['act'] = 'getData'; // cf : saveData
		$CD = $chatbot->controlChannelData($data);
		
		$this->botId = $botId;		
		$this->url_host = $g['url_host'];
		$this->talkAuthorization = $CD['auth_code'];
		$this->apiEvent = "https://gw.talk.naver.com/chatbot/v1/event";
		$this->webhookResponse = file_get_contents('php://input');
		$this->webhookEventObject = json_decode($this->webhookResponse);
	}

	// CURL로 $api 주소로 $body 내용을 전송합니다.
	private function httpPost($api,$body){
		$ch = curl_init($api);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json; charser=UTF-8',
		'Authorization: '.$this->talkAuthorization));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	// 네이버 톡톡 Request Event 유형 (채팅창 오픈 / 친구 이벤트 등)
	public function getEventName(){
		$eventName = $this->webhookEventObject->event;
		return $eventName;
	}

	// 유저가 채팅창에 입력해서 보내진 내용 텍스트
	public function getMessageText(){
		$webhook = $this->webhookEventObject;
		$result = $webhook->{"textContent"}->text;
		return $result;
	}

	// 유저가 채팅창에 선택메뉴 선택시 보내진 코드
	public function getSelectedCode(){
		$webhook = $this->webhookEventObject;
		$result = $webhook->{"textContent"}->code;
		return $result;
	}

	// 유저가 입력타입 체크  : button, typing
	public function getInputType(){
		$webhook = $this->webhookEventObject;
		$result = $webhook->{"textContent"}->inputType;
		return $result;
	}

	// 메시지 보낸 유저의 식별 아이디
	public function getUserId(){
		$userId = $this->webhookEventObject->user;
		return $userId;
	}
	// 메시지 보낸 사진
	public function getImgUrl(){
		$imgUrl = $this->webhookEventObject->imageContent->imageUrl;
		return $imgUrl;
	}

	// 일반 답장 텍스트 보내기 (응답대상 자동 처리)
	public function reply($text){
		$api = $this->apiEvent;
		$webhook = $this->webhookEventObject;
		$userToken = $webhook->{"user"};
		$body = array (
		  'event' => 'send',
		  'user' => $userToken,
		  'textContent' =>
		  array (
		    'text' => $text,
		    'code' => '',
		  ),
		);

		$result = $this->httpPost($api,$body);
		return $result;
	}


	// 네이버 톡톡 - 일반 답장 텍스트 보내기 (응답대상 지정 가능)
	public function push($body){
		$api = $this->apiEvent;
		$result = $this->httpPost($api, $body);
		return $result;
	 }

	// 텍스트 보내기
	public function pushText($to, $text){
		$body = array (
		  'event' => 'send',
		  'user' => $to,
		  'textContent' =>
		  array (
		    'text' => $text,
		  ),
		);
		$this->push($body);
	}

	public function pushBtn($to, $btn_array){
		$tmp_arr = array();

		$type = $btn_array[0];
		$content = $btn_array[1];
		if($type=='link'){
			$title = '링크이동 버튼';
			$url = $content;
			$tmp_arr[] = array('type' => 'LINK', 'data' => array('title' => $title,'url' => $url,'mobileUrl' => $url));
		}else if($type=='node'){
			$title = '대화이동 버튼';
			$code ='node-'.$content;
			$tmp_arr[] = array('type' => 'TEXT', 'data' => array('title' => $title,'code' => $code));
		}

		$body = array(
			'event' => 'send',
			'user' => $to,
			'compositeContent' => array(
				'compositeList' => array(
					0 => array(
						'buttonList' => $tmp_arr,
					) ,
				) ,
			) ,
		);
		$this->push($body);
	}

	public function pushHmenu($to, $menu_array){
		$tmp_arr = array();
		$userId = $this->getUserId();
		foreach ($menu_array as $key => $jsonData) {
			$menu = json_decode($jsonData);

			$title = $menu->title;
			$code = 'hMenu-'.$menu->uid;
			$tmp_arr[] = array('type' => 'TEXT', 'data' => array('title' => $title,'code' => $code));
		}
	    //$this->pushComposite($userId, $tmp_arr);

		$body = array(
			'event' => 'send',
			'user' => $to,
			'compositeContent' => array(
				'compositeList' => array(
					0 => array(
						'buttonList' => $tmp_arr,
					) ,
				) ,
			) ,
		);
		$this->push($body);

	}

	public function pushCardGroup($to, $card_array){
		$content = array();
		$data = array();
		$userId = $this->getUserId();
	  	foreach ($card_array as $key => $jsonData) {
			$data = json_decode($jsonData);

		    $content[] = array(
				'title' => $data->title,
				'description' => $data->summary,
				'image' => array(
					'imageUrl' => $this->url_host.$data->img_url,
				),
	    	);

	    	if($data->res_link){
				$content['buttonList'] = array(
					0 => array(
						'type' => 'LINK',
						'data' => array(
							'title' => '이동',
							'url' => $data->res_link?$data->res_link:$this->url_host,
							'mobileUrl' => $data->mobile_link?$data->mobile_link:$this->url_host,
						) ,
					) ,
				);
			}


		}

    	$this->pushComposite($userId, $content);
	}

	public function pushImgGroup($to, $img_array){
		$content = array();
		$data = array();
		$userId = $this->getUserId();
	  	foreach ($img_array as $key => $jsonData) {
			$data = json_decode($jsonData);
		    $content[] = array(
				'image' => array(
					'imageUrl' => $this->url_host.$data->img_url,
				)
			);
		}

    	$this->pushComposite($userId, $content);
	}


	// 콤포넌트 보내기
	public function pushComposite($to, $content){
		$body = array(
			'event' => 'send',
			'user' => $to,
			'compositeContent' => array(
				'compositeList' => $content ,
			) ,
		);
		$this->push($body);
	}


	// 이미지 보내기 (응답대상 지정 가능)
	public function pushImage($to, $imageUrl){
		$body = array (
		  'event' => 'send',
		  'user' => $to,
		  'imageContent' => array (
		    'imageUrl' => $imageUrl,
		  )
		);
		$this->push($body);
	}

	public function pushMenuRespond($_data){
        global $chatbot;

        $result = $_data['result'];
        $data = $_data['data'];
        $userId = $this->getUserId();

		foreach ($result as $item) {
		    $itemQty = count($item);
		    for($i=0;$i<$itemQty;$i++){
		        $type = $item[$i]['type'];
			    $content = $item[$i]['content'];
			    if($type=='text'){
			    	$this->pushText($userId,$content);
			    }else if($type=='img'){
			    	$imgUrl = $this->url_host.$content;
		            $this->pushImage($userId,$imgUrl);
			    }else if($type=='link'){
			    	$btn_array = array($type,$content);
	                $this->pushBtn($userId,$btn_array);
			    }else if($type=='node'){
			    	$data['node'] = $content;
			    	$this->getNodeRespond($data);
			    }
		    }
		}
	}

	public function getNodeRespond($data){
		global $chatbot;

		$userId = $this->getUserId();
		
		$result = $chatbot->getApiResponse($data);
	    foreach ($result as $resItem) {
		    $type = $resItem['type'];
		    $content = $resItem['content'];
		    if($type=='text'){
		    	$this->pushText($userId,$content);
		    }else if($type=='hMenu'){
		        $this->pushHmenu($userId,$content);
		    }else if($type=='card'){
		    	$this->pushCardGroup($userId,$content);
		    }else if($type=='img'){
		    	$this->pushImgGroup($userId,$content);
		    }else if($type=='if'){
	            $_data = array();
				$_data['data'] = $data;
				$_data['result'] = $content;
				$this->pushMenuRespond($_data);
		    }
		}
	}

}



// 라인 봇 객체 생성
$bot = new NaverTalkBot();

// 답장할 유저의 아이디 / 유저가 보낸 이벤트 유형 / 유저가 보낸 이미지 / 유저가 보낸 메시지
$userId = $bot->getUserId();
$eventName = $bot->getEventName();
$receive_img = $bot->getImgUrl();
$receive_msg = $bot->getMessageText();
$receive_code = $bot->getSelectedCode();
$receive_inputType = $bot->getInputType();

$data = array();
$data['userId'] = $userId;
$data['chatType'] = 'N';
$data['channel'] = 'navertalk';
$data['msg'] = $receive_msg;// '줌팁이 뭔가요?';
$data['botId'] = $bot->botId;//'vdCEhUke2L1G5x6';
$data['api'] = true;
$url_host = $bot->url_host;

// send 이벤트 처리
if($eventName=='open'){
	$data['node'] = 1;
	$bot->getNodeRespond($data);
}else if($eventName=='send'){
	// 메시지 분리하기 - 이미지가 왔을 때
	if($receive_img) $bot->pushText($userId, '이미지 인식기능은 준비중입니다.');

	// 메시지 분리하기 - 코드 or 텍스트가 왔을 때
	else if($receive_msg||$receive_code){

        if($receive_inputType=='typing') $bot->getNodeRespond($data);
		else if($receive_inputType=='button'){ // 선택버튼 클릭한 경우
			$code_arr = explode('-',$receive_code);
			$resType = $code_arr[0]; //
			$uid = $code_arr[1];
			// $bot->pushText($userId,$resType.'-'.$uid);

			if($resType=='hMenu'){ // 버튼 메뉴 클릭한 경우
				$data['uid'] = $uid;
				$result = $chatbot->getMenuRespond($data);
				if($result[0]){
					$_data = array();
					$_data['data'] = $data;
					$_data['result'] = $result;
					$bot->pushMenuRespond($_data);

				}else{
					$bot->getNodeRespond($data); // content 없는 경우 그냥 입력한 것으로 간주
				}

			}
		}
	}
}


?>