<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
require_once $g['path_core'].'function/encryption.php';

$pData = getEscString($_POST['pData']);

if(!trim($pData['bot']) || !trim($pData['r_data'])) {
    echo "<script>alert('잘못된 접근입니다'); history.back();</script>"; exit;
}

$aWeek = array("일", "월", "화", "수", "목", "금", "토");
$dToday = date("Y-m-d");
$dNowTime = date("H:i");

// 정보 출력용
function getReserveInfoHtml($data) {
    $aCategoryItem = array("upart"=>"소속(부서)", "uname"=>"이름", "uphone"=>"휴대폰번호", "udate"=>"신청일시", "ucontent"=>"장애증상");
    $html = "";

    foreach($aCategoryItem as $item=>$item_text) {
        if(isset($data[$item]) && $data[$item]) {
            $html .="<li>";
            if($item == "ucontent") {
                $html .="    <div style='margin-top:15px;'>";
                $html .="        <span class='item' style='display;block; float:none;'>".$item_text."</span>";
                $html .="        <div class='cont' style='margin-top:5px; padding:10px; border:1px solid #ccc; border-radius:5px; background:#fff;'>".nl2br($data[$item])."</div>";
                $html .="    </div>";
            } else {
                $html .="    <div>";
                $html .="        <span class='item'>".$item_text."</span>";
                $html .="        <div class='cont'>".$data[$item]."</div>";
                $html .="    </div>";
            }
            $html .="</li>";
        }
    }
    return $html;
}

//--------------------------------------------------
$chatbot = new Chatbot();

$result=array();
$result['error']=false;
$result['log'] = false;

$chatbot->vendor = $vendor = $pData['vendor'];
$chatbot->botuid = $bot = $pData['bot'];
$chatbot->dialog = $dialog = $pData['dialog'];
$chatbot->botid = $botid = $pData['botid'];
$chatbot->cmod = $cmod = $pData['cmod']; // vod or cs
$chatbot->roomToken = $roomToken = $pData['roomToken'];
$chatbot->bottype = $bottype = $pData['bot_type'];
$chatbot->channel = $channel = $pData['channel'];

// aramjo context
$chatbot->getBotContext($pData);
//-----------------------------------------------
$category = trim($pData['category']);
$hform = trim($pData['hform']);
$action = trim($pData['action']);
$step = trim($pData['step']);
$last_chat = trim($pData['last_chat']);

if(!$hform || !$step) {
    echo "<script>alert('잘못된 접근입니다2'); history.back();</script>"; exit;
}

// 암호화 해독
if($pData['nonceVal']) {
    $nonceVal = $pData['nonceVal'];
    $encryption = new Encryption();
    $pData['r_data'] = $encryption->decrypt($pData['r_data'], $nonceVal);
}

$r_data = json_decode($pData['r_data'], true);
$botData = $chatbot->getBotDataFromId($botid);

$TMPL = array();
$TMPL['bot_avatar_src'] = $botData['bot_avatar_src'];
$TMPL['bot_name'] = $botData['bot_name'];
$TMPL['date'] = (date('a') == 'am' ? '오전 ':'오후 ').date('g').':'.date('i');
$TMPL['category_type'] = $category;
$TMPL['hform_type'] = $hform;
$TMPL['action'] = $action;
$TMPL['last_chat'] = $last_chat;

// api 조회용
$_data = array();
$_data['vendor'] = $vendor;
$_data['bot'] = $bot;
$_data['getParam'] = array();
$_data['postParam'] = array();

