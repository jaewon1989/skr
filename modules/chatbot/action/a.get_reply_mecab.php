<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$botid = $_POST['botid'];
$message = $_POST['message']; 

// 사용자 채팅 로그 등록 : 맨 마지막 인수 = by_who
$chatbot->addChatLog($botid,$message,$response,$reply,'user');

$msg_array = $chatbot->getMopAndPattern($message);
$msg_pat = $msg_array['pat'];
$reply='';
$RCD = getDbSelect($table[$m.'rule'],'uid>0','pattern,reply');
$NUM = getDbRows($table[$m.'rule'],'uid>0');
while ($R=db_fetch_array($RCD)){
    $pattern = $R['pattern'];
    similar_text($pattern,$msg_pat,$percent);  
    if($percent > 90){
       $reply .= $R['reply'];
       break;
    }else{
        $reply.='';
    }
}
if($reply==''){
   $msg_arr = explode(' ',$message);
   $response =$msg_arr[0].'에 대해서 좀더 자세히 말씀해주시기 바랍니다.';  
}else{
   $response =$reply; 
}

// TMPL 값 세팅 : 챗봇 데이타 파싱(bot_avatar_src, bot_name...)
$botData = $chatbot->getBotDataFromId($botid);
foreach ($botData as $key=>$value) {
   $TMPL[$key] = $value;    
}
$TMPL['date'] = $date['year'].'-'.substr($date['month'],4,2).'-'.substr($date['today'],6,2);
$TMPL['response'] = $response;

// 챗봇 박스 출력 
$chatbox = new skin('chat/bot_msg');
$html =$chatbox->make();

// 챗봇 채팅 로그 등록 : 맨 마지막 인수 = by_who
//$chatbot->addChatLog($botid,$message,$response,$reply,'bot');

// 최종 답변 리턴 
$result['content'] = $html;
echo json_encode($result);
exit;
?>
