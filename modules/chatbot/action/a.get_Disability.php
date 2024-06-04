<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
include_once $g['dir_module'].'var/define.path.php'; // class, 모듈, 레이아웃 패스 세팅
$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$linkType = trim($_REQUEST['linkType']);
$vendor = trim($_REQUEST['vendor']);
$botuid = trim($_REQUEST['botuid']);
$mod = trim($_REQUEST['mod']);
$p = trim($_REQUEST['p']);
$d_start = trim($_REQUEST['d_start']);
$d_end = trim($_REQUEST['d_end']);
$keyword = trim($_REQUEST['keyword']);
$hTokens = trim($_REQUEST['hTokens']);

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
    $_wh_chat = "vendor='".$vendor."' and bot='".$botuid."' and category='disability' ".$_period_chatLog;

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

        $totalList .='<tr class="'.$r_finish.'">';
        $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$d_regis.'</td>';
        $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$R['addval'].'</td>';
        $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$R['name'].'</td>';
        $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$phone.'</td>';
        $totalList .='  <td class="txt-oflo" style="text-align:left;">'.nl2br($R['content']).'</td>';
        $totalList .='  <td class="txt-oflo" style="text-align:center;">'.$status.'</td>';
        $totalList .='</tr>';
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

if($linkType == 'excel_down') {
    include_once $g['dir_module'] . "lib/phpExcel/PHPExcel.php";
    include_once $g['dir_module'] . "lib/phpExcel/PHPExcel/IOFactory.php";

    if($vendor && $botuid) {
        $chExcelTitle = "장애접수 목록";
        $objPHPExcel = new PHPExcel();
        $chTemplateFile = $g['dir_module'] . 'lib/tp_disabilityData.xlsx';
        $objReader = PHPExcel_IOFactory::createReaderForFile($chTemplateFile);
        $objPHPExcel = $objReader->load($chTemplateFile);

        $objPHPExcel->getProperties()->setCreator("persona")
                                    ->setLastModifiedBy("persona")
                                    ->setTitle($chExcelTitle)
                                    ->setSubject($chExcelTitle);

        $sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

        $nIndex = 2;

        $_wh_chat = "vendor='".$vendor."' and bot='".$botuid."' and category='disability'";
        $query = "select * from ".$table[$m.'reserve']." where ".$_wh_chat." order by d_regis desc ";
        $RCD = db_query($query,$DB_CONNECT);

        while($R=db_fetch_array($RCD)){
            $phone = getStrToPhoneFormat($R['phone']);
            $d_regis = date("Y-m-d H:i:s", strtotime($R['d_regis']));
            $r_finish = $R['status'] == 'finish' ? '완료' : '대기';

            $sheetIndex->setCellValue("A$nIndex", $d_regis)
                        ->setCellValue("B$nIndex", $R['addval'])
                        ->setCellValue("C$nIndex", $R['name'])
                        ->setCellValue("D$nIndex", $phone)
                        ->setCellValue("E$nIndex", $R['content'])
                        ->setCellValue("F$nIndex", $r_finish);
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
