<?php
class Jusobot {
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

    public function __construct() {
    }

    public function getJusobotResponse($data){
        global $g, $m, $table, $chatbot, $callbot;

        $this->bot = $callbot->bot;
        $this->vendor = $callbot->vendor;
        $this->dialog = $callbot->dialog;
        $this->botid = $data['botId'];
        $this->msg = trim($data['msg']);
        $this->msg_type = trim($data['msg_type']);
        $this->roomToken = $data['roomToken'];
        $this->userId = $data['userId'];

        if($this->msg != "") {
            // 발화문 저장
            $userChat = array();
            $userChat['printType'] ='T';
            $userChat['userId'] = $this->userId;
            $userChat['content'] = $this->msg;
            $userLastChat = $chatbot->addChatLog($userChat);
            $this->last_chat = $userLastChat['last_chat'];

            $r_data = $data['r_data'];
            $r_data['last_chat'] = $this->last_chat;
            $r_data['msg_type'] = $data['msg_type'];

            // 주소봇 로딩 및 응답 추출
            require_once $g['dir_module'].'lib/addrBot/client.php'; // addrBot 패키지

            $data['user_utt'] = $this->msg;
            $data['bot'] = $this->bot;
            $data['room_token'] = $data['roomToken'];
            $data['access_token'] = $data['accessToken'];//'access_token';

            $addrBot = new Peso\addrbot\Client();
            $addr_result = $addrBot->processAddrBot($data);

            $result = $_result = [];

            // 응답 로그 저장
            if($this->last_chat && $addr_result['response']) {
                $callbot->getCallbotBotChatLog($addr_result['response']);
            }

            if($addr_result['goal_success']) {
                $r_data['step'] = 'finish';
                $response['res_end'] = true;
            }
            $next_status = array('action'=>'recognize');
            $response['next_status'] = $next_status;
            $response['content'] = $addr_result['response'];

            $r_data['next_status'] = $next_status;
            $r_data['content'] = $response['content'];
            $response['r_data'] = $r_data;
            $response['type'] = "text";
            $response['bargein'] = false;

            $_result[] = $response;
            return $_result;
        }
    }

}
?>