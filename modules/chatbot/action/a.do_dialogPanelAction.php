<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
require_once $g['dir_module'].'var/define.path.php';

if($_SESSION['mbr_uid'] == '') {
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

if(isset($data['node'])){
    $node = $data['node'];
    $nodeName = $data['nodeName'];
    $nodeParent = $data['nodeParent'];
}

// linkType 분기
if($data['linkType']=='save-graph'){
    $result['content'] = $chatbot->regisDialog($data);

}else if($data['linkType']=='getIntent'){
    $data['vendorOnly'] = true;
	$_data = $chatbot->getIntentData($data);
	$result['content'] = $_data['content'];
	$result['query'] = $_data['query'];

}else if($data['linkType']=='getEntity'){
	$data['vendorOnly'] = true;
    $_data = $chatbot->getEntityData($data);
	$result['content'] = $_data['content'];
	$result['query'] = $_data['query'];

}else if($data['linkType']=='initData'){
	$getIntent = $chatbot->getIntentData($data);
	$getEntity = $chatbot->getEntityData($data);
	$getContext = $chatbot->getContextData($data);

	$data['vendorOnly'] = true;
	$getVendorIntent = $chatbot->getIntentData($data); // vendor 전용 intent 만 추출
	$getVendorEntity = $chatbot->getEntityData($data); // vendor 전용 entity 만 추출
	$getNodeList = $chatbot->getNodeList($data);

	// api 리스트 추출
	$apiData = $data;
	$apiData['act'] = 'getApiListData';
	$getApiList = $chatbot->controlLegacyApiData($apiData);
	$result['intent'] = $getIntent['content'];
	$result['entity'] = $getEntity['content'];
	$result['context'] = $getContext['content'];
	$result['vendorIntent'] = $getVendorIntent['content'];
	$result['vendorEntity'] = $getVendorEntity['content'];
	$result['nodeList'] = $getNodeList['data']; // array("id"=>"id","name"=>"name");
	$result['apiList'] = $getApiList; // array("uid"=>"uid","name"=>"name");
	$result['multiMenuHeaderData'] = $chatbot->getMultiMenuHeaderData($data);

	// topic tab
	$result['topic_tab'] = $chatbot->getDialogTemplate('dialog/topic_tab');
	$result['topic_defaultTab'] = $chatbot->getDialogTemplate('dialog/topic_defaultTab');

	// 해당 챗봇 토픽 데이타
	$result['topicData'] = $chatbot->controlTopic($data);

	// html 양식
	$getHFormList = $chatbot->getHtmlFormList($data);
	$result['hformList'] = $getHFormList['data']; // array("id"=>"id","name"=>"name");

}else if($data['linkType']=='getTemplate'){
    // Dialog inputFilter template
    $result['inputFilter_inputBox'] = $chatbot->getDialogTemplate('dialog/inputFilter_inputBox');
	$result['inputFilter_addBtn'] = $chatbot->getDialogTemplate('dialog/inputFilter_addBtn');
	$result['inputFilter_deleteBtn'] = $chatbot->getDialogTemplate('dialog/inputFilter_deleteBtn');
	$result['inputFilter_andOr'] = $chatbot->getDialogTemplate('dialog/inputFilter_andOr');
	$result['if_inputFilter_inputBox'] = $chatbot->getDialogTemplate('dialog/if_inputFilter_inputBox');
	$result['if_inputFilter_addBtn'] = $chatbot->getDialogTemplate('dialog/if_inputFilter_addBtn');
	$result['if_inputFilter_deleteBtn'] = $chatbot->getDialogTemplate('dialog/if_inputFilter_deleteBtn');
	$result['if_inputFilter_andOr'] = $chatbot->getDialogTemplate('dialog/if_inputFilter_andOr');
    // Dialog context html
    $result['nodeContext_row'] = $chatbot->getDialogTemplate('dialog/contextForm_row');
    $result['contextRow_addBtn'] = $chatbot->getDialogTemplate('dialog/contextRow_addBtn');
    $result['multiMenuContext_row'] = $chatbot->getDialogTemplate('dialog/multiMenuContextForm_row');
    $result['multiMenuContextRow_addBtn'] = $chatbot->getDialogTemplate('dialog/multiMenuContextRow_addBtn');

    // Dialog form html
    $result['multiMenuForm_row'] = $chatbot->getDialogTemplate('dialog/multiMenuForm_row');

    // dialog apiResContext html
    $result['apiResContext_row'] = $chatbot->getDialogTemplate('dialog/apiResContextForm_row');
    $result['apiResContextRow_addBtn'] = $chatbot->getDialogTemplate('dialog/apiResContextRow_addBtn');

	// Dialog respond template
	$result['respond_cti_action'] = $chatbot->getDialogTemplate('dialog/ctiAction_checkBox');
	$result['respond_menuItem_resGroup'] = $chatbot->getDialogTemplate('dialog/respond_menuItem_resGroup');
	$result['respond_menuItem_resHeader'] = $chatbot->getDialogTemplate('dialog/respond_menuItem_resHeader');
	$result['respond_header'] = $chatbot->getDialogTemplate('dialog/respond_header');
	$result['respond_header_menuIcon'] = $chatbot->getDialogTemplate('dialog/respond_header_menuIcon');
	$result['respond_text_label'] = $chatbot->getDialogTemplate('dialog/respond_text_label');
	$result['respond_text_body'] = $chatbot->getDialogTemplate('dialog/respond_text_body');
    $result['respond_text_item'] = $chatbot->getDialogTemplate('dialog/respond_text_item');
	$result['respond_card_label'] = $chatbot->getDialogTemplate('dialog/respond_card_label');
	$result['respond_card_body'] = $chatbot->getDialogTemplate('dialog/respond_card_body');
	$result['respond_card_item'] = $chatbot->getDialogTemplate('dialog/respond_card_item');
	$result['respond_img_label'] = $chatbot->getDialogTemplate('dialog/respond_img_label');
	$result['respond_img_body'] = $chatbot->getDialogTemplate('dialog/respond_img_body');
	$result['respond_img_item'] = $chatbot->getDialogTemplate('dialog/respond_img_item');
	$result['respond_vMenu_label'] = $chatbot->getDialogTemplate('dialog/respond_vMenu_label');
	$result['respond_vMenu_body'] = $chatbot->getDialogTemplate('dialog/respond_vMenu_body');
	$result['respond_hMenu_label'] = $chatbot->getDialogTemplate('dialog/respond_hMenu_label');
	$result['respond_hMenu_body'] = $chatbot->getDialogTemplate('dialog/respond_hMenu_body');
	$result['respond_hMenu_item'] = $chatbot->getDialogTemplate('dialog/respond_hMenu_item');
	$result['respond_if_label'] = $chatbot->getDialogTemplate('dialog/respond_if_label');
	$result['respond_if_body'] = $chatbot->getDialogTemplate('dialog/respond_if_body');
	$result['respond_if_item'] = $chatbot->getDialogTemplate('dialog/respond_if_item');
	$result['respond_map_label'] = $chatbot->getDialogTemplate('dialog/respond_map_label');
	$result['respond_map_body'] = $chatbot->getDialogTemplate('dialog/respond_map_body');
	$result['respond_qrcode_label'] = $chatbot->getDialogTemplate('dialog/respond_qrcode_label');
	$result['respond_qrcode_body'] = $chatbot->getDialogTemplate('dialog/respond_qrcode_body');
	$result['tts_speed_checkbox'] = $chatbot->getDialogTemplate('dialog/tts_speed_checkbox');
	$result['link_checkBox'] = $chatbot->getDialogTemplate('dialog/link_checkBox');

	// legacy req list option
	$result['legacy_reqOption'] = $chatbot->getDialogTemplate('dialog/legacy_reqOption');

	// intent Ex
	$result['intentEx_list'] = $chatbot->getDialogTemplate('dialog/intentEx_list');
	$result['intentEx_row'] = $chatbot->getDialogTemplate('dialog/intentEx_row');
	$result['entityEx_list'] = $chatbot->getDialogTemplate('dialog/entityEx_list');
	$result['entityEx_row'] = $chatbot->getDialogTemplate('dialog/entityEx_row');
	$result['no_data'] = $chatbot->getDialogTemplate('dialog/no_data');

	// api
	$result['apiParamInput_row'] = $chatbot->getDialogTemplate('vendor/apiParamInput_row');

    // topic tab
	$result['topic_tab'] = $chatbot->getDialogTemplate('dialog/topic_tab');
	$result['topic_defaultTab'] = $chatbot->getDialogTemplate('dialog/topic_defaultTab');

	// 해당 챗봇 토픽 데이타
	$result['topicData'] = $chatbot->controlTopic($data);

}else if($data['linkType']=='getNodeData'){

    $data['tmod'] = true;
    $result = $chatbot->getNodeData($data); // node 데이타를 배열로 리턴

}else if($data['linkType']=='delete-resGroup'){
	$resGroup = $data['resGroup'];
	$_wh = 'vendor='.$vendor.' and bot='.$bot.' and dialog='.$dialog.' and node='.$node;
	$Group_wh = $_wh." and id='".$resGroup."'";
	$Item_wh = $_wh." and resGroupId='".$resGroup."'";
	getDbDelete($table[$m.'dialogResGroup'],$Group_wh);

	$RCD = getDbSelect($table[$m.'dialogResItem'],$Item_wh,'*');
	while($R = db_fetch_array($RCD)){
	    getDbDelete($table[$m.'dialogResItem'],'uid='.$R['uid']);
	    getDbDelete($table[$m.'dialogResItemOC'],'item='.$R['uid']);
	}

	$result['content'] = $resGroup;

}else if($data['linkType']=='show-resGroup'||$data['linkType']=='hide-resGroup'){

    if($data['linkType']=='show-resGroup'){
       getDbUpdate($table[$m.'dialogResGroup'],"hidden=0",'uid='.$data['group_uid']);
       $result['content'] ='노출 처리되었습니다.';
    }else{
       getDbUpdate($table[$m.'dialogResGroup'],"hidden=1",'uid='.$data['group_uid']);
       $result['content'] ='숨김 처리되었습니다.';
    }

}else if($data['linkType']=='uploadImg'){
	$data['file'] = $_FILES['file'];
    $result = $chatbot->uploadFile($data);

}else if($data['linkType']=='saveNode'){
	$result = $chatbot->saveNodeData($data);

}else if($data['linkType']=='saveNodeConfig'){
	$result = $chatbot->saveNodeConfigData($data);

}else if($data['linkType']=='addNode'){
   $last_id = $chatbot->regisNode($data);
   $result['content'] = $last_id;

}else if($data['linkType']=='deleteNode'){
   $result['content'] = $chatbot->deleteDialogNode($data);

}else if($data['linkType']=='getIntentEx'){
   $result['content'] = $chatbot->getIntentEx($data);

}else if($data['linkType']=='save-intent'){
    $_data = $chatbot->regisIntent($data);
    $result['intent_uid'] = $_data['intent_uid'];
    $result['intent_name'] = $_data['intent_name'];

}else if($data['linkType']=='delete-intentEx'){
    $uid = $data['intentEx'];
    getDbDelete($table[$m.'intentEx'],'uid='.$uid);

    $result['intent_uid'] = $data['intent'];
    $result['intent_name'] = $data['intentName'];

}else if($data['linkType']=='delete-intent'){
	$result['content'] = $chatbot->deleteIntent($data);

}else if($data['linkType']=='getEntityEx'){
   $result['content'] = $chatbot->getEntityEx($data);

}else if($data['linkType']=='save-entity'){
    $_data = $chatbot->regisEntity($data);
    $result['entity_uid'] = $_data['entity_uid'];
    $result['entity_name'] = $_data['entity_name'];

}else if($data['linkType']=='delete-entityEx'){
    $uid = $data['entityEx'];
    getDbDelete($table[$m.'entityVal'],'uid='.$uid);

    $result['entity_uid'] = $data['entity'];
    $result['entity_name'] = $data['entityName'];

}else if($data['linkType']=='delete-entity'){
	$result['content'] = $chatbot->deleteEntity($data);
}else if($data['linkType']=='get-recommendData'){
	$_data = array();
	$_data['str'] = $data['keyword'];
	$_data['type'] = $data['type'];
	$search_result = $chatbot->getNaverSearch($_data);

    if($_data['type']=='entity'){
    	$list = '';
	    $word = array();

	    // 대한항공 임시 작업
	    $koair_file = file_get_contents($g['dir_module'].'lib/nlp/tests/koair_keyword.txt');
        $koair_line = explode("\n",$koair_file);
	    $ko_list='';
	    foreach ($koair_line as $entity) {
	      	$ko_list.='
		   <a href="#" class="list-group-item list-group-item-action">'.$entity.'</a>';
	    }

	    foreach ($search_result as $entity=>$count) {
		   $word [] = $entity;
		}

	    // 리스트는 counting 기준 sort
	    arsort($search_result);

		foreach ($search_result as $entity=>$count) {
		   $list.='
		   <a href="#" class="list-group-item list-group-item-action">'.$entity.'</a>';
		}

        $result['list'] ='';
        // 대한항공 임시 작업
		if($data['keyword']=='대한항공'||$data['keyword']=='대한 항공'){
            $result['list'] .= $ko_list;
		}
		$result['list'] .= $list;
		$result['word'] = $word;
    }else if($_data['type']=='intent'){
    	$list = '';
	    $word = array();
	    foreach ($search_result as $intent=>$count) {
		   $word [] = $intent;
		}

	    // 리스트는 counting 기준 sort
	    arsort($search_result);

		foreach ($search_result as $intent=>$count) {
		   $list.='
		   <a href="#" class="list-group-item list-group-item-action">'.$intent.'</a>';
		}
		$result['list'] = $list;
		$result['word'] = $word;
    }

}else if($data['linkType']=='saveEntityData'){
	$chatbot->saveEntityData($data);

}else if($data['linkType']=='atwho-intent'){
    // print_r($data);
    // exit;
}else if($data['linkType']=='getDataTable'){
	$get_intentData = $chatbot->getIntentData($data);
    $intentData = $get_intentData['content'];
    $intentQty = count($intentData);
    $getTable = $chatbot->getEIdataTable($data);
    $result['width'] = 265+(140*(int)$intentQty)+50; // 261 : 구분 넓이, 140 은 각 tr/td 넓이
    $result['content'] = $getTable['defaultTable'];
    $result['entityTable'] = $getTable['entityTable'];

}else if($data['linkType']=='delete-resItem'){
    if(!$data['uid'] && $data['fileName']) {
        $chatbot->deleteBotFile($data['fileName']);
        $fileName = substr(strrchr($data['fileName'],'/'), 1);
        getDbDelete($table[$m.'upload'],"vendor=".$vendor." and bot=".$bot." and tmpname='".$fileName."'");
    }
}else if($data['linkType']=='delete-ifResItem'){
	$result['content'] = $chatbot->deleteResItem($data);

}else if($data['linkType'] == 'get-legacyApiParam'){
	$data['mod'] = 'dialog'; // dialog 모드인 경우 dialogResApiParam 테이블 값이 있는지 체크
    $data['act'] = 'get';
    $result = $chatbot->controlLegacyApiData($data);

}else if($data['linkType'] == 'save-dialogResApiParamOutput'){
	$data['mod'] = 'dialog'; // dialog 모드인 경우 dialogResApiParam 테이블에 저장
    $data['act'] = 'save';
    $result = $chatbot->controlLegacyApiData($data);

}else if($data['linkType'] == 'test-legacyApiParam'){
    $data['mod'] = 'dialog'; // dialog 모드인 경우 무조건 apiReq, apiReqParam 테이블 데이터로 테스트 전송
    $data['act'] = 'test';
    $AR = $chatbot->controlLegacyApiData($data);
    $result['statusCode'] = $AR['statusCode'];
    $result['content'] = json_decode($AR['body'],true);

}else if($data['linkType'] == 'get-dialogAllContext'){
	$result['content'] = $chatbot->getContextData($data);

}else if($linkType=='addTempData' || $linkType=='delTempData' || $linkType=='editTempData'){
    $result['content'] = $chatbot->controlTempData($data);

}else if($linkType =='update-topicOrder' || $linkType =='update-topicName' || $linkType=='add-topic' || $linkType =='delete-topic'){

	$data['mbruid'] = $_SESSION['mbr_uid'];
	$data['act'] = $data['linkType'];
	$result['content'] = $chatbot->controlTopic($data);

	// topic tab
	$result['topic_tab'] = $chatbot->getDialogTemplate('dialog/topic_tab');
	$result['topic_defaultTab'] = $chatbot->getDialogTemplate('dialog/topic_defaultTab');

	// 토픽 데이타
	$data['act'] = '';
	$result['topicData'] = $chatbot->controlTopic($data);

}else if($data['linkType']=='show-topic'||$data['linkType']=='hide-topic'){

    if($data['linkType']=='show-topic'){
       getDbUpdate($table[$m.'dialog'],"active=1",'uid='.$data['dialog']);
    }else{
       getDbUpdate($table[$m.'dialog'],"active=0",'uid='.$data['dialog']);
    }

}else if($data['linkType'] == 'get-searchIE') {
    $keyword = $data['keyword'];
    $tbl = 'rb_chatbot_'.$data['type'];

    $_wh = "hidden=0 and vendor='".$data['vendor']."' and bot='".$data['bot']."' ";
    if($data['type'] == 'entity') $_wh .="and type='V' ";
    if($keyword) $_wh .="and name like '%".$keyword."%'";

    $result= array();
    $RCD = getDbArray($tbl,$_wh,'uid,type,name','uid','asc',0,1);
    while($R = db_fetch_array($RCD)){
        $result[] = array("uid"=>$R['uid'],"type"=>$R['type'],"name"=>$R['name']);
    }

}else if($data['linkType'] == 'learning-intent') {
    $intentCount = getDbRows($table[$m.'intentEx'], "vendor=".$data['vendor']." and bot=".$data['bot']." and hidden=0");
    if($intentCount > 0) {
        $result['content'] = $chatbot->getTrainIntentPesoNLP($data);
    } else {
        $result['fail'] = true;
        $result['content'] = '학습할 인텐트 데이터가 없습니다.';
    }

}else if($data['linkType'] == 'getUseTopic') {
    // dev에서 유효한 토픽 검색
    $mbruid = $_SESSION['mbr_uid'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $g['front_host'].'/api/v1/use_topic/'.$mbruid);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSLVERSION,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $aResult = curl_exec($ch);
    curl_close ($ch);

    echo $aResult; exit;

}else if($data['linkType']=='do-nodeMenu'){
	require_once $g['dir_module'].'includes/nodeMenu.class.php';
    $nodeMenu = new nodeMenu();
    $result = $nodeMenu->doNodeMenu($data); // 최종적으로 $dialogNodeJson 값 리턴

} else if($data['linkType']=='graphExport'){
    function getNodeOrder($nodes, $pos=0) {
	    global $aNodes;
    	foreach($nodes as $idx=>$node) {
    	    if(!$node['dialogNodeUid']) continue;
    	    $children = null;
    	    $node['pos'] = $node['depth'] <= 1 ? ($pos+1) : $pos.'-'.($idx+1);
    	    if(isset($node['children']) && count($node['children']) > 0) {
    	        $children = $node['children'];
    	        unset($node['children']);
    	    }
    	    $aNodes[] = $node;
    	    if($node['depth'] <= 1) $pos++;
    	    if(count($children) > 0) getNodeOrder($children, $node['pos']);
    	}
    	return $aNodes;
    }
    function getExcelWrite($nIndex, $result) {
        global $sheetIndex;

        $sheetIndex->setCellValue("A".$nIndex, $result['nodePos']);
	    $sheetIndex->setCellValue("B".$nIndex, $result['nodeName']);
	    $sheetIndex->setCellValue("C".$nIndex, $result['nodeInput']);
	    $sheetIndex->setCellValue("D".$nIndex, $result['nodeAction']);
	    $sheetIndex->setCellValue("E".$nIndex, $result['nodeJumpName']);
	    $sheetIndex->setCellValue("F".$nIndex, $result['nodeGroupType']);
	    $sheetIndex->setCellValue("G".$nIndex, $result['nodeGroupView']);
	    $sheetIndex->setCellValue("H".$nIndex, $result['nodeItemContent']);
        $sheetIndex->setCellValue("I".$nIndex, $result['nodeItemInput']);
        $sheetIndex->setCellValue("J".$nIndex, $result['nodeItemTitle']);
        $sheetIndex->setCellValue("K".$nIndex, $result['nodeOCType']);
        $sheetIndex->setCellValue("L".$nIndex, $result['nodeOCContent']);
    }
    function getExcelMerge($nStart, $nEnd) {
        global $sheetIndex;

        $sheetIndex->mergeCells('A'.$nStart.':A'.$nEnd);
        $sheetIndex->mergeCells('B'.$nStart.':B'.$nEnd);
        $sheetIndex->mergeCells('C'.$nStart.':C'.$nEnd);
        $sheetIndex->mergeCells('D'.$nStart.':D'.$nEnd);
        $sheetIndex->mergeCells('E'.$nStart.':E'.$nEnd);
    }

    // 엑셀 초기설정
    include_once $g['dir_module'] . "lib/phpExcel/PHPExcel.php";
    include_once $g['dir_module'] . "lib/phpExcel/PHPExcel/IOFactory.php";

    $B = getDbData($table[$m.'bot'], "uid=".$data['bot'], 'name');

    $chExcelTitle = $B['name']."_대화그래프";
    $objPHPExcel = new PHPExcel();
    $chTemplateFile = $g['dir_module'] . 'lib/tp_graphData.xlsx';
    $objReader = PHPExcel_IOFactory::createReaderForFile($chTemplateFile);
    $objPHPExcel = $objReader->load($chTemplateFile);

    $objPHPExcel->getProperties()->setCreator("persona")
                                ->setLastModifiedBy("persona")
                                ->setTitle($chExcelTitle)
                                ->setSubject($chExcelTitle);

    $sheetIndex = $objPHPExcel->setActiveSheetIndex(0);
    $sheetIndex->getStyle('A:L')->getAlignment()->applyFromArray(
        array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true)
    );

    $nIndex = 2;
    $aResType = array(
        'text'=>'텍스트', 'hMenu'=>'버튼', 'card'=>'카드', 'img'=>'이미지', 'if'=>'조건', 'link'=>'링크', 'node'=>'대화상자', 'tel'=>'전화번호', 'api'=>'API', 'context'=>'컨텍스트', 'form'=>'Form'
    );

    // tree 배열 조회
    $aNodes = array();
	$dialogNode = $chatbot->getNodeTreeJson($data, false);
    $aNodes = getNodeOrder($dialogNode, 0);

    $i = 0;
    foreach($aNodes as $key=>$node) {
        $result = array();

        $nMergeStart = $nIndex;

        $result['nodePos'] = $node['pos'];
	    $result['nodeName'] = $node['name'];

        // 노드 기본 정보 및 인풋
	    $_table = $table[$m.'dialogNode']." A left join ".$table[$m.'dialogNode']." B on A.bot = B.bot and A.jumpTo_node = B.id and B.dialog = '".$data['dialog']."' ";
	    $aNodeData = getDbData($_table, "A.uid=".$node['dialogNodeUid'], 'A.*, B.name as jumpNode');
	    $result['nodeInput'] = '';
	    if(trim($aNodeData['recCondition'])) {
    	    $aInput = explode(',', $aNodeData['recCondition']);
    	    foreach($aInput as $val) {
    	        if(!trim($val)) continue;
    	        $val = explode('|', $val);
    	        $result['nodeInput'] .='['.strtoupper(substr($val[0],0,1)).':'.$val[5].']';
    	    }
    	}
	    $result['nodeAction'] = $aNodeData['node_action'] == 1 ? '입력대기' : '대화상자 이동';
	    $result['nodeJumpName'] = $aNodeData['node_action'] == 1 ? '' : $aNodeData['jumpNode'];

	    // 토픽 연결 노드
	    if($aNodeData['use_topic']) {
	        $result['nodeGroupType'] = "토픽";
	        $result['nodeGroupView'] = "O";

	        $aTopic = getDbData($table[$m.'dialog'], "uid=".$aNodeData['use_topic'], 'name');
	        $result['nodeItemContent'] = $aTopic['name'];

	        getExcelWrite($nIndex, $result);
	        $i++; $nIndex++;
	        continue;
	    }

	    // resGroup
	    $query = "Select hidden, id, resType From ".$table[$m.'dialogResGroup']." ";
	    $query .="Where bot='".$data['bot']."' and dialog='".$data['dialog']."' and node='".$aNodeData['id']."' Order by gid ASC";
	    $rowsGroup = $chatbot->getAssoc($query);

	    if(count($rowsGroup) == 0) {
	        getExcelWrite($nIndex, $result);
	        $i++; $nIndex++;
	        continue;
	    }

	    $j = 0;
	    foreach ($rowsGroup as $group){
	        $nMergeGroupStart = $nIndex;

	        $result['nodeGroupType'] = $aResType[$group['resType']];
	        $result['nodeGroupView'] = $group['hidden'] ? 'X' : 'O';

	        $query = "Select uid, resType, title, content, img_url, recCondition From ".$table[$m.'dialogResItem']." ";
	        $query .="Where bot='".$data['bot']."' and dialog='".$data['dialog']."' and node='".$aNodeData['id']."' and resGroupId='".$group['id']."' Order by gid ASC";
	        $rowsItem = $chatbot->getAssoc($query);
	        if(count($rowsItem) == 0) {
	            getExcelWrite($nIndex, $result);

	            if($j == (count($rowsGroup)-1)) {
	                getExcelMerge($nMergeStart, $nIndex);
	            }
    	        $j++; $nIndex++;
    	        continue;
    	    }

    	    $k = 0;
    	    foreach ($rowsItem as $item){
    	        $result['nodeItemType'] = $result['nodeItemTitle'] = $result['nodeItemContent'] = $result['nodeOCType'] = $result['nodeOCContent'] = "";

    	        $result['nodeItemType'] = $aResType[$item['resType']];
    	        $result['nodeItemTitle'] = trim($item['title']);
    	        if($item['resType'] == "img") {
    	            $result['nodeItemContent'] = substr($item['img_url'], (strrpos($item['img_url'], "/")+1));
    	        } else {
    	            $result['nodeItemContent'] = stripslashes(strip_tags(trim($item['content'])));
    	        }
    	        $result['nodeItemInput'] = "";
    	        if(trim($item['recCondition'])) {
    	            $aInput = explode(',', $item['recCondition']);
    	            foreach($aInput as $val) {
                	    if(!trim($val)) continue;
                	    $val = explode('|', $val);
                	    $result['nodeItemInput'] .='['.strtoupper(substr($val[0],0,1)).':'.$val[5].']';
                	}
                }

                if($item['resType'] == "hMenu" || $item['resType'] == "if") {
                    $query = "Select A.resType, A.text_val, A.varchar_val, B.name as nodeName From ".$table[$m.'dialogResItemOC']." A ";
                    $query .="left join ".$table[$m.'dialogNode']." B on A.resType = 'node' and A.bot = B.bot and A.varchar_val = B.id and B.dialog = '".$data['dialog']."' ";
                    $query .="Where A.bot='".$data['bot']."' and A.item='".$item['uid']."' and (A.text_val <> '|||' and (A.text_val <> '' or A.varchar_val <> '')) ";
                    $query .="Order by A.gid ASC";
                    $rowsItemOC = $chatbot->getAssoc($query);
                    if(count($rowsItemOC) == 0) {
        	            getExcelWrite($nIndex, $result);

        	            if($j == (count($rowsGroup)-1) && $k == (count($rowsItem)-1)) {
        	                getExcelMerge($nMergeStart, $nIndex);
        	            }
            	        $k++; $nIndex++;
            	        continue;
            	    }

            	    $nMergeOCStart = $nIndex;
            	    $n = 0;
                    foreach ($rowsItemOC as $oc){
                        $result['nodeOCType'] = $result['nodeOCContent'] = "";

                        $result['nodeOCType'] = $aResType[$oc['resType']];
            	        if($oc['resType'] == "text" || $oc['resType'] == "context" || $oc['resType'] == "form") {
            	            $result['nodeOCContent'] = stripslashes(strip_tags(trim($oc['text_val'])));
            	        }
            	        if($oc['resType'] == "link" || $oc['resType'] == "img" || $oc['resType'] == "node" || $oc['resType'] == "tel") {
            	            if($oc['resType'] == "img") $result['nodeOCContent'] = substr($oc['varchar_val'], (strrpos($oc['varchar_val'], "/")+1));
            	            else if($oc['resType'] == "node") $result['nodeOCContent'] = $oc['nodeName'];
            	            else $result['nodeOCContent'] = $oc['varchar_val'];
            	        }

            	        getExcelWrite($nIndex, $result);

            	        if($n == (count($rowsItemOC)-1)) {
            	            $sheetIndex->mergeCells('J'.$nMergeOCStart.':J'.$nIndex);
            	        }
            	        if($k == (count($rowsItem)-1) && $n == (count($rowsItemOC)-1)) {
            	            $sheetIndex->mergeCells('F'.$nMergeGroupStart.':F'.$nIndex);
            	            $sheetIndex->mergeCells('G'.$nMergeGroupStart.':G'.$nIndex);
            	        }
            	        if($j == (count($rowsGroup)-1) && $k == (count($rowsItem)-1) && $n == (count($rowsItemOC)-1)) {
            	            getExcelMerge($nMergeStart, $nIndex);
            	            $sheetIndex->mergeCells('F'.$nMergeGroupStart.':F'.$nIndex);
            	            $sheetIndex->mergeCells('G'.$nMergeGroupStart.':G'.$nIndex);
            	        }
            	        $n++; $nIndex++;
            	    }

            	} else {
            	    getExcelWrite($nIndex, $result);

            	    if($k == (count($rowsItem)-1)) {
            	        $sheetIndex->mergeCells('F'.$nMergeGroupStart.':F'.$nIndex);
            	        $sheetIndex->mergeCells('G'.$nMergeGroupStart.':G'.$nIndex);
            	    }
            	    if($j == (count($rowsGroup)-1) && $k == (count($rowsItem)-1)) {
            	        getExcelMerge($nMergeStart, $nIndex);
        	        }
            	    $nIndex++;
                }
                $k++;
            }
            $j++;
	    }
	    $i++;
	}

	$sheetIndex->getStyle("A1:L".($nIndex-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $sheetIndex->setTitle($chExcelTitle);
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

} else if($data['linkType']=='addBargein'){
    if(!$data['itemtype'] || !$data['bargein'] || !$data['itemuid']) exit;

    $tbl = $data['itemtype'] == 'item' ? $table[$m.'dialogResItem'] : $table[$m.'dialogResItemOC'];
    $bargein = $data['bargein'] == 'true' ? 1 : 0;
    getDbUpdate($tbl, "bargein=".$bargein,'uid='.$data['itemuid']);
    $result['bargein'] = $bargein;

} else if($data['linkType'] == 'getNodeJson') {
    $nodes = $chatbot->getNodeTreeJson($data);
    echo $nodes; exit;
}

echo json_encode(mb_convert_encoding($result, 'UTF-8', 'auto'));
exit;
?>
