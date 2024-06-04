<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
include_once $g['dir_module'].'var/define.path.php'; // class, 모듈, 레이아웃 패스 세팅
require_once 'statistics/controller/StatisticsController.php';
$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$linkType = trim($_POST['linkType']);
$vendor = trim($_POST['vendor']);
$botuid = trim($_POST['botuid']);
$mod = trim($_POST['mod']);
$page = trim($_POST['page']);
$d_start = trim($_POST['d_start']);
$d_end = trim($_POST['d_end']);
$keyword = trim($_POST['keyword']);
$hTokens = trim($_POST['hTokens']);

// 시작일, 종료일 '-' 제거
$_period_counter = $_period_chatLog = "";
if($d_start && $d_end) {
    $d_start = str_replace('-','', $d_start);
    $d_end = str_replace('-','', $d_end);

    $d_start_prev = date("Ymd", strtotime("-1 month", strtotime($d_start)));
    $d_end_prev = date("Ymd", strtotime("-1 month", strtotime($d_end)));

    $_period_counter = " and (d_regis between '".$d_start."' and '".$d_end."') ";
    $_period_chatLog = " and (left(d_regis,8) between '".$d_start."' and '".$d_end."') ";
    $_period_unknown = " and (date between '".$d_start."' and '".$d_end."') ";

    $_period_counter_prev = " and (d_regis between '".$d_start_prev."' and '".$d_end_prev."') ";
    $_period_chatLog_prev = " and (left(d_regis,8) between '".$d_start_prev."' and '".$d_end_prev."') ";
    $_period_unknown_prev = " and (date between '".$d_start_prev."' and '".$d_end_prev."') ";
}

$durationQuery = "
    WITH RECURSIVE date_range AS ( 
        SELECT STR_TO_DATE('".$d_start."', '%Y%m%d') AS ymd
        UNION ALL 	
        SELECT DATE_ADD(nymd.ymd, INTERVAL 1 DAY) AS ymd FROM date_range AS nymd
        WHERE nymd.ymd < STR_TO_DATE('".$d_end."', '%Y%m%d')
    )";

// 20240429 spikecow
$statisticsController = new StatisticsController();

