<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$data = $_POST;
$linkType = $data['linkType'];

// linkType 분기 
if($linkType=='getTemplate'){
    // Dialog inputFilter template
    $result['LCbot_Box'] = $chatbot->getDialogTemplate('LC/LCbot_Box');
	$result['LCbot_Block'] = $chatbot->getDialogTemplate('LC/LCbot_Block');
	$result['LCbot_entityList'] = $chatbot->getDialogTemplate('LC/LCbot_entityList');
	$result['LCbot_resRow'] = $chatbot->getDialogTemplate('LC/LCbot_resRow');

  
}else if($linkType=='initLC'){ // LC 시작전 세팅 

	$keywordArray = explode(PHP_EOL,$data['source']);
    
    $chatbot->botid = $_POST['botId'];
    $chatbot->cmod = $_POST['cmod'];// LC 

	$bot_id = $chatbot->botid;
    $botData = $chatbot->getBotDataFromId($bot_id);

	// 인텐트 학습데이타 세팅 
	$data= array();
	$data['vendor'] = $botData['vendor'];
	$data['bot'] = $botData['bot'];
	$data['dialog'] = $botData['dialog'];

	// intent & entity train data set 세팅 
	$intentTrainData = $chatbot->getIntentTrainData($data);
	$entityTrainData = $chatbot->getEntityTrainData($data);

	$result['vendor'] = $botData['vendor'];
	$result['botUid'] = $botData['bot_uid'];
	$result['bot'] = $botData['bot_uid'];
	$result['dialog'] = $botData['dialog'];
	$result['intentTrainData'] = $intentTrainData;
	$result['entityTrainData'] = $entityTrainData;
	$result['keywordArray'] = $keywordArray;
	$result['exNum'] = $_POST['exNum'];
	$result['cmod'] = $_POST['cmod'];


}else if($linkType=='testBot'){
	
	$botid = $_POST['botId'];
	$msg_type = "text";
	$cmod = $_POST['cmod'];
	
	$chatbot->vendor = $_POST['vendor'];
	$chatbot->botuid = $_POST['bot'];
	$chatbot->dialog = $_POST['dialog'];
	$chatbot->botid = $botid;
	$chatbot->cmod = $cmod;
    
    $dt = array();  
	$dt['vendor'] = $_POST['vendor'];
	$dt['botUid'] = $_POST['bot'];
	$dt['dialog'] = $_POST['dialog'];
	$dt['bot'] = $_POST['bot'];
	$dt['botId'] = $botid;
	$dt['msg_type'] = $msg_type;   
	$dt['cmod'] = $cmod;
	$dt['exNum'] = $_POST['exNum'];
	$dt['order'] = $_POST['order'];
	$dt['keyword'] = $_POST['keyword'];
	$dt['intentTrainData'] = $_POST['intentTrainData'];//json_encode($chatbot->getIntentTrainData($dt),JSON_UNESCAPED_UNICODE);
	$dt['entityTrainData'] = $_POST['entityTrainData'];//json_encode($chatbot->getEntityTrainData($dt),JSON_UNESCAPED_UNICODE);
    $dt['api'] = true;
    
    $_data = array();
    $_data['str'] = $dt['keyword'];
	$_data['type'] = $cmod;
	$_data['qty'] = $dt['exNum'];
	$search_result = $chatbot->getNaverSearchLC($_data);
    // $search_result = array(
    //     "줌팁이 뭔가요?",
    //     "납부안내",
    //     "회사위치",
    //     "회사 연락처"
    // ); 

	foreach ($search_result as $opense_text) {
	    $dt['clean_input'] = $opense_text;
	    $result['data']['chat'][] = $chatbot->ProcessInput($dt);
	}
	$result['data']['keyword'] = $dt['keyword'];
	$result['data']['order'] = $dt['order'];   

	// print_r($result);
	// exit; 

}else if($linkType=='downLoadFail'){
    
    $dataArray = explode(',',$data['failData']);

	header( "Content-type: application/vnd.ms-excel;" ); 
    header( "Content-Disposition: attachment; filename=답변실패 문장_".$date['today'].".xls" ); 
    header( "Content-Description: PHP4 Generated Data" );

    echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
    echo '<table border="1">';
    echo '<tr>';
    echo '<td style="background-color:yellow;">질문</td>';
    echo '<td style="background-color:yellow;">동사</td>';
    echo '<td style="background-color:yellow;">명사</td>';
    echo '</tr>';

    $_intent = array();
    $_entity = array();

    foreach($dataArray as $failData){
    
        $_intent['sentence'] = $failData;
        $_intent['type'] = 'intent';
        $_entity['sentence'] = $failData;
        $_entity['type'] = 'entity';

        $intentData = $chatbot->getPSFromSentence($_intent);	
        $entityData = $chatbot->getPSFromSentence($_entity);
       
        $intentString = '';
	    foreach ($intentData as $intent=>$count) {
		   $intentString.= $intent.', ';
		}
		$intentString = rtrim($intentString,', ');

		$entityString = '';
	    foreach ($entityData as $entity=>$count) {
		   $entityString.= $entity.', ';
		}
		$entityString = rtrim($entityString,', ');

        echo '<tr>';
        echo '<td>'.$failData.'</td>';
        echo '<td>'.$intentString.'</td>';
        echo '<td>'.$entityString.'</td>';
        echo '</tr>';
    }
    echo '</table>'; 
    exit;
}

echo json_encode($result);
exit;
?>
