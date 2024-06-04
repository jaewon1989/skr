<?php
	header("Content-type:text/html;charset=utf-8");

	class SSOLogin {
		private $auth_uri;

		public function __construct(){
		    global $g;

			$this->auth_uri = $g['front_host'].'/api/v1';
		}

		public function get__API__RES($data) {
			$apiURL = $this->auth_uri.'/isLogin/'.$data['userId'].'/'.$data['loginToken'];

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiURL);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			$response = curl_exec($ch);
            $aResInfo = curl_getinfo($ch);
            curl_close($ch);

            $result = array();
            $result['statusCode'] = $aResInfo['http_code'];
            $result['body'] = $response;
			return $result;
		}

		public function getSSOData($data){
			$orange__F = $data;
			$key = md5('bottalksauth');
			$decryped = getAESDecrypt($orange__F, $key);
			$decryped_arr = explode('|',$decryped);
			$userId = $decryped_arr[0];
			$loginToken = $decryped_arr[1];

			$sendData = array();
			$sendData['userId'] = $userId;
			$sendData['loginToken'] = $loginToken;
			$AR = $this->get__API__RES($sendData); // json 타입
			return $AR;
		}

		public function getLogWrite($chLog) {
		    $file = $_SERVER['DOCUMENT_ROOT'].'/_tmp/out/test.txt';
			if(file_exists($file)){
				$fp = fopen($file,"a+");
				fputs($fp,"[".date("Y-m-d H:i:s")."] ".$chLog."\n");
				fclose($fp);
			}
		}
	}

	//-----------------------------------------------------------------
	if (trim($_POST['orange__F'])) {
		$orange__F = trim($_POST['orange__F']);
		$page = trim($_POST['page']);
		$botid = trim($_POST['botid']);
		$oSSO = new SSOLogin();
		$result = $oSSO->getSSOData($orange__F);

		$bResult = false;
		if ($result['statusCode'] =='200') {
			$sendRes = json_decode($result['body'],true);
			if ($sendRes['status'] == "success") {
				$sendContent = $sendRes['result']['content'];
				$_SESSION['mbr_uid'] = $sendContent['mbruid'];
				$_SESSION['mbr_db'] = $sendContent['dbname'];
				$_SESSION['mbr_bot'] = $sendContent['bot'];
				$_SESSION['mbr_info'] = $sendContent['mbr_info'];
				$_SESSION['bot_info'] = $sendContent['bot_info'];
				$_SESSION['sso_login'] = true;

				$_DB['name'] = $_SESSION['mbr_db'];
				$_DB['head'] = 'rb';
                $_DB['port'] = '3306';
                $_DB['type'] = 'InnoDB';
				$DB = $_DB;

				// 전체 table column 체크
				$DB_CONNECT = isConnectedToDB($DB);
				include_once $g['path_module'].'chatbot/includes/botTemp.class.php';
				$BT = new botTemp();
				$BT->getSysColsCheck();

				// 시스템 엔터티 체크
				$aVendor = getDbData('rb_chatbot_vendor', "mbruid='".$_SESSION['mbr_uid']."'", "uid");
				$_data = array();
				$_data['vendor'] = $aVendor['uid'];
				$BT->updateSysResource($_data);

    		    if($botid) {
    		        $aBot = getDbData('rb_chatbot_bot', "mbruid='".$_SESSION['mbr_uid']."' and id='".$botid."'", "uid");
    		        $bot = $aBot['uid'];
    		    }

    		    $bResult = true;
			}
		}

		if($bResult) {
		    if($page && $bot) {
		        $chLink = "/adm/".$page."?bot=".$bot;
		    } else {
		        $chLink = "/adm";
		    }
		} else {
		    $chLink = "/error.html";
		}
		header("Location: ".$chLink); exit;
	}

	// adm 링크 체크
	if (strpos($_SERVER['REQUEST_URI'], "adm") !== false) {
		if (!$_SESSION['sso_login'] || strpos($_SERVER['HTTP_REFERER'], $g['root_host']) === false) {
			header("Location: /error.html"); exit;
		}
	}

	// 대화창 (봇id.chatbot.bottalks.co.kr), 카카오톡 연동 (chatbot.bottalks.co.kr/chatapi_kakao/봇id)  봇아이디 체크
	if (strpos($_SERVER['HTTP_HOST'], '.'.$g['chatbot_host']) !== false || strpos($_SERVER['REQUEST_URI'], '/chatapi_') !== false) {
	    if (strpos($_SERVER['HTTP_HOST'], '.'.$g['chatbot_host']) !== false) {
	        $aHost = explode(".", $_SERVER['HTTP_HOST']);
	        $botID = $aHost[0];
	    } else if (strpos($_SERVER['REQUEST_URI'], '/chatapi_kakao') !== false) {
	        $aHost = explode('/',$_SERVER['REQUEST_URI']);
	        $botID = $aHost[2];
	    }

		if (!$_SESSION['S_DB_'.$botID]) {
		    // dev쪽으로 db 정보 문의
		    $apiURL = $g['front_host'].'/api/v1/account_info/'.$botID;

		    $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiURL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($ch);
			$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close ($ch);

            if($httpCode == 200 && $response) {
                $dbinfo = json_decode($response, true);

                if ($dbinfo['dbname']) {
        		    $_UDB['name'] = $dbinfo['dbname'];
        			//$_SESSION['mbr_uid'] = $dbinfo['mbruid'];
        			$_SESSION['S_DB_'.$botID]['name'] = $_UDB['name'];
        			$_SESSION['S_DB_'.$botID]['host'] = $_DB['host'];
        			$_SESSION['S_DB_'.$botID]['user'] = $_DB['user'];
        			$_SESSION['S_DB_'.$botID]['pass'] = $_DB['pass'];
        			$_SESSION['S_DB_'.$botID]['port'] = $_DB['port'];

        			$DB = $_SESSION['S_DB_'.$botID];
    			} else {
    				header("Location: /error.html"); exit;
    			}
    		}
		} else {
			$DB = $_SESSION['S_DB_'.$botID];
		}
	}
?>