function getCountIntent() {
    global $table, $m, $DB_CONNECT, $vendor, $botuid, $_period_chatLog, $_period_chatLog_prev;

    $query ="Select count(*) as nCnt From ".$table[$m."intent"]." Where bot=".$botuid." and hidden=0";
    $aResult = db_fetch_assoc(db_query($query,$DB_CONNECT));

    $nVariance = 0;
    if($GLOBALS['d_start'] && $GLOBALS['d_end']) {
        $query ="Select A.prevCnt, B.nowCnt From ( ";
        $query .="  Select count(*) as prevCnt From ".$table[$m."intent"]." Where bot=".$botuid." and hidden=0 ".$_period_chatLog_prev." ";
        $query .=") as A, ( ";
        $query .="  Select count(*) as nowCnt From ".$table[$m."intent"]." Where bot=".$botuid." and hidden=0 ".$_period_chatLog." ";
        $query .=") as B";
        $aResultCnt = db_fetch_assoc(db_query($query,$DB_CONNECT));
        $nVariance = getVarianceRate($aResultCnt['prevCnt'], $aResultCnt['nowCnt']);
    }
    return ['cnt'=>$aResult['nCnt'], 'variance'=>$nVariance];
}
function getCountEntity() {
    global $table, $m, $DB_CONNECT, $vendor, $botuid, $_period_chatLog, $_period_chatLog_prev;

    $query ="Select count(*) as nCnt From ".$table[$m."entity"]." Where bot=".$botuid." and hidden=0";
    $aResult = db_fetch_assoc(db_query($query,$DB_CONNECT));

    $nVariance = 0;
    if($GLOBALS['d_start'] && $GLOBALS['d_end']) {
        $query ="Select A.prevCnt, B.nowCnt From ( ";
        $query .="  Select count(*) as prevCnt From ".$table[$m."entity"]." Where bot=".$botuid." and hidden=0 ".$_period_chatLog_prev." ";
        $query .=") as A, ( ";
        $query .="  Select count(*) as nowCnt From ".$table[$m."entity"]." Where bot=".$botuid." and hidden=0 ".$_period_chatLog." ";
        $query .=") as B";
        $aResultCnt = db_fetch_assoc(db_query($query,$DB_CONNECT));
        $nVariance = getVarianceRate($aResultCnt['prevCnt'], $aResultCnt['nowCnt']);
    }
    return ['cnt'=>$aResult['nCnt'], 'variance'=>$nVariance];
}
function getCountNode() {
    global $table, $m, $DB_CONNECT, $vendor, $botuid, $_period_chatLog, $_period_chatLog_prev;

    $query = "Select count(*) as nCnt From ".$table[$m."dialogNode"]." Where bot=".$botuid." and hidden=0 and dialog = (";
    $query .="  Select uid From ".$table[$m."dialog"]." Where type='D' and active=1 and bot='".$botuid."'";
    $query .=")";
    $aResult = db_fetch_assoc(db_query($query,$DB_CONNECT));

    $nVariance = 0;
    if($GLOBALS['d_start'] && $GLOBALS['d_end']) {
        $query ="Select A.prevCnt, B.nowCnt From ( ";
        $query .="  Select count(*) as prevCnt From ".$table[$m."dialogNode"]." Where bot=".$botuid." and hidden=0 ".$_period_chatLog_prev." and dialog = (";
        $query .="      Select uid From ".$table[$m."dialog"]." Where type='D' and active=1 and bot='".$botuid."' ";
        $query .="  ) ";
        $query .=") as A, ( ";
        $query .="  Select count(*) as nowCnt From ".$table[$m."dialogNode"]." Where bot=".$botuid." and hidden=0 ".$_period_chatLog." and dialog = (";
        $query .="      Select uid From ".$table[$m."dialog"]." Where type='D' and active=1 and bot='".$botuid."' ";
        $query .="  ) ";
        $query .=") as B";
        $aResultCnt = db_fetch_assoc(db_query($query,$DB_CONNECT));
        $nVariance = getVarianceRate($aResultCnt['prevCnt'], $aResultCnt['nowCnt']);
    }
    return ['cnt'=>$aResult['nCnt'], 'variance'=>$nVariance];
}
function getCountAccess() {
    global $table, $m, $DB_CONNECT, $vendor, $botuid, $_period_counter, $_period_counter_prev;

    //$query = "select sum(page) as nCnt from ".$table[$m.'counter']." where vendor='".$vendor."' and botuid='".$botuid."' and type=2 ".$_period_counter;
    $query = "select sum(page) as nCnt from ".$table[$m.'counter']." where vendor='".$vendor."' and botuid='".$botuid."' ".$_period_counter;
    $aResult = db_fetch_assoc(db_query($query,$DB_CONNECT));

    $nVariance = 0;
    if($GLOBALS['d_start'] && $GLOBALS['d_end']) {
        $query ="Select A.prevCnt, B.nowCnt From ( ";
        $query .="  Select sum(page) as prevCnt From ".$table[$m."counter"]." Where vendor='".$vendor."' and botuid=".$botuid." and type=2 ".$_period_counter_prev." ";
        $query .=") as A, ( ";
        $query .="  Select sum(page) as nowCnt From ".$table[$m."counter"]." Where vendor='".$vendor."' and botuid=".$botuid." and type=2 ".$_period_counter." ";
        $query .=") as B";
        $aResultCnt = db_fetch_assoc(db_query($query,$DB_CONNECT));
        $nVariance = getVarianceRate($aResultCnt['prevCnt'], $aResultCnt['nowCnt']);
    }
    return ['cnt'=>$aResult['nCnt'], 'variance'=>$nVariance];
}
function getCountSession() {
    global $table, $m, $DB_CONNECT, $vendor, $botuid, $_period_chatLog, $_period_chatLog_prev;

    $query = "select count(*) as nCnt From (";
    $query .="  select min(uid) from ".$table[$m.'chatLog']." where vendor='".$vendor."' and bot='".$botuid."' and roomToken <> ''".$_period_chatLog." group by roomToken ";
    $query .=") A ";
    $aResult = db_fetch_assoc(db_query($query,$DB_CONNECT));

    $nVariance = $prevCnt = 0;
    if($GLOBALS['d_start'] && $GLOBALS['d_end']) {
        $query ="Select A.prevCnt, B.nowCnt From ( ";
        $query .="  Select count(*) as prevCnt From ( ";
        $query .="      select min(uid) from ".$table[$m.'chatLog']." where vendor='".$vendor."' and bot='".$botuid."' and roomToken <> ''".$_period_chatLog_prev." group by roomToken ";
        $query .="  ) as A ";
        $query .=") as A, ( ";
        $query .="  Select count(*) as nowCnt From ( ";
        $query .="      select min(uid) from ".$table[$m.'chatLog']." where vendor='".$vendor."' and bot='".$botuid."' and roomToken <> ''".$_period_chatLog." group by roomToken ";
        $query .="  ) as A ";
        $query .=") as B";
        $aResultCnt = db_fetch_assoc(db_query($query,$DB_CONNECT));
        $nVariance = getVarianceRate($aResultCnt['prevCnt'], $aResultCnt['nowCnt']);
        $prevCnt = $aResultCnt['prevCnt'];
    }
    return ['cnt'=>$aResult['nCnt'], 'prevCnt'=>$prevCnt, 'variance'=>$nVariance];
}
function getCountConversation() {
    global $table, $m, $DB_CONNECT, $vendor, $botuid, $_period_chatLog, $_period_chatLog_prev;

    $query = "select count(*) as nCnt From ".$table[$m.'chatLog']." Where vendor='".$vendor."' and bot='".$botuid."' and roomToken <> '' ".$_period_chatLog;
    $aResult = db_fetch_assoc(db_query($query,$DB_CONNECT));

    $nVariance = $prevCnt = 0;
    if($GLOBALS['d_start'] && $GLOBALS['d_end']) {
        $query ="Select A.prevCnt, B.nowCnt From ( ";
        $query .="  Select count(*) as prevCnt From ".$table[$m."chatLog"]." Where vendor='".$vendor."' and bot=".$botuid." and roomToken <> '' ".$_period_chatLog_prev." ";
        $query .=") as A, ( ";
        $query .="  Select count(*) as nowCnt From ".$table[$m."chatLog"]." Where vendor='".$vendor."' and bot=".$botuid." and roomToken <> '' ".$_period_chatLog." ";
        $query .=") as B";
        $aResultCnt = db_fetch_assoc(db_query($query,$DB_CONNECT));
        $nVariance = getVarianceRate($aResultCnt['prevCnt'], $aResultCnt['nowCnt']);
        $prevCnt = $aResultCnt['prevCnt'];
    }
    return ['cnt'=>$aResult['nCnt'], 'prevCnt'=>$prevCnt, 'variance'=>$nVariance];
}
function getCountUnknown() {
    global $table, $m, $DB_CONNECT, $vendor, $botuid, $_period_chatLog;

    $_wh_chat = "vendor='".$vendor."' and bot='".$botuid."' and roomToken <> '' and is_unknown = 1 ".$_period_chatLog;
    $query = "select count(*) as nCnt From ".$table[$m.'chatLog']." Where ".$_wh_chat;
    $aResult = db_fetch_assoc(db_query($query,$DB_CONNECT));
    return $aResult['nCnt'];
}

function getVarianceRate($prev, $now) {
    $growthRate = $prev == 0 && $now == 0 ? 0 : ($prev == 0 ? 100 : ($now - $prev) / $prev * 100);
    return number_format($growthRate, 1);
}
function getVarianceInfo($variance) {
    $_class = $variance > 0 ? "green" : "red";
    $_txt = $variance > 0 ? "Higher" : "Less";
    return "<span class='".$_class."'>".$variance."%</span> ".$_txt." than last month";
}