// 일반 예약 프로세스
if($action == "request") {
    switch($step) {
        case('start') :
            $skinFile = $hform.'_'.$action.'_auth';
            $skin = new skin($skinFile);
            $content = $skin->make('lib');
            $result['msg'] = $content;
            $result['log'] = true;
        break;

        case('auth') :
            if(!$r_data['reserve_idx']) {
                if(!trim($r_data['upart'])) {
                    $result['error'] = true;
                    $result['err_msg'] = "소속(부서)명을 입력해주세요.";
                    echo json_encode($result); exit;
                }
                if(!trim($r_data['uname'])) {
                    $result['error'] = true;
                    $result['err_msg'] = "이름을 입력해주세요.";
                    echo json_encode($result); exit;
                }
                if(!trim($r_data['uphone'])) {
                    $result['error'] = true;
                    $result['err_msg'] = "휴대폰번호를 입력해주세요.";
                    echo json_encode($result); exit;
                }
                if(!preg_match("/01[016789][\d]{3,4}[\d]{4}/", trim($r_data['uphone']))) {
                    $result['error'] = true;
                    $result['err_msg'] = "휴대폰번호가 정확하지 않습니다.";
                    echo json_encode($result); exit;
                }
                if(trim($r_data['uagree']) != 'true') {
                    $result['error'] = true;
                    $result['err_msg'] = "개인정보의 수집·이용에 동의해주세요.";
                    echo json_encode($result); exit;
                }
            }

            if($r_data['ucontent']) $TMPL['ucontent'] = $r_data['ucontent'];

            $skinFile = $hform.'_'.$action.'_contents';
            $skin = new skin($skinFile);
            $result['msg'] = $skin->make('lib');
            $result['log'] = true;
        break;

        case('contents') :
            if(!trim($r_data['ucontent'])) {
                $result['error'] = true;
                $result['err_msg'] = "장애증상을 입력해주세요.";
                echo json_encode($result); exit;
            }

            $TMPL['data_row'] = getReserveInfoHtml($r_data);

            $skinFile = $hform.'_'.$action.'_confirm';
            $skin = new skin($skinFile);
            $result['msg'] = $skin->make('lib');
            $result['log'] = true;
        break;

        case('disability_confirm') :
            // 입력정보로 예약 post
            if(!trim($r_data['upart']) || !trim($r_data['uname']) || !trim($r_data['uphone'])) {
                $result['error'] = true;
                $result['err_msg'] = "신청 정보가 부족합니다.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['ucontent'])) {
                $result['error'] = true;
                $result['err_msg'] = "장애증상 정보가 부족합니다.";
                echo json_encode($result); exit;
            }

            if($cmod != 'dialog' && $cmod != 'skin' && $cmod != 'LC' && $cmod != 'TS') {
                $d_regis = $date['totime'];

                if($r_data['reserve_idx']) {
                    getDbUpdate('rb_chatbot_reserve', "content='".$r_data['ucontent']."'", 'uid='.$r_data['reserve_idx']);
                } else {
                    $QKEY = "vendor, bot, roomToken, category, name, phone, content, addval, status, d_regis";
                    $QVAL = "'$vendor', '$bot', '$roomToken', 'disability', '".$r_data['uname']."', '".$r_data['uphone']."', '".$r_data['ucontent']."', '".$r_data['upart']."', 'ready', '$d_regis'";
                    getDbInsert('rb_chatbot_reserve', $QKEY, $QVAL);
                }
            }

            //----------------------------
            $req_type = $r_data['reserve_idx'] ? "변경" : "신청";
            $bot_msg = "감사합니다. 정상적으로 ".$req_type."되었습니다.";
            $TMPL['response'] = "<span>".nl2br($bot_msg)."</span>";

            $skinFile = $hform.'_'.$action.'_result';
            $skin = new skin($skinFile);
            $content = $skin->make('lib');
            $result['msg'] = $content;
            $result['log'] = true;
            $result['finish'] = true;
        break;

        case('cancel') :
            // 모든 예약 process cancel
            $req_type = $r_data['reserve_idx'] ? "변경" : "신청";
            $bot_msg = $req_type."이 취소되었습니다.";
            $TMPL['response'] = "<span>".nl2br($bot_msg)."</span>";

            $skinFile = $hform.'_cancel';
            $skin = new skin($skinFile);
            $content = $skin->make('lib');
            $result['msg'] = $content;
            $result['log'] = true;
        break;
    }
}

