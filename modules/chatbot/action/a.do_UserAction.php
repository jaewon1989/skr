<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
include_once $g['dir_module'].'var/define.path.php'; // class, 모듈, 레이아웃 패스 세팅
$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$d_regis=$date['totime'];
$mbruid = $my['uid'];
$uid = $_POST['uid'];
$vendor = $_POST['vendor'];
$data = $_POST['data'];


if($act=='add'){
   $is_added = getDbRows($table[$m.'added'],'mbruid='.$mbruid.' and botuid='.$uid);
   if($is_added) $result['message'] = '이미 추가되었습니다.';
   else {
       $QKEY = "mbruid,botuid,vendor,memo,d_regis";
       $QVAL = "'$mbruid','$uid','$vendor','','$d_regis'";
       getDbInsert($table[$m.'added'],$QKEY,$QVAL);

       $result['message'] = '챗봇이 추가되었습니다.';
   }
}else if($act=='setBotActive'){
    if($setVal=='on') {
        getDbUpdate($table[$m.'bot'],'display=1','uid='.$botuid);
        $result['message'] = '챗봇이 활성화 처리되었습니다.';
    }
    else{
        getDbUpdate($table[$m.'bot'],'display=0','uid='.$botuid);
        $result['message'] = '챗봇이 비활성화 처리되었습니다.';
    }
    $result['content']='';
}else if($act=='change-managerAuth'){

    foreach ($manager_members as $key=>$mbruid) {
        $a_val = $auth[$key];
        getDbUpdate($table[$m.'manager'],'auth='.$a_val,'mbruid='.$mbruid.' and vendor='.$vendor);
    }
}else if($act=='delete-bot'){
    $chatbot->getBotDelete($uid);
    $result['message'] = '챗봇이 정상적으로 삭제되었습니다.';
}else if($act=='count-bot'){
    $bot_id = $_POST['bot_id'];
    $chatbot->regisBotCount($bot_id);
}else if($act=='search-bot'){
    $where = array("keyword"=>$keyword);
    $result['content'] = $chatbot->getUserBotList($my['uid'],'search',$where,30,1);
}else if($act=='show-recommendedGoods'){
    $B = getDbData($table[$m.'bot'],"id='".$bot_id."'",'vendor,uid');
    $getVendorGoods = $chatbot->getVendorGoods($B['vendor'],$B['uid'],'chat-button');
    if($getVendorGoods){
        $goods_list = '<ul><li><h3><span>이런 상품 어떠신가요?</span></h3></li>';
        $goods_list .= $getVendorGoods;
    }else{
        $goods_list ='<ul><li><h3><span>추천상품이 존재하지 않습니다.</span></h3></li></ul>';
    }

    $TMPL['selection_class'] = '-selection';
    $botData = $chatbot->getBotDataFromId($bot_id);
    foreach ($botData as $key=>$value) {
       $TMPL[$key] = $value;
    }
    $TMPL['date'] = $date['year'].'-'.substr($date['month'],4,2).'-'.substr($date['today'],6,2);
    $TMPL['response'] = $goods_list;

    $bot_msg = new skin('chat/bot_msg');
    $result['content'] = $bot_msg->make();

}else if($act=='show-previewMessage'){
    $B = getDbData($table[$m.'bot'],"uid='".$botuid."'",'vendor,uid,id');
    $bot_id = $B['id'];
    $botData = $chatbot->getBotDataFromId($bot_id);
    foreach ($botData as $key=>$value) {
       $TMPL[$key] = $value;
    }
    $TMPL['date'] = $date['year'].'-'.substr($date['month'],4,2).'-'.substr($date['today'],6,2);
    $TMPL['response'] = trim($message);

    $bot_msg = new skin('chat/bot_msg');
    $result['content'] = $bot_msg->make();
}else if($act=='del-eventGoods'){
    getDbDelete($table[$m.'goods'],'uid='.$uid);
    $result['content'] = 'OK';

}else if($act =='refresh-moniteringBot'){ // 모니터링 봇 refresh

    $result['content'] = $chatbot->getUserChatLog($data);
}else if($act =='init-context'){
    $_SESSION['context'] ='';
    $result['content'] = $_SESSION['context'];

}else if($act =='save-chatMsg'){
    $role = $data['role'];

    if($role=='bot') {
        if($data['bot_type'] == "call" && $data['humanMod']) {
            $_data = [];
            $_data['cti_chatapi_host'] = $g['cti_chatapi_host'];
            $_data['bot'] = $data['bot'];
            $_data['roomToken'] = $data['roomToken'];
            $_data['content'] = $data['content'];

            require_once $g['dir_include'].'callbot.class.php';
            $callbot = new Callbot();
            $callbot->sendHumanModResponse($_data);
        }

        $result['content'] = $chatbot->addBotChatLog($data);
    } else {
        $result['content'] = $chatbot->addChatLog($data);
    }
}

if($act=='change-managerAuth') getLink('reload','parent.','','');
else{
    echo json_encode($result);
    exit;
}
exit;
?>
