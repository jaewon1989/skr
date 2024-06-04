<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
include_once $g['dir_module'].'var/define.path.php'; // 모듈 설정값
include_once $g['dir_module'].'includes/base.class.php';
include_once $g['dir_module'].'includes/module.class.php';

if($_SESSION['mbr_uid'] == '') {
    echo json_encode(array(-1, 401)); exit;
}

$chatbot = new Chatbot();
$BT = new botTemp();

$data = $_POST['data']; //  slash 제거
// javascript 막기
$data = getStripScript($data);

$add_method = $data['add_method'];
$vendor = $data['vendor'];
$name = $data['name'];
$induCat = $data['induCat'];
$intro = $data['intro'];
$remoteBot = $data['remoteBot'];
$remoteDB = $data['remoteDB'];
$remoteServer = $data['remoteServer'];
$is_temp = $data['is_temp']; // mod = suAdm 일때 템플릿 추가하는 경우
$roleType = $data['roleType'];
$bottype = $data['bottype'];
$callno = $data['callno'];
$roleName = $bottype == 'chat' ? ($roleType=='topic'?'토픽':'챗봇') : '콜봇';

if($name == '') {
    echo json_encode(array(-1, $roleName.'명을 입력해주세요')); exit;
}
if($add_method == 'new' && $bottype == 'call') {
    if($callno == '') {
        echo json_encode(array(-1, '전화번호를 입력해주세요')); exit;
    }
    if(!getCheckValidFormat('phone', $callno)) {
        echo json_encode(array(-1, '전화번호가 올바르지 않습니다.')); exit;
    }
}
if($roleType == 'bot' && $induCat == '') {
    echo json_encode(array(-1, '업종을 선택해주세요')); exit;
}

$mbruid = $my['uid'];
$ranStr = $BT->getBotIdString();
$auth = 1;
$id	     = $id?trim($id):$ranStr;
$name	 = $bot_name?trim($bot_name):trim($name);
$type = 1;
$hidden		= $hidden ? intval($hidden) : 0;
$is_temp	= $is_temp ? intval($is_temp) : 0;
$display	= $hidepost || $hidden ? 0 : 1;
$language = $language?$language:'KOR';
$d_regis = $date['totime'];
$mingid = getDbCnt($table[$m.'bot'],'min(gid)','');
$gid = $mingid ? $mingid-1 : 1000000000;

if($remoteBot){ // sys.chatbot 카피
	$_data = array();
	$_data['targetBot'] = $remoteBot;
	$_data['vendor'] = $vendor;
	$_data['mbruid'] = $my['uid'];
	$_data['botId'] = $id;
	$_data['botname'] = $name;
	$_data['intro'] = $intro;
	if($remoteDB) {
	    $_data['targetDB'] = $remoteDB;
	}

	$BT->dbTargetMod = 'sys';
	$BT->dbTargetServer = $remoteServer;

    $BT->copyBot($_data);

}else{ // 봇 신규생성

    $QKEY = "bottype,`role`,is_temp,gid,type,auth,vendor,induCat,hidden,display,name,service,intro,website,boturl,mbruid,id,callno,content,html,tag,lang,hit,likes,report,point,d_regis,d_modify,avatar,upload,monitering_fa";
    $QVAL = "'".$bottype."', '".$roleType."', '".$is_temp."', '".$gid."', '".$type."', '".$auth."', '".$vendor."', '".$induCat."', '".$hidden."', '".$display."', '".$name."', '".$service."', '".$intro."', '".$website."', '".$boturl."', '".$mbruid."', '".$id."', '".$callno."', '".$content."', '".$html."', '".$tag."', '".$language."', 0, 0, 0, 0, '".$d_regis."', '".$d_modify."', '".$avatar."', '".$upload."', ''";
    getDbInsert($table[$m.'bot'],$QKEY,$QVAL);
}

$mod = $data['mod'];

// 방금 등록한 bot  정보
$lastBotUid = getDbCnt($table[$m.'bot'],'max(uid)','');
$R = getDbData($table[$m.'bot'],'uid='.$lastBotUid,'*');
$R['mod'] = $mod;

// 신규 봇을 등록자 그룹 봇에 추가
$group = getDbData($table['s_mbrgroup'], 'uid='.$my['mygroup'],'*');
$_bots = $group['bot'].",".$lastBotUid;
getDbUpdate($table['s_mbrgroup'], "bot='".$_bots."'", 'uid='.$my['mygroup']);

$result = array();
if($mod =='suAdm') $result['content'] = $R;
else if($mod =='adm') $result['content'] = $chatbot->getVendorBotRow($R);
$result['refer'] = array($R,$lastBotUid);

echo json_encode($result);
exit;

?>
