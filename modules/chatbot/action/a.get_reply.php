<?php
if (!defined('__KIMS__')) exit;
require_once $g['dir_module'] . 'var/var.php';
require_once $g['dir_module'] . 'var/define.path.php';


$chatbot = new Chatbot();

$result = [];
$result['error'] = false;

$pData = getEscString($_POST['pData']);

try {
    if (!trim($pData['bot'])) {
        throw new Exception($pData['errorMsg']);
    }

    $chatbot->q_StartTime = $pData['q_StartTime'];
    $chatbot->cmod = $pData['cmod']; // vod or cs
    $chatbot->vendor = $pData['vendor'];
    $chatbot->botuid = $pData['bot'];
    $chatbot->dialog = $pData['dialog'];
    $chatbot->botid = $pData['botid'];
    $chatbot->mbruid = $pData['mbruid'];
    $chatbot->contaxt['user_input'] = $pData['msg']; // verify 하기전 원본 메세지
    $chatbot->roomToken = $pData['roomToken'];
    $chatbot->botActive = $pData['botActive'];
    $chatbot->bot_skin = $pData['bot_skin'];
    $chatbot->bottype = $pData['bot_type'];
    $chatbot->channel = $pData['channel'];
    $chatbot->cgroup = $pData['cgroup'];
    $chatbot->errorMsg = $pData['errorMsg'];

    $pData['botUid'] = $pData['bot'];
    $pData['botId'] = $pData['botid'];

    if (isset($pData['msg']) && $pData['msg']) {
        $aRsvStr = array('rsv_request' => '예약할께', 'rsv_search' => '예약확인해줘', 'rsv_modify' => '예약변경할께', 'rsv_cancel' => '예약취소할께');
        $pData['msg'] = array_key_exists($pData['msg'], $aRsvStr) ? $aRsvStr[$pData['msg']] : $pData['msg'];
        $pData['clean_input'] = $chatbot->verifyUserInput($pData['msg']);
    }

    // aramjo context load
    $chatbot->getBotContext($pData);

    switch ($pData['req_type']) {
        // 사용자 입력, faq 응답 요청
        case('text') :
            if (!trim($pData['clean_input'])) {
                throw new Exception($pData['errorMsg']);
            }

            // FAQ 자동완성 검색
            if (isset($pData['faq_mod']) && $pData['faq_mod']) {
                if ($pData['faq_mod'] == 'search' && mb_strlen($pData['clean_input'], 'utf-8') >= 2) {
                    $clean_input = getMorphStrReplace($pData['clean_input']);
                    //형태소 분석 (불용어 제거)
                    $chSample = getRemoveStopWords(getMecabMorph($clean_input, '|'));
                    $aSample = rtrim(str_replace(' ', '', preg_replace('/(\+?[a-zA-Z])/iu', '', $chSample)), '|');
                    $aSample = explode('|', $aSample);
                    $_aSample = '';
                    foreach ($aSample as $word) $_aSample .= (mb_strlen($word, 'utf-8') > 1 ? $word . '|' : '');
                    $_aSample = rtrim($_aSample, '|');

                    $_match = "match(question) against('" . $clean_input . "*' in boolean mode)";
                    $_wh = "vendor=" . $pData['vendor'] . " and bot=" . $pData['bot'] . " and " . $_match;

                    $query = "Select uid, question, " . $_match . " as score ";
                    $query .= "From " . $table[$m . 'faq'] . " Where " . $_wh . " ";
                    $query .= "Order by score DESC Limit 5";
                    $RCD = db_query($query, $DB_CONNECT);
                    $_search = array();
                    while ($R = db_fetch_assoc($RCD)) {
                        $question = preg_replace('/(' . $_aSample . ')/u', '<b>$0</b>', preg_replace('/(\s)?(\r\n|\r|\n)/', ' ', $R['question']));
                        $_search[] = array('uid' => $R['uid'], 'question' => $question);
                    }

                    $result['search'] = $_search;
                }

                if ($pData['faq_mod'] == 'get_answer' && $pData['faq_uid']) {
                    $R = getDbData($table[$m . 'faq'], "vendor=" . $pData['vendor'] . " and bot=" . $pData['bot'] . " and uid='" . $pData['faq_uid'] . "'", 'answer');
                    if ($R['answer']) {
                        $userChat = array();
                        $userChat['roomToken'] = $pData['roomToken'];
                        $userChat['botActive'] = $pData['botActive'];
                        $userChat['printType'] = 'T';
                        $userChat['content'] = $pData['clean_input'];
                        $userLastChat = $chatbot->addChatLog($userChat);

                        $_data = $reply = array();
                        $_data['parse'] = false;
                        $_data['api'] = false;
                        $_data['text'] = stripslashes($R['answer']);
                        $botMsg = $chatbot->getBotTextMsg($_data);

                        $_result = array();
                        $_result['response'] = array(array("text", $botMsg));
                        $_result['res_type'] = 'mix';

                        // 챗봇 아웃풋 log 저장
                        $botChat['printType'] = 'text';
                        $botChat['findType'] = 'F';
                        $botChat['score'] = '100';
                        $botChat['content'] = $_result['response'];
                        $botChat['last_chat'] = $userLastChat['last_chat'];
                        $chatbot->addBotChatLog($botChat);

                        $reply = $_result;
                    }
                }

            } else {
                // 온톨로지 응답 검색
                $reply = $chatbot->ProcessInput($pData); // array(res,res_type)
            }

            if ($reply) {
                if ($reply['res_type'] == 'text' || !$reply['res_type']) {
                    $_data = array();
                    $_data['parse'] = false;
                    $_data['text'] = $reply['response'];
                    $content = $chatbot->getBotTextMsg($_data);
                } else {
                    // res_type = mix
                    $content = $reply['response'];
                }

                // 최종 답변 리턴
                $result['content'] = $content;
                $result['botUid'] = $chatbot->botuid;
                $result['vendor'] = $chatbot->vendor;
                $result['botid'] = $pData['botid'];
                $result['query'] = $reply['query'];
                $result['showTimer'] = $_data['showTimer'];
                $result['debug'] = $chatbot->debug;
                $result['reply'] = $reply;
                $result['dateTime'] = $pData['dateTime'];
                $result['roomToken'] = $pData['roomToken'];
                $result['cmod'] = $pData['cmod'];
            } else {
                $resut['content'] = '';
            }
            break;

        // 버튼, 노드 응답 요청
        case('hMenu') :
            $_wh = 'vendor='.$pData['vendor'].' and bot='.$pData['bot'].' and dialog='.$pData['dialog'].' and id='.$pData['node'];
            $R = getDbData($table[$m . 'dialogNode'], $_wh, 'timeout, timeout_msg');

            if ($pData['jump']) $reply = $chatbot->getNodeRespond($pData); // 특정 노드로 점프
            else $reply = $chatbot->getMenuRespond($pData); // array(res,res_type)

            $result['content'] = $reply;
            $result['nodeTimeout'] = $R['timeout'];
            $result['nodeTimeoutMsg'] = $R['timeout_msg'];
            break;
    }

    // aramjo context save
    $chatbot->setBotContext($pData);

} catch (Exception $exception) {
    $errorMsg = !empty($exception->getMessage()) ? $exception->getMessage() : '잘못된 접근입니다.';
    $result['reply']['response'] = [
        [
            0 => "text",
            1 => $chatbot->getBotTextMsg(['parse' => false,
                'text' => $errorMsg])
        ]
    ];
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit;
?>
