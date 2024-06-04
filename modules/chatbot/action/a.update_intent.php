<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.path.php'; // class, 모듈, 레이아웃 패스 세팅 
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
    $R = getDbData($table[$m.'unknown'], "uid='".$val."'", "sentence");
    if(trim($R['sentence'])) {
        $is_row = getDbRows($table[$m.'intentEx'], "bot='".$botuid."' and intent='".$intent."' and content='".trim($R['sentence'])."'");
        if(!$is_row) {
            $RD = getDbData($table[$m.'dialog'], "type='D' and bot='".$botuid."'", "uid");
            $dialog = $RD['uid'];
            
            $QKEY = "type,vendor,bot,dialog,intent,hidden,content";
        	$QVAL = "'V','$vendor','$botuid','$dialog','$intent','0','".trim($R['sentence'])."'";
            getDbInsert($table[$m.'intentEx'],$QKEY,$QVAL);
            $intentEx_uid = getDbCnt($table[$m.'intentEx'],'max(uid)','');
            $bLearning = true;
        }
    }
    
	getDbUpdate($table[$m.'unknown'],"is_learn=1,d_learn='".$d_learn."', add_intentex='".$intentEx_uid."'",'uid='.$val);
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