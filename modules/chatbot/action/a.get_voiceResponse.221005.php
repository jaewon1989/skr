<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';

if(!trim($_POST['pData']['bot']) || !trim($_POST['pData']['msg'])) {
    echo "<script>alert('잘못된 접근입니다'); history.back();</script>"; exit;
}

function getResponseContent($response) {
    $_content = array();
    if($response){
    	if(is_array($response)){
            foreach ($response as $i => $resItem){
                if(!$resItem) continue;
                if(is_array($resItem[0])){
                    $itemType = $resItem[0][0];
                    $itemCont = $resItem[0][1];
                }else{
                    $itemType = $resItem[0];
                    $itemCont = $resItem[1];
                }
                if($itemType == "if") {
                    foreach ($itemCont as $item) {
                        if(!$item) continue;
                        for($i=0, $nCnt=count($item); $i<$nCnt; $i++){
                            $iftype = $item[$i]['type'];
                            $ifcontent = $item[$i]['content'];
                            $_content[] = array("type"=>$iftype,"content"=>str_replace("\n", " ", $ifcontent));
                        }
                    }
                } else {
                    $_content[] = array("type"=>$itemType,"content"=>str_replace("\n", " ", $itemCont));
                }

            }
        }else{
            $_content[]= array("type"=>'text',"content"=>str_replace("\n", " ", $response));
        }
    }
    return $_content;
}

$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$pData = $_POST['pData'];
// javascript 막기
$pData = getStripScript($pData);

$chatbot->q_StartTime = $pData['q_StartTime'];
$cmod = $chatbot->cmod = $pData['cmod']; // vod or cs
$chatbot->vendor = $pData['vendor'];
$chatbot->botuid = $pData['bot'];
$chatbot->dialog = $pData['dialog'];
$chatbot->botid = $pData['botid'];
$chatbot->mbruid = $pData['mbruid'];
$chatbot->context['user_input'] = $pData['msg']; // verify 하기전 원본 메세지
$chatbot->roomToken = $pData['roomToken'];
$chatbot->botActive = $pData['botActive'];
$chatbot->bot_skin = $pData['bot_skin'];
$chatbot->bottype = $pData['bot_type'];
$chatbot->channel = $pData['channel'];

$pData['botUid'] = $pData['bot'];
$pData['botId'] = $pData['botid'];
$pData['clean_input'] = $chatbot->verifyUserInput($pData['msg']);

// aramjo context
$chatbot->getBotContext($pData);

$data['intentTrainData'] = json_encode($chatbot->getIntentTrainData($pData),JSON_UNESCAPED_UNICODE);
$data['entityTrainData'] = json_encode($chatbot->getEntityTrainData($pData),JSON_UNESCAPED_UNICODE);

$remote = $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
//file_put_contents($_SERVER['DOCUMENT_ROOT']."/_tmp/cache/voice_".date("Ymd").".log", date("Y-m-d H:i:s")." [request] ".$remote." ".$pData['clean_input']."\n", FILE_APPEND);

$reply = $chatbot->ProcessInput($pData); // array(res,res_type)
$content = getResponseContent($reply['response']);
if($content[0]['type'] == "node" && $content[0]['content']) {
    $pData['node'] = $content[0]['content'];
    $reply = $chatbot->getNodeRespond($pData);
    $content = getResponseContent($reply);
}

//file_put_contents($_SERVER['DOCUMENT_ROOT']."/_tmp/cache/voice_".date("Ymd").".log", date("Y-m-d H:i:s")." [reponse] ".json_encode($content, JSON_UNESCAPED_UNICODE)."\n", FILE_APPEND);

// 최종 답변 리턴
$result['content'] = $content;

echo json_encode($result);
exit;
?>
