<?php
// /chatapi_line 주소와 맵핑
// 테스트 방법 : 네이버 라인 검색 - "페소" 검색 후 추가해서 채팅 진행

// "이미지", "푸시", "동영상", "지도" 일반 텍스트 기능 사용이 가능합니다.
// https://developers.line.me/en/docs/messaging-api/reference/#send-reply-message 참고

if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

// 로그를 납깁니다. (작업시 임시 파일)
$myfile = fopen($g['dir_module']."includes/sns_log/navertalk_log.txt", "a") or die("Unable to open file!");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str);
fwrite($myfile, $json_str."\r\n");
fclose($myfile);

class Linebot {
	private $channelAccessToken;
	private $channelSecret;
	private $webhookResponse;
	private $webhookEventObject;
	private $apiReply;
	private $apiPush;
	public $url_host;
	public $botId;

	public function __construct(){
		global $chatbot,$g;

        $shost = $_SERVER['HTTP_HOST'];
		$aHost = explode(".", $shost);
		$botId = $aHost[0];

		$data = array();
		$data['botId'] = $botId;
		$data['channel'] = "line";
		$data['name_array'] = array("channel_secret","access_token");
		$data['act'] = 'getData'; // cf : saveData
		$CD = $chatbot->controlChannelData($data);
		
		$this->botId = $botId;		
		$this->url_host = $g['url_host'];
		$this->channelAccessToken = $CD['access_token'];
		$this->channelSecret = $CD['channel_secret'];
		$this->apiReply = 'https://api.line.me/v2/bot/message/reply';
		$this->apiPush = 'https://api.line.me/v2/bot/message/push';
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
		'Authorization: Bearer '.$this->channelAccessToken));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	// 유저가 채팅창에 입력해서 보내진 내용 텍스트 얻기
	public function getMessageText(){
		$webhook = $this->webhookEventObject;
		$messageText = $webhook->{"events"}[0]->{"message"}->{"text"};
		return $messageText;
	}

    // 메세지 타입 얻기 : text, sticker, image(첨부이미지, 사진 찍어서 올린것도 포함),audio
	public function getMessageType(){
		$webhook = $this->webhookEventObject;
		$messageText = $webhook->{"events"}[0]->{"message"}->{"type"};
		return $messageText;
	}

	// postback 데이터 얻기
	public function getPostBackData(){
		$webhook = $this->webhookEventObject;
		$postback = $webhook->{"events"}[0]->{"postback"}->{"data"};
		return $postback;
	}

	// 메시지 보낸 유저의 식별 아이디
	public function getUserId(){
		$webhook = $this->webhookEventObject;
		$userId = $webhook->{"events"}[0]->{"source"}->{"userId"};
		return $userId;
	}

    // 이벤트 타입 얻기 : message, postback,
	public function getEventType(){
		$webhook = $this->webhookEventObject;
		$eventType = $webhook->{"events"}[0]->{"type"};
		return $eventType;
	}

	// 일반 답장 텍스트 보내기 (응답대상 자동 처리)
	public function reply($text){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"};
		$body["replyToken"] = $replyToken;
		$body["messages"][0] = array(
			"type" => "text",
			"text"=>$text
		);

		$result = $this->httpPost($api,$body);
		return $result;
	}


	// 일반 답장 텍스트 보내기 (응답대상 지정 가능)
	public function push($body){
		$api = $this->apiPush;
		$result = $this->httpPost($api, $body);
		return $result;
    }

    // 텍스트 보내기
    public function pushText($text){
    	$to = $this->getUserId();
		$body = array(
		    'to' => $to,
		    'messages' => [
				array(
				    'type' => 'text',
				    'text' => $text
				)
		    ]
		);
		$this->push($body);
	}

	 // 이미지 보내기
   	 public function pushImage($imageUrl){
   	 	    $previewImageUrl = '';
        	$to = $this->getUserId();

        	$body = array(
		    'to' => $to,
		    'messages' => [
				array(
				    'type' => 'image',
				    'originalContentUrl' => $imageUrl,
				    'previewImageUrl' => $previewImageUrl ? $previewImageUrl : $imageUrl
				)
		    ]
		);
		$this->push($body);
    }

    // 버튼 출력 (링크 전용)
	public function pushBtn($btn_array){
        $to = $this->getUserId();

        $type = $btn_array[0];
		$content = $btn_array[1];
		if($type=='link'){
			$title = '링크이동 버튼';
			$link = $content;
			$action = array('type' => 'uri','label'=> $title,'uri' => $link);
		}else if($type=='node'){
			$title = '대화이동 버튼';
			$code ='node-'.$content;
			$action = array('type' => 'postback','label' => $title ,'data'=> $code);
		}

    	$body = array(
		    'to' => $to,
		    'messages' => [
		        array(
					"type" => "template",
			        "altText" => "This is a buttons template",
			        "template" => array(
				        "type"=> "buttons",
				        "text"=> $title,
				        "defaultAction" => array(
			                "type" => "uri",
			                "uri" => $link
			            ),
				        "actions" =>[
                            $action
				        ]
		            )
			    )
		    ]
		);

		$this->push($body);
	}

    // 가로메뉴 보내기
	public function pushHmenu($menu_array){
        $to = $this->getUserId();

    	$body = array(
		    'to' => $to,
		    'messages' => [
		        array(
					"type" => "flex",
			        "altText" => "This is a flex message",
			        "contents" => array(
				        "type"=> "carousel",
				        "contents"=>[]
			        )
			    )
		    ]
		);

		foreach ($menu_array as $index => $jsonData) {
            $data = json_decode($jsonData);
            $title = $data->title;
            $code = 'hMenu-'.$data->uid;
            $body['messages'][0]['contents']['contents'][$index] = array(
            	"type" => "bubble",
			    "body" => array(
			        "type" => "box",
			        "layout" => "horizontal",
			        "contents" => [
			            array(
				            "type" => "text",
							"text" => $title,
							"action" => array(
                                "type" => "postback",
								"label" => $title,
								"data" => $code,
							)
			            )
			        ]
			    )
            );
        }

		$this->push($body);
	}

    // 이미지 그룹 보내기
    public function pushImgGroup($img_array){
    	$to = $this->getUserId();

    	$body = array(
		    'to' => $to,
		    'messages' => [
		        array(
					"type" => "template",
			        "altText" => "This is a image carousel",
			        "template" => array(
				        "type"=> "image_carousel",
				        "columns"=>[]
		            )
			    )
		    ]
		);
        foreach ($img_array as $index => $jsonData) {
            $data = json_decode($jsonData);
            $img_url = $this->url_host.$data->img_url;
            $uid = $data->uid;
            $body['messages'][0]['template']['columns'][$index] = array(
            	"imageUrl" => $img_url,
				"action" => array("type" => "postback","data" => "img-".$uid)
            );
        }

		$this->push($body);
    }

    // 카드 그룹 보내기
    public function pushCardGroup($card_array){

    	$to = $this->getUserId();

    	$body = array(
		    'to' => $to,
		    'messages' => [
		        array(
					"type" => "template",
			        "altText" => "this is a carousel template",
			        "template" => array(
				        "type"=> "carousel",
				        "columns"=>[],
		                "imageAspectRatio" => "rectangle",
		                "imageSize" => "cover"
			        )
			    )
		    ]
		);

        foreach ($card_array as $index => $jsonData) {
        	$data = json_decode($jsonData);
    		$pc_link = $data->res_link;
    		$mobile_link = $data->mobile_link;
    		$link = $mobile_link?$mobile_link:($pc_link?$pc_link:$this->url_host);
        	$body['messages'][0]['template']['columns'][$index] = array(
	        	"thumbnailImageUrl" => $this->url_host.$data->img_url,
	            "imageBackgroundColor" => "#FFFFFF",
	            "title" => $data->title,
	            "text" => $data->summary,
	            "defaultAction" => array(
	                "type" => "uri",
	                "uri" => $link
	            ),
	            "actions" => [
	                array('type' => 'uri','label' => '상세보기' ,'uri' => $link)
	            ]

	        );
        }

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
			    	$this->pushText($content);
			    }else if($type=='img'){
			    	$imgUrl = $this->url_host.$content;
		            $this->pushImage($imgUrl);
			    }else if($type=='link'){
			    	$btn_array = array($type,$content);
	                $this->pushBtn($btn_array);
			    }else if($type=='node'){
			    	$data['node'] = $content;
			    	$this->getNodeRespond($data);
			    }
		    }
		}
	}

	public function getNodeRespond($data){
		global $chatbot;
		
		$result = $chatbot->getApiResponse($data);
	    foreach ($result as $resItem) {
		    $type = $resItem['type'];
		    $content = $resItem['content'];
		    if($type=='text'){
		    	$this->pushText($content);
		    }else if($type=='hMenu'){
		        $this->pushHmenu($content);
		    }else if($type=='card'){
		    	$this->pushCardGroup($content);
		    }else if($type=='img'){
		    	$this->pushImgGroup($content);
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
$bot = new Linebot();

$eventType = $bot->getEventType(); // 이벤트 타입 추출
$receive_msg = $bot->getMessageText();// 라인에서 받은 메시지 event-> message-> text 값
$postBackData = $bot->getPostBackData(); // 라인에서 콜백받은 데이타 (버튼 메뉴에서 사용)
$userId = $bot->getUserId();

$data = array();
$data['userId'] = $userId;
$data['chatType'] = 'L';
$data['channel'] = 'line';
$data['msg'] = $receive_msg;// '줌팁이 뭔가요?';
$data['botId'] = $bot->botId;//'vdCEhUke2L1G5x6';
$data['api'] = true;
$url_host = $bot->url_host;

// 이벤트 타입 분리
if($eventType=='postback'){ // 메뉴 버튼 누른 경우
    $code_arr = explode('-',$postBackData);
	$resType = $code_arr[0]; //
	$uid = $code_arr[1];

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

}else if($eventType == 'message'){
    $msg_type = $bot->getMessageType(); // 라인에서 받은 메시지 event-> message-> type 값

	if($msg_type =='text') $bot->getNodeRespond($data);
	else{
        $_data['type'] = $msg_type; // image, audio, video, sticker....
		$unKnownMsg = $chatbot->getMediaEventMsg($_data);
	    $bot->pushText($unKnownMsg);
	}
}

exit;

?>