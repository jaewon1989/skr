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

// 내부 예약일과 시간 정보 생성
function getReserveDates($data) {
    global $hform, $category;

    $aResult = array();
    $aResult['result'] = 1;
    $aResult['dates'] = array();

    $dStart = new DateTime(date("Y-m-d"));
    $dEnd = new DateTime(date("Y-m-d"));
    $dEnd = $dEnd->modify("+60 day");
    $interval = new DateInterval('P1D');

    $period = new DatePeriod($dStart, $interval, $dEnd);
    foreach($period as $date) {
        if($category != "hotel") {
            if($date->format("N") < 6) $aResult['dates'][] = $date->format("Y-m-d");
        } else {
            $aResult['dates'][] = $date->format("Y-m-d");
        }
    }

    $data['dataOnly'] = true;
    $aResult['times'] = getReserveTimes($data);
    return $aResult;
}

function getReserveTimes($data) {
    $aResult = array();
    $aResult['result'] = 1;
    $aResult['times'] = array();

    for($i=9; $i<=17; $i++) {
        $time = ($i<10 ? "0".$i : $i);
        $aResult['times'][] = $time.":00";
        $aResult['times'][] = $time.":30";
    }
    return $data['dataOnly'] ? $aResult['times'] : $aResult;
}
//--------------------------------------------------
// 외부 API로 예약 가능일 출력
function getReserveScheduleDate($data) {
    global $hform, $category, $action, $r_data, $chatbot, $TMPL, $dToday, $dNowTime;

    $result = array();

    if($data['noApi']) {
        $apiResult = getReserveDates($data);
    } else {
        $apiResult = $chatbot->getReserveAPI($data);
    }

    $html = $am_time = $pm_time = "";

    if(is_array($apiResult['times']) && count($apiResult['times']) > 0) {
        foreach($apiResult['times'] as $time) {
            $time = substr($time, 0, 5);
            $disabled = ($r_data['date']." ".$time < $dToday." ".$dNowTime) ? "disabled" : "";
            $on = $time == $r_data['time'] ? "on" : "";
            $hour = substr($time, 0, 2);
            $time_name = $time;
            if((int)$hour < 12) {
                $am_time .="   <li><button type='button' class='btn_radio ".$on." ".$disabled."' data-time='".$time."' ".$disabled.">".$time_name."</button></li>";
            } else {
                $pm_time .="   <li><button type='button' class='btn_radio ".$on." ".$disabled."' data-time='".$time."' ".$disabled.">".$time_name."</button></li>";
            }
        }

        if($am_time) {
            $html .="<div class='ul_button_title'>오전</div>";
            $html .="<ul class='reserve_time am ul_button four'>".$am_time."</ul>";
        }
        if($pm_time) {
            $html .="<div class='ul_button_title mt10'>오후</div>";
            $html .="<ul class='reserve_time pm ul_button four'>".$pm_time."</ul>";
        }
        $TMPL['data_row'] = $html;
    } else {
        $TMPL['data_row'] = "<div class='reserve_time acenter mt10 mb10'>희망하신 날짜의 예약이 불가능합니다.<br>다른 날짜를 선택해주세요.</div>";
    }

    $result['dates'] = array();
    foreach($apiResult['dates'] as $date) {
        if($date < $dToday) continue;
        $result['dates'][] = $date;
    }

    if(!$data['dataOnly']) {
        $skinFile = $hform.'_'.$category.'_'.$action.'_date';
        $skin = new skin($skinFile);
        $result['content'] = $skin->make('lib');
    }
    return $result;
}

// 외부 API로 예약 타임 출력
function getReserveScheduleTime($data) {
    global $r_data, $chatbot, $dToday, $dNowTime;

    $result = array();

    if($data['noApi']) {
        $apiResult = getReserveTimes($data);
    } else {
        $apiResult = $chatbot->getReserveAPI($data);
    }

    $html = $am_time = $pm_time = "";
    if($apiResult['result']) {
        // json값만 전달할 경우 json_data와 실행할 함수명 전달
        foreach($apiResult['times'] as $time) {
            $time = substr($time, 0, 5);
            $disabled = ($r_data['date_temp']." ".$time < $dToday." ".$dNowTime) ? "disabled" : "";
            $on = ($r_data['date'] == $r_data['date_temp'] && $time == $r_data['time']) ? "on" : "";
            $hour = substr($time, 0, 2);
            $time_name = $time;
            if((int)$hour < 12) {
                $am_time .="<li><button type='button' class='btn_radio ".$on." ".$disabled."' data-time='".$time."' ".$disabled.">".$time_name."</button></li>";
            } else {
                $pm_time .="<li><button type='button' class='btn_radio ".$on." ".$disabled."' data-time='".$time."' ".$disabled.">".$time_name."</button></li>";
            }
        }

        if($am_time) {
            $html .="<div class='ul_button_title'>오전</div>";
            $html .="<ul class='reserve_time am ul_button four'>".$am_time."</ul>";
        }
        if($pm_time) {
            $html .="<div class='ul_button_title mt10'>오후</div>";
            $html .="<ul class='reserve_time pm ul_button four'>".$pm_time."</ul>";
        }

        $result['times'] = $html;
    } else {
        $result['times'] = $html;
    }
    return $result;
}

// 의사 리스트 html
function getReserveDoctor($data) {
    global $g, $hform, $category, $action, $r_data, $chatbot, $TMPL;
    $tempDataDir = $g['path_tmp'].'cache';
    $result = array();

    // 1일 지난 파일 삭제
    $files = glob($tempDataDir.'/*');
    foreach($files as $file) {
        if((time()-filemtime($file)) >= (60*60*24*1)) @unlink($file);
    }

    $apiResult = $chatbot->getReserveAPI($data);
    if($apiResult['result']) {
        $apiData = $apiResult['doctor'];
        $html = "";
        foreach($apiData as $aData) {
            $on = $aData['idx'] == $r_data['doctor_idx'] ? "on" : "";
            if($aData['image']) {
                if(parse_url($aData['image'], PHP_URL_SCHEME) != "https") {
                    $aImg = explode('/', $aData['image']);
                    $_file = $tempDataDir.'/'.($_SESSION['mbr_uid'] ? $_SESSION['mbr_uid'] : $my['uid']).'_'.$chatbot->botuid.'_'.$aImg[(count($aImg)-1)];
                    if(file_exists($_file)) {
                        $aData['image'] = $_file;
                    } else {
                        $aData['image'] = $chatbot->getRemoteImage($aData['image'], $_file);
                    }
                }
            }
            $aData['image'] = preg_match('/\.jpg|\.jpeg|\.png|\.gif/i', $aData['image']) ? $aData['image'] : $g['path_core'].'images/no_doctor.png';
            $image = "style='background-image:url(".$aData['image'].")';";

            $html .="<li>";
            $html .="   <div class='btn_radio photo ".$on."' data-idx='".$aData['idx']."' data-name='".$aData['name']."'>";
            $html .="       <div class='face' ".$image."></div>";
            $html .="       <div class='name'>".$aData['name']."</div>";
            $html .="   </div>";
            $html .="</li>";
        }
        $TMPL['data_row'] = $html;
    } else {
        $TMPL['data_row'] = "<div class='acenter mt10 mb10'>데이터가 존재하지 않습니다.</div>";
        $TMPL['submit_disp'] = "dispnone";
    }

    $skinFile = $hform.'_'.$category.'_'.$action.'_doctor';
    $skin = new skin($skinFile);
    $result['content'] = $skin->make('lib');
    return $result;
}

