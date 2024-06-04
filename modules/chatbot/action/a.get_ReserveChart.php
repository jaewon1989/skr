<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
include_once $g['dir_module'].'var/define.path.php'; // class, 모듈, 레이아웃 패스 세팅
$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$linkType = trim($_POST['linkType']);
$vendor = trim($_POST['vendor']);
$botuid = trim($_POST['botuid']);
$mod = trim($_POST['mod']);
$p = trim($_POST['p']);
$d_start = trim($_POST['d_start']);
$d_end = trim($_POST['d_end']);
$keyword = trim($_POST['keyword']);
$hTokens = trim($_POST['hTokens']);

// 시작일, 종료일 '-' 제거
$_period_counter = $_period_chatLog = "";
if($d_start && $d_end) {
    $d_start = str_replace('-','', $d_start);
    $d_end = str_replace('-','', $d_end);

    $_period_counter = " and (d_regis between '".$d_start."' and '".$d_end."') ";
    $_period_chatLog = " and (left(d_regis,8) between '".$d_start."' and '".$d_end."') ";
}

if($linkType == 'reserve') {

    // 접속 그래프 쿼리 세팅
    $_wh = "vendor='".$vendor."' and botuid='".$botuid."'".$_period_counter;
    $_wh_chat = "vendor='".$vendor."' and bot='".$botuid."'".$_period_chatLog;

    if($mod != "paging") {
        $query = "select d_regis, sum(page) as page from ".$table[$m.'counter']." where ".$_wh." group by d_regis order by d_regis";
        $query ="Select mode, d_regis, page From (";
        $query .="  Select 'all' as mode, d_regis, sum(page) as page from ".$table[$m.'counter']." Where ".$_wh." group BY botuid, d_regis ";
        $query .="  union all ";
        $query .="  Select 'f' as mode, d_regis, page from ".$table[$m.'counter']." Where ".$_wh." and type=1 group BY botuid, type, d_regis ";
        $query .="  union all ";
        $query .="  Select 's' as mode, d_regis, page from ".$table[$m.'counter']." Where ".$_wh." and type=2 group BY botuid, type, d_regis ";
        $query .="  union all ";
        $query .="  Select 't' as mode, left(d_regis,8), count(*) as page from ".$table[$m.'chatLog']." Where ".$_wh_chat." and roomToken <> '' group BY left(d_regis,8) ";
        $query .=") as A Order by d_regis ASC, mode ASC";
        $RCD = db_query($query,$DB_CONNECT);

        $aAccess = array();
        while($R=db_fetch_array($RCD)){
            $date = substr($R['d_regis'],0,4)."/".substr($R['d_regis'],4,2)."/".substr($R['d_regis'],6,2);
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
    }

    // 리스트 테이블
    $recnum = 10;
    $p = $p ? $p : 1;

    $NUM = getDbRows($table[$m.'reserve'], $_wh_chat);
    $TPG = getTotalPage($NUM,$recnum);
    $pageLink = getPageLink($recnum, $p, $TPG, '');

    $query = "select * from ".$table[$m.'reserve']." where ".$_wh_chat." order by d_regis desc ";
    $query .="Limit ".($p-1)*$recnum.", ".$recnum." ";
    $RCD = db_query($query,$DB_CONNECT);

    $totalList = '';
    while($R=db_fetch_array($RCD)){
        $phone = getStrToPhoneFormat($R['phone']);
        $d_regis = date("Y-m-d H:i:s", strtotime($R['d_regis']));
        $d_reserve = date("Y-m-d H:i", strtotime($R['d_reserve']));
        $r_finish = $R['status'] == 'finish' ? 'r_finish' : '';

        $status = "<select class='s_status' name='status' uid='".$R['uid']."'>";
        $status .=" <option value='ready' ".($R['status'] == 'ready' ? 'selected' : '').">대기</option>";
        $status .=" <option value='finish' ".($R['status'] == 'finish' ? 'selected' : '').">완료</option>";
        $status .="</select>";

        if($my['cgroup'] == "kblife") {
            $R2 = $R['addval'] ? json_decode($R['addval'], true) : array();
            $R2['action'] = !isset($R2['action']) ? "상담신청" : $R2['action'];
            $method = $R2['exeRstCase'] == "email" ? "이메일" : "전화 (".$R2['counselTime1']."시~".$R2['counselTime2']."시)";

            $totalList .='<tr class="'.$r_finish.'">';
            $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$R2['action'].'</td>';
            $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$R['name'].'</td>';
            $totalList .='  <td class="txt-oflo" style="text-align:center;">'.getStrToPhoneFormat($R['phone']).'</td>';
            $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$R2['uemail'].'</td>';
            $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$R2['ujumin1'].'-'.$R2['ujumin2'].'</td>';
            $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$R2['sido'].' '.$R2['sigugun'].'</td>';
            $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$d_regis.'</td>';
            $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$status.'</td>';
            $totalList .='  <td class="txt-oflo" style="text-align:center;"><a href="javascript:;" class="rsv_info cb-button">상세정보 <i class="fa fa-angle-down"></i></td>';
            $totalList .='</tr>';
            $totalList .='<tr class="chatLog">';
            $totalList .='  <td colspan="8">';
            $totalList .='      <div class="chatLogWrap">';
            $totalList .='          <table class="tbHeader">';
            $totalList .='              <colgroup><col width="15%"><col width="15%"><col width="15%"><col width="15%"><col width="40%"><col width="17"></colgroup>';
            $totalList .='              <tr>';
            $totalList .='                  <th>상담내용</th>';
            $totalList .='                  <th>관심상품</th>';
            $totalList .='                  <th>관심주제</th>';
            $totalList .='                  <th>상담방법</th>';
            $totalList .='                  <th>내용</th>';
            $totalList .='                  <th></th>';
            $totalList .='              </tr>';
            $totalList .='          </table>';
            $totalList .='          <div id="logListWrap" class="logListWrap">';
            $totalList .='              <table class="tbLogList">';
            $totalList .='                  <colgroup><col width="15%"><col width="15%"><col width="15%"><col width="15%"><col width="40%"></colgroup>';
            $totalList .='                  <tbody class="tBodyLogList">';
            $totalList .='                      <tr>';
            $totalList .='                          <td>'.$R2['exeRstCont'].'</td>';
            $totalList .='                          <td>'.$R2['interestProd'].'</td>';
            $totalList .='                          <td>'.$R2['interestTopic'].'</td>';
            $totalList .='                          <td>'.$method.'</td>';
            $totalList .='                          <td class="aleft">'.nl2br($R['content']).'</td>';
            $totalList .='                      </tr>';
            $totalList .='                  </tbody>';
            $totalList .='              </table>';
            $totalList .='          </div>';
            $totalList .='      </div>';
            $totalList .='  </td>';
            $totalList .='</tr>';
        } else {
            $totalList .='<tr class="'.$r_finish.'">';
            $totalList .='  <td class="txt-oflo" style="text-align:left;">비회원('.$R['roomToken'].')</td>';
            $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$d_regis.'</td>';
            $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$R['name'].'</td>';
            $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$phone.'</td>';
            $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$d_reserve.'</td>';
            $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$status.'</td>';
            $totalList .='</tr>';
        }
    }
    $result['totalList']=$totalList;
    $result['pageLink']=$pageLink;
}

if($linkType == 'status') {
    $uid = trim($_POST['uid']);
    $status = trim($_POST['status']);
    if($uid && $status) {
        getDbUpdate($table[$m.'reserve'], "status='$status'", "uid=$uid");
    } else {
        $result['error'] = true;
        $result['msg'] = '중요변수가 부족합니다.';
    }
}

echo json_encode($result);
exit;
?>
