<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // ��� ������ 
include_once $g['dir_module'].'var/define.path.php'; // class, ���, ���̾ƿ� �н� ���� 
$chatbot = new Chatbot();

$vendor = trim($_POST['vendor']);
$botuid = trim($_POST['botuid']);
$intent = trim($_POST['intent']);
$unknownItemsUid = trim($_POST['unknownItemsUid']);

if(!$vendor || !$botuid || !$intent || !$unknownItemsUid) exit;

$unUid_array = explode(',',$unknownItemsUid);
$d_learn = $date['today'];
$bLearning = false;
foreach ($unUid_array as $val) {
    if(!$val) continue;
    $intentEx_uid = '';
    $R = getDbData($table[$m.'chatLog'], "uid='".$val."'", "content");
    if(trim($R['content'])) {
        $is_row = getDbRows($table[$m.'intentEx'], "bot='".$botuid."' and intent='".$intent."' and content='".trim($R['content'])."'");
        if(!$is_row) {
            $RD = getDbData($table[$m.'dialog'], "type='D' and bot='".$botuid."'", "uid");
            $dialog = $RD['uid'];
            
            $QKEY = "type,vendor,bot,dialog,intent,hidden,content";
        	$QVAL = "'V','$vendor','$botuid','$dialog','$intent','0','".trim($R['content'])."'";
            getDbInsert($table[$m.'intentEx'],$QKEY,$QVAL);
            $intentEx_uid = getDbCnt($table[$m.'intentEx'],'max(uid)','');
            $bLearning = true;
        }
    }
}

if($bLearning) {
    $data = array('vendor'=>$vendor, 'bot'=>$botuid);
    $chatbot->getTrainIntentPesoNLP($data);
}

$result = array();
$result['intent'] = $intent;
$result['unknownItemsName'] = $unknownItemsName;
$result['unknownItemsUid'] = $unknownItemsUid;
$result['chk_new'] = $chk_new;

echo json_encode($result);
exit;

?>