// 정보 출력용
function getReserveInfoHtml($data) {
    global $category, $aWeek;
    $aItem = array();
    $aItem['hospital'] = array("uname"=>"예약자명", "uphone"=>"휴대폰번호", "branch_name"=>"지점", "department_name"=>"진료과목", "doctor_name"=>"의사명", "date"=>"예약시간");
    $aItem['academy'] = array("uname"=>"예약자명", "uphone"=>"휴대폰번호", "date"=>"예약시간");
    $aItem['normal'] = array("uname"=>"예약자명", "uphone"=>"휴대폰번호", "date"=>"예약시간");
    $aItem['hotel'] = array(
        "customer_name"=>"예약자명", "customer_email"=>"이메일", "reservation_status"=>"예약상태", "created_at"=>"예약일시", "checkin"=>"체크인", "checkout"=>"체크아웃", "room_name"=>"객실정보",
        "room_price"=>"예약인원", "goods_price"=>"상품", "total_price"=>"총 결제금액", "payment_status"=>"결제상태", "payment_method"=>"결제방법", "description"=>"요청사항"
    );
    $html = "";

    $aCategoryItem = $aItem[$category];
    foreach($aCategoryItem as $item=>$item_text) {
        if(isset($data[$item]) && $data[$item]) {
            if($item == "date") {
                $dWeek = $data['week'] ? $data['week'] : $aWeek[date("w", strtotime($data['date']))];
                $r_date = $data['date'];
                $r_date .="(".$dWeek.")";

                if($data['time']) {
                    $time_name = date('a h:i', strtotime($data['time']));
                    $time_name = str_replace("pm", "오후", str_replace("am", "오전", $time_name));
                    $r_date .=" ".$time_name;
                }
                $value = $r_date;
            } else {
                $value = $data[$item];
            }

            $html .="<li>";
            $html .="    <div>";
            $html .="        <span class='item'>".$item_text."</span>";
            $html .="        <div class='cont'>".$value."</div>";
            $html .="    </div>";
            $html .="</li>";
        }
    }
    return $html;
}

function getReserveCartOnda($data) {
    global $chatbot, $_data;

    $_data['end_point'] = 'bookings';

    $postParam = array();
    $postParam['checkin'] = $data['checkin'];
    $postParam['checkout'] = $data['checkout'];

    $postParam['nights'] = $data['hotel_room_nights'];

    $cart_item = array();
    $cart_item['rateplan_entry_id'] = (int)$data['hotel_room_rateplan_id'];
    $cart_item['adults'] = (int)$data['hotel_room_adult'];
    $cart_item['childrens'] = (int)$data['hotel_room_child'];
    $cart_item['total_price'] = $data['hotel_room_all_price'] ? (int)$data['hotel_room_all_price'] : (int)$data['hotel_room_price'];

    // product item
    $cart_item['extra_products'] = array();
    if(count($data['hotel_room_goods']) > 0) {
        foreach($data['hotel_room_goods'] as $aGoods) {
            $cart_item['extra_products'][] = array('id'=>(int)$aGoods['id'], 'quantity'=>(int)$aGoods['quantity']);
        }
    }

    $postParam['cart_items'][] = $cart_item;

    $_data['postParam']['_json'] = json_encode($postParam, JSON_UNESCAPED_UNICODE);
    $apiResult = $chatbot->getReserveAPI($_data);
    return $apiResult;
}

function getReserveHotelNights($checkin, $checkout) {
    $checkin = new DateTime($checkin);
    $checkout = new DateTime($checkout);
    $interval = $checkin->diff($checkout);
    return (int)$interval->format('%R%a');
}

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
            if($category != "hotel") {
                // 예약 인증정보만 출력
                $skinFile = $hform.'_'.$action.'_auth';
                $skin = new skin($skinFile);
                $content = $skin->make('lib');
                $result['msg'] = $content;
            } else {
                $r_data['date'] = !$r_data['date'] ? $dToday : $r_data['date'];
                $r_data['nights'] = !$r_data['nights'] ? 1 : $r_data['nights'];
                $dStart = new DateTime($r_data['date']);
                $dEnd = $dStart->modify("+".$r_data['nights']." day");
                $dEnd = $dEnd->format("Y-m-d");
                $result['dates'][] = $r_data['date'];
                $result['dates'][] = $dEnd;

                $skinFile = $hform.'_'.$category.'_'.$action.'_date';
                $skin = new skin($skinFile);
                $result['msg'] = $skin->make('lib');
            }
        break;

        case('auth') :
            if(!$r_data['reserve_idx']) {
                if(!trim($r_data['uname'])) {
                    $result['error'] = true;
                    $result['err_msg'] = "예약자명을 입력해주세요.";
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

            // 업종별 초기 예약 스텝
            // 병원
            if($category == "hospital") {
                $_data['postParam']['bot_id'] = $botid;
                $_data['postParam']['mode'] = 'get_branch';
                $_data['postParam']['date'] = $r_data['date'];

                $apiResult = $chatbot->getReserveAPI($_data);
                if($apiResult['result']) {
                    // 지점 1개일 경우 진료과목으로 조회
                    if(count($apiResult['branch']) == 1) {
                        $r_data['branch_idx'] = $apiResult['branch'][0]['idx'];
                        $r_data['branch_name'] = $apiResult['branch'][0]['name'];

                        $_data['postParam']['mode'] = 'get_department';
                        $_data['postParam']['branch_idx'] = $r_data['branch_idx'];
                        $_data['postParam']['branch_name'] = $r_data['branch_name'];
                        $_data['postParam']['date'] = $r_data['date'];

                        $apiResult = $chatbot->getReserveAPI($_data);
                        if($apiResult['result']) {
                            // 진료과목 1개일 경우 의사로 조회
                            if(count($apiResult['department']) == 1) {
                                $r_data['department_idx'] = $apiResult['department'][0]['idx'];
                                $r_data['department_name'] = $apiResult['department'][0]['name'];

                                $_data['postParam']['mode'] = 'get_doctor';
                                $_data['postParam']['branch_idx'] = $r_data['branch_idx'];
                                $_data['postParam']['branch_name'] = $r_data['branch_name'];
                                $_data['postParam']['department_idx'] = $r_data['department_idx'];
                                $_data['postParam']['department_name'] = $r_data['department_name'];
                                $_data['postParam']['date'] = $r_data['date'];

                                $aDoctors = getReserveDoctor($_data);
                                $result['msg'] = $aDoctors['content'];
                                $result['json_data'] = $r_data;
                                $result['func'] = "setReservedInfo";

                            } else {
                                $apiData = $apiResult['department'];

                                $html = "";
                                foreach($apiData as $aData) {
                                    $on = $aData['idx'] == $r_data['department_idx'] ? "on" : "";
                                    $html .="<li><button type='button' class='btn_radio ".$on."' data-idx='".$aData['idx']."' data-name='".$aData['name']."'>".$aData['name']."</button></li>";
                                }
                                $TMPL['data_row'] = $html;

                                $skinFile = $hform.'_'.$category.'_'.$action.'_department';
                                $result['json_data'] = $r_data;
                                $result['func'] = "setReservedInfo";
                            }
                        } else {
                            $skinFile = $hform.'_'.$category.'_'.$action.'_department';
                            $TMPL['data_row'] = "<div class='acenter mt10 mb10'>데이터가 존재하지 않습니다.</div>";
                            $TMPL['submit_disp'] = "dispnone";
                            $result['finish'] = true;
                        }
                    } else {
                        $apiData = $apiResult['branch'];
                        $skinFile = $hform.'_'.$category.'_'.$action.'_branch';

                        $html = "";
                        foreach($apiData as $aData) {
                            $on = $aData['idx'] == $r_data['branch_idx'] ? "on" : "";
                            $html .="<li><button type='button' class='btn_radio ".$on."' data-idx='".$aData['idx']."' data-name='".$aData['name']."'>".$aData['name']."</button></li>";
                        }
                        $TMPL['data_row'] = $html;
                    }

                } else {
                    $skinFile = $hform.'_'.$category.'_'.$action.'_branch';
                    $TMPL['data_row'] = "<div class='acenter mt10 mb10'>데이터가 존재하지 않습니다.</div>";
                    $TMPL['submit_disp'] = "dispnone";
                    $result['finish'] = true;
                }

                if(!$result['msg']) {
                    $skin = new skin($skinFile);
                    $content = $skin->make('lib');
                    $result['msg'] = $content;
                }
            }

            // 학원, 일반
            if($category == "academy" || $category == "normal") {
                $r_data['date'] = !$r_data['date'] ? $dToday : $r_data['date'];

                $_data['postParam']['bot_id'] = $botid;
                $_data['postParam']['mode'] = 'get_dates';
                $_data['postParam']['date'] = $r_data['date'];
                $_data['noApi'] = true; // 외부 API 사용하지 않을 때

                $aDates = getReserveScheduleDate($_data);
                $result['dates'] = $aDates['dates'];
                $result['msg'] = $aDates['content'];
            }
        break;

        case('branch') :
            if(!trim($r_data['branch_idx']) && !trim($r_data['branch_name'])) {
                $result['error'] = true;
                $result['err_msg'] = "지점을 선택해주세요.";
                echo json_encode($result); exit;
            }

            // api 조회
            $_data['postParam']['bot_id'] = $botid;
            $_data['postParam']['mode'] = 'get_department';
            $_data['postParam']['branch_idx'] = $r_data['branch_idx'];
            $_data['postParam']['branch_name'] = $r_data['branch_name'];
            $_data['postParam']['date'] = $r_data['date'];

            $apiResult = $chatbot->getReserveAPI($_data);
            if($apiResult['result']) {
                // 진료과목 1개일 경우 의사로 조회
                if(count($apiResult['department']) == 1) {
                    $r_data['department_idx'] = $apiResult['department'][0]['idx'];
                    $r_data['department_name'] = $apiResult['department'][0]['name'];

                    $_data['postParam']['mode'] = 'get_doctor';
                    $_data['postParam']['branch_idx'] = $r_data['branch_idx'];
                    $_data['postParam']['branch_name'] = $r_data['branch_name'];
                    $_data['postParam']['department_idx'] = $r_data['department_idx'];
                    $_data['postParam']['department_name'] = $r_data['department_name'];
                    $_data['postParam']['date'] = $r_data['date'];

                    $aDoctors = getReserveDoctor($_data);
                    $result['msg'] = $aDoctors['content'];
                    $result['json_data'] = $r_data;
                    $result['func'] = "setReservedInfo";

                } else {
                    $apiData = $apiResult['department'];
                    $skinFile = $hform.'_'.$category.'_'.$action.'_department';

                    $html = "";
                    foreach($apiData as $aData) {
                        $on = $aData['idx'] == $r_data['department_idx'] ? "on" : "";
                        $html .="<li><button type='button' class='btn_radio ".$on."' data-idx='".$aData['idx']."' data-name='".$aData['name']."'>".$aData['name']."</button></li>";
                    }
                    $TMPL['data_row'] = $html;
                }
            } else {
                $skinFile = $hform.'_'.$category.'_'.$action.'_department';
                $TMPL['data_row'] = "<div class='acenter mt10 mb10'>데이터가 존재하지 않습니다.</div>";
                $TMPL['submit_disp'] = "dispnone";
                $result['finish'] = true;
            }

            if(!$result['msg']) {
                $skin = new skin($skinFile);
                $content = $skin->make('lib');
                $result['msg'] = $content;
            }
        break;

        case('department') :
            if(!trim($r_data['department_idx']) && !trim($r_data['department_name'])) {
                $result['error'] = true;
                $result['err_msg'] = "진료과목을 선택해주세요.";
                echo json_encode($result); exit;
            }

            // api 조회
            $_data['postParam']['bot_id'] = $botid;
            $_data['postParam']['mode'] = 'get_doctor';
            $_data['postParam']['branch_idx'] = $r_data['branch_idx'];
            $_data['postParam']['branch_name'] = $r_data['branch_name'];
            $_data['postParam']['department_idx'] = $r_data['department_idx'];
            $_data['postParam']['department_name'] = $r_data['department_name'];
            $_data['postParam']['date'] = $r_data['date'];

            $aDoctors = getReserveDoctor($_data);
            $result['msg'] = $aDoctors['content'];
        break;

        case('doctor') :
            if(!trim($r_data['doctor_idx']) && !trim($r_data['doctor_name'])) {
                $result['error'] = true;
                $result['err_msg'] = "의사를 선택해주세요.";
                echo json_encode($result); exit;
            }

            // api 조회
            $_data['postParam']['bot_id'] = $botid;
            $_data['postParam']['mode'] = 'get_dates';
            $_data['postParam']['name'] = $r_data['uname'];
            $_data['postParam']['phone'] = $r_data['uphone'];
            $_data['postParam']['branch_idx'] = $r_data['branch_idx'];
            $_data['postParam']['branch_name'] = $r_data['branch_name'];
            $_data['postParam']['department_idx'] = $r_data['department_idx'];
            $_data['postParam']['department_name'] = $r_data['department_name'];
            $_data['postParam']['doctor_idx'] = $r_data['doctor_idx'];
            $_data['postParam']['doctor_name'] = $r_data['doctor_name'];
            $_data['postParam']['date'] = $r_data['date'];
            if($r_data['reserve_idx']) {
                $_data['postParam']['reserve_idx'] = $r_data['reserve_idx'];
            }

            $aDates = getReserveScheduleDate($_data);
            $result['dates'] = $aDates['dates'];
            $result['msg'] = $aDates['content'];
        break;

        case('date') :
            // 호텔예약(온다)일 경우 예약 가능객실 출력
            if($category == "hotel") {
                $checkin = $r_data['checkin'];
                $checkout = $r_data['checkout'];
                if(!$checkin && !$checkout) {
                    $result['error'] = true;
                    $result['err_msg'] = "입실일, 퇴실일을 선택해주세요.";
                    echo json_encode($result); exit;
                } else {
                    if(!$checkin) {
                        $result['error'] = true;
                        $result['err_msg'] = "입실일을 선택해주세요.";
                        echo json_encode($result); exit;
                    }
                    if(!$checkout) {
                        $result['error'] = true;
                        $result['err_msg'] = "퇴실일을 선택해주세요.";
                        echo json_encode($result); exit;
                    }
                    if($checkin > $checkout) {
                        $result['error'] = true;
                        $result['err_msg'] = "입실일이 퇴실일보다 빠르게 설정되었습니다.";
                        echo json_encode($result); exit;
                    }
                }

                // hotel 예약일 경우 GET
                $_data['end_point'] = 'bookings/search';
                $_data['getParam']['checkin'] = $checkin;
                $_data['getParam']['checkout'] = $checkout;
                $_data['getParam']['locale'] = 'ko-KR';

                $skinFile = $hform.'_'.$category.'_'.$action.'_room';

                $aCheckIn = explode("-", $checkin);
                $aCheckOut = explode("-", $checkout);
                $TMPL['checkin_date'] = (int)$aCheckIn[1]."월 ".(int)$aCheckIn[2]."일";
                $TMPL['checkout_date'] = (int)$aCheckOut[1]."월 ".(int)$aCheckOut[2]."일";
                $TMPL['period_date'] = getDatePeriodKorean($checkin, $checkout);
                $TMPL['nights'] = getReserveHotelNights($checkin, $checkout);

                // 예약 가능 객실 검색, 출력
                $apiResult = $chatbot->getReserveAPI($_data);
                if(!isset($apiResult['result']) && !$apiResult['result']) {
                    $aRoomTypes = $apiResult['roomtypes'];
                    $roomListType = strpos($pData['bot_skin'], "_card") !== false ? "card" : "list";

                    $html = "";

                    if($roomListType == "card") {
                        $html .="<div class='card_room swiper-container'>";
                        $html .="    <div class='swiper-wrapper'>";
                    }

                    foreach($aRoomTypes as $aRoom) {
                        if($aRoom['avail'] == 0 || $aRoom['is_available'] == false) continue;

                        $price = $aRoom['rateplans'][0]['basic_price'];
                        $room_infos = $aRoom['size_sq_meter']."㎡, 방".$aRoom['in_rooms'];
                        $room_infos .=($aRoom['kitchens'] ? ", 주방".$aRoom['kitchens'] : "");
                        $room_infos .=($aRoom['livingrooms'] ? ", 거실".$aRoom['livingrooms'] : "");
                        //$room_infos .=($aRoom['bathrooms'] ? ", 욕실".$aRoom['bathrooms'] : "");

                        if($roomListType == "list") {
                            // list형 룸스킨
                            $html .="<div class='cardbox photo btn_hotel_room'>";
                            $html .="    <div class='face swiper-container' data-roomid='".$aRoom['id']."'>";
                            $html .="        <ul class='swiper-wrapper'>";
                            if(count($aRoom['images']) == 0) {
                                $html .="        <li class='swiper-slide'></li>";
                            } else {
                                foreach($aRoom['images'] as $aImage) {
                                    $html .="    <li class='swiper-slide' style='background-image:url(".$aImage['file_url'].");'></li>";
                                }
                            }
                            $html .="        </ul>";
                            $html .="    </div>";
                            $html .="    <div class='info'>";
                            $html .="        <div class='name'>".$aRoom['name']."</div>";
                            $html .="        <div class='subs'>".$aRoom['min_capacity']."인 기준 / 최대 ".$aRoom['max_capacity']."인 | ".$room_infos."</div>";
                            foreach($aRoom['rateplans'] as $aRate) {
                                $refunds = $aRate['refundable'] ? json_encode($aRate['refunds']) : "";

                                $html .="    <div class='rateplan' data-roomid='".$aRoom['id']."' data-rateplanid='".$aRate['rateplan_entry_id']."' data-price='".$aRate['total_price']."' data-min='".$aRoom['min_capacity']."' data-max='".$aRoom['max_capacity']."' data-adult='".$aRate['extra_adult_price']."' data-child='".$aRate['extra_child_price']."'>";
                                $html .="        <div class='rplan'>";
                                $html .="            <span class='rate_name'>".$aRate['name']."</span>";
                                $html .="            <span class='rate_price'>&#8361; ".number_format($aRate['total_price'])."</span>";
                                $html .="            <button type='button' class='btn_rateplan btn_select kwd-item'>선택</button>";
                                $html .="        </div>";
                                $html .="        <div class='add_input_info' _type='person'>";
                                $html .="            <div class='fl'>";
                                $html .="                <div class='item'>성인</div>";
                                $html .="                <ul class='ul_count_float adult' data-min='1'>";
                                $html .="                    <li class='btn_cnt btn_cnt_minus'><button type='button'>-</button></li>";
                                $html .="                    <li class='count_cnt'><span class='count_no'>".$aRoom['min_capacity']."</span>명</li>";
                                $html .="                    <li class='btn_cnt btn_cnt_plus'><button type='button'>+</button></li>";
                                $html .="                    <input type='hidden' class='count_input' name='nAdult' value='".$aRoom['min_capacity']."'>";
                                $html .="                </ul>";
                                $html .="            </div>";
                                $html .="            <div class='fr'>";
                                $html .="                <div class='item'>아동</div>";
                                $html .="                <ul class='ul_count_float child' data-min='0'>";
                                $html .="                    <li class='btn_cnt btn_cnt_minus'><button type='button'>-</button></li>";
                                $html .="                    <li class='count_cnt'><span class='count_no'>0</span>명</li>";
                                $html .="                    <li class='btn_cnt btn_cnt_plus'><button type='button'>+</button></li>";
                                $html .="                    <input type='hidden' class='count_input' name='nChild' value='0'>";
                                $html .="                </ul>";
                                $html .="            </div>";
                                $html .="        </div>";
                                $html .="        <div class='total_price'>";
                                $html .="            <span>총 결제금액</span><span>&#8361; <em>".number_format($aRate['total_price'])."</em></span>";
                                $html .="            <input type='hidden' class='extra_adult_price' name='extra_adult_price' value='0'>";
                                $html .="            <input type='hidden' class='extra_child_price' name='extra_child_price' value='0'>";
                                $html .="            <input type='hidden' class='total_price' name='total_price' value='".$aRate['total_price']."'>";
                                $html .="            <input type='hidden' class='refunds' name='refunds' value='".$refunds."'>";
                                $html .="        </div>";
                                $html .="    </div>";
                            }
                            $html .="    </div>";
                            $html .="</div>";

                        } else {

                            // card형 룸스킨
                            $html .="<div class='cardbox swiper-slide'>";
                            $html .="   <div class='card_img swiper-container' data-roomid='".$aRoom['id']."'>";
                            $html .="       <ul class='swiper-wrapper'>";
                            if(count($aRoom['images']) == 0) {
                                $html .="       <li class='swiper-slide'></li>";
                            } else {
                                foreach($aRoom['images'] as $aImage) {
                                    $html .="   <li class='swiper-slide' style='background-image:url(".$aImage['file_url'].");'></li>";
                                }
                            }


                            $html .="       </ul>";
                            $html .="   </div>";
                            $html .="   <div class='info'>";
                            $html .="       <div class='name'>".$aRoom['name']."</div>";
                            $html .="       <div class='subs'>".$aRoom['min_capacity']."인 기준 / 최대 ".$aRoom['max_capacity']."인 | ".$room_infos."</div>";
                            foreach($aRoom['rateplans'] as $aRate) {
                                $refunds = $aRate['refundable'] ? json_encode($aRate['refunds']) : "";

                                $html .="   <div class='rateplan' data-roomid='".$aRoom['id']."' data-rateplanid='".$aRate['rateplan_entry_id']."' data-price='".$aRate['total_price']."' data-min='".$aRoom['min_capacity']."' data-max='".$aRoom['max_capacity']."' data-adult='".$aRate['extra_adult_price']."' data-child='".$aRate['extra_child_price']."'>";
                                $html .="       <div class='rplan'>";
                                $html .="           <span class='rate_name'>".$aRate['name']."</span>";
                                $html .="           <span class='rate_price'>&#8361; ".number_format($aRate['total_price'])."</span>";
                                $html .="           <button type='button' class='btn_rateplan btn_select kwd-item'>선택</button>";
                                $html .="       </div>";
                                $html .="       <div class='add_input_info' _type='person'>";
                                $html .="           <div class='fl'>";
                                $html .="               <div class='item'>성인</div>";
                                $html .="               <ul class='ul_count_float adult' data-min='1'>";
                                $html .="                   <li class='btn_cnt btn_cnt_minus'><button type='button'>-</button></li>";
                                $html .="                   <li class='count_cnt'><span class='count_no'>".$aRoom['min_capacity']."</span>명</li>";
                                $html .="                   <li class='btn_cnt btn_cnt_plus'><button type='button'>+</button></li>";
                                $html .="                   <input type='hidden' class='count_input' name='nAdult' value='".$aRoom['min_capacity']."'>";
                                $html .="               </ul>";
                                $html .="           </div>";
                                $html .="           <div class='fr'>";
                                $html .="               <div class='item'>아동</div>";
                                $html .="               <ul class='ul_count_float child' data-min='0'>";
                                $html .="                   <li class='btn_cnt btn_cnt_minus'><button type='button'>-</button></li>";
                                $html .="                   <li class='count_cnt'><span class='count_no'>0</span>명</li>";
                                $html .="                   <li class='btn_cnt btn_cnt_plus'><button type='button'>+</button></li>";
                                $html .="                   <input type='hidden' class='count_input' name='nChild' value='0'>";
                                $html .="               </ul>";
                                $html .="           </div>";
                                $html .="       </div>";
                                $html .="       <div class='total_price'>";
                                $html .="           <span>총 결제금액</span><span>&#8361; <em>".number_format($aRate['total_price'])."</em></span>";
                                $html .="           <input type='hidden' class='extra_adult_price' name='extra_adult_price' value='0'>";
                                $html .="           <input type='hidden' class='extra_child_price' name='extra_child_price' value='0'>";
                                $html .="           <input type='hidden' class='total_price' name='total_price' value='".$aRate['total_price']."'>";
                                $html .="           <input type='hidden' class='refunds' name='refunds' value='".$refunds."'>";
                                $html .="       </div>";
                                $html .="   </div>";
                            }
                            $html .="   </div>";
                            $html .="   <div class='btn_wrap'>";
                            $html .="       <button type='button' class='kwd-item cancel cancel_reserve' data-hform='".$hform."' data-action='".$action."' data-category='".$category."' data-lastchat='".$last_chat."' data-step='cancel'>취소</button>";
                            $html .="       <button type='button' class='kwd-item submit_reserve' data-hform='".$hform."' data-action='".$action."' data-category='".$category."' data-lastchat='".$last_chat."' data-step='hotel_room'>확인</button>";
                            $html .="   </div>";
                            $html .="</div>";
                        }
                    }

                    if($roomListType == "card") {
                        $html .="   </div>";
                        $html .="</div>";
                    }

                    $TMPL['data_row'] = $html;

                } else {
                    $TMPL['data_row'] = "<div class='acenter mt10 mb10'>데이터가 존재하지 않습니다.</div>";
                    $TMPL['submit_disp'] = "dispnone";
                    $result['finish'] = true;
                }

            } else {
                if(!trim($r_data['date'])) {
                    $result['error'] = true;
                    $result['err_msg'] = "예약일을 선택해주세요.";
                    echo json_encode($result); exit;
                }
                if(!trim($r_data['time'])) {
                    $result['error'] = true;
                    $result['err_msg'] = "예약시간을 선택해주세요.";
                    echo json_encode($result); exit;
                }

                $TMPL['data_row'] = getReserveInfoHtml($r_data);
                $TMPL['r_type_name'] = $r_data['reserve_idx'] ? "변경" : "신청";

                $skinFile = $hform.'_'.$action.'_confirm';
            }

            $skin = new skin($skinFile);
            $content = $skin->make('lib');
            $result['msg'] = $content;
        break;

        case('time') :
            if(!trim($r_data['date_temp'])) {
                $result['error'] = true;
                $result['err_msg'] = "예약일을 선택해주세요.";
                echo json_encode($result); exit;
            }

            // api 조회
            $_data['postParam']['bot_id'] = $botid;

            if($category == "academy" || $category == "normal") {
                $_data['postParam']['mode'] = 'get_times';
                $_data['postParam']['date'] = $r_data['date_temp'];
                $_data['noApi'] = true; // 외부 API 사용하지 않을 때
            } else {
                $_data['postParam']['mode'] = 'get_times';
                $_data['postParam']['name'] = $r_data['uname'];
                $_data['postParam']['phone'] = $r_data['uphone'];
                $_data['postParam']['branch_idx'] = $r_data['branch_idx'];
                $_data['postParam']['branch_name'] = $r_data['branch_name'];
                $_data['postParam']['department_idx'] = $r_data['department_idx'];
                $_data['postParam']['department_name'] = $r_data['department_name'];
                $_data['postParam']['doctor_idx'] = $r_data['doctor_idx'];
                $_data['postParam']['doctor_name'] = $r_data['doctor_name'];
                $_data['postParam']['date'] = $r_data['date_temp'];
                if($r_data['reserve_idx']) {
                    $_data['postParam']['reserve_idx'] = $r_data['reserve_idx'];
                }
            }

            $aTimes = getReserveScheduleTime($_data);
            $result['json_data']['times'] = $aTimes['times'];
            if($aTimes['times']) {
                $result['func'] = "setReserveTimes";
            }
        break;

        // 호텔 객실 선택
        case('hotel_room') :
            if(!trim($r_data['hotel_room_idx']) || !trim($r_data['hotel_room_rateplan_id'])) {
                $result['error'] = true;
                $result['err_msg'] = "예약 객실을 선택해주세요.";
                echo json_encode($result); exit;
            }

            // 상품 검색, 출력
            $_data['end_point'] = 'products';
            $_data['getParam']['locale'] = 'ko-KR';

            $apiResult = $chatbot->getReserveAPI($_data);
            if(count($apiResult) > 0) {
                $html = "";
                foreach($apiResult as $aGoods) {
                    $html .="<li>";
                    $html .="    <button type='button' class='fwrap btn_radio btn_toggle btn_hotel_goods'>";
                    $html .="        <span class='fl name'><i class='fa fa-check-circle' aria-hidden='true'></i>".$aGoods['name']."</span>";
                    $html .="        <span class='fr aright des'>&#8361; ".number_format($aGoods['basic_price'])."</span>";
                    $html .="    </button>";
                    $html .="    <div class='add_input_info' _type='goods' data-idx='".$aGoods['id']."' data-price='".$aGoods['basic_price']."'>";
                    $html .="        <div class='fl w55'>";
                    $html .="            <div class='item'>갯수</div>";
                    $html .="            <ul class='ul_count_float' data-min='0'>";
                    $html .="                <li class='btn_cnt btn_cnt_minus'><button type='button'>-</button></li>";
                    $html .="                <li class='count_cnt'><span class='count_no'>1</span></li>";
                    $html .="                <li class='btn_cnt btn_cnt_plus'><button type='button'>+</button></li>";
                    $html .="                <input type='hidden' class='count_input' name='nGoods' value='1'>";
                    $html .="            </ul>";
                    $html .="        </div>";
                    $html .="        <div class='fr'>";
                    $html .="            <span class='goods_price'>&#8361; <em>".number_format($aGoods['basic_price'])."</em></span>";
                    $html .="            <input type='hidden' class='total_price' name='total_price' value='".$aGoods['basic_price']."'>";
                    $html .="        </div>";
                    $html .="    </div>";
                    $html .="</li>";
                }

                $skinFile = $hform.'_'.$category.'_'.$action.'_goods';
                $TMPL['total_room_price'] = $r_data['hotel_room_price'];
                $TMPL['total_room_price_text'] = number_format($r_data['hotel_room_price']);
                $TMPL['data_row'] = $html;
            } else {
                // 상품이 없다면 예약결제 창 표시

                $skinFile = $hform.'_'.$category.'_'.$action.'_confirm';

                $_data['api_path'] = "/be/notice";
                $apiResult = $chatbot->getReserveAPI($_data);
                $TMPL['agree_notice'] = nl2br($apiResult);

                $_data['api_path'] = "/be/privacy";
                $apiResult = $chatbot->getReserveAPI($_data);
                $TMPL['agree_privacy'] = nl2br($apiResult);

                $_data['api_path'] = "/be/marketing";
                $apiResult = $chatbot->getReserveAPI($_data);
                $TMPL['agree_marketing'] = nl2br($apiResult);

                if($pData['hotel_room_refunds']) {
                    $aRefunds = json_decode(stripslashes($pData['hotel_room_refunds']), true);
                    $html = "<h1>취소 환불 규정</h1>";
                    $html .="<fieldset class='form_field'>";
                    $html .="   <ul class='ul_confirm'>";
                    foreach($aRefunds['penalties'] as $aPenalties) {
                        $html .="<li>";
                        $html .="   <span class='item'>".($aPenalties['day'] == 0 ? "입실 당일" : $aPenalties['day']."일전 취소")."</span>";
                        $html .="   <div class='cont'>".$aPenalties['percent']."% 패널티</div>";
                        $html .="</li>";
                    }
                    $html .="   </ul>";
                    $html .="   <div class='mt10 kr13'>* 패널티 적용 기준시간 : ".$aRefunds['time']."</div>";
                    $html .="</fieldset>";
                    $html .="<hr class='hr' />";

                    $TMPL['reserve_refunds'] = $html;
                }

                $html = "";
                $html .="<li><div><span class='item'>체크인</span><div class='cont'>".$r_data['checkin']."(".$aWeek[date("w", strtotime($r_data['checkin']))].")</div></div></li>";
                $html .="<li><div><span class='item'>체크아웃</span><div class='cont'>".$r_data['checkout']."(".$aWeek[date("w", strtotime($r_data['checkout']))].")</div></div></li>";
                $html .="<li><div><span class='item'>숙박일</span><div class='cont'>".$r_data['hotel_room_nights']."박</div></div></li>";
                $html .="<li><div><span class='item'>예약객실</span><div class='cont'>".$r_data['hotel_room_name']."</div></div></li>";
                $html .="<li><div><span class='item'>객실료</span><div class='cont'>&#8361; ".number_format($r_data['hotel_room_basic_price'])."</div></div></li>";
                if($r_data['hotel_room_add_adult_price']) {
                    $html .="<li><div><span class='item'>성인추가요금</span><div class='cont'>&#8361; ".number_format($r_data['hotel_room_add_adult_price'])."</div></div></li>";
                }
                if($r_data['hotel_room_add_child_price']) {
                    $html .="<li><div><span class='item'>아동추가요금</span><div class='cont'>&#8361; ".number_format($r_data['hotel_room_add_child_price'])."</div></div></li>";
                }
                $html .="<li><div><span class='item'>총 결제금액</span><div class='cont'>&#8361; ".number_format($r_data['hotel_room_price'])."</div></div></li>";
                $TMPL['reserve_cart_data'] = $html;
                $result['log'] = true;
            }

            $skin = new skin($skinFile);
            $content = $skin->make('lib');
            $result['msg'] = $content;
        break;

        case('hotel_goods') :
            // 예약결제 창 표시
            $skinFile = $hform.'_'.$category.'_'.$action.'_confirm';

            $_data['api_path'] = "/be/notice";
            $apiResult = $chatbot->getReserveAPI($_data);
            $TMPL['agree_notice'] = nl2br($apiResult);

            $_data['api_path'] = "/be/privacy";
            $apiResult = $chatbot->getReserveAPI($_data);
            $TMPL['agree_privacy'] = nl2br($apiResult);

            $_data['api_path'] = "/be/marketing";
            $apiResult = $chatbot->getReserveAPI($_data);
            $TMPL['agree_marketing'] = nl2br($apiResult);

            if($pData['hotel_room_refunds']) {
                $aRefunds = json_decode(stripslashes($pData['hotel_room_refunds']), true);
                $html = "<h1>취소 환불 규정</h1>";
                $html .="<fieldset class='form_field'>";
                $html .="   <ul class='ul_confirm'>";
                foreach($aRefunds['penalties'] as $aPenalties) {
                    $html .="<li>";
                    $html .="   <span class='item'>".($aPenalties['day'] == 0 ? "입실 당일" : $aPenalties['day']."일전 취소")."</span>";
                    $html .="   <div class='cont'>".$aPenalties['percent']."% 패널티</div>";
                    $html .="</li>";
                }
                $html .="   </ul>";
                $html .="   <div class='mt10 kr13'>* 패널티 적용 기준시간 : ".$aRefunds['time']."</div>";
                $html .="</fieldset>";
                $html .="<hr class='hr' />";

                $TMPL['reserve_refunds'] = $html;
            }

            $html = "";
            $html .="<li><div><span class='item'>체크인</span><div class='cont'>".$r_data['checkin']."(".$aWeek[date("w", strtotime($r_data['checkin']))].")</div></div></li>";
            $html .="<li><div><span class='item'>체크아웃</span><div class='cont'>".$r_data['checkout']."(".$aWeek[date("w", strtotime($r_data['checkout']))].")</div></div></li>";
            $html .="<li><div><span class='item'>숙박일</span><div class='cont'>".$r_data['hotel_room_nights']."박</div></div></li>";
            $html .="<li><div><span class='item'>예약객실</span><div class='cont'>".$r_data['hotel_room_name']."</div></div></li>";
            $html .="<li><div><span class='item'>객실료</span><div class='cont'>&#8361; ".number_format($r_data['hotel_room_basic_price'])."</div></div></li>";
            if($r_data['hotel_room_add_adult_price']) {
                $html .="<li><div><span class='item'>성인추가요금</span><div class='cont'>&#8361; ".number_format($r_data['hotel_room_add_adult_price'])."</div></div></li>";
            }
            if($r_data['hotel_room_add_child_price']) {
                $html .="<li><div><span class='item'>아동추가요금</span><div class='cont'>&#8361; ".number_format($r_data['hotel_room_add_child_price'])."</div></div></li>";
            }
            if(count($r_data['hotel_room_goods']) > 0) {
                foreach($r_data['hotel_room_goods'] as $idx=>$aGoods) {
                    $goods_name = $aGoods['quantity'] > 1 ? $aGoods['name']."(x".$aGoods['quantity'].")" : $aGoods['name'];
                    $html .="<li><div><span class='item'>".$goods_name."</span><div class='cont'>&#8361; ".number_format($aGoods['price'])."</div></div></li>";
                }
            }
            $html .="<li><div><span class='item'>총 결제금액</span><div class='cont'>&#8361; ".number_format($r_data['hotel_room_all_price'])."</div></div></li>";
            $TMPL['reserve_cart_data'] = $html;

            $skin = new skin($skinFile);
            $content = $skin->make('lib');
            $result['msg'] = $content;
            $result['log'] = true;

        break;

        case('hotel_confirm') :
            if(!trim($r_data['upaymethod'])) {
                $result['error'] = true;
                $result['err_msg'] = "결제방법을 선택해주세요.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['uagreecheck'])) {
                $result['error'] = true;
                $result['err_msg'] = "약관에 동의해주세요.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['ulast_name'])) {
                $result['error'] = true;
                $result['err_msg'] = "성을 입력해주세요.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['ufirst_name'])) {
                $result['error'] = true;
                $result['err_msg'] = "이름을 입력해주세요.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['uphone'])) {
                $result['error'] = true;
                $result['err_msg'] = "휴대폰번호를 입력해주세요.";
                echo json_encode($result); exit;
            }
            if(!getCheckValidFormat('mobile', $r_data['uphone'])) {
                $result['error'] = true;
                $result['err_msg'] = "휴대폰번호가 정확하지 않습니다.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['uemail'])) {
                $result['error'] = true;
                $result['err_msg'] = "이메일을 입력해주세요.";
                echo json_encode($result); exit;
            }
            if(!getCheckValidFormat('email', $r_data['uemail'])) {
                $result['error'] = true;
                $result['err_msg'] = "이메일 주소가 정확하지 않습니다.";
                echo json_encode($result); exit;
            }

            // 객실 금액 계산 확인
            $_add_adult_price = $_add_child_price = $_room_price = $_goods_price = 0;
            $nTotalCnt = ((int)$r_data['hotel_room_adult']+(int)$r_data['hotel_room_child']);
            if($nTotalCnt > (int)$r_data['hotel_room_min']) {
                if((int)$r_data['hotel_room_adult'] > (int)$r_data['hotel_room_min']) {
                    $_add_adult_price = ((((int)$r_data['hotel_room_adult']-(int)$r_data['hotel_room_min'])*(int)$r_data['hotel_room_rateplan_adult'])*(int)$r_data['hotel_room_nights']);
                    $_add_child_price = (((int)$r_data['hotel_room_child']*(int)$r_data['hotel_room_rateplan_child'])*(int)$r_data['hotel_room_nights']);
                } else {
                    $_add_child_price = ((($nTotalCnt-(int)$r_data['hotel_room_min'])*(int)$r_data['hotel_room_rateplan_child'])*(int)$r_data['hotel_room_nights']);
                }
                $_room_price = ((int)$r_data['hotel_room_basic_price'] + ($_add_adult_price + $_add_child_price));
            } else {
                $_room_price = (int)$r_data['hotel_room_basic_price'];
            }

            if(isset($r_data['hotel_room_goods']) && count($r_data['hotel_room_goods']) > 0) {
                foreach($r_data['hotel_room_goods'] as $idx=>$aGoods) {
                    $_goods_price += ((int)$aGoods['basic_price'] * (int)$aGoods['quantity']);
                }
            }

            $_total_price = $r_data['hotel_room_all_price'] ? (int)$r_data['hotel_room_all_price'] : (int)$r_data['hotel_room_price'];
            if(($_room_price + $_goods_price) != $_total_price) {
                $result['error'] = true;
                $result['err_msg'] = "결제 금액이 맞지 않습니다.";
                echo json_encode($result); exit;
            }

            // 예약 생성
            if($cmod != 'dialog' && $cmod != 'skin' && $cmod != 'LC' && $cmod != 'TS') {
                $r_data['hotel_room_all_price'] = $_total_price;
                $apiResult = getReserveCartOnda($r_data);
                if(!$apiResult['result']) {
                    $result['error'] = true;
                    $result['err_msg'] = "예약 생성 실패!";
                    echo json_encode($result); exit;
                }
                $reservation_id = $apiResult['reservation_id'];

                // 예약 확정
                $_data['end_point'] = "bookings/".$reservation_id."/confirm";

                $postParam = array();
                $postParam['reservation_id'] = $reservation_id;
                $postParam['customer'] = array();
                $postParam['customer']['last_name'] = trim($r_data['ulast_name']);
                $postParam['customer']['first_name'] = trim($r_data['ufirst_name']);
                $postParam['customer']['mobile'] = getStrToPhoneFormat(trim($r_data['uphone']));
                $postParam['customer']['email'] = trim($r_data['uemail']);
                $postParam['payment_method'] = $r_data['upaymethod'] ? $r_data['upaymethod'] : "wired";
                $postParam['description'] = trim($r_data['udescription']);

                $_data['method'] = "put";
                $_data['postParam']['_json'] = json_encode($postParam, JSON_UNESCAPED_UNICODE);
                $apiResult = $chatbot->getReserveAPI($_data);
                if($apiResult['result']) {
                    $bot_msg = "예약이 완료되었습니다.<br>";
                    $bot_msg .="예약번호: ".$apiResult['reservation']['id']."<br>";
                    $bot_msg .=$apiResult['reservation']['customer_email']."로 예약정보가 발송되었습니다.";
                } else {
                    $bot_msg = "죄송합니다. 예약 신청이 되지 못했습니다.<br>다시 예약 부탁드립니다.";
                }
            } else {
                $bot_msg = "예약이 완료되었습니다.";
            }

            $TMPL['response'] = "<span>".nl2br($bot_msg)."</span>";

            $skinFile = $hform.'_'.$action.'_result';
            $skin = new skin($skinFile);
            $content = $skin->make('lib');
            $result['msg'] = $content;
            $result['log'] = true;
            $result['finish'] = true;
        break;

        case('confirm') :
            // 입력정보로 예약 post
            if(!trim($r_data['uname']) || !trim($r_data['uphone'])) {
                $result['error'] = true;
                $result['err_msg'] = "예약자 정보가 부족합니다.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['date']) || !trim($r_data['time'])) {
                $result['error'] = true;
                $result['err_msg'] = "예약일정 정보가 부족합니다.";
                echo json_encode($result); exit;
            }

            $_data['postParam']['bot_id'] = $botid;

            if($r_data['reserve_idx']) {
                $_data['postParam']['mode'] = 'get_reserve_modify';
                $_data['postParam']['reserve_idx'] = $r_data['reserve_idx'];
            } else {
                $_data['postParam']['mode'] = 'get_reserve_submit';
            }

            if($cmod != 'dialog' && $cmod != 'skin' && $cmod != 'LC' && $cmod != 'TS') {
                // 학원일 경우 내부 예약 테이블에 직접 입력
                if($category == "academy" || $category == "normal") {
                    $d_reserve = str_replace("-", "", $r_data['date']).str_replace(":", "", $r_data['time']);
                    $d_regis = $date['totime'];

                    if($r_data['reserve_idx']) {
                        getDbUpdate('rb_chatbot_reserve', "d_reserve='".$d_reserve."'", 'uid='.$r_data['reserve_idx']);
                    } else {
                        $QKEY = "vendor, bot, roomToken, category, name, phone, d_reserve, status, d_regis";
                        $QVAL = "'$vendor', '$bot', '$roomToken', '$category','".$r_data['uname']."', '".$r_data['uphone']."', '$d_reserve', 'ready', '$d_regis'";
                        getDbInsert('rb_chatbot_reserve', $QKEY, $QVAL);
                    }

                } else {

                    $_data['postParam']['name'] = $r_data['uname'];
                    $_data['postParam']['phone'] = $r_data['uphone'];
                    $_data['postParam']['branch_idx'] = $r_data['branch_idx'];
                    $_data['postParam']['branch_name'] = $r_data['branch_name'];
                    $_data['postParam']['department_idx'] = $r_data['department_idx'];
                    $_data['postParam']['department_name'] = $r_data['department_name'];
                    $_data['postParam']['doctor_idx'] = $r_data['doctor_idx'];
                    $_data['postParam']['doctor_name'] = $r_data['doctor_name'];
                    $_data['postParam']['date'] = $r_data['date'];
                    $_data['postParam']['time'] = $r_data['time'];

                    $apiResult = $chatbot->getReserveAPI($_data);

                    if($apiResult['result'] == 0 || $apiResult['result'] == 101) {
                        $result['error'] = true;
                        $result['err_msg'] = "죄송합니다.<br>예약이 이뤄지지 않았습니다.";
                        if($apiResult['message']) $result['err_msg'] .=$apiResult['message'];
                        echo json_encode($result); exit;
                    }
                }
            }

            //----------------------------
            $req_type = $r_data['reserve_idx'] ? "변경" : "신청";
            $bot_msg = "감사합니다. 예약이 정상적으로 ".$req_type."되었습니다.";
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
            $bot_msg = "예약 ".$req_type."이 취소되었습니다.";
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
        if($category == "hotel") {
            $aRStatus = array('cancel'=>'예약취소', 'confirm'=>'예약확정', 'pending'=>'예약대기', 'pending_cancel'=>'예약대기 취소', );
            $aRPayMethod = array('card'=>'신용카드', 'wired'=>'무통장입금');
            $aRPayStatus = array('pending'=>'결제대기', 'full_paid'=>'결제완료');

            if(!trim($r_data['reserve_idx'])) {
                $result['error'] = true;
                $result['err_msg'] = "예약번호를 입력해주세요.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['uname'])) {
                $result['error'] = true;
                $result['err_msg'] = "예약자명을 입력해주세요.";
                echo json_encode($result); exit;
            }

            // 업소정보 조회
            $_data['end_point'] = 'location';
            $_data['getParam'] = array();
            $_data['getParam']['locale'] = 'ko-KR';
            $apiResult = $chatbot->getReserveAPI($_data);
            $TMPL['cs_phone'] = $apiResult['phone'];

            // 예약 검색, 출력
            $_data['end_point'] = 'bookings/chatbot';;
            $_data['getParam'] = array();
            $_data['getParam']['reservation_id'] = $r_data['reserve_idx'];
            $_data['getParam']['customer_name'] = $r_data['uname'];
            $_data['getParam']['locale'] = 'ko-KR';
            $apiResult = $chatbot->getReserveAPI($_data);
            if(count($apiResult) == 0 || (isset($apiResult['result']) && $apiResult['result'] == 0)) {
                $bot_msg = "접수된 예약 정보가 없습니다.";
                $TMPL['response'] = "<span>".nl2br($bot_msg)."</span>";
                $skin = new skin('chat/bot_msg');
                $content = $skin->make();
                $result['msg'] = $content;
                $result['finish'] = true;
            } else {
                $_r_data = array();
                //$_r_data['customer_name'] = $apiResult['customer_name'];
                $_r_data['customer_email'] = $apiResult['customer_email'];
                $_r_data['reservation_status'] = $aRStatus[$apiResult['reservation_status']];
                $_r_data['created_at'] = date("Y-m-d H:i:s", strtotime($apiResult['created_at']));
                $_r_data['checkin'] = $apiResult['checkin'];
                $_r_data['checkout'] = $apiResult['checkout'];

                $reservation_items = $apiResult['reservation_items'][0];
                $_r_data['room_name'] = $reservation_items['roomtype_name'];
                $_r_data['room_price'] = "성인 ".$reservation_items['adults'];
                if($reservation_items['childrens'] > 0) {
                    $_r_data['room_price'] .=", 아동 ".$reservation_items['childrens'];
                }
                $_r_data['room_price'] .=" (&#8361; ".number_format($reservation_items['total_amount']).")";

                $_r_data['goods_price'] = "";
                if(isset($reservation_items['reservation_item_products']) && $reservation_items['reservation_item_products'] > 0) {
                    foreach($reservation_items['reservation_item_products'] as $aGoods) {
                        $_r_data['goods_price'] .=$aGoods['product']['name']." ".$aGoods['quantity']." (&#8361; ".number_format($aGoods['total_amount']).")<br>";
                    }
                    $_r_data['goods_price'] = rtrim($_r_data['goods_price'], "<br>");
                }
                $_r_data['total_price'] = "&#8361; ".number_format($apiResult['total_amount']);

                $_r_data['payment_status'] = $aRPayStatus[$apiResult['payment_status']];
                $_r_data['payment_method'] = $aRPayMethod[$apiResult['payment_method']];
                if($apiResult['payment_method'] == "wired") {
                    $_r_data['payment_method'] .="<br>(".$apiResult['property']['be_bank_name'].", ".$apiResult['property']['be_bank_account'].", 예금주 ".$apiResult['property']['be_depositor'].")";
                }
                $_r_data['description'] = stripslashes($apiResult['description']);

                $TMPL['customer_name'] = $apiResult['customer_name'];
                $TMPL['data_row'] = getReserveInfoHtml($_r_data);
                $skinFile = $hform.'_'.$category.'_search_result';
                $skin = new skin($skinFile);
                $content = $skin->make('lib');
                $result['msg'] = $content;
                $result['log'] = true;
            }

        } else {

            if(!trim($r_data['uname'])) {
                $result['error'] = true;
                $result['err_msg'] = "예약자명을 입력해주세요.";
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

            // 내부 예약
            if($category == "academy" || $category == "normal") {
                $apiResult = [];

                $query = "Select * From rb_chatbot_reserve Where bot=".$pData['bot']." and category='".$category."' and name='".$r_data['uname']."' and phone='".$r_data['uphone']."'";
                $query .="Order by uid desc limit 1";
                $R = db_fetch_assoc(db_query($query, $DB_CONNECT));
                if($R['uid']) {
                    $_date = date("Y-m-d", strtotime($R['d_reserve']));
                    $_time = date("H:i", strtotime($R['d_reserve']));
                    $apiResult['result'] = true;
                    $apiResult['reserve'] = ['reserve_idx'=>$R['uid'], 'name'=>$R['name'], 'phone'=>$R['phone'], 'date'=>$_date, 'time'=>$_time];
                } else {
                    $apiResult['result'] = 0;
                }

            } else {
                // api 조회
                $_data['postParam']['bot_id'] = $botid;
                $_data['postParam']['mode'] = "get_reserve_search";
                $_data['postParam']['name'] = $r_data['uname'];
                $_data['postParam']['phone'] = $r_data['uphone'];

                $apiResult = $chatbot->getReserveAPI($_data);
            }
            if($apiResult['result'] == 0 || $apiResult['reserve'] == "" || count($apiResult['reserve']) == 0) {
                $bot_msg = "접수된 예약 정보가 없습니다.";
                $TMPL['response'] = "<span>".nl2br($bot_msg)."</span>";
                $skin = new skin('chat/bot_msg');
                $content = $skin->make();
                $result['msg'] = $content;
                $result['finish'] = true;
            } else {

                $reserveData = $apiResult['reserve'];
                $reserveData['week'] = $aWeek[date("w", strtotime($reserveData['date']))];
                $reserveData['uname'] = $reserveData['name'];
                $reserveData['uphone'] = $reserveData['phone'];

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
                // 내부 예약
                if($category == "academy" || $category == "normal") {
                    $query = "Delete From rb_chatbot_reserve Where bot=".$pData['bot']." and category='".$category."' and uid='".$r_data['reserve_idx']."'";
                    db_query($query, $DB_CONNECT);
                    $apiResult['result'] = 1;
                } else {
                    $_data['postParam']['bot_id'] = $botid;
                    $_data['postParam']['mode'] = "get_reserve_cancel";
                    $_data['postParam']['name'] = $r_data['uname'];
                    $_data['postParam']['phone'] = $r_data['uphone'];
                    $_data['postParam']['reserve_idx'] = $r_data['reserve_idx'];
                    $apiResult = $chatbot->getReserveAPI($_data);
                }
            } else {
                $apiResult['result'] = 1;
            }

            if($apiResult['result']) {
                $bot_msg = "예약 신청이 취소되었습니다.";
                $TMPL['response'] = "<span>".nl2br($bot_msg)."</span>";
                $skinFile = $hform.'_cancel';
                $skin = new skin($skinFile);
                $content = $skin->make('lib');
                $result['msg'] = $content;
                $result['log'] = true;
            }
        } else {
            $result['error'] = true;
            $result['err_msg'] = "예약취소 정보가 부족합니다.";
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
