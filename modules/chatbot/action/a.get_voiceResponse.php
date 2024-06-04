<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';

if(!trim($_POST['pData']['bot']) || !trim($_POST['pData']['msg'])) {
    echo "<script>alert('잘못된 접근입니다'); history.back();</script>"; exit;
}

$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$pData = $_POST['pData'];
// javascript 막기
$pData = getStripScript($pData);

$chatbot->q_StartTime = $pData['q_StartTime'];
$data = array();
$data['botId'] = $pData['botid'];
$data['msg_type'] = $pData['msg_type'];
$data['msg'] = $chatbot->verifyUserInput($pData['msg']);
$data['roomToken'] = $pData['roomToken'];
$data['userId'] = $pData['userId'];
$data['channel'] = $pData['channel'];
$data['cmod'] = $pData['cmod'];
$data['api'] = true;

if($data['msg']) {
    $reply = $chatbot->getApiResponse($data);
    $response = getBotApiResponseContent($reply);
    if($response[0]['type'] == "node" && $response[0]['content']) {
        $data['node'] = $response[0]['content'];
        $reply = $chatbot->getApiResponse($data);
        $response = getBotApiResponseContent($reply);
    }
}

// 최종 답변 리턴
$result['content'] = $response;

echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit;
?>
