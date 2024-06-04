<?php
	if(!defined('__KIMS__')) exit;

	include_once $g['dir_module'].'var/var.php'; // 모듈 설정값

	$m = trim($_REQUEST['m']);
	$botid = trim($_REQUEST['bot']);

	$bResult = 0;
	$btnChatbot = '/_core/skin/images/btn_chatbot.png';
	$pc_btn_bottom = '30px';
    $pc_btn_right = '70px';
    $m_btn_bottom = '25px';
    $m_btn_right = '20px';

    $chatUrl = $_cloud_ == true ? "https://".$botid.".chatbot.bottalks.co.kr" : $g['url_root']."/R2".$botid;

	$aData = array();

	if ($m && $botid) {
	    if($_cloud_ == true) {
    	    $apiURL = $g['front_host']."/api/v1/account_info/".$botid;

    	    $ch = curl_init();
    		curl_setopt($ch, CURLOPT_URL, $apiURL);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    		curl_setopt($ch, CURLOPT_SSLVERSION, 1);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    		$response = curl_exec($ch);
    		$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    		curl_close ($ch);

    		if($httpCode == 200 && $response) {
    		    $result = json_decode($response, true);
    		    if($result['dbname']) {
    		        $bResult = 1;

    		        $_UDB['name'] = $result['dbname'];
    		        $_SESSION['mbr_uid'] = $result['mbruid'];
    		        $_SESSION['S_DB_'.$botid]['name'] = $_UDB['name'];
    		        $_SESSION['S_DB_'.$botid]['host'] = $_DB['host'];
    		        $_SESSION['S_DB_'.$botid]['user'] = $_DB['user'];
    		        $_SESSION['S_DB_'.$botid]['pass'] = $_DB['pass'];
    		        $DB = $_SESSION['S_DB_'.$botid];
    		        $DB_CONNECT = isConnectedToDB($DB);
    		    }
    		}
    	}

        $query = "Select uid From rb_chatbot_bot ";
	    $query .="Where hidden='0' and display='1' and id='".$botid."' ";
		$aResult = db_fetch_assoc(db_query($query, $DB_CONNECT));
		if ($aResult['uid']) {
		    $query = "Select name, value From rb_chatbot_botSettings ";
    	    $query .="Where bot='".$aResult['uid']."' and name in ('chatBtn','pc_btn_bottom','pc_btn_right','m_btn_bottom','m_btn_right') ";
    		$RCD = db_query($query, $DB_CONNECT);
    		while ($R = db_fetch_array($RCD)) {
    		    if($R['name'] == "chatBtn") $btnChatbot = $R['value'] ? $R['value'] : "/_core/skin/images/btn_chatbot.png";
    		    if($R['name'] == "pc_btn_bottom") $pc_btn_bottom = $R['value'] ? $R['value'] : "30px";
    		    if($R['name'] == "pc_btn_right") $pc_btn_right = $R['value'] ? $R['value'] : "70px";
    		    if($R['name'] == "m_btn_bottom") $m_btn_bottom = $R['value'] ? $R['value'] : "25px";
    		    if($R['name'] == "m_btn_right") $m_btn_right = $R['value'] ? $R['value'] : "20px";
    		}
    	}

    	$nWidth = $nHeight = 65;
    	if(file_exists($_SERVER['DOCUMENT_ROOT'].$btnChatbot)) {
        	$aSize = getimagesize($_SERVER['DOCUMENT_ROOT'].$btnChatbot);
        	$nWidth = $aSize[0];
        	$nHeight = $aSize[1];
        }

        $aData = array(
    	    'btnChatbot'=>$btnChatbot, 'pc_btn_bottom'=>$pc_btn_bottom, 'pc_btn_right'=>$pc_btn_right, 'm_btn_bottom'=>$m_btn_bottom, 'm_btn_right'=>$m_btn_right,
    	    'width'=>$nWidth, 'height'=>$nHeight
    	);
	}

	$aData['bottalks_use'] = $bResult;
	$aData['mobile'] = !$g['mobile'] ? 0 : 1;
	$aJson = json_encode($aData);
?>
<script>
	if (parent && parent!=this) {
		window.addEventListener("message", function(ev) {
			if (ev.data.bottalks_load == true) {
				location.replace("<?=$chatUrl?>");
			}
		});

		window.parent.postMessage(<?=$aJson?>, '*');
	}
</script>
<?
	exit;
?>