if($action == "search" || $action == "modify" || $action == "cancel") {
    if($step == "auth") {
        if(!trim($r_data['uname'])) {
            $result['error'] = true;
            $result['err_msg'] = "이름을 입력해주세요.";
            echo json_encode($result); exit;
        }
        if(!trim($r_data['uphone'])) {
            $result['error'] = true;
            $result['err_msg'] = "휴대폰번호를 입력해주세요.";
            echo json_encode($result); exit;
        }
        if(!preg_match("/01[016789][\d]{3,4}[\d]{4}/", trim($r_data['uphone']))) {
            $result['error'] = true;
            $result['err_msg'] = "휴대폰번호가 정확하지 않습니다.";
            echo json_encode($result); exit;
        }

        $query = "Select * From rb_chatbot_reserve Where bot=".$pData['bot']." and category='disability' and name='".$r_data['uname']."' and phone='".$r_data['uphone']."'";
        $query .="Order by uid desc limit 1";
        $R = db_fetch_assoc(db_query($query, $DB_CONNECT));
        if($R['uid']) {
            $_date = date("Y-m-d H:i:s", strtotime($R['d_regis']));
            $apiResult['result'] = true;
            $apiResult['reserve'] = ['reserve_idx'=>$R['uid'], 'name'=>$R['name'], 'phone'=>$R['phone'], 'date'=>$_date, 'part'=>$R['addval'], 'content'=>$R['content']];
        } else {
            $apiResult['result'] = 0;
        }

        if($apiResult['result'] == 0 || $apiResult['reserve'] == "" || count($apiResult['reserve']) == 0) {
            $bot_msg = "접수된 정보가 없습니다.";
            $TMPL['response'] = "<span>".nl2br($bot_msg)."</span>";
            $skin = new skin('chat/bot_msg');
            $content = $skin->make();
            $result['msg'] = $content;
            $result['finish'] = true;
        } else {

            $reserveData = $apiResult['reserve'];
            $reserveData['upart'] = $reserveData['part'];
            $reserveData['uname'] = $reserveData['name'];
            $reserveData['uphone'] = $reserveData['phone'];
            $reserveData['ucontent'] = $reserveData['content'];

            $TMPL['data_row'] = getReserveInfoHtml($reserveData);
            $result['json_data'] = $reserveData;
            $result['func'] = "setReservedInfo";

            $skinFile = $hform.'_search_result';
            $skin = new skin($skinFile);
            $content = $skin->make('lib');
            $result['msg'] = $content;
            $result['log'] = true;
            $result['finish'] = true;
        }
    }

    // 예약 조회 취소
    if($step == "search_cancel") {
        $bot_msg = "취소되었습니다.";
        $TMPL['response'] = "<span>".nl2br($bot_msg)."</span>";

        $skinFile = $hform.'_cancel';
        $skin = new skin($skinFile);
        $content = $skin->make('lib');
        $result['msg'] = $content;
        $result['log'] = true;
    }

    // 예약 취소
    if($action == "cancel" && $step == "cancel") {
        if($r_data['reserve_idx']) {
            if($cmod != 'dialog' && $cmod != 'skin' && $cmod != 'LC' && $cmod != 'TS') {
                $query = "Delete From rb_chatbot_reserve Where bot=".$pData['bot']." and category='disability' and uid='".$r_data['reserve_idx']."'";
                db_query($query, $DB_CONNECT);
                $apiResult['result'] = 1;
            }

            if($apiResult['result']) {
                $bot_msg = "신청이 취소되었습니다.";
                $TMPL['response'] = "<span>".nl2br($bot_msg)."</span>";
                $skinFile = $hform.'_cancel';
                $skin = new skin($skinFile);
                $content = $skin->make('lib');
                $result['msg'] = $content;
                $result['log'] = true;
            }
        } else {
            $result['error'] = true;
            $result['err_msg'] = "취소 정보가 부족합니다.";
            echo json_encode($result); exit;
        }
    }
}

if($last_chat && $result['msg']) {
    $botChat = array();
    $botChat['content'] = array(array("hform", $result['msg']));
    $botChat['last_chat'] = $last_chat; // 사용자 chat uid
    $botChat['same_chat'] = 1;
    $chatbot->addBotChatLog($botChat);
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit;
?>
