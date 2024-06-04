<?php
// /chatapi_facebook 주소와 맵핑

// 테스트 방법
// 1. 페이스북 봇톡스 개발자 계정으로 https://developers.facebook.com/apps/256219451623729/messenger/settings/ 에서 테스트할 유저 계정을 추가 후 로그인하여 테스트
// (원래는 messaging API 를 사용하려면 권한이 있어야하지만 실제 서비스를 할 목적이 아니므로 테스트 사용자를 추가하여 테스트합니다. 추후 업체들은 messaging api 권한 승인 필요)
// 2. 추가된 테스트 계정으로 로그인을하여, 봇톡스 페이지로 이동, 메시지 보내기를 합니다.
// 3. 자동 답변이 오는 지 확인합니다. 현재 유저메시지와 유저 번호를 반환합니다. (별도 추가기능 없음)


if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

$shost = $_SERVER['HTTP_HOST'];
$aHost = explode(".", $shost);
$botId = $aHost[0];

$data = array();
$data['botId'] = $botId;
$data['channel'] = "fb";
$data['act'] = 'getData'; // cf : saveData
$CD = $chatbot->controlChannelData($data);


// 로그를 납깁니다. (작업시 임시 파일)
$json_str = file_get_contents('php://input');
file_put_contents($_SERVER['DOCUMENT_ROOT']."/_tmp/cache/facebook_log_".$botId.".txt", "user : ".$json_str."\n", FILE_APPEND);

/* validate verify token needed for setting up web hook */
if (isset($_REQUEST['hub_verify_token'])) {
    if ($_REQUEST['hub_verify_token'] == $CD['verify_token']) {
        echo $_REQUEST['hub_challenge'];
        http_response_code(200); exit;
    } else {
        echo 'Invalid Verify Token';
        exit;
    }
}

class FBbot {
    private $pageAccessToken; // 페이지 엑세스 토큰
    private $webhookObject; // GET과 POST 방식으로 받은 값
    private $webhookEventObject; // JSON 값으로 넘어온 값
    private $verifyToken; // API 발급시 임의로 넣은 인증 토큰 - Setting 클래스에서 수정
    private $apiReply;
    private $apiPush;
    public $url_host;
    public $botId;

    public function __construct(){
        global $chatbot,$g,$CD,$botId;
        
        $this->botId = $botId;		
		$this->url_host = $g['url_host'];
        $this->pageAccessToken = $CD['access_token'];
        $this->verifyToken = $CD['verify_token'];
        $this->apiPush = "https://graph.facebook.com/v14.0/me/messages?access_token=".$this->pageAccessToken;
        $this->webhookObject = $_REQUEST;
        $this->webhookEventObject = json_decode(file_get_contents('php://input'));
    }
    
    // CURL로 $api 주소로 $body 내용을 전송합니다.
    private function httpPost($api,$body){
        global $g, $botId;
        $body = json_encode($body);
        
        //file_put_contents($_SERVER['DOCUMENT_ROOT']."/_tmp/cache/facebook_log_".$botId.".txt", "bot : ".$body."\n", FILE_APPEND);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $server_output = curl_exec($ch);
        $aResInfo = curl_getinfo($ch);
        curl_close ($ch);
        return $server_output;
    }

    // messaging 오브젝트 추출
    public function getMessagingObj(){
        return $this->webhookEventObject->{"entry"}[0]->{"messaging"}[0];
    }

    // 유저가 채팅창에 입력해서 보내진 내용 텍스트 얻기
    public function getMessageText(){
        $msg_obj = $this->getMessagingObj();
        return $msg_obj->{"message"}->{"text"};
    }

    // 메세지 타입 얻기 : text, sticker, image(첨부이미지, 사진 찍어서 올린것도 포함),audio
    public function getMessagingType(){
        $msg_obj = $this->getMessagingObj();

        if(isset($msg_obj->{"postback"})) $result = 'postback';
        else if(isset($msg_obj->{"message"})){
            if(isset($msg_obj->{"message"}->{"text"})) $result = 'text';
            else if(isset($msg_obj->{"message"}->{"attachments"}[0])){
                $result = $msg_obj->{"message"}->{"attachments"}[0]->{"type"};
            }
        }
        return $result;
    }

    // postback 데이터 얻기
    public function getPostBackData(){
        $msg_obj = $this->getMessagingObj();
        $postback_payload = $msg_obj->{"postback"}->{"payload"};
        if($postback_payload) return $postback_payload;
        else return;
    }

    // 메시지 보낸 유저의 식별 아이디
    public function getUserId(){
        $msg_obj = $this->getMessagingObj();
        return $msg_obj->{"sender"}->{"id"};
    }

    // 해당 $body Array를 api 주소로 전송합니다.
    public function push($body) {
        $api = $this->apiPush;
        $result = $this->httpPost($api, $body);
        return $result;
    }

    // $text 내용을 답장으로 전송합니다.
    public function pushText($text){

        $userId = $this->getUserId();
        $body = array(
            "messaging_type" => "RESPONSE",
            "recipient" => array( "id" =>  $userId),
            "message" => array('text' => $text)
        );

        return $this->push($body);
    }

