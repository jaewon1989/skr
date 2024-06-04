<?php
    // Nexus CS 채팅 상담사 응답용 API
    /*
    클라우드 : https://봇아이디.chatbot.bottalks.co.kr/chatapi_cs/cs_msg
    일반접속 : https://챗봇주소/chatapi_cs/봇아이디/cs_msg

    전송 데이터 :
    {"roomToken":"sadfdfsdf", "msg":"상담사 응답내용"}
    */

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, X-Bottalks-Key");

    if(!defined('__KIMS__')) exit;
    require_once $g['dir_module'].'var/var.php';
    require_once $g['dir_module'].'var/define.path.php';

    require_once $g['dir_module'].'includes/cschat.class.php';

    $chatbot = new Chatbot();
    $cschat = new Cschat();

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $mode = isset($_REQUEST['mode']) && trim($_REQUEST['mode']) ? $_REQUEST['mode'] : "";
        if($mode == "chat_open") {
            $cschat->getCSChatOpen();

        } else if($mode == "user_msg") {
            $cschat->getCSChatUserMsg();

        } else if($mode == "chat_end") {
            $cschat->getCSChatEnd();

        } else if($mode == "chat_force_end") {
            $cschat->getForceChatEnd();

        } else if($mode == "cs_connect") {
            $cschat->getCheckCSConnect();

        } else if($mode == "cs_msg") {
            $cschat->getCheckCSResponse();

        } else if($mode == "cs_end") {
            $cschat->getCheckCSEnd();

        } else if($mode == "userinfo") {
            $skin = new skin("cschat_user_info");
            $content = $skin->make('lib');

            $cschat->getResultAPIJSON(200, ['result'=>true, 'data'=>$content]);
        }
    }
    exit;
?>