if($linkType == 'all_data' || $linkType == 'user' || $linkType == 'conversation' || $linkType == 'wordgroup') {
    if($linkType == 'all_data') {
        // 인텐트,엔터티,대화상자 수
        $aCountIntent = getCountIntent();
        $aCountEntity = getCountEntity();
        $aCountNode = getCountNode();

        $result['totalIntent'] = number_format($aCountIntent['cnt']);
        $result['totalEntity'] = number_format($aCountEntity['cnt']);
        $result['totalNode'] = number_format($aCountNode['cnt']);

        $result['totalIntentPer'] = getVarianceInfo($aCountIntent['variance']);
        $result['totalEntityPer'] = getVarianceInfo($aCountEntity['variance']);
        $result['totalNodePer'] = getVarianceInfo($aCountNode['variance']);

        $statisticsResponse = $statisticsController->getUserStatisticsListByDuration(StatisticsRequestModel::of($vendor, $botuid, $d_start, $d_end));

        $result['userTotalAccessCount'] = $statisticsResponse->userTotalAccessCount;
        $result['userTotalRevisitAccessRate'] = $statisticsResponse->userTotalRevisitAccessRate;
        $result['totalUnAnsweredCount'] = $statisticsResponse->totalUnAnsweredCount;
        $result['totalAnsweredRate'] = $statisticsResponse->totalAnsweredRate;
    }

    // 총 누적 접속 수
    $aCountAccess = getCountAccess();
    $result['totalAccess'] = number_format($aCountAccess['cnt']);
    $result['totalAccessPer'] = getVarianceInfo($aCountAccess['variance']);

    // 총 누적 세션수(룸토큰수)
    $aCountSession = getCountSession();
    $totalUser = $aCountSession['cnt'];
    $totalUserPrev = $aCountSession['prevCnt'];
    $result['totalUser'] = number_format($totalUser);
    $result['totalUserPer'] = getVarianceInfo($aCountSession['variance']);

    // 총 대화 수
    $aCountConversation = getCountConversation();
    $totalChat = $aCountConversation['cnt'];
    $totalChatPrev = $aCountConversation['prevCnt'];
    $result['totalChat'] = number_format($totalChat);
    $result['totalChatPer'] = getVarianceInfo($aCountConversation['variance']);

    $result['perChat'] = $totalChat && $totalUser ? number_format($totalChat/$totalUser) : 0;
    $perChatPrev = $totalChatPrev && $totalUserPrev ? number_format($totalChatPrev/$totalUserPrev) : 0;
    $perChatPer = getVarianceRate($perChatPrev, $result['perChat']);
    $result['perChatPer'] = getVarianceInfo($perChatPer);

    // 버튼 로그
    $query = "select replace(content,'에 대해서 문의드립니다.','') as content, count(*) as cnt From ".$table[$m.'chatLog']." ";
    $query .="Where vendor='".$vendor."' and bot='".$botuid."' ";
    $query .="and printType='B' and (content <> '' AND content <> '에 대해서 문의드립니다.') ";
    $query .=$_period_chatLog;
    $query .="Group BY replace(content,'에 대해서 문의드립니다.','') Order by cnt DESC Limit 20 ";
    $RCD = db_query($query,$DB_CONNECT);
    $btnLogHtml1 = $btnLogHtml2 = "";
    $i=0;
    while($R=db_fetch_array($RCD)){
        $html = "<tr><td class='txt-oflo'>".$R['content']."</td><td class='txt-oflo'>".$R['cnt']."</td></tr>";
        if($i < 10) {
            $btnLogHtml1 .=$html;
        } else {
            $btnLogHtml2 .=$html;
        }
        $i++;
    }
    $result['btnLogHtml1'] = $btnLogHtml1;
    $result['btnLogHtml2'] = $btnLogHtml2;

    if($linkType == 'all_data') {
        // 대화상자, 많이 한 질문, 많이 사용한 단어
        $_data = array();
        $_data['vendor'] = $vendor;
        $_data['bot'] = $botuid;
        $_data['recnum'] =10;
        $_data['d_start'] = $d_start;
        $_data['d_end'] = $d_end;
        $_data['mod'] = 'node';
        $statisData = $chatbot->getFavorateQuestionData($_data);
        $result['nodeHtml'] = $statisData[1];
        $result['nodeBtn'] = $statisData[2];

        $_data['mod'] = 'question';
        $statisData = $chatbot->getFavorateQuestionData($_data);
        $result['questionHtml'] = $statisData[1];
        $result['questionBtn'] = $statisData[2];

        $_data['mod'] = 'word';
        $statisData = $chatbot->getFavorateQuestionData($_data);
        $result['wordHtml'] = $statisData[1];
        $result['wordBtn'] = $statisData[2];
    }

    if($linkType == 'wordgroup') {
        $result['aWordJson'] = array();
        $result['wordHtml'] = $result['wordBtn'] = '';

        $_data = array();
        $_data['vendor'] = $vendor;
        $_data['bot'] = $botuid;
        $_data['d_start'] = $d_start;
        $_data['d_end'] = $d_end;

        $aLogWord = $chatbot->getChatLogWord($_data);
        if(count($aLogWord) > 0) {
            $nMax = max(array_values($aLogWord));
            $nMin = min(array_values($aLogWord));
            $nStandard = (array_sum(array_values($aLogWord)) / count($aLogWord));

            foreach($aLogWord as $key=>$val) {
                $weight = ($nMax - $nMin) == 0 ? 0 : (($val - $nMin) / ($nMax - $nMin));
                $result['aWordJson'][] = array("text"=>$key, "weight"=>$weight, "link"=>"#".$key);
            }

            $keyword = $result['aWordJson'][0]['text'];
            $_data['recnum'] =10;
            $_data['keyword'] = $keyword;
            $_data['mod'] = 'wordgroup';

            $statisData = $chatbot->getWordGroupData($_data);
            $result['wordHtml'] = $statisData[1];
            $result['wordBtn'] = $statisData[2];
        }
    }

    if($linkType == 'all_data' || $linkType == 'user') {
        // 접속 그래프 쿼리 세팅
        $_wh = "vendor='".$vendor."' and botuid='".$botuid."'".$_period_counter;
        $_wh_chat = "vendor='".$vendor."' and bot='".$botuid."'".$_period_chatLog;
        //$query = "select d_regis, sum(page) as page from ".$table[$m.'counter']." where ".$_wh." group by d_regis order by d_regis";
        //$query ="Select mode, d_regis, page From (";

        $query = $durationQuery." select 'all' as mode, duration.ymd as d_regis, COALESCE(rcca.page, 0) AS page from date_range AS duration left join (";
        $query .="  Select d_regis, sum(page) as page from ".$table[$m.'counter']." Where ".$_wh." group BY d_regis ";
        $query .=") as rcca on rcca.d_regis = duration.ymd";

        $query .="  union all ";

        $query .="select 'f' as mode, duration.ymd as d_regis, COALESCE(rccf.page, 0) AS page from date_range AS duration left join (";
        $query .="  Select d_regis, page from ".$table[$m.'counter']." Where ".$_wh." and type=1 group BY d_regis ";
        $query .=") as rccf on rccf.d_regis = duration.ymd";

        $query .="  union all ";

        $query .="select 's' as mode, duration.ymd as d_regis, COALESCE(rccs.page, 0) AS page from date_range AS duration left join (";
        $query .="  Select d_regis, page from ".$table[$m.'counter']." Where ".$_wh." and type=2 group BY d_regis ";
        $query .=") as rccs on rccs.d_regis = duration.ymd";

        $query .="  union all ";

        $query .="select 't' as mode, duration.ymd as d_regis, COALESCE(rcct.page, 0) AS page from date_range AS duration left join (";
        $query .="  Select left(d_regis,8) as d_regis, count(*) as page from ".$table[$m.'chatLog']." Where ".$_wh_chat." and roomToken <> '' group BY left(d_regis,8) ";
        $query .=") as rcct on rcct.d_regis = duration.ymd";

        //$query .=") as A Order by d_regis ASC, mode ASC";
        $RCD = db_query($query,$DB_CONNECT);
        $aAccess = array();
        while($R=db_fetch_array($RCD)){
            $date = substr($R['d_regis'],0,4)."/".substr($R['d_regis'],5,2)."/".substr($R['d_regis'],8,2);
            $aAccess[$date][$R['mode']] = $R['page'];
        }
        $page_date = $page_all = $page_f = $page_s = '';
        foreach($aAccess as $date=>$data) {
            $page_date .="'".$date."',";
            $page_all .="'".$data['all']."',";
            $page_f .="'".($data['f'] ? $data['f'] : 0)."',";
            $page_s .="'".($data['s'] ? $data['s'] : 0)."',";
        }

        $page_date = rtrim($page_date,',');
        $page_all = rtrim($page_all,',');
        $page_f = rtrim($page_f,',');
        $page_s = rtrim($page_s,',');

        $TMPL['page_date'] = $page_date;
        $TMPL['page_all'] = $page_all;
        $TMPL['page_f'] = $page_f;
        $TMPL['page_s'] = $page_s;

        // 총 유입수 차트
        $total_chart=new skin('vendor/total_chart');
        $result['total_chart']=$total_chart->make();
        $result['tmpl'] = $TMPL;

        if($linkType == 'user') {
            // 리스트 테이블
            $totalList = '';
            foreach($aAccess as $date=>$data) {
                $allUser = number_format($data['all']);
                $newUser = $data['f'] ? number_format($data['f']) : 0;
                $newUser .=' ('.($newUser ? number_format(($newUser/$allUser*100),1) : '0.0').'%)';
                $reUser = $data['s'] ? $data['s'] : 0;
                $reUser .=' ('.($reUser ? number_format(($reUser/$allUser*100),1) : '0.0').'%)';
                $talk = $data['t'] ? number_format($data['t']) : 0;

                $totalList .='<tr>';
                $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$date.'</td>';
                $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$allUser.'</td>';
                $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$reUser.'</td>';
                $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$newUser.'</td>';
                $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$talk.'</td>';
                $totalList .='</tr>';
            }
            $result['totalList']=$totalList;
        }
    }

    if($linkType == 'all_data' || $linkType == 'conversation') {
        // 대화 현황 그래프
        $_wh = "vendor='".$vendor."' and bot='".$botuid."' ";
        $_wh_chatlog = $_wh.($d_start && $d_end ? "and (left(d_regis,8) between '".$d_start."' and '".$d_end."') " : "");

        //$query = "Select chType, d_regis, nCnt From (";
        $query = $durationQuery." select 'session' as chType, duration.ymd as d_regis, COALESCE(rccs.nCnt, 0) AS nCnt from date_range AS duration left join (";
        $query .="  Select d_regis, count(roomToken) as nCnt From (";
        $query .="      Select left(d_regis,8) as d_regis, roomToken From ".$table[$m.'chatLog']." Where ".$_wh_chatlog." and roomToken <> '' Group by left(d_regis,8), roomToken ";
        $query .="  ) as A Group by d_regis";
        $query .=") as rccs on rccs.d_regis = duration.ymd";

        $query .="  union all ";

        $query .= " select 'chat' as chType, duration.ymd as d_regis, COALESCE(rccc.nCnt, 0) AS nCnt from date_range AS duration left join (";
        $query .="  Select left(d_regis,8) as d_regis, count(*) as nCnt From ".$table[$m.'chatLog']." ";
        $query .="  Where ".$_wh_chatlog." and roomToken <> '' Group by left(d_regis,8) ";
        $query .=") as rccc on rccc.d_regis = duration.ymd";

        $query .="  union all ";

        $query .= " select 'unknown' as chType, duration.ymd as d_regis, COALESCE(rccu.nCnt, 0) AS nCnt from date_range AS duration left join (";
        $query .="  Select left(d_regis,8) as d_regis, count(*) as nCnt From ".$table[$m.'chatLog']." ";
        $query .="  Where ".$_wh_chatlog." and roomToken <> '' and is_unknown = 1 Group by left(d_regis,8) ";
        $query .=") as rccu on rccu.d_regis = duration.ymd";
        //$query .=") A Order by d_regis ASC, chType ASC ";

        $RCD = db_query($query,$DB_CONNECT);

        $aConv = array();
        while($R=db_fetch_array($RCD)){
            $date = substr($R['d_regis'],0,4)."/".substr($R['d_regis'],5,2)."/".substr($R['d_regis'],8,2);
            $aConv[$date][$R['chType']] = $R['nCnt'];
        }
        $page_date = $page_data = $fall_data = '';
        foreach($aConv as $date=>$data) {
            $page_date .="'".$date."',";
            $page_data .="'".($data['chat'] ? $data['chat'] : 0)."',";
            $fall_data .="'".($data['unknown'] ? $data['unknown'] : 0)."',";
        }

        $page_date = rtrim($page_date,',');
        $page_data = rtrim($page_data,',');
        $fall_data = rtrim($fall_data,',');

        $TMPL['url_layout'] = $g['url_layout'];
        $TMPL['page_date'] = $page_date;
        $TMPL['page_data'] = $page_data;
        $TMPL['fall_data'] = $fall_data;

        // 총 유입수 차트
        $total_chart=new skin('vendor/total_conversation');
        $result['conversation_chart']=$total_chart->make();
        $result['tmpl'] = $TMPL;

        if($linkType == 'conversation') {
            // 리스트 테이블
            $totalList = '';
            foreach($aConv as $date=>$data) {
                $allSession = $data['session'] ? number_format($data['session']) : 0;
                $allChat = $data['chat'] ? number_format($data['chat']) : 0;
                $perChat = ($allSession && $allChat ? number_format($allSession/$allChat) : 0);
                $fall = $data['unknown'] ? number_format($data['unknown']) : 0;
                $fall .=' ('.($fall ? number_format(($fall/$allChat*100),1) : '0.0').'%)';

                $totalList .='<tr>';
                $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$date.'</td>';
                $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$allSession.'</td>';
                $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$allChat.'</td>';
                $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$perChat.'</td>';
                $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$fall.'</td>';
                $totalList .='</tr>';
            }
            $result['totalList']=$totalList;
        }
    }
}

