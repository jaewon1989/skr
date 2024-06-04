<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
require_once $g['dir_module'].'var/define.path.php';

if(!isset($_SESSION['mbr_uid']) || !$_SESSION['mbr_uid']) {
    echo json_encode(array(-1, 401)); exit;
}

$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$data = $_POST;
// javascript 막기
$data = getStripScript($data);

$vendor = $data['vendor'];
$bot = $data['bot'];
$dialog = $data['dialog'];
$linkType = $data['linkType'];

$recnum = 15;
$p = $data['p'] ? $data['p'] : 1;
$_wh = "vendor=$vendor and bot=$bot ";

// linkType 분기 
if($linkType=='search'){    
    if($data['category1']) $_wh .="and category1 = '".$data['category1']."' ";
    if($data['category1'] && $data['category2']) $_wh .="and category2 = '".$data['category2']."' ";
    if($data['category1'] && $data['category2'] && $data['category3']) $_wh .="and category3 = '".$data['category3']."' ";
    
    if($data['chField'] && $data['chFind']) {
        $_wh .="and ".$data['chField']." like '%".$data['chFind']."%' ";
    }
    
    $NUM = getDbRows($table[$m.'faq'], $_wh);
    $TPG = getTotalPage($NUM,$recnum);
    $pageLink = getPageLink($recnum, $p, $TPG, '');
    
    $query = "select * from ".$table[$m.'faq']." where ".$_wh." order by uid desc ";
    $query .="Limit ".($p-1)*$recnum.", ".$recnum." ";
    $RCD = db_query($query,$DB_CONNECT);
    
    $faqList = '';
    while($R=db_fetch_array($RCD)){
        $faqList .='<tr>';
        $faqList .='  <td><input type="hidden" name="uid[]" value="'.$R['uid'].'" /><input type="checkbox" data-role="select-all" data-uid="'.$R['uid'].'" /></td>';
        $faqList .='  <td><input type="text" class="form-control" name="category1[]" value="'.htmlspecialchars($R['category1']).'" /></td>';
        $faqList .='  <td><input type="text" class="form-control" name="category2[]" value="'.htmlspecialchars($R['category2']).'" /></td>';
        $faqList .='  <td><input type="text" class="form-control" name="category3[]" value="'.htmlspecialchars($R['category3']).'" /></td>';
        $faqList .='  <td><input type="text" class="form-control" name="question[]" value="'.htmlspecialchars($R['question']).'" /></td>';
        $faqList .= ' <td><textarea class="form-control faq-answer-area" name="answer[]">' . htmlspecialchars($R['answer']) . '</textarea></td>';
        $faqList .='</tr>';
    }    
    
    $result['faqList']=$faqList;
    $result['pageLink']=$pageLink;
    
    if($data['category1'] && !$data['category2']) {
        $query ="Select category2 From ".$table[$m."faq"]." Where vendor=$vendor and bot=$bot and category1 = '$category1' and category2 <> '' group by category2";
        $RCD = db_query($query,$DB_CONNECT);
        $s_category2 = '';
        while($R=db_fetch_array($RCD)){
            $s_category2 .="<option value='".$R['category2']."'>".$R['category2']."</option>";
        }
        $result['category2']=$s_category2;
    } else if($data['category1'] && $data['category2']) {
        $query ="Select category3 From ".$table[$m."faq"]." Where vendor=$vendor and bot=$bot and category1 = '$category1' and category2 = '$category2' and category3 <> '' group by category3";
        $RCD = db_query($query,$DB_CONNECT);
        $s_category3 = '';
        while($R=db_fetch_array($RCD)){
            $s_category3 .="<option value='".$R['category3']."'>".$R['category3']."</option>";
        }
        $result['category3']=$s_category3;
    }
    
}else if($linkType=='save'){
    for($i=0, $nCnt=count($data['uid']); $i<$nCnt; $i++) {
        $uid = trim($data['uid'][$i]);
        $category1 = trim($data['category1'][$i]);
        $category2 = trim($data['category2'][$i]);
        $category3 = trim($data['category3'][$i]);
        $question = trim(str_replace('\\', '', $data['question'][$i]));
        $answer = trim(str_replace('\\', '', $data['answer'][$i]));
        $d_regis = $date['totime'];
        
        // 질문, 답변이 없을 경우 제외
        if($question == '' && $answer == '') continue;
        
        // 중복 질문 제외
        /*$is_row = getDbRows($table[$m.'faq'], $_wh." and question = '".$question."'");
        if($is_row > 0) continue;*/


        // 신규 입력
        if ($uid == '') {
            $QKEY = "vendor, bot, category1, category2, category3, question, answer, d_regis";
            $QVAL = "'$vendor', '$bot', '$category1', '$category2', '$category3', '" . addslashes($question) . "', '" . addslashes($answer) . "', '$d_regis'";
            getDbInsert($table[$m . 'faq'], $QKEY, $QVAL);
        } else {
            $QVAL = "category1='$category1', category2='$category2', category3='$category3', question='" . addslashes($question) . "', answer='" . addslashes($answer) . "'";
            getDbUpdate($table[$m . 'faq'], $QVAL, 'uid=' . $uid);
        }

    }
    
}else if($linkType=='delete'){
    $aUid = explode("|", $data['aUid']);
    $uids = array();
    foreach($aUid as $uid) {
        if($uid) $uids[] = $uid;
    }
    $uids = implode(", ", $uids);
    getDbDelete($table[$m.'faq'], "uid in (".$uids.")");

}else if($linkType=='delete_all'){
    getDbDelete($table[$m.'faq'], "1>0");

}else if($linkType =='upload'){
    $saveDir = $g['path_tmp'].'cache';
    
    // 1시간 전 파일 삭제
    $nNowTime = time();
    $aDir = dir($saveDir);
    while ($chFileName = $aDir->read()) {
        if ($chFileName != "." && $chFileName != "..") {
            $nFileTime = fileatime($saveDir."/".$chFileName);
            if (($nNowTime - 1800) > $nFileTime) unlink($saveDir."/".$chFileName);
        }
    }
    
    //선택한 파일 정보 값
    $data['file'] = $_FILES['file'];
    $Upfile = $data['file']; // 선택한 파일
    $tmpname = $Upfile['tmp_name']; // 임시파일
    $realname = $Upfile['name']; // 실제 파일
    $fileExt    = strtolower(getExt($realname)); // 확장자 얻기
    
    if (is_uploaded_file($tmpname)) {
        $newname =$g['time_start'].'_'.rand(10000, 99999).'_'.$realname; // 년월일_파일명.확장자
        $saveFile = $saveDir.'/'.$newname;
        if ($Overwrite == 'true' || !is_file($saveFile)) {
            move_uploaded_file($tmpname,$saveFile);
            @chmod($saveFile,0644); // 새로 들어왔으니 권한 신규 부여
        }
    }
        
    include_once $g['dir_module'] . "lib/phpExcel/PHPExcel.php";
    include_once $g['dir_module'] . "lib/phpExcel/PHPExcel/IOFactory.php";
	
	if(file_exists($saveFile)) {
        $CPHPExcel = new PHPExcel();
        $objReader = PHPExcel_IOFactory::createReaderForFile($saveFile);
        $objReader->setReadDataOnly(true);
        $CPHPExcel = $objReader->load($saveFile);
        $CPHPExcel->setActiveSheetIndex(0);
        $objWorksheet = $CPHPExcel->getActiveSheet();
        
        $nMaxColumn = $objWorksheet->getHighestColumn();
        $nMaxRow = $objWorksheet->getHighestRow();
        
        $d_regis = $date['totime'];
        
        foreach ($objWorksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = array();
            foreach ($cellIterator as $cell) {
                $cells[] = trim(addslashes($cell->getValue()));
            }
            
            if($cells[0] == '대분류' && $cells[1] == '중분류') continue;
            if($cells[3] == '' || $cells[4] == '') continue;

            $cells[3] = trim(str_replace('\\', '', $cells[3]));
            $cells[4] = trim(str_replace('\\', '', $cells[4]));

            // 중복 질문 제외
            $is_row = getDbRows($table[$m.'faq'], $_wh." and question = '".$cells[3]."'");
            if($is_row > 0) continue;
            
            $QKEY = "vendor, bot, category1, category2, category3, question, answer, d_regis";
            $QVAL = "'$vendor', '$bot', '".$cells[0]."', '".$cells[1]."', '".$cells[2]."', '".$cells[3]."', '".$cells[4]."', '$d_regis'";
            getDbInsert($table[$m.'faq'], $QKEY, $QVAL);
        }
    }        

}else if($linkType =='down'){
    if($mod =='form') {
        $chTemplateFile = $g['dir_module'] . "lib/tp_faq.xlsx";
        $chFileName = iconv("UTF-8", "EUC-KR", "FAQ 업로드 양식");
    
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$chFileName.'.xlsx"');
        header('Cache-Control: max-age=0');
        header("Content-Transfer-Encoding:binary");
        header("Content-charset:euc-kr");
        
        $fp = fopen($chTemplateFile, "r");
        if (!fpassthru($fp)) {
            fclose($fp);
        }        
        exit;
    }
    
    if($data['mod'] == 'data') {
        include_once $g['dir_module'] . "lib/phpExcel/PHPExcel.php";
        include_once $g['dir_module'] . "lib/phpExcel/PHPExcel/IOFactory.php";
        
        $chExcelTitle = "FAQ 데이터";
        $objPHPExcel = new PHPExcel();
        $chTemplateFile = $g['dir_module'] . 'lib/tp_faq.xlsx';
        $objReader = PHPExcel_IOFactory::createReaderForFile($chTemplateFile);
        $objPHPExcel = $objReader->load($chTemplateFile);
    			
        $objPHPExcel->getProperties()->setCreator("persona")
                                    ->setLastModifiedBy("persona")
                                    ->setTitle($chExcelTitle)
                                    ->setSubject($chExcelTitle);
                                    
        $sheetIndex = $objPHPExcel->setActiveSheetIndex(0);
        
        $nIndex = 2;
        
        $query = "Select * From ".$table[$m.'faq']." Where vendor='".$vendor."' and bot='".$bot."' Order by uid ASC";
        $RCD = db_query($query, $DB_CONNECT);
        while($R = db_fetch_array($RCD)) {
            $sheetIndex->setCellValue("A$nIndex", stripslashes($R['category1']))
                        ->setCellValue("B$nIndex", stripslashes($R['category2']))
                        ->setCellValue("C$nIndex", stripslashes($R['category3']))
                        ->setCellValue("D$nIndex", stripslashes($R['question']))
                        ->setCellValue("E$nIndex", stripslashes($R['answer']));
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
