<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$data = $_POST;

$vendor = $data['vendor'];
$bot = $data['bot'];
$dialog = $data['dialog'];

// linkType 분기
if($data['linkType']=='uploadImg'){
	$data['file'] = $_FILES['file'];
    $result = $chatbot->uploadFile($data);

}else if($data['linkType']=='updateBot'){
    $result['content'] = $chatbot->updateBotData($data);

}else if($data['linkType']=='importData'){
    $data['file'] = $_FILES['file'];
    $result = $chatbot->importDataByFile($data);
}else if($data['linkType'] =='getTemplate'){
	// intent Ex
	$result['intentEx_list'] = $chatbot->getDialogTemplate('dialog/intentEx_list');
	$result['intentEx_row'] = $chatbot->getDialogTemplate('dialog/intentEx_row');
	$result['entityEx_list'] = $chatbot->getDialogTemplate('dialog/entityEx_list');
	$result['entityEx_row'] = $chatbot->getDialogTemplate('dialog/entityEx_row');
	$result['no_data'] = $chatbot->getDialogTemplate('dialog/no_data');

}else if($data['linkType']=='getItemEx'){
	if($data['type']=='intent') $result['content'] = $chatbot->getIntentEx($data);
	else if($data['type']=='entity') $result['content'] = $chatbot->getEntityEx($data);

}else if($data['linkType']=='getGraphTable'){
	$result['content'] = $chatbot->getGraphTable($data);

}else if($data['linkType']=='addTempData' || $data['linkType']=='delTempData' || $data['linkType']=='editTempData'){
    $result['content'] = $chatbot->controlTempData($data);
}else if($data['linkType'] == 'getTempLabelList'){
	$data['act'] ='getLabel';
	$result['content'] = $chatbot->controlTempData($data);
}else if($data['linkType'] == 'changeTempLabelOrder'){
 	$result['content'] = $chatbot->controlTempData($data);
}else if($data['linkType'] == 'control-botActive'){
	getDbUpdate($table[$m.'bot'],'active='.$data['active'],'uid='.$data['uid']);
	$result['content'] = $data['active'];
}else if($linkType =='deleteBot'){
    $botTemp = new botTemp;
    $_data = $data['data'];
    $_dd = array("vendor"=>$_data['vendor'],"bot"=>$_data['bot']);
    $result = $botTemp->deleteBot($_dd);

}else if($linkType =='multiDeleteBot' || $linkType=='makeTemp'){
    $botTemp = new botTemp;
    $uid_arr = json_decode(stripcslashes($data['data']['uid_arr']),true);

    if($linkType=='multiDeleteBot'){
        foreach ($uid_arr as $uid) {
	        $B = getDbData($table[$m.'bot'],'uid='.$uid,'*');
	        $vendor = $B['vendor'];
	        $_dd = array("vendor"=>$vendor,"bot"=>$uid);
	        $result = $botTemp->deleteBot($_dd);
	    }
    }else if($linkType=='makeTemp'){
        foreach ($uid_arr as $uid) {
           	getDbUpdate($table[$m.'bot'],'is_temp=1','uid='.$uid);
        }
    }

}else if($linkType =='copyBot'){
    $botTemp = new botTemp;
    $_data = $data['data'];
    $_dd = array(
        "vendor"=>$_data['vendor'],
        "targetBot"=>$_data['bot'],
        "mbruid"=>$my['uid'],
    );
    $result = $botTemp->copyBot($_dd);

}else if($linkType =='get-localBotList'){
    $query = "Select * From rb_chatbot_bot Where vendor='".$data['vendor']."' and bottype ='".$data['botType']."' and hidden=0 Order by uid ASC ";
    $aBot = db_query($query, $DB_CONNECT);
    $result = array();
    while($R=db_fetch_array($aBot)) {
        $result[] = array("uid"=>$R['uid'],"name"=>$R['name'],"induCat"=>$R['induCat']);
    }

}else if($linkType =='get-remoteBotList'){
    $botTemp = new botTemp;
    $_data = $data['data'];
    $_dd = array(
        "targetServer"=>$_data['server'],
        "targetDB"=>$_data['db'],
        "mbruid"=>$my['uid'],
    );
    $result['content'] = $botTemp->getRemoteBotList($_dd);

}else if($linkType =='get-remoteDBList'){
    $botTemp = new botTemp;
    $_data = $data['data'];
    $_dd = array(
        "targetServer"=>$_data['server']
    );
    $result['content'] = $botTemp->getRemoteDBList($_dd);
}

echo json_encode($result);
exit;
?>
