<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
require_once $g['path_core'].'function/encryption.php';

$pData = getEscString($_POST['pData']);

if(!trim($pData['bot'])) {
    echo "<script>alert('잘못된 접근입니다'); history.back();</script>"; exit;
}

$aWeek = array("일", "월", "화", "수", "목", "금", "토");
$dToday = date("Y-m-d");
$dNowTime = date("H:i");

function getSido($sido="") {
    $aSido = array();
    $aSido['강원'] = array('강릉시', '고성군', '동해시', '삼척시', '속초시', '양구군', '양양군', '영월군', '원주시', '인제군', '정선군', '철원군', '춘천시', '태백시', '평창군', '홍천군', '화천군', '횡성군');
    $aSido['경기'] = array('가평군', '고양시 덕양구', '고양시 일산동구', '고양시 일산서구', '과천시', '광명시', '광주시', '구리시', '군포시', '김포시', '남양주시', '동두천시', '부천시 소사구', '부천시 오정구', '부천시 원미구', '성남시 분당구', '성남시 수정구', '성남시 중원구', '수원시 권선구', '수원시 영통구', '수원시 장안구', '수원시 팔달구', '시흥시', '안산시 단원구', '안산시 상록구', '안성시', '안양시 동안구', '안양시 만안구', '양주시', '양평군', '여주시', '연천군', '오산시', '용인시 기흥구', '용인시 수지구', '용인시 처인구', '의왕시', '의정부시', '이천시', '파주시', '평택시', '포천시', '하남시', '화성시');
    $aSido['경기도'] = array('화성시');
    $aSido['경남'] = array('거제시', '거창군', '고성군', '김해시', '남해군', '밀양시', '사천시', '산청군', '양산시', '의령군', '진주시', '창녕군', '창원시 마산합포구', '창원시 마산회원구', '창원시 성산구', '창원시 의창구', '창원시 진해구', '통영시', '하동군', '함안군', '함양군', '합천군');
    $aSido['경북'] = array('경산시', '경주시', '고령군', '구미시', '군위군', '김천시', '문경시', '봉화군', '상주시', '성주군', '안동시', '영덕군', '영양군', '영주시', '영천시', '예천군', '울릉군', '울진군', '의성군', '청도군', '청송군', '칠곡군', '포항시 남구', '포항시 북구');
    $aSido['광주'] = array('광산구', '남구', '동구', '북구', '서구');
    $aSido['대구'] = array('남구', '달서구', '달성군', '동구', '북구', '서구', '수성구', '중구');
    $aSido['대전'] = array('대덕구', '동구', '서구', '유성구', '중구');
    $aSido['부산'] = array('강서구', '금정구', '기장군', '남구', '동구', '동래구', '부산진구', '북구', '사상구', '사하구', '서구', '수영구', '연제구', '영도구', '중구', '해운대구');
    $aSido['서울'] = array('강남구', '강동구', '강북구', '강서구', '관악구', '광진구', '구로구', '금천구', '노원구', '도봉구', '동대문구', '동작구', '마포구', '서대문구', '서초구', '성동구', '성북구', '송파구', '양천구', '영등포구', '용산구', '은평구', '종로구', '중구', '중랑구');
    $aSido['세종'] = array();
    $aSido['울산'] = array('남구', '동구', '북구', '울주군', '중구');
    $aSido['인천'] = array('강화군', '계양구', '남구', '남동구', '동구', '부평구', '서구', '연수구', '옹진군', '중구');
    $aSido['전남'] = array('강진군', '고흥군', '곡성군', '광양시', '구례군', '나주시', '담양군', '목포시', '무안군', '보성군', '순천시', '신안군', '여수시', '영광군', '영암군', '완도군', '장성군', '장흥군', '진도군', '함평군', '해남군', '화순군');
    $aSido['전북'] = array('고창군', '군산시', '김제시', '남원시', '무주군', '부안군', '순창군', '완주군', '익산시', '임실군', '장수군', '전주시 덕진구', '전주시 완산구', '정읍시', '진안군');
    $aSido['제주'] = array('서귀포시', '제주시');
    $aSido['충남'] = array('계룡시', '공주시', '금산군', '논산시', '당진시', '보령시', '부여군', '서산시', '서천군', '아산시', '예산군', '천안시 동남구', '천안시 서북구', '청양군', '태안군', '홍성군');
    $aSido['충북'] = array('괴산군', '단양군', '보은군', '영동군', '옥천군', '음성군', '제천시', '증평군', '진천군', '청주시 상당구', '청주시 서원구', '청주시 청원구', '청주시 흥덕구', '충주시');

    if($sido == "") {
        return array_keys($aSido);
    } else {
        return $aSido[$sido];
    }
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
$category = !$category ? "normal" : $category;
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

if($action == "get_sigugun") {
    $sido = $pData['sido'];
    $aSigungu = getSido($sido);
    $htmlSigungu = "<option value=''>시/구/군 선택</option>";
    foreach($aSigungu as $val) {
        $htmlSigungu .= "<option value='".$val."'>".$val."</option>";
    }
    $result['json_data'] = $htmlSigungu;
    $result['func'] = "setSigugun";
}

// 일반 예약 프로세스
if($action == "request" || $action == "join") {
    switch($step) {
        case('start') :
            $aExeRstCont = array("상품안내(무배당 KB계획이다있는연금보험)", "보험가입 안내", "내보험진단", "불편사항", "기타");
            $htmlExeRstCont = "";
            foreach($aExeRstCont as $i=>$val) {
                $htmlExeRstCont .="<li>";
                $htmlExeRstCont .=" <div class='check_wrap'>";
                $htmlExeRstCont .="     <input type='radio' class='ip_r' id='exerstcont_".$i."' name='exeRstCont' value='".$val."' ".($i == 0 ? "checked" : "").">";
                $htmlExeRstCont .="     <label for='exerstcont_".$i."'>".$val."</label>";
                $htmlExeRstCont .=" </div>";
                $htmlExeRstCont .="</li>";
            }
            $TMPL['htmlExeRstCont'] = $htmlExeRstCont;

            $aInterestProd = array("연금보험", "종신보험", "변액보험", "건강보험", "저축보험", "온라인보험", "기타");
            $htmlInterestProd = "";
            foreach($aInterestProd as $i=>$val) {
                $htmlInterestProd .="<li>";
                $htmlInterestProd .=" <div class='check_wrap'>";
                $htmlInterestProd .="     <input type='radio' class='ip_r' id='interestProd_".$i."' name='interestProd' value='".$val."' ".($val == "기타" ? "checked" : "").">";
                $htmlInterestProd .="     <label for='interestProd_".$i."'>".$val."</label>";
                $htmlInterestProd .=" </div>";
                $htmlInterestProd .="</li>";
            }
            $TMPL['htmlInterestProd'] = $htmlInterestProd;

            $aInterestTopic = array("사망보장", "노후보장", "질병보장", "자산성장", "사업성장", "상속", "기타");
            $htmlInterestTopic = "";
            foreach($aInterestTopic as $i=>$val) {
                $htmlInterestTopic .="<li>";
                $htmlInterestTopic .=" <div class='check_wrap'>";
                $htmlInterestTopic .="     <input type='radio' class='ip_r' id='interestTopic_".$i."' name='interestTopic' value='".$val."'>";
                $htmlInterestTopic .="     <label for='interestTopic_".$i."'>".$val."</label>";
                $htmlInterestTopic .=" </div>";
                $htmlInterestTopic .="</li>";
            }
            $TMPL['htmlInterestTopic'] = $htmlInterestTopic;

            $htmlSido = "";
            $aSido = getSido();
            foreach($aSido as $val) {
                $htmlSido .= "<option value='".$val."'>".$val."</option>";
            }
            $TMPL['htmlSido'] = $htmlSido;

            $htmlCounselTime1 = $htmlCounselTime2 = "";
            for($i=9; $i<=17; $i++) {
                $htmlCounselTime1 .="<option value='".$i."'>".$i."시</option>";
            }
            for($i=10; $i<=18; $i++) {
                $htmlCounselTime2 .="<option value='".$i."'>".$i."시</option>";
            }
            $TMPL['htmlCounselTime1'] = $htmlCounselTime1;
            $TMPL['htmlCounselTime2'] = $htmlCounselTime2;

            $TMPL['id_agree'] = "agree_".rand(1000,9999);

            $skinFile = $hform.'_'.$action.'_start';
            $skin = new skin($skinFile);
            $result['msg'] = $skin->make('lib');
            $result['log'] = true;
        break;

        case('confirm') :
            if($action == "join") {
                $r_data['exeRstCont'] = "보험가입 안내";
                $r_data['interestProd'] = "연금보험";
                $r_data['interestTopic'] = "노후보장";
                $r_data['sido'] = "서울";
                $r_data['sigugun'] = "강남구";
                $r_data['exeRstCase'] = "call";
                $r_data['counselTime1'] = "10";
                $r_data['counselTime2'] = "10";
                $r_data['contentText'] = "보험신청 내용";
            }

            // 입력정보로 예약 post
            if(!trim($r_data['uname'])) {
                $result['error'] = true;
                $result['err_msg'] = "이름을 입력해주세요.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['umobile'])) {
                $result['error'] = true;
                $result['err_msg'] = "휴대폰번호를 입력해주세요.";
                echo json_encode($result); exit;
            }
            if(!getCheckValidFormat('mobile', $r_data['umobile'])) {
                $result['error'] = true;
                $result['err_msg'] = "휴대폰번호가 정확하지 않습니다.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['uemail'])) {
                $result['error'] = true;
                $result['err_msg'] = "이메일 주소를 입력해주세요.";
                echo json_encode($result); exit;
            }
            if(!getCheckValidFormat('email', $r_data['uemail'])) {
                $result['error'] = true;
                $result['err_msg'] = "이메일 주소가 정확하지 않습니다.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['ujumin1'])) {
                $result['error'] = true;
                $result['err_msg'] = "생년월일 앞자리를 입력해주세요.";
                echo json_encode($result); exit;
            }
            if(!preg_match('/^[0-9]{6}$/', $r_data['ujumin1'])) {
                $result['error'] = true;
                $result['err_msg'] = "생년월일 입력이 잘못되었습니다.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['ujumin2'])) {
                $result['error'] = true;
                $result['err_msg'] = "생년월일 뒷자리를 입력해주세요.";
                echo json_encode($result); exit;
            }
            if(!preg_match('/^[0-9]{1}$/', $r_data['ujumin2'])) {
                $result['error'] = true;
                $result['err_msg'] = "생년월일 입력이 잘못되었습니다.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['exeRstCont'])) {
                $result['error'] = true;
                $result['err_msg'] = "상담내용을 체크해주세요.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['interestProd'])) {
                $result['error'] = true;
                $result['err_msg'] = "관심상품을 체크해주세요.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['interestTopic'])) {
                $result['error'] = true;
                $result['err_msg'] = "관심주제를 체크해주세요.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['sido']) || !trim($r_data['sigugun'])) {
                $result['error'] = true;
                $result['err_msg'] = "지역을 선택해주세요.";
                echo json_encode($result); exit;
            }
            if(!trim($r_data['exeRstCase'])) {
                $result['error'] = true;
                $result['err_msg'] = "상담방법을 체크해주세요.";
                echo json_encode($result); exit;
            }
            if($r_data['exeRstCase'] == "call") {
                if(!trim($r_data['counselTime1']) || !trim($r_data['counselTime2'])) {
                    $result['error'] = true;
                    $result['err_msg'] = "상담시간을 선택해주세요.";
                    echo json_encode($result); exit;
                }
            }
            if(!trim($r_data['contentText'])) {
                $result['error'] = true;
                $result['err_msg'] = "내용을 입력해주세요.";
                echo json_encode($result); exit;
            }
            if(!isset($r_data['agree']) || $r_data['agree'] != "true") {
                $result['error'] = true;
                $result['err_msg'] = "개인정보 수집 동의를 체크해주세요.";
                echo json_encode($result); exit;
            }

            $_data['postParam']['bot_id'] = $botid;

            if($cmod != 'dialog' && $cmod != 'skin' && $cmod != 'LC' && $cmod != 'TS') {
                $d_regis = $date['totime'];

                $contentText = $r_data['contentText'];
                unset($r_data['contentText']);

                $r_data['action'] = $action == "request" ? "상담신청" : "가입신청";
                $addval = json_encode($r_data, JSON_UNESCAPED_UNICODE);

                $QKEY = "vendor, bot, roomToken, category, name, phone, d_reserve, content, addval, status, d_regis";
                $QVAL = "'$vendor', '$bot', '$roomToken', '$category','".$r_data['uname']."', '".$r_data['umobile']."', '$d_reserve', '".$contentText."', '".$addval."', 'ready', '$d_regis'";
                getDbInsert('rb_chatbot_reserve', $QKEY, $QVAL);
            }

            //----------------------------
            $bot_msg = $action == "request" ? "감사합니다. 등록되었습니다." : "감사합니다. 가입신청이 완료되었습니다.\n보험료 결제 전 연락드리겠습니다.";
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
            $bot_msg = "취소되었습니다.";
            $TMPL['response'] = "<span>".nl2br($bot_msg)."</span>";

            $skinFile = $hform.'_cancel';
            $skin = new skin($skinFile);
            $content = $skin->make('lib');
            $result['msg'] = $content;
            $result['log'] = true;
        break;
    }
}

if($action == "search") {
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

    // api 조회
    $_data['postParam']['bot_id'] = $botid;
    $_data['postParam']['mode'] = "get_reserve_search";
    $_data['postParam']['name'] = $r_data['uname'];
    $_data['postParam']['phone'] = $r_data['uphone'];

    $apiResult = $chatbot->getReserveAPI($_data);
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
