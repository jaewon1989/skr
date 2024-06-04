<?
	header("content-type:text/html; charset=utf-8");
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, POST, PUT");
	header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
	
	error_reporting(E_ERROR);
	
	$chConfDir = "/data/chatbot";
    include_once $chConfDir.'/bottalksConf.php';

	// Slim REST API 체크
	require dirname(__file__).'/class/Slim/Slim.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();

	$app->post('/:action_mod/:bot_id/:access_token',function($action_mod, $bot_id, $access_token) use ($app) {
	    global $g, $_db_bot;
	    
		// action 타입
		$aAction = array(
		    "vhost_create", "vhost_delete", "vhost_stop", "vhost_resume",
		    "account_create", "account_delete", "mbrid_modify", "mbrdata_modify", "bot_usable"
		);
		
		$mod = $action_mod;
		$id = $bot_id;
		$token = $access_token;
		
		// DB 설정
		if(strpos($action_mod, "vhost_") === false) {
		    include_once "./class.db.php";
		    
		    $botAccount = new botAccount();

    		if (!$mod || !$id || !$token) {
    			$botAccount->getLogWrite("[".$user_id." - ".$mod."] Error Not exists mod or id.");
    			echo $botAccount->getErrorJSON(false, "Error Not exists mod or id."); exit;
    		}

    		if (!in_array($mod, $aAction)) {
    			$botAccount->getLogWrite("[".$user_id." - ".$mod."] Error Invalid action.");
    			echo $botAccount->getErrorJSON(false, "Error Invalid action."); exit;
    		}

    		$authResult = $botAccount->getAccessAuth($mod, $id, $token);
    		if (!$authResult['bResult']) {
    			$botAccount->getLogWrite("[".$user_id." - ".$mod."] Error Invalid access token.");
    			echo $botAccount->getErrorJSON(false, "Error Invalid access token."); exit;
    		} else {
    			// DB Name 재정의 (Prefix 'bot_' 적용);
    			$dbname = $botAccount->chDB_Prefix.$id;

    			if ($mod == "account_create") {
    				// DB 생성일 경우 회원 정보 체크				
    				$aPostData = json_decode($app->request()->getBody(), true);
    				echo $botAccount->getDBCreate($dbname, $aPostData);
    			}
    			if ($mod == "account_delete") {
    				echo $botAccount->getDBDrop($dbname);
    			}
    			if ($mod == "mbrid_modify" || $mod == "mbrdata_modify") {
    			    $chLog = "[".date("Y-m-d H:i:s")."] ".$app->request()->getBody();
		            file_put_contents(dirname(__file__)."/log/account_".date("Ymd").".log", $chLog."\n", FILE_APPEND);
		        
    				$aPostData = json_decode($app->request()->getBody(), true);
    				echo $botAccount->getDBModifyMember($dbname, $aPostData);
    			}
    			if ($mod == "bot_usable") {
    				$aPostData = json_decode($app->request()->getBody(), true);
    				echo $botAccount->getBOTUsable($dbname, $aPostData);
    			}
    		}
    		
		} else {
		
		// Apache 설정
		    
		    include_once "./class.vhost.php";

    		$vhostAccount = new vhostAccount($mod, $token);

    		if (!$mod || !$id || !$token) {
    			echo $vhostAccount->getErrorJSON(false, "Error Not exists mod or id."); exit;
    		}

    		if (!in_array($mod, $aAction)) {
    			echo $vhostAccount->getErrorJSON(false, "Error Invalid action."); exit;
    		}

    		$authResult = $vhostAccount->getAccessAuth($mod, $id, $token);
    		if (!$authResult['bResult']) {
    			echo $vhostAccount->getErrorJSON(false, "Error Invalid access token."); exit;
    		} else {
    			if ($mod == "vhost_create") {
    				echo $vhostAccount->getVHostCreate($id);
    			}
    			if ($mod == "vhost_delete") {
    			    // account 삭제일 경우 botid 배열형태로 존재
    				$aPostData = json_decode($app->request()->getBody(), true);
    				echo $vhostAccount->getVHostDelete($aPostData);
    			}
    			if ($mod == "vhost_stop") {
    				echo $vhostAccount->getVHostStop($id);
    			}
    			if ($mod == "vhost_resume") {
    				echo $vhostAccount->getVHostResume($id);
    			}
    		}
    	}
	});

	$app->run();
?>
