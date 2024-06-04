<?php
if (!defined('__KIMS__')) exit;
include_once $g['dir_module'] . 'var/var.php'; // 모듈 설정값
include_once $g['dir_module'] . 'var/define.path.php'; // class, 모듈, 레이아웃 패스 세팅
$chatbot = new Chatbot();

// 학습 완료/대기 변경 대상 item 들이 있는 경우 
if (isset($_POST['unknownItems'])) {
    $is_learn = 'done' === $state ? 1 : 0;
    $sql = 'is_learn=' . $is_learn;
    if ('done' === $state) $sql .= ",d_learn='" . $date['today'] . "'";
    else if ('wait' === $state) $sql .= ",d_learn='', add_intentex=0";

    foreach ($unknownItems as $uid) {
        if ('wait' === $state) {
            $R = getDbData($table[$m . 'unknown'], "uid=" . $uid, "add_intentex");
            $add_intentex = $R['add_intentex'];
            if ($add_intentex) getDbDelete($table[$m . 'intentEx'], "uid=" . $add_intentex);
        }
        getDbUpdate($table[$m . 'unknown'], $sql, 'uid=' . $uid);
    }
}

$result = array();
$result['error'] = false;

$data = array();
$data['vendor'] = $vendor;
$data['bot'] = $botuid;
$data['page'] = $page;
$data['mod'] = $mod;

if ('question' === $mod || 'word' === $mod) {
    $getResultData = $chatbot->getFavorateQuestionData($data);
} else {
    $getResultData = $chatbot->getUnKnownData($data);
}
$query = $getResultData[0];
$list = $getResultData[1];
$pageBtn = $getResultData[2];

$result['query'] = $query;
$result['list'] = $list;
$result['pageBtn'] = $pageBtn;

echo json_encode($result);
exit;
?>