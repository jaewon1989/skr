<?
	/*
	1. vhostManager.sh (버추얼 생성, 추가 스크립트) 미리 작성
	visudo 명령으로 apache 실행 계정의 sudo 실행 권한을 설정
	예) ex: daemon ALL=(ALL) NOPASSWD: /home/web/vhostManager.sh

	2. httpd-vhosts.conf에 bottalks 디렉토리의 모든 conf 파일 인클루드하도록 미리 설정
	# IncludeOptional conf/extra/bottalks/*.conf

	3. bottalks 디렉토리 하부의 conf 파일 형식은 아래와 같다.
	파일명 : 봇아이디.chat.bottalks.co.kr.conf
	서버네임 : 봇아이디.chat.bottalks.co.kr

	4. 버추얼호스트 생성은 Rest API Post 방식으로 처리
	Rest API 접속 : API 스크립트 디렉토리 경로/명령모드/봇 ID/인증토큰 (http://WEB IP/api 디렉토리/vhost_create/testID/인증토큰)
	*/

	//error_reporting(E_ALL ^ E_NOTICE);

	header("content-type:text/html; charset=utf-8");
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, POST, PUT");
	header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");

	class vhostAccount {
		var $chVShell, $chConfDir, $chConfBakupDir, $chSubDomain, $chLogDir, $chAuthUrl, $chSlaveUrl, $chActionMod, $chAccess_token;

		public function __construct($chActionMod="", $chAccess_token="") {
			// vhost 생성 shellscript 경로
			$this->chVShell = "/root/.vhostManager.sh";
			
			// vhost conf 파일 저장 디렉토리
			$this->chConfDir = "/usr/local/apache/conf/extra/bottalks";
			
			// vhost conf 파일 백업 디렉토리
			$this->chConfBakupDir = "/usr/local/apache/conf/extra/bottalks_backup";
			
			// vhost 생성 타겟 서브도메인
			$this->chSubDomain = "chatbot.bottalks.co.kr";
			
			// 실행 로그 디렉토리
			$this->chLogDir = dirname(__file__)."/log";
			
			// 접속 토큰 인증 확인 URL
			$this->chAuthUrl = "http://dev.bottalks.co.kr";

			// Web Slave Url
			$this->chSlaveUrl = "http://112.175.117.246:35279";

			$this->chActionMod = $chActionMod;
			$this->chAccess_token = $chAccess_token;
		}

		public function getVHostCreate($chBotID) {
			$bConfExists = false;
			$chTargetDomain = $chBotID.".".$this->chSubDomain;

			if (file_exists($this->chConfDir."/".$chTargetDomain.".conf")) {
				$bConfExists = true;
			} else {
				$chCommand = "sudo ".$this->chVShell." create ".$chBotID." ".$this->chSubDomain;
				exec($chCommand, $aOutput, $pResult);

				if ($pResult > 0) {
					$aResult = $this->getErrorJSON(false, "Error create vhost.");
					$this->getLogWrite("[".$chBotID." - Create] Error create vhost.");
				} else {
					$bConfExists = true;
					$this->getLogWrite("[".$chBotID." - Create] Ok create vhost.");
				}
			}
			if ($bConfExists) {
				$slaveResult = $this->getSlaveAccountControl($chBotID);
				if ($slaveResult['bResult']) {
					$aResult = $this->getErrorJSON(true, 'ok', $chBotID);
				} else {
					$aResult = $this->getErrorJSON(false, 'ok', $chBotID);
				}
			}
			return $aResult;
		}

		public function getVHostDelete($aPostData) {
			$bConfDelete = false;
			
			if (is_array($aPostData) && count($aPostData) > 0) {
			    $aBotID = $aPostData['bot'];
			    if (is_array($aBotID) && count($aBotID) > 0) {
			        foreach ($aBotID as $chBotID) {
            			$chTargetDomain = $chBotID.".".$this->chSubDomain;

            			if (file_exists($this->chConfDir."/".$chTargetDomain.".conf")) {
            				$chCommand = "sudo ".$this->chVShell." delete ".$chBotID." ".$this->chSubDomain;
            				exec($chCommand, $aOutput, $pResult);

            				if ($pResult > 0) {
            					$this->getLogWrite("[".$chBotID." - Delete] Error delete vhost.");
            				} else {            				    
            					$this->getLogWrite("[".$chBotID." - Delete] Ok delete vhost.");
            				}
            			}            			
            		}
            		
            		$chCommand = "sudo ".$this->chVShell." graceful";
            		exec($chCommand, $aOutput, $pResult);
            		
       				$aPostData = json_encode($aPostData);
       				$slaveResult = $this->getSlaveAccountControl($aBotID[0], $aPostData);
            	}
            }
            
            $aResult = $this->getErrorJSON(true, 'ok');
			return $aResult;
		}
		
		public function getVHostStop($chBotID) {
			$bStop = false;
			$chTargetDomain = $chBotID.".".$this->chSubDomain;

			if (file_exists($this->chConfDir."/".$chTargetDomain.".conf")) {
				$chCommand = "sudo ".$this->chVShell." stop ".$chBotID." ".$this->chSubDomain;
				exec($chCommand, $aOutput, $pResult);

				if ($pResult > 0) {
					$aResult = $this->getErrorJSON(false, "Error stop vhost.");
					$this->getLogWrite("[".$chBotID." - Stop] Error stop vhost.");
				} else {
					$bStop = true;
					$this->getLogWrite("[".$chBotID." - Stop] Ok stop vhost.");
				}
			}
			if ($bStop) {
				$slaveResult = $this->getSlaveAccountControl($chBotID);
				if ($slaveResult['bResult']) {
					$aResult = $this->getErrorJSON(true, 'ok', $chBotID);
				} else {
					$aResult = $this->getErrorJSON(false, 'ok', $chBotID);
				}
			}
			return $aResult;
		}
		
		public function getVHostResume($chBotID) {
			$bResume = false;
			$chTargetDomain = $chBotID.".".$this->chSubDomain;

			if (file_exists($this->chConfBakupDir."/".$chTargetDomain.".conf")) {
				$chCommand = "sudo ".$this->chVShell." resume ".$chBotID." ".$this->chSubDomain;
				exec($chCommand, $aOutput, $pResult);

				if ($pResult > 0) {
					$aResult = $this->getErrorJSON(false, "Error resume vhost.");
					$this->getLogWrite("[".$chBotID." - Resume] Error resume vhost.");
				} else {
					$bResume = true;
					$this->getLogWrite("[".$chBotID." - Resume] Ok resume vhost.");
				}
			}
			if ($bResume) {
				$slaveResult = $this->getSlaveAccountControl($chBotID);
				if ($slaveResult['bResult']) {
					$aResult = $this->getErrorJSON(true, 'ok', $chBotID);
				} else {
					$aResult = $this->getErrorJSON(false, 'ok', $chBotID);
				}
			}
			return $aResult;
		}

		public function getAccessAuth($mod, $botid, $token) {
			$chUrl = $this->chAuthUrl;
			$data = array("r"=>"bts", "m"=>"service", "a"=>"access_auth", "mod"=>$mod, "sid"=>$botid, "token"=>$token);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $chUrl);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$aResult = curl_exec($ch);
			curl_close ($ch);

			$aResult = json_decode($aResult, true);
			return $aResult;
		}

		public function getSlaveAccountControl($userid, $aPostData="") {
			$apiURL = $this->chSlaveUrl."/api/".$this->chActionMod."/".$userid."/".$this->chAccess_token;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, true);
			if ($aPostData) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $aPostData);
			}
			curl_setopt($ch, CURLOPT_URL, $apiURL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSLVERSION,1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$aResult = curl_exec($ch);
			curl_close ($ch);

			$aResult = json_decode($aResult, true);
			return $aResult;
		}

		public function getErrorJSON($bResult, $bResultMsg="", $bResultData="") {
			$aArray = array("bResult"=>$bResult, "bResultMsg"=>$bResultMsg, "bResultData"=>$bResultData);
			return json_encode($aArray);
		}

		public function getLogWrite($chLog) {
			if(file_exists($this->chLogDir)){
				$fp = fopen($this->chLogDir."/vhost_".date("Ymd").".log","a+");
				fputs($fp,"[".date("Y-m-d H:i:s")."] ".$chLog."\n");
				fclose($fp);
			}
		}

	}

	// Slim REST API 체크
	require dirname(__file__).'/class/Slim/Slim.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();

	$app->post('/:action_mod/:bot_id/:access_token',function($action_mod, $bot_id, $access_token) use ($app) {
		// action 타입
		$aAction = array("vhost_create", "vhost_delete", "vhost_stop", "vhost_resume");

		$mod = $action_mod;
		$id = $bot_id;
		$token = $access_token;

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
	});

	$app->run();
?>