if($linkType == 'page_data') {
    // 대화상자, 많이 한 질문, 많이 사용한 단어
    $_data = array();
    $_data['vendor'] = $vendor;
    $_data['bot'] = $botuid;
    $_data['recnum'] =10;
    $_data['mod'] = $mod;
    $_data['page'] = $page;
    $_data['d_start'] = $d_start;
    $_data['d_end'] = $d_end;
    if($mod == 'wordgroup') {
        $_data['keyword'] = $keyword;
        $statisData = $chatbot->getWordGroupData($_data);
    } else {
        $statisData = $chatbot->getFavorateQuestionData($_data);
    }
    $result['chHtml'] = $statisData[1];
    $result['chBtn'] = $statisData[2];
}

if($linkType == 'node_analysis') {
    // 대화 상자 Chart를 위한 JSON
    $nodes = $pointers = array();
    $query = "Select uid From ".$table[$m."dialog"]." Where type='D' and active=1 and bot='".$botuid."'";
    $aInfo = db_fetch_assoc(db_query($query,$DB_CONNECT));
    $dialog = $aInfo['uid'];

    $_wh = "vendor='".$vendor."' and bot='".$botuid."' and dialog='".$dialog."' and track_flag=1 and is_unknown=0 and hidden=0";
    $query = "Select id as nodeid, parent, depth, name From ".$table[$m.'dialogNode']." Where ".$_wh." Order by parent ASC, gid ASC";
    $RCD = db_query($query,$DB_CONNECT);
    $aNodes = array();

    while($R=db_fetch_assoc($RCD)){
        $aNodes[] = $R;
        $R['size'] = 10;

        if(!isset($pointers[$R['nodeid']])) {
            $pointers[$R['nodeid']] = $R;
        }
        if(!empty($R['parent'])) {
            if(!isset($pointers[$R['parent']])) {
                $pointers[$R['parent']] = $R;
            }
            $pointers[$R['parent']]['children'][] =  &$pointers[$R['nodeid']];
        } else {
            $nodes[$R['nodeid']] = &$pointers[$R['nodeid']];
        }
    }

    $nodes = array_values($nodes);
    $nodes = count($nodes[0]) > 0 ? $nodes[0] : array();
    $result['nodes'] = $nodes;

    if($mod != 'excel_export') {
        // 총 대화상자 수
        $result['total'] = array();
        $aCountNode = getCountNode();
        $result['total']['totalNode'] = number_format($aCountNode['cnt']);

        // 총 대화 수
        $aCountConversation = getCountConversation();
        $totalChat = $aCountConversation['cnt'];
        $result['total']['totalChat'] = number_format($totalChat);

        // 총 누적 세션수(룸토큰수)
        $aCountSession = getCountSession();
        $totalUser = $aCountSession['cnt'];
        $result['total']['totalUser'] = number_format($totalUser);

        // 인당 대화 수
        $result['total']['perChat'] = $totalChat && $totalUser ? number_format($totalChat/$totalUser) : 0;

        // 총 unknown 수
        $result['total']['totalUnknown'] = number_format(getCountUnknown());

        // 총 누적 접속 수
        $aCountAccess = getCountAccess();
        $result['total']['totalAccess'] = number_format($aCountAccess['cnt']);
    }

    //--------- 대화상자 현황 ------------//
    // 전체 질문(응답) 수
    $query = "Select count(*) as nCnt From ".$table[$m.'chatLog']." Where vendor='".$vendor."' and bot='".$botuid."' and (node <> '' and is_unknown = 0) ".$_period_chatLog." ";
    $aInfo = db_fetch_assoc(db_query($query,$DB_CONNECT));
    $nTotalQ = $aInfo['nCnt'];

    for($i=0, $nCnt=count($aNodes); $i<$nCnt; $i++) {
        $query = "Select count(*) as nCnt From ".$table[$m.'chatLog']." Where bot='".$botuid."' and node='".$aNodes[$i]['name']."' ".$_period_chatLog." ";
        $aInfo = db_fetch_assoc(db_query($query,$DB_CONNECT));
        $nCntQ = $aInfo['nCnt'];
        $aNodes[$i]['nCntQ'] = $nCntQ;
    }
    array_multisort(array_column($aNodes, 'nCntQ'), SORT_DESC, $aNodes);

    // html 리스트 출력
    if($mod != 'excel_export') {
        $i = 1;
        $chHtml = "";
        foreach($aNodes as $item) {
            $chHtml .="<tr>";
            $chHtml .=" <td>".$i."</td>";
            $chHtml .=" <td>".$item['name']."</td>";
            $chHtml .=" <td>".number_format($item['nCntQ'])."</td>";
            $chHtml .=" <td>".($nTotalQ ? number_format(($item['nCntQ']/$nTotalQ)*100, 1) : '0.0')."%</td>";
            $chHtml .="</tr>";
            $i++;
        }

        $result['stateHtml'] = $chHtml;

    } else {

    // 엑셀 파일 출력
        include_once $g['dir_module'] . "lib/phpExcel/PHPExcel.php";
        include_once $g['dir_module'] . "lib/phpExcel/PHPExcel/IOFactory.php";

        $chExcelTitle = "대화분석_대화상자";
        $objPHPExcel = new PHPExcel();
        $chTemplateFile = $g['dir_module'] . 'lib/tp_node.xlsx';
        $objReader = PHPExcel_IOFactory::createReaderForFile($chTemplateFile);
        $objPHPExcel = $objReader->load($chTemplateFile);

        $objPHPExcel->getProperties()->setCreator("persona")
                                    ->setLastModifiedBy("persona")
                                    ->setTitle($chExcelTitle)
                                    ->setSubject($chExcelTitle);

        $sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

        $nIndex = 2;
        foreach($aNodes as $item) {
            $nCntQ = number_format($item['nCntQ']);
            $nCntRes = ($nTotalQ ? number_format(($item['nCntQ']/$nTotalQ)*100, 1) : '0.0')."%";

            $sheetIndex->setCellValue("A$nIndex", ($nIndex-1))
                       ->setCellValue("B$nIndex", $item['name'])
                       ->setCellValue("C$nIndex", $nCntQ)
                       ->setCellValue("D$nIndex", $nCntRes);
            $nIndex++;
        }

        $sheetIndex->setTitle($chExcelTitle."_".date("Y.m.d"));
        $objPHPExcel->setActiveSheetIndex(0);
        $chFileName = iconv("UTF-8", "EUC-KR", $chExcelTitle."_".$date['today']);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$chFileName.'.xlsx"');
        header('Cache-Control: max-age=0');
        header("Content-Transfer-Encoding:binary");
        header("Content-charset:euc-kr");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}

