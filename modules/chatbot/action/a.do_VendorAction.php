<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
require_once $g['dir_module'].'var/define.path.php';

if($_SESSION['mbr_uid'] == '') {
    echo json_encode(array(-1, 401)); exit;
}

$chatbot = new Chatbot();

$callEntity = $chatbot->callEntity;
$callIntent = $chatbot->callIntent;

$result=array();
$result['error']=false;

$data = $_POST;
// javascript 막기
$data = getStripScript($data);

$vendor = $data['vendor'];
$bot = $data['bot'];
$dialog = $data['dialog'];
$linkType = $data['linkType'];

// linkType 분기
if($linkType=='uploadImg'){
	$data['file'] = $_FILES['file'];
    $result = $chatbot->uploadFile($data);

}else if($linkType=='updateBot'){
    $result['content'] = $chatbot->updateBotData($data);

}else if($linkType=='importData'){
    $data['file'] = $_FILES['file'];
    $result = $chatbot->importDataByFile($data);
}else if($linkType =='getTemplate'){

    if($data['page'] == 'adm/legacy'){
        $result['apiParamInput_row'] = $chatbot->getDialogTemplate('vendor/apiParamInput_row');
    }else{
        $result['intentEx_list'] = $chatbot->getDialogTemplate('dialog/intentEx_list');
        $result['intentEx_row'] = $chatbot->getDialogTemplate('dialog/intentEx_row');
        $result['entityEx_list'] = $chatbot->getDialogTemplate('dialog/entityEx_list');
        $result['entityEx_row'] = $chatbot->getDialogTemplate('dialog/entityEx_row');
    }
	$result['no_data'] = $chatbot->getDialogTemplate('dialog/no_data');

}else if($linkType =='getItemEx'){
	if($data['type']=='intent') $result['content'] = $chatbot->getIntentEx($data);
	else if($data['type']=='entity') $result['content'] = $chatbot->getEntityEx($data);
}else if($linkType == 'open-settingChannelModal'){
    $data['act'] = 'getData';
    $data['channel'] = $data['sns'];
    $channelData = $chatbot->controlChannelData($data);
    foreach ($channelData as $name => $value) {
    	$TMPL[$name] = $value;
    }
    $skin = new skin('channel/guide_'.$data['sns']);
    $guidHtml = $skin->make();
	$result['content'] = $guidHtml;

}else if($linkType == 'save-channelSettings'){
    $data['act'] = 'saveData';
    $data['channel'] = $data['sns'];
    $data['nameArray'] = array();
    if($data['channel'] == 'ntok'){
    	$data['nameArray']['auth_code'] = $data['auth_code'];

    }else if($data['channel'] == 'line'){
        $data['nameArray']['channel_id'] = $data['channel_id'];
        $data['nameArray']['channel_secret'] = $data['channel_secret'];
        $data['nameArray']['access_token'] = $data['access_token'];

    }else if($data['channel'] == 'fb'){
        $data['nameArray']['verify_token'] = $data['verify_token'];
        $data['nameArray']['access_token'] = $data['access_token'];

    }else if($data['channel'] == 'botks'){
        $data['nameArray']['client_secret'] = $data['client_secret'];
        $data['nameArray']['access_token'] = $data['access_token'];
    }
    $channelData = $chatbot->controlChannelData($data);

    $result['content'] = $channelData;

}else if($linkType == 'save-legacySettings'){
    $result['content'] = $chatbot->controlLegacyData($data);

}else if($linkType == 'save-vendorResponse'){
    $data['act'] = 'save';
    $result['content'] = $chatbot->controlVendorResponse($data);

}else if($linkType == 'save-legacyApiParam'){
    $data['act'] = 'save';
    $result = $chatbot->controlLegacyApiData($data);

}else if($linkType == 'get-legacyApiParam'){
    $data['act'] = 'get';
    $result = $chatbot->controlLegacyApiData($data);

}else if($linkType == 'del-legacyApiParam'){
    $data['act'] = 'delete';
    $result['content'] = $chatbot->controlLegacyApiData($data);

}else if($linkType == 'test-legacyApiParam'){
    $data['act'] = 'test';
    $AR = $chatbot->controlLegacyApiData($data);
    $result['statusCode'] = $AR['statusCode'];
    $result['content'] = json_decode($AR['body'],true);

}else if($linkType == 'export-intent' || $linkType == 'export-entity'){
    include_once $g['dir_module'] . "lib/phpExcel/PHPExcel.php";
    include_once $g['dir_module'] . "lib/phpExcel/PHPExcel/IOFactory.php";

    $chExcelTitle = $linkType == 'export-intent' ? "인텐트 데이터" : "엔터티 데이터";
    $objPHPExcel = new PHPExcel();
    $chTemplateFile = $g['dir_module'] . 'lib/'.($linkType == 'export-intent' ? 'tp_intentData.xlsx' : 'tp_entityData.xlsx');
    $objReader = PHPExcel_IOFactory::createReaderForFile($chTemplateFile);
    $objPHPExcel = $objReader->load($chTemplateFile);

    $objPHPExcel->getProperties()->setCreator("persona")
                                ->setLastModifiedBy("persona")
                                ->setTitle($chExcelTitle)
                                ->setSubject($chExcelTitle);

    $sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

    $nIndex = 2;

    if($linkType == 'export-intent') {
        $query = "Select A.uid, A.name, B.uid as uid_ex, B.content From ".$table[$m.'intent']." A ";
        $query .="left join ".$table[$m.'intentEx']." B on A.uid = B.intent and B.hidden = 0 ";
        $query .="Where A.vendor='".$data['vendor']."' and A.bot='".$data['bot']."' and A.hidden=0 ";
        $query .="Order by A.uid DESC, uid_ex DESC ";

        $RCD = db_query($query, $DB_CONNECT);
        while($R = db_fetch_array($RCD)) {
            $sheetIndex->setCellValue("A$nIndex", $R['name'])
                        ->setCellValue("B$nIndex", $R['content'])
                        ->setCellValue("C$nIndex", $R['uid']);
            $nIndex++;
        }
        $sheetIndex->getStyle("A1:C".($nIndex-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    } else {
        $query = "Select A.uid, A.name, B.uid as uid_val, B.name as value, B.synonyms From ".$table[$m.'entity']." A ";
        $query .="left join ".$table[$m.'entityVal']." B on A.uid = B.entity and B.hidden = 0 ";
        $query .="Where A.vendor='".$data['vendor']."' and A.bot='".$data['bot']."' and A.hidden=0 and A.type='V' ";
        $query .="Order by A.uid DESC, uid_val DESC";

        $RCD = db_query($query, $DB_CONNECT);
        while($R = db_fetch_array($RCD)) {
            $sheetIndex->setCellValue("A$nIndex", $R['name'])
                        ->setCellValue("B$nIndex", $R['value'])
                        ->setCellValue("C$nIndex", $R['synonyms'])
                        ->setCellValue("D$nIndex", $R['uid']);
            $nIndex++;
        }
        $sheetIndex->getStyle("A1:D".($nIndex-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
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

}else if($linkType =='export-intentForm' || $linkType =='export-entityForm' || $linkType =='export-TSTest'){
    if($linkType =='export-intentForm') {
        $chName = "인텐트";
        $chTemplateFile = $g['dir_module'] . "lib/tp_intent.xlsx";
        $chFileName = iconv("UTF-8", "EUC-KR", $chName." 업로드 양식");
    } else if($linkType =='export-entityForm') {
        $chName = "엔터티";
        $chTemplateFile = $g['dir_module'] . "lib/tp_entity.xlsx";
        $chFileName = iconv("UTF-8", "EUC-KR", $chName." 업로드 양식");
    } else if($linkType =='export-TSTest') {
        $chFileName = $data['fileName'];
        $chTemplateFile = $g['path_tmp'].'out/'.$chFileName;
        $chFileName = iconv("UTF-8", "EUC-KR", "대화테스트_결과");
    }

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

}else if($linkType =='update-qa'|| $linkType =='del-qa'){

    $linkType = $linkType;
    $dd = $_POST['data'];
    $uid_arr = json_decode(stripcslashes($dd['uid_arr']),true);
    $ques_arr = json_decode(stripcslashes($dd['ques_arr']),true);
    $answ_arr = json_decode(stripcslashes($dd['answ_arr']),true);
    $tbl = $table[$m.'qaSet'];

    if($linkType=='update-qa'){

        foreach ($uid_arr as $index => $uid) {
            $ques = $ques_arr[$index];
            $answ = $answ_arr[$index];
            $upQ ="ques='$ques',answ='$answ'";
            getDbUpdate($tbl,$upQ,'uid='.$uid);
        }
    }else if($linkType=='del-qa'){
        //print_r($uid_arr);

        foreach ($uid_arr as $uid) {
            getDbDelete($tbl,'uid='.$uid);
        }
    }

}else if($linkType =='get-responseHint'){
    $_data = $data['data'];
    $result['content'] = $chatbot->getResponseHint($_data);

}else if($linkType =='add-tagFA' || $linkType =='del-tagFA' || $linkType =='get-tagFA'){
    $_data = $data['data'];
    $_data['act'] = $linkType;
    $result = $chatbot->controlTagFA($_data);

}else if($linkType =='deleteBot'){
    $botTemp = new botTemp;
    $_data = $data['data'];
    $_dd = array("vendor"=>$_data['vendor'],"bot"=>$_data['bot'], "mbruid"=>$my['uid']);
    $result = $botTemp->deleteBot($_dd);

}else if($linkType =='copyBot'){
    $botTemp = new botTemp;
    $_data = $data['data'];
    $_dd = array(
        "vendor"=>$_data['vendor'],
        "targetBot"=>$_data['bot'],
        "mbruid"=>$my['uid'],
    );
    $result = $botTemp->copyBot($_dd);

    // 신규 봇을 등록자 그룹 봇에 추가
    $group = getDbData($table['s_mbrgroup'], 'uid='.$my['mygroup'],'*');
    $_bots = $group['bot'].",".$result;
    getDbUpdate($table['s_mbrgroup'], "bot='".$_bots."'", 'uid='.$my['mygroup']);

    // 인텐트 재학습
    $_dd = array("vendor"=>$_data['vendor'], "bot"=>$result);
    $chatbot->getTrainIntentPesoNLP($_dd);

}else if($linkType=='save-entity'){
    $_data = $chatbot->regisEntity($data);
    $result['entity_uid'] = $_data['entity_uid'];
    $result['entity_name'] = $_data['entity_name'];

}else if($linkType=='save-intent'){
    $_data = $chatbot->regisIntent($data);
    $result['intent_uid'] = $_data['intent_uid'];
    $result['intent_name'] = $_data['intent_name'];

}else if($linkType=='initData'){
    $getIntent = $chatbot->getIntentData($data);
    $getEntity = $chatbot->getEntityData($data);
    $result['intent'] = $getIntent['content'];
    $result['entity'] = $getEntity['content'];

}else if($linkType=='delete-entityEx'){
    $uid = $data['entityEx'];
    getDbDelete($table[$m.'entityVal'],'uid='.$uid);

    $result['entity_uid'] = $data['entity'];
    $result['entity_name'] = $data['entityName'];

}else if($linkType=='delete-intentEx'){
    $uid = $data['intentEx'];
    getDbDelete($table[$m.'intentEx'],'uid='.$uid);

    $result['intent_uid'] = $data['intent'];
    $result['intent_name'] = $data['intentName'];

}else if($linkType=='delete-entity'){
    $result['content'] = $chatbot->deleteEntity($data);

}else if($linkType=='delete-intent'){
    $result['content'] = $chatbot->deleteIntent($data);

}else if($linkType=='delete-api'){
    $api_arr = $data['api_arr'];
    $data['delType'] = 'api';
    foreach ($api_arr as $api) {
        $data['api'] = $api;
        $chatbot->deleteLegacy($data);
    }
}else if($linkType =='delete-req'){
    $data['delType'] ='req';
    $chatbot->deleteLegacy($data);

}else if($linkType=='delete-items'){
    if($data['type'] == 'intent') {
        getDbDelete($table[$m.'intentEx'], "intent in (".$data['uids'].")");
        getDbDelete($table[$m.'intent'],"uid in (".$data['uids'].")");
    } else if($data['type'] == 'entity') {
        getDbDelete($table[$m.'entityVal'],"vendor='".$data['vendor']."' and bot='".$data['bot']."' and entity in (".$data['uids'].")");
        getDbDelete($table[$m.'entity'],"vendor='".$data['vendor']."' and bot='".$data['bot']."' and uid in (".$data['uids'].")");
    }

}else if($data['linkType'] == 'learning-intent') {
    $intentCount = getDbRows($table[$m.'intentEx'], "vendor=".$vendor." and bot=".$bot." and hidden=0");
    if($intentCount > 0) {
        $result['content'] = $chatbot->getTrainIntentPesoNLP($data);
    } else {
        $result['fail'] = true;
        $result['content'] = '학습할 인텐트 데이터가 없습니다.';
    }

}else if($data['linkType'] == 'check_logout') {
    if($data['mod'] == 'out') {
        if ($_SESSION['mbr_uid']) {
            getDbUpdate($table['s_mbrdata'],'now_log=0','memberuid='.$_SESSION['mbr_uid']);
        }
        session_destroy();
    }

    $result['data'] = $data['mod'];

}else if($linkType == 'getCallbotLog'){ // 콜봇 모니터링 대화로그
    $result['content'] = $chatbot->getUserChatLog($data);

//============================================================
}else if($data['linkType'] == 'updateIntro') {
    if($data['intro_use'] && trim($data['intro_greeting'])=='') {
        $result[0] = '인트로 인사말을 입력해주세요.';
        echo json_encode($result); exit;
    }
    if($data['intro_profile']) {
        $bProfile = false;
        for($i=0, $nCnt=count($data['intro_profile_img']); $i<$nCnt; $i++) {
            if($data['intro_profile_img'][$i] || $data['intro_profile_uid'][$i]) {
                $bProfile = true;
                break;
            }
        }
        if($bProfile == false) {
            $result[0] = '프로필 이미지를 등록해주세요.';
            echo json_encode($result); exit;
        }
    }
    if($data['intro_menu']) {
        if(!is_array($data['intro_menu_name']) || count($data['intro_menu_name']) == 0 || count($data['intro_menu_url']) == 0) {
            $result[0] = '표시할 메뉴를 입력해주세요.';
            echo json_encode($result); exit;
        }
        for($i=0, $nCnt=count($data['intro_menu_name']); $i<$nCnt; $i++) {
            if(!trim($data['intro_menu_name'][$i])) {
                $result[0] = '표시할 메뉴를 입력해주세요.';
                echo json_encode($result); exit;
            }
            if(!trim($data['intro_menu_url'][$i])) {
                $result[0] = '이동할 메뉴 URL을 입력해주세요.';
                echo json_encode($result); exit;
            }
            if(!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$data['intro_menu_url'][$i])) {
                $result[0] = '올바른 URL을 입력해주세요';
                echo json_encode($result); exit;
            }
        }
    }
    if($data['intro_logo']) {
        if(!trim($data['intro_logo_url']) && !trim($data['intro_logo_url_uid'])) {
            $result[0] = '로고 이미지를 등록해주세요.';
            echo json_encode($result); exit;
        }
    }

    $tbl = $table[$m.'botSettings'];

    $_data = array();
    $_data['data']['vendor'] = $vendor;
    $_data['data']['bot'] = $bot;

    $_data['nameArray'] = array();
    $_data['nameArray']['intro_use'] = $data['intro_use'];
    $chatbot->updateBotSettings($_data);

    $_data['nameArray'] = array();
    $_data['nameArray']['intro_greeting'] = $data['intro_greeting'];
    $chatbot->updateBotSettings($_data);

    $_data['nameArray'] = array();
    $_data['nameArray']['intro_sub_greeting'] = $data['intro_sub_greeting'];
    $chatbot->updateBotSettings($_data);

    $_data['nameArray'] = array();
    $_data['nameArray']['intro_profile'] = $data['intro_profile'];
    $chatbot->updateBotSettings($_data);

    $_data['nameArray'] = array();
    $_data['nameArray']['intro_menu'] = $data['intro_menu'];
    $chatbot->updateBotSettings($_data);

    $_data['nameArray'] = array();
    $_data['nameArray']['intro_logo'] = $data['intro_logo'];
    $chatbot->updateBotSettings($_data);

    $_data['nameArray'] = array();
    $_data['nameArray']['intro_logo_url'] = $data['intro_logo_url'];
    if($data['intro_logo_url']) {
        $_info = getDbData($tbl, "vendor='".$data['vendor']."' and bot='".$data['bot']."' and name='intro_logo_url'", 'value');
        if($_info['value']) {
            $data['file_url'] = $_info['value'];
            $chatbot->deleteBotFile($data);
        }
        $data['file_url'] = $data['intro_logo_url'];
        $uploadFile = $chatbot->setFileTempToSave($data);
        $_data['nameArray']['intro_logo_url'] = $uploadFile;
        $chatbot->updateBotSettings($_data);
    }

    $_data['nameArray'] = array();
    $_data['nameArray']['intro_channel'] = $data['intro_channel'];
    $chatbot->updateBotSettings($_data);

    if($data['intro_profile']) {
        for($i=0, $nCnt=count($data['intro_profile_img']); $i<$nCnt; $i++) {
            if(!$data['intro_profile_img'][$i]) continue;
            if($data['intro_profile_uid'][$i]) {
                $_info = getDbData($tbl, 'uid='.$data['intro_profile_uid'][$i], 'value');
                if($_info['value']) {
                    $data['file_url'] = $_info['value'];
                    $chatbot->deleteBotFile($data);
                }

                $data['file_url'] = $data['intro_profile_img'][$i];
                $uploadFile = $chatbot->setFileTempToSave($data);

                getDbUpdate($tbl, "value='".$uploadFile."'",'uid='.$data['intro_profile_uid'][$i]);
            } else {
                if($data['intro_profile_img'][$i]) {
                    $data['file_url'] = $data['intro_profile_img'][$i];
                    $uploadFile = $chatbot->setFileTempToSave($data);

                    $QKEY = "vendor,bot,name,value";
                    $QVAL ="'".$vendor."','".$bot."','intro_profile_img','".$uploadFile."'";
                    getDbInsert($tbl,$QKEY,$QVAL);
                }
            }
        }
    }
    if($data['intro_menu']) {
        for($i=0, $nCnt=count($data['intro_menu_name']); $i<$nCnt; $i++) {
            $menuValue = trim($data['intro_menu_name'][$i]).'|'.trim($data['intro_menu_url'][$i]);
            if($data['intro_menu_uid'][$i]) {
                getDbUpdate($tbl, "value='".$menuValue."'",'uid='.$data['intro_menu_uid'][$i]);
            } else {
                $QKEY = "vendor,bot,name,value";
                $QVAL ="'".$vendor."','".$bot."','intro_menu_name','".$menuValue."'";
                getDbInsert($tbl,$QKEY,$QVAL);
            }
        }
    }

    $result[0] = '100';

}else if($data['linkType'] == 'profile_delete') {
    $tbl = $table[$m.'botSettings'];

    if($data['uid']) {
        $R = getDbData($tbl,'uid='.$uid,'*');
        $data['file_url'] = $R['value'];
        $chatbot->deleteBotFile($data);
        getDbDelete($tbl,'uid='.$uid);
    }
    $result[0] = '100';

}else if($data['linkType'] == 'intro_menu_delete') {
    $tbl = $table[$m.'botSettings'];

    if($data['uid']) {
        getDbDelete($table[$m.'botSettings'],'uid='.$uid);
        $result[0] = '100';
    } else {
        $result[0] = '잘못된 접근입니다.';
    }

}else if($data['linkType'] == 'chat_TSTest') {
    include_once $g['dir_module'] . "lib/phpExcel/PHPExcel.php";
    include_once $g['dir_module'] . "lib/phpExcel/PHPExcel/IOFactory.php";

    $data['file'] = $_FILES['file'];
    $rows = $chatbot->importDataByFile($data); // 엑셀 rows

    $B = getDbData($table[$m.'bot'], "uid = ".$data['bot'], "*");
    $data['botId'] = $B['id'];

    // botId 로 관련 데이타 추출
    $B = $chatbot->getBotDataFromId($data['botId']);

    $dt = array();
    $dt['cmod'] = $chatbot->cmod = 'TS';
    $dt['vendor'] = $chatbot->vendor = $B['vendor'];
    $dt['bot'] = $chatbot->botuid = $data['bot'];
    $dt['botid'] = $chatbot->botid = $data['botId'];
    $dt['dialog'] = $chatbot->dialog = $B['dialog'];
    $dt['botUid'] = $data['bot'];
    $dt['botId'] = $data['botId'];
    $dt['roomToken'] = $data['roomToken'];
    $dt['msg_type'] = 'text';
    $dt['api'] = true;

    $start_i = 0;

    $html = "";

    $chExcelTitle = "대화테스트";
    $objPHPExcel = new PHPExcel();
    $chTemplateFile = $g['dir_module'] . 'lib/tp_chatTSTest.xlsx';
    $objReader = PHPExcel_IOFactory::createReaderForFile($chTemplateFile);
    $objPHPExcel = $objReader->load($chTemplateFile);

    $objPHPExcel->getProperties()->setCreator("persona")
                                ->setLastModifiedBy("persona")
                                ->setTitle($chExcelTitle)
                                ->setSubject($chExcelTitle);

    $sheetIndex = $objPHPExcel->setActiveSheetIndex(0);
    $nIndex = 2;

    for($i = $start_i, $nCnt=count($rows); $i < $nCnt; $i++) {
        $user_input = trim($rows[$i][0]);
        $dt['clean_input'] = $chatbot->verifyUserInput($user_input);
        if(!$dt['clean_input']) continue;

        $reply = $chatbot->ProcessInput($dt);
        $aResItem = $aResponse = array();

        if(!is_array($reply['response'])) {
            $aResItem[] = array('type'=>'text', 'content'=>$reply['response']);
        } else {
            foreach ($reply['response'] as $resGroup) {
                if(!is_array($resGroup[0])) {
                    $resType = $reply['faqRes'] ? 'faq' : $resGroup[0];
                    $content = $resGroup[1];
                    $aResItem[] = array('type'=>$resType, 'content'=>$content);
                } else {
                    foreach($resGroup as $resItem){
                        if(!$resItem[0]) continue;

                        $resType = $resItem[0];
                        $content = $resItem[1];
                        $if_gid = $resGroup['if_gid'];
                        $aResItem[] = array('type'=>$resType, 'content'=>$content, 'if_gid'=>$if_gid);
                    }
                }
            }
        }

        foreach ($aResItem as $resItem) {
            $aTemp = array();
            $resType = $resItem['type'];
            $content = $resItem['content'];
            $if_gid = $resItem['if_gid'] ? $resItem['if_gid'] : "";

            $aTemp['type'] = $resItem['type'];

            if($resType == 'faq') {
                $aTemp['typeName'] = "FAQ";
                $aTemp['content'][] = stripslashes($content);
                $reply['intentScore'] = $reply['faqScore'];
            }else if($resType =='text') {
                $aTemp['typeName'] = "텍스트";
                $aTemp['content'][] = stripslashes($content);
            }else if($resType=='img'){
                $aTemp['typeName'] = "이미지";
                foreach ($content as $imgItem) {
                    $aImg = json_decode($imgItem, true);
                    $aTemp['content'][] = stripslashes($aImg['img_url']); //substr($img_url, (strrpos($img_url, "/")+1));
                }
            }else if($resType=='hMenu'){
                $aTemp['typeName'] = "버튼";
                foreach ($content as $btnItem) {
                    $aBtn = json_decode($btnItem, true);
                    $aTemp['content'][] = $aBtn['title'];
                }
            }else if($resType=='card'){
                $aTemp['typeName'] = "카드";
                foreach ($content as $cardItem) {
                    $aCard = json_decode($cardItem, true);
                    $aTemp['content'][] = $aCard['title'];
                }
            } else if($resType =='if'){
                $aTemp['typeName'] = "조건(".$if_gid.")";
                foreach($content as $ifGroup){
                    foreach ($ifGroup as $ifItem) {
                        $ifResType = $ifItem['type'];
                        $ifcontent = $ifItem['content'];
                        $aTemp['content'][] = stripslashes($ifcontent);
                    }
                }
            }
            $aResponse[] = $aTemp;
        }

        $rowspan = count($aResponse) > 1 ? "rowspan='".count($aResponse)."'" : "";

        if(count($aResponse) <= 1) {
            $resTypeName = $aResponse[0]['typeName'];
            $resContent = "";
            $imgFileName = "";
            foreach($aResponse[0]['content'] as $resVal) {
                if($aResponse[0]['type'] == "img") {
                    $resContent .="<span class='test_resimg' style='background-image:url(".$resVal.");'></span>";
                    $imgFileName .=substr($resVal, (strrpos($resVal, "/")+1)).", ";
                } else {
                    $resContent .=$resVal.", ";
                }
            }
            $resContent = rtrim($resContent, ", ");
            $imgFileName = rtrim($imgFileName, ", ");

            $html .="<tbody>";
            $html .="   <tr>";
            $html .="       <td>".($i+1)."</td>";
            $html .="       <td>".$user_input."</td>";
            $html .="       <td>".$reply['intentName']."</td>";
            $html .="       <td>".$reply['intentScore']."</td>";
            $html .="       <td>";
            $html .="           <div class='test_mop'><span class='test_mophead'>형태소</span><span class='test_mopcon'>".$reply['mopData']."</span></div>";
            $html .="           <div class='test_mop'><span class='test_mophead'>엔터티</span><span class='test_mopcon'>".$reply['entityList']."</span></div>";
            $html .="       </td>";
            $html .="       <td>".($reply['unknown'] ? "unknown" : ($reply['nodeName'] ? $reply['nodeName'] : ""))."</td>";
            $html .="       <td>".$resTypeName."</td>";
            $html .="       <td>".$resContent."</td>";
            $html .="       <td>".(count($aResponse) > 0 && $reply['nodeName'] && !$reply['unknown'] ? "●" : "-")."</td>";
            $html .="   </tr>";
            $html .="</tbody>";

            // 엑셀 출력
            $sheetIndex->setCellValue("A$nIndex", ($i+1))
                        ->setCellValue("B$nIndex", $user_input)
                        ->setCellValue("C$nIndex", $reply['intentName'])
                        ->setCellValue("D$nIndex", $reply['intentScore'])
                        ->setCellValue("E$nIndex", $reply['mopData'])
                        ->setCellValue("F$nIndex", $reply['entityList'])
                        ->setCellValue("G$nIndex", ($reply['unknown'] ? "unknown" : ($reply['nodeName'] ? $reply['nodeName'] : "")))
                        ->setCellValue("H$nIndex", $resTypeName)
                        ->setCellValue("I$nIndex", ($aResponse[0]['type'] == "img" ? $imgFileName : $resContent))
                        ->setCellValue("J$nIndex", (count($aResponse) > 0 && $reply['nodeName'] && !$reply['unknown'] ? "●" : "-"));

            $sheetIndex->getStyle("A".$nIndex.":J".$nIndex)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            $nIndex++;

        } else {
            $html .="<tbody>";
            for($j=0, $nCnt2=count($aResponse); $j<$nCnt2; $j++) {
                $resTypeName = $aResponse[$j]['typeName'];
                $resContent = "";
                $imgFileName = "";
                foreach($aResponse[$j]['content'] as $resVal) {
                    if($aResponse[$j]['type'] == "img") {
                        $resContent .="<span class='test_resimg' style='background-image:url(".$resVal.");'></span>";
                        $imgFileName .=substr($resVal, (strrpos($resVal, "/")+1)).", ";
                    } else {
                        $resContent .=$resVal.", ";
                    }
                }
                $resContent = rtrim($resContent, ", ");
                $imgFileName = rtrim($imgFileName, ", ");

                if($j == 0) {
                    $nMergeStart = $nIndex;

                    $html .="   <tr>";
                    $html .="       <td ".$rowspan.">".($i+1)."</td>";
                    $html .="       <td ".$rowspan.">".$user_input."</td>";
                    $html .="       <td ".$rowspan.">".$reply['intentName']."</td>";
                    $html .="       <td ".$rowspan.">".$reply['intentScore']."</td>";
                    $html .="       <td ".$rowspan.">";
                    $html .="           <div class='test_mop'><span class='test_mophead'>형태소</span><span class='test_mopcon'>".$reply['mopData']."</span></div>";
                    $html .="           <div class='test_mop'><span class='test_mophead'>엔터티</span><span class='test_mopcon'>".$reply['entityList']."</span></div>";
                    $html .="       </td>";
                    $html .="       <td ".$rowspan.">".($reply['unknown'] ? "unknown" : ($reply['nodeName'] ? $reply['nodeName'] : ""))."</td>";
                    $html .="       <td>".$resTypeName."</td>";
                    $html .="       <td>".$resContent."</td>";
                    $html .="       <td ".$rowspan.">".($reply['unknown'] || !$reply['nodeName'] ? "-" : "●")."</td>";
                    $html .="   </tr>";

                    $sheetIndex->setCellValue("A$nIndex", ($i+1))
                                ->setCellValue("B$nIndex", $user_input)
                                ->setCellValue("C$nIndex", $reply['intentName'])
                                ->setCellValue("D$nIndex", $reply['intentScore'])
                                ->setCellValue("E$nIndex", $reply['mopData'])
                                ->setCellValue("F$nIndex", $reply['entityList'])
                                ->setCellValue("G$nIndex", ($reply['unknown'] ? "unknown" : ($reply['nodeName'] ? $reply['nodeName'] : "")))
                                ->setCellValue("H$nIndex", $resTypeName)
                                ->setCellValue("I$nIndex", ($aResponse[$j]['type'] == "img" ? $imgFileName : $resContent))
                                ->setCellValue("J$nIndex", ($reply['unknown'] || !$reply['nodeName'] ? "-" : "●"));

                    $nIndex++;

                } else {
                    $nMergeEnd = $nIndex;

                    $html .="   <tr>";
                    $html .="       <td>".$resTypeName."</td>";
                    $html .="       <td>".$resContent."</td>";
                    $html .="   </tr>";

                    $sheetIndex->setCellValue("A$nIndex", "")
                                ->setCellValue("B$nIndex", "")
                                ->setCellValue("C$nIndex", "")
                                ->setCellValue("D$nIndex", "")
                                ->setCellValue("E$nIndex", "")
                                ->setCellValue("F$nIndex", "")
                                ->setCellValue("G$nIndex", "")
                                ->setCellValue("H$nIndex", $resTypeName)
                                ->setCellValue("I$nIndex", ($aResponse[$j]['type'] == "img" ? $imgFileName : $resContent))
                                ->setCellValue("J$nIndex", "");

                    if($j == (count($aResponse)-1)) {
                        $sheetIndex->getStyle("A".$nIndex.":J".$nIndex)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                        $sheetIndex->mergeCells('A'.$nMergeStart.':A'.$nMergeEnd);
                        $sheetIndex->mergeCells('B'.$nMergeStart.':B'.$nMergeEnd);
                        $sheetIndex->mergeCells('C'.$nMergeStart.':C'.$nMergeEnd);
                        $sheetIndex->mergeCells('D'.$nMergeStart.':D'.$nMergeEnd);
                        $sheetIndex->mergeCells('E'.$nMergeStart.':E'.$nMergeEnd);
                        $sheetIndex->mergeCells('F'.$nMergeStart.':F'.$nMergeEnd);
                        $sheetIndex->mergeCells('G'.$nMergeStart.':G'.$nMergeEnd);
                        $sheetIndex->mergeCells('J'.$nMergeStart.':J'.$nMergeEnd);
                    }

                    $nIndex++;
                }
            }
            $html .="</tbody>";
        }
    }

    $sheetIndex->setTitle($chExcelTitle);
    $objPHPExcel->setActiveSheetIndex(0);
    $chFileName = 'chat_test_'.$my['uid'].'_'.$data['bot'].'.xlsx';
    $chFilePath = $g['path_tmp'].'out/'.$chFileName;

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save($chFilePath);

    $result['html'] = $html;
    $result['excel'] = $chFileName;

}else if($linkType == 'bot_order') {
    $role = $data['role'];
    $aUid = explode(",", $data['orders']);
    foreach($aUid as $idx=>$uid) {
        getDbUpdate($table[$m.'bot'], "nrank='".$idx."'", "role='".$role."' and uid='".$uid."'");
    }
    $result[0] = '100';
}else if($linkType == 'get-randString') {
    $_data = array();
    $_data['type'] = $data['typeName'];
    $_data['src'] = $data['srcName'];
    $_data['len'] = $data['len'];
    $ranString = $chatbot->getRandomString($_data);
    $result['typeName'] = $data['typeName'];
    $result['content'] = $ranString;

}else if($linkType == 'set_humanMod') {
    if($data['bottype'] == "call") {
        getDbUpdate($table[$m.'token'], "humanMod='".$data['humanMod']."'", "bot='".$bot."' and access_mod='callInput' and roomToken='".$data['roomToken']."'");
    }
}

echo json_encode($result);
exit;
?>