    // 버튼 출력 (링크 전용)
    public function pushBtn($btn_array){
        $type = $btn_array[0];
        $content = $btn_array[1];
        if($type=='link'){
            $title = '링크이동 버튼';
            $link = $content;
            $button = array('type' => 'web_url','url' => $link,'title'=> $title,"webview_height_ratio" => "full");
        }else if($type=='node'){
            $title = '대화이동 버튼';
            $code ='node-'.$content;
            $button = array('type' => 'postback','title' => $title ,'payload'=> $code);
        }

        $userId = $this->getUserId();
        $body = array(
            "messaging_type" => "RESPONSE",
            "recipient" => array( "id" =>  $userId),
            "message" => array(
                "attachment" => array(
                    "type" => "template",
                    "payload" => array(
                        "template_type" => "button",
                        "text" => "아래의 버튼을 눌러주세요",
                        "buttons" => [
                             $button
                        ]
                    )
                )
            )
        );

        $this->push($body);
    }

    // 가로메뉴 보내기
    public function pushHmenu($menu_array){
        $userId = $this->getUserId();
        $body = array(
            "messaging_type" => "RESPONSE",
            "recipient" => array( "id" =>  $userId),
            "message" => array(
                "attachment" => array(
                    "type" => "template",
                    "payload" => array(
                        "template_type" => "button",
                        "text" => "아래의 메뉴를 선택해주세요",
                        "buttons" => []
                    )
                )
            )
        );

        foreach ($menu_array as $index => $jsonData) {
            $data = json_decode($jsonData);
            $title = $data->title;
            $code = 'hMenu-'.$data->uid;
            $body['message']['attachment']['payload']['buttons'][$index] = array(
                "type" => "postback",
                "title" => $title,
                "payload" => $code
            );
        }

        return $this->push($body);
    }

    // 이미지 그룹 보내기
    public function pushImgGroup($img_array){
        $userId = $this->getUserId();

        $body = array(
            "messaging_type" => "RESPONSE",
            "recipient" => array( "id" =>  $userId),
            "message"=>array(
                "attachment"=>array(
                    "type"=> "template",
                    "payload"=>array(
                        "template_type"=> "generic",
                        "elements"=> []
                    )
                )
            )
        );
        foreach ($img_array as $index => $jsonData) {
            $data = json_decode($jsonData);
            $img_url = $this->url_host.$data->img_url;
            $title = $this->title?$this->title:'.';
            $uid = $data->uid;
            $body['message']['attachment']['payload']['elements'][$index] = array(
                "image_url" => $img_url,
                "title"=> $title,
            );
        }

        return $this->push($body);
    }

    // 이미지 1개 전송
    public function pushImage($data){
        $userId = $this->getUserId();

        $body = array(
            "messaging_type" => "RESPONSE",
            "recipient" => array( "id" =>  $userId),
            "message"=>array(
                "attachment"=>array(
                    "type"=> "template",
                    "payload"=>array(
                        "template_type"=> "generic",
                        "elements"=> []
                    )
                )
            )
        );

        $img_url = $this->url_host.$data['img_url'];
        $title = $data['title']?$data['title']:'.';
        $body['message']['attachment']['payload']['elements'][0] = array(
            "image_url" => $img_url,
            "title"=> $title,
        );

        return $this->push($body);
    }

    // 카드 그룹 보내기
    public function pushCardGroup($card_array){

        $userId = $this->getUserId();

        $body = array(
            "messaging_type" => "RESPONSE",
            "recipient" => array( "id" =>  $userId),
            "message"=>array(
                "attachment"=>array(
                    "type"=> "template",
                    "payload"=>array(
                        "template_type"=> "generic",
                        "elements"=> []
                    )
                )
            )
        );

        foreach ($card_array as $index => $jsonData) {
            $data = json_decode($jsonData);
            $pc_link = $data->res_link;
            $mobile_link = $data->mobile_link;
            $link = $mobile_link?$mobile_link:($pc_link?$pc_link:$this->url_host);
            $body['message']['attachment']['payload']['elements'][$index] = array(
                "image_url"=> $this->url_host.$data->img_url,
                "title"=> $data->title,
                "subtitle"=> $data->summary,
                "default_action" => array(
                    "type"=>"web_url",
                    "url"=> $link,
                    "webview_height_ratio" =>"full",
                )
            );
        }

        return $this->push($body);
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
                    $dt = array();
                    $dt['img_url'] = $content;
                    $this->pushImage($dt);
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

       http_response_code(200); exit;
    }
}


// 페북/webhook  봇 객체 생성
$bot = new FBbot();
$messagingType = $bot->getMessagingType(); // 메세징 타입 추출
$receive_msg = $bot->getMessageText();// 페북/webhook 에서 받은 메시지 event-> message-> text 값
$postBackData = $bot->getPostBackData(); // 페북/webhook 에서 콜백받은 데이타 (버튼 메뉴에서 사용)
$userId = $bot->getUserId();

$data['userId'] = $userId;
$data['chatType'] = 'F';
$data['channel'] = 'facebook';
$data['msg'] = $receive_msg;// '줌팁이 뭔가요?';
$data['api'] = true;

// 이벤트 타입 분리
if($messagingType=='postback'){ // 메뉴 버튼 누른 경우
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

}else{
    if($messagingType =='text') $bot->getNodeRespond($data);
    else{
        $_data['type'] = $messagingType; // image, audio, video, sticker....
        $unKnownMsg = $chatbot->getMediaEventMsg($_data);
        $bot->pushText($unKnownMsg);
    }
}

//$bot->pushText('dddd');

// // // 나머지 경우
// //http_response_code(404); exit;
exit;
?>