if($linkType == 'node_conversation') {
    include_once $g['path_core'] . "function/simple_html_dom.php";

    $nodeNames = trim($_POST['nodeNames']);
    if(!$nodeNames) {
        $result['convHtml'] = '';
    } else {
        $_period_chatLog = "";
        if($d_start && $d_end) {
            $d_start = str_replace('-','', $d_start);
            $d_end = str_replace('-','', $d_end);
            $_period_chatLog = " and (left(A.d_regis,8) between '".$d_start."' and '".$d_end."') ";
        }

        $recnum = 10;
        $page = $page ? $page : 1;

        $nodeNames = explode('|', $nodeNames);
        $nodeNames = "'".implode("','", $nodeNames)."'";

        // 응답 로그 기준으로 뽑을 경우 텍스트/버튼 등 한 질문에 복수의 응답이 건별로 등록되어 있어서 chat으로 그룹화

        $_where = "and A.vendor='$vendor' and A.bot='$botuid' and (A.node <> '' and A.is_unknown=0) ".$_period_chatLog." and A.node in (".$nodeNames.") ";

        $query ="Select count(*) as nCnt From ".$table[$m.'chatLog']." A Where 1>0 ".$_where." ";
        $aInfo = db_fetch_assoc(db_query($query,$DB_CONNECT));
        $nTotal = $aInfo['nCnt'];
        $totalPage=ceil($nTotal/$recnum);

        $query = "Select A.uid, A.node, A.entity, A.content, B.content as response From ".$table[$m.'chatLog']." A ";
        $query .="left join ".$table[$m.'botChatLog']." B on A.uid = B.chat ";
        $query .="Where 1>0 ".$_where." ";
        $query .="Group by A.uid ";
        $query .="Order by field(A.node, ".$nodeNames."), A.uid ASC ";

        // 엑셀 파일 출력 아닐 경우
        if($mod != 'excel_export') {
            $query .="limit ".($page-1)*$recnum.", ".$recnum." ";
        }

        $RCD = db_query($query,$DB_CONNECT);
        $chHtml = "";

        // html 리스트 출력
        if($mod != 'excel_export') {
            while($R=db_fetch_assoc($RCD)){
                $keywords = $response = "";
                if($R['entity']) {
                    $aEntity = explode(",", $R['entity']);
                    foreach($aEntity as $val) {
                        $aEntityVal = explode("|", $val);
                        $keywords .=$aEntityVal[0].", ";
                    }
                }
                $keywords = rtrim($keywords, ", ");

                if($R['response']) {
                    $response = "";
                    $oHtml = str_get_html($R['response']);
                    if($oHtml->find('div.cb-chatting-balloon', 0)) {
                        $response = $oHtml->find('div.cb-chatting-balloon', 0)->find('span', 0)->plaintext;
                    } else {
                        if($oHtml->find('[data-role=menuType-resItem]')) {
                            $response = "버튼 [";
                            foreach($oHtml->find('[data-role=menuType-resItem]') as $btn) {
                                $response .=$btn->title.", ";
                            }
                            $response = rtrim($response, ', ').']';
                        }
                    }
                }

                $chHtml .="<tr>";
                $chHtml .=" <td>".$R['node']."</td>";
                $chHtml .=" <td class='aleft'>".$R['content']."</td>";
                $chHtml .=" <td>".$keywords."</td>";
                $chHtml .=" <td class='aleft'>".$response."</td>";
                $chHtml .="</tr>";
            }
            $result['convHtml'] = $chHtml;

            // 이전 버튼 세팅
            if($page==1) $prev_disabled = 'disabled';
            else $prev_page=$page-1;

            // 다음 버튼 세팅
            if($nTotal > $recnum){
                if($page < $totalPage) $next_page=$page+1;
                else{
                    $next_page ='';
                    $next_disabled ='disabled';
                }
            }else{
               $next_page ='';
               $next_disabled ='disabled';
            }

            $pageBtn='';
            $pageBtn.='<div class="btn-group" style="margin-right:10px;font-weight:400">'.$page.'/'.$totalPage.'</div>';
            $pageBtn.='<div class="btn-group">';
            $pageBtn.=' <button type="button" class="btn btn-sm btn-default btn_page" data-page="'.$prev_page.'" '.$prev_disabled.'><i class="fa fa-angle-left"></i></button>';
            $pageBtn.=' <button type="button" class="btn btn-sm btn-default btn_page" data-page="'.$next_page.'" '.$next_disabled.'><i class="fa fa-angle-right"></i></button>';
            $pageBtn.='</div>';
            $result['pageHtml'] = $pageBtn;

        } else {

        // 엑셀 파일 출력
            include_once $g['dir_module'] . "lib/phpExcel/PHPExcel.php";
            include_once $g['dir_module'] . "lib/phpExcel/PHPExcel/IOFactory.php";

            $chExcelTitle = "대화분석_대화내역상세";
            $objPHPExcel = new PHPExcel();
            $chTemplateFile = $g['dir_module'] . 'lib/tp_nodeConv.xlsx';
            $objReader = PHPExcel_IOFactory::createReaderForFile($chTemplateFile);
            $objPHPExcel = $objReader->load($chTemplateFile);

            $objPHPExcel->getProperties()->setCreator("persona")
                                        ->setLastModifiedBy("persona")
                                        ->setTitle($chExcelTitle)
                                        ->setSubject($chExcelTitle);

            $sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

            $nIndex = 2;
            while($R=db_fetch_assoc($RCD)){
                $keywords = $response = "";
                if($R['entity']) {
                    $aEntity = explode(",", $R['entity']);
                    foreach($aEntity as $val) {
                        $aEntityVal = explode("|", $val);
                        $keywords .=$aEntityVal[0].", ";
                    }
                }
                $keywords = rtrim($keywords, ", ");

                if($R['response']) {
                    $response = "";
                    $oHtml = str_get_html($R['response']);
                    if($oHtml->find('div.cb-chatting-balloon', 0)) {
                        $response = $oHtml->find('div.cb-chatting-balloon', 0)->find('span', 0)->plaintext;
                    } else {
                        if($oHtml->find('[data-role=menuType-resItem]')) {
                            $response = "버튼 [";
                            foreach($oHtml->find('[data-role=menuType-resItem]') as $btn) {
                                $response .=$btn->title.", ";
                            }
                            $response = rtrim($response, ', ').']';
                        }
                    }
                }

                $sheetIndex->setCellValue("A$nIndex", $R['node'])
                           ->setCellValue("B$nIndex", $R['content'])
                           ->setCellValue("C$nIndex", $keywords)
                           ->setCellValue("D$nIndex", $response);
                $nIndex++;
            }

            $sheetIndex->setTitle($chExcelTitle."_".date("Y.m.d"));
            $objPHPExcel->setActiveSheetIndex(0);
            $chFileName = iconv("UTF-8", "EUC-KR", $chExcelTitle."_".$date['today']);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$chFileName.'.xlsx"');
            header('Cache-Control: max-age=0');
            header("Content-Transfer-Encoding:binary");
            header("Content-charset:euc-kr");

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }
    }
}

if($linkType == 'node_flow') {
    $_where = "A.vendor = $vendor and A.bot = $botuid and (A.node <> '' and A.node <> '인사' and A.node <> 'Welcome') and A.is_unknown = 0 ";

    $_period_chatLog = "";
    if($d_start && $d_end) {
        $d_start = str_replace('-','', $d_start);
        $d_end = str_replace('-','', $d_end);
        $_where .=" and (left(A.d_regis,8) between '".$d_start."' and '".$d_end."') ";
    }
    if($hTokens) {
        $aTokens = explode(',', $hTokens);
        $aTokens = "'".implode("','", $aTokens)."'";
        $_where .=" and A.roomToken in (".$aTokens.") ";
    }

    $recnum = 10;
    $page = $page ? $page : 1;

    // 대화 흐름 로딩
    $query = "Select count(*) as nCnt From ( ";
    $query .="  Select count(*) From ".$table[$m.'chatLog']." A ";
    $query .="  Where ".$_where." ";
    $query .="  Group by A.roomToken ";
    $query .=") as A ";
    $aInfo = db_fetch_assoc(db_query($query,$DB_CONNECT));
    $nTotal = $aInfo['nCnt'];
    $totalPage=ceil($nTotal/$recnum);

    $query = "Select ";
    $query .="  A.uid, A.roomToken, A.nodeid, min(A.d_regis) as d_start, max(A.d_regis) as d_end, A.agent, ";
    //$query .="  group_concat(A.node Order by A.depth ASC, A.nodeid ASC) as names, group_concat(A.nodeid Order by A.depth ASC, A.nodeid ASC) as nodes ";
    $query .="  group_concat(A.node Order by A.uid ASC) as names, group_concat(A.nodeid Order by A.uid ASC) as nodes ";
    $query .="From ( ";
	$query .="  Select A.uid, A.roomToken, A.node, A.d_regis, A.agent, C.id as nodeid, C.depth From ".$table[$m.'chatLog']." A ";
	$query .="  inner join ".$table[$m.'dialog']." B on A.bot = B.bot and B.type = 'D' ";
	$query .="  inner join ".$table[$m.'dialogNode']." C on A.bot = C.bot and A.node = C.name and B.uid = C.dialog ";
	$query .="  Where ".$_where." ";
    $query .=") as A Group by A.roomToken Order by A.uid DESC ";
    if ($mod != 'excel_export') {
        $query .= "limit " . ($page - 1) * $recnum . ", " . $recnum . " ";
    }
    $RCD = db_query($query,$DB_CONNECT);

    if(!$hTokens) {
        $aNode = $aLink = array();
        $aNode[0] = array('name'=>'Welcome');

        while($R=db_fetch_array($RCD)){
            $nodes = explode(',', $R['nodes']);
            $names = explode(',', $R['names']);
            $nodes = array_unique($nodes);
            $names = array_unique($names);
            for($i=0, $nCnt=count($nodes); $i<$nCnt; $i++) {
                if(!$nodes[$i]) continue;
                if(!array_key_exists($nodes[$i], $aNode)) {
                    $aNode[$nodes[$i]] = array('name'=>$names[$i], 'token'=>$R['roomToken']);
                }
                if($i == 0) {
                    $aLink[] = array('source'=>0, 'target'=>array_search($nodes[$i], array_keys($aNode)), 'value'=>0.2, 'token'=>$R['roomToken']);
                } else {
                    $_source = $source = array_search($nodes[($i-1)], array_keys($aNode));
                    $_target = $target = array_search($nodes[$i], array_keys($aNode));
                    if($_source > $_target) {
                        $source = $_target;
                        $target = $_source;
                    }

                    $aLink[] = array('source'=>$source, 'target'=>$target, 'value'=>2, 'token'=>$R['roomToken']);
                }
            }
        }
        $result['node_json'] = array('nodes'=>array_values($aNode), 'links'=>$aLink);
    }

    // 포인터를 처음으로 이동
    mysqli_data_seek($RCD, 0);

    // html 리스트 출력
    if($mod != 'excel_export') {
        $chHtml = "";
        $i = 0;
        while($R=db_fetch_array($RCD)){
            if($page == 1 && $i >= $recnum) break;

            $mobile = isMobileConnect($R['agent']);
            $channel = $mobile ? "Mobile" : "PC";

            $d_regis = date('Y-m-d', strtotime($R['d_start']));
            $d_start = date('H:i:s', strtotime($R['d_start']));
            $d_end = date('H:i:s', strtotime($R['d_end']));
            $diff1 = date_create($R['d_start']);
            $diff2 = date_create($R['d_end']);
            $diff= date_diff($diff2, $diff1);
            $d_diff = $diff->format("%H:%I:%S");

            $names = explode(',', $R['names']);
            $names = array_unique($names);
            $ul_node = "<ul class='ul_node_box'><li><span>Welcome</span></li>";
            for($j=0, $nCnt=count($names); $j<$nCnt; $j++) {
                if(!$names[$j]) continue;
                $ul_node .="<li><span>".$names[$j]."</span></li>";
            }
            $ul_node .="</ul>";

            $chHtml .="<tr>";
            $chHtml .=" <td>".$R['roomToken']."</td>";
            $chHtml .=" <td>".$channel."</td>";
            $chHtml .=" <td>".$d_regis."</td>";
            $chHtml .=" <td>".$d_start."</td>";
            $chHtml .=" <td>".$d_end."</td>";
            $chHtml .=" <td>".$d_diff."</td>";
            $chHtml .=" <td>직접종료</td>";
            $chHtml .=" <td class='aleft'>".$ul_node."</td>";
            $chHtml .="</tr>";

            $i++;
        }
        $result['convHtml'] = $chHtml;

        // 이전 버튼 세팅
        if($page==1) $prev_disabled = 'disabled';
        else $prev_page=$page-1;

        // 다음 버튼 세팅
        if($nTotal > $recnum){
            if($page < $totalPage) $next_page=$page+1;
            else{
                $next_page ='';
                $next_disabled ='disabled';
            }
        }else{
            $next_page ='';
            $next_disabled ='disabled';
        }

        $pageBtn='';
        //$pageBtn.='<div class="btn-group" style="margin-right:10px;font-weight:400">'.$page.'/'.$totalPage.'</div>';
        $pageBtn.='<div class="btn-group">';
        $pageBtn.=' <button type="button" class="btn btn-sm btn-default btn_page" data-page="'.$prev_page.'" '.$prev_disabled.'><i class="fa fa-angle-left"></i></button>';
        $pageBtn.='<span>'.$page.'/'.$totalPage.'</span>';
        $pageBtn.=' <button type="button" class="btn btn-sm btn-default btn_page" data-page="'.$next_page.'" '.$next_disabled.'><i class="fa fa-angle-right"></i></button>';
        $pageBtn.='</div>';
        $result['pageHtml'] = $pageBtn;

    } else {

    // 엑셀 파일 출력
        include_once $g['dir_module'] . "lib/phpExcel/PHPExcel.php";
        include_once $g['dir_module'] . "lib/phpExcel/PHPExcel/IOFactory.php";

        $chExcelTitle = "대화분석_대화흐름상세";
        $objPHPExcel = new PHPExcel();
        $chTemplateFile = $g['dir_module'] . 'lib/tp_nodeFlow.xlsx';
        $objReader = PHPExcel_IOFactory::createReaderForFile($chTemplateFile);
        $objPHPExcel = $objReader->load($chTemplateFile);

        $objPHPExcel->getProperties()->setCreator("persona")
                                    ->setLastModifiedBy("persona")
                                    ->setTitle($chExcelTitle)
                                    ->setSubject($chExcelTitle);
        $sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

        $nIndex = 2;
        while($R=db_fetch_assoc($RCD)){
            $mobile = isMobileConnect($R['agent']);
            $channel = $mobile ? "Mobile" : "PC";

            $d_regis = date('Y-m-d', strtotime($R['d_start']));
            $d_start = date('H:i:s', strtotime($R['d_start']));
            $d_end = date('H:i:s', strtotime($R['d_end']));
            $diff1 = date_create($R['d_start']);
            $diff2 = date_create($R['d_end']);
            $diff= date_diff($diff2, $diff1);
            $d_diff = $diff->format("%H:%I:%S");

            $names = explode(',', $R['names']);
            $names = array_unique($names);
            $nodes = "Welcome > ";
            for($j=0, $nCnt=count($names); $j<$nCnt; $j++) {
                if(!$names[$j]) continue;
                $nodes .=$names[$j]." > ";
            }
            $nodes = rtrim($nodes, " > ");

            $sheetIndex->setCellValue("A$nIndex", $R['roomToken'])
                       ->setCellValue("B$nIndex", $channel)
                       ->setCellValue("C$nIndex", $d_regis)
                       ->setCellValue("D$nIndex", $d_start)
                       ->setCellValue("E$nIndex", $d_end)
                       ->setCellValue("F$nIndex", $d_diff)
                       ->setCellValue("G$nIndex", '직접종료')
                       ->setCellValue("H$nIndex", $nodes);
            $nIndex++;
        }

        $sheetIndex->setTitle($chExcelTitle."_".date("Y.m.d"));
        $objPHPExcel->setActiveSheetIndex(0);
        $chFileName = iconv("UTF-8", "EUC-KR", $chExcelTitle."_".$date['today']);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$chFileName.'.xlsx"');
        header('Cache-Control: max-age=0');
        header("Content-Transfer-Encoding:binary");
        header("Content-charset:euc-kr");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}

echo json_encode($result);
exit;
?>
