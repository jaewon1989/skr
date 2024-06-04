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

	class vhostAccount {
		var $chVShell, $chConfDir, $chConfBakupDir, $chSubDomain, $chLogDir, $chAuthUrl, $chSlaveUrl, $chActionMod, $chAccess_token;
		var $chLogData;

		public function __construct($chActionMod="", $chAccess_token="") {
		    global $g, $_db_bot;
		    
			// vhost 생성 shellscript 경로
			$this->chVShell = "/data/.vhostManager.sh";
			
			// vhost conf 파일 저장 디렉토리
			$this->chConfDir = "/data/conf/apache/bottalks";
			
			// vhost conf 파일 백업 디렉토리
			$this->chConfBakupDir = "/data/conf/apache/bottalks_backup";
			
			// vhost 생성 타겟 서브도메인
			$this->chSubDomain = $g['chatbot_host'];
			
			// 실행 로그 디렉토리
			$this->chLogDir = dirname(__file__)."/log";
			
			// 접속 토큰 인증 확인 URL
			$this->chAuthUrl = $g['front_host'];

			// Web Slave Url
			//$this->chSlaveUrl = "http://112.175.117.246:35279";

			$this->chActionMod = $chActionMod;
			$this->chAccess_token = $chAccess_token;
			
			$this->chLogData = "";
		}

		public function getVHostCreate($chBotID) {
		    $this->chLogData = "";
		    
			$bConfExists = false;
			$chTargetDomain = $chBotID.".".$this->chSubDomain;

			if (file_exists($this->chConfDir."/".$chTargetDomain.".conf")) {
				$bConfExists = true;
			} else {
				$chCommand = $this->chVShell." create ".$chBotID." ".$this->chSubDomain;
				exec($chCommand, $aOutput, $pResult);

				if ($pResult > 0) {
					$aResult = $this->getErrorJSON(false, "Error create vhost.");
					$this->chLogData .= "[".$chBotID." - Create] Error create vhost.";
				} else {
				    // nc apache graceful 실행
				    
					$bConfExists = true;
					$this->chLogData .= "[".$chBotID." - Create] Ok create vhost.";
				}
			}
			if ($bConfExists) {
			    /*
				$slaveResult = $this->getSlaveAccountControl($chBotID);
				if ($slaveResult['bResult']) {
					$aResult = $this->getErrorJSON(true, 'ok', $chBotID);
				} else {
					$aResult = $this->getErrorJSON(false, 'ok', $chBotID);
				}
				*/
				$aResult = $this->getErrorJSON(true, 'ok', $chBotID);
			}
			$this->getLogWrite($this->chLogData);
			return $aResult;
		}

		public function getVHostDelete($aPostData) {
		    $this->chLogData = "";
			$bConfDelete = false;
			
			if (is_array($aPostData) && count($aPostData) > 0) {
			    $aBotID = $aPostData['bot'];
			    if (is_array($aBotID) && count($aBotID) > 0) {
			        foreach ($aBotID as $chBotID) {
            			$chTargetDomain = $chBotID.".".$this->chSubDomain;

            			if (file_exists($this->chConfDir."/".$chTargetDomain.".conf")) {
            				$chCommand = $this->chVShell." delete ".$chBotID." ".$this->chSubDomain;
            				exec($chCommand, $aOutput, $pResult);

            				if ($pResult > 0) {
            				    $this->chLogData .= "[".$chBotID." - Delete] Error delete vhost.";
            				} else {
            				    $this->chLogData .= "[".$chBotID." - Delete] Ok delete vhost.";
            				}
            			}            			
            		}
            		
       				//$aPostData = json_encode($aPostData);
       				//$slaveResult = $this->getSlaveAccountControl($aBotID[0], $aPostData);
            	}
            }
            
            $this->getLogWrite($this->chLogData);
            $aResult = $this->getErrorJSON(true, 'ok');
			return $aResult;
		}
		
		public function getVHostStop($chBotID) {
		    $this->chLogData = "";
			$bStop = false;
			$chTargetDomain = $chBotID.".".$this->chSubDomain;

			if (file_exists($this->chConfDir."/".$chTargetDomain.".conf")) {
				$chCommand = $this->chVShell." stop ".$chBotID." ".$this->chSubDomain;
				exec($chCommand, $aOutput, $pResult);

				if ($pResult > 0) {
					$aResult = $this->getErrorJSON(false, "Error stop vhost.");
					$this->chLogData .= "[".$chBotID." - Stop] Error stop vhost.";
				} else {
					$bStop = true;
					$this->chLogData .= "[".$chBotID." - Stop] Ok stop vhost.";
				}
			}
			if ($bStop) {
			    /*
				$slaveResult = $this->getSlaveAccountControl($chBotID);
				if ($slaveResult['bResult']) {
					$aResult = $this->getErrorJSON(true, 'ok', $chBotID);
				} else {
					$aResult = $this->getErrorJSON(false, 'ok', $chBotID);
				}
				*/
				
				// nc apache graceful 실행
				
				$aResult = $this->getErrorJSON(true, 'ok', $chBotID);
			}
			
			$this->getLogWrite($this->chLogData);
			return $aResult;
		}
		
		public function getVHostResume($chBotID) {
		    $this->chLogData = "";
			$bResume = false;
			$chTargetDomain = $chBotID.".".$this->chSubDomain;

			if (file_exists($this->chConfBakupDir."/".$chTargetDomain.".conf")) {
				$chCommand = $this->chVShell." resume ".$chBotID." ".$this->chSubDomain;
				exec($chCommand, $aOutput, $pResult);

				if ($pResult > 0) {
					$aResult = $this->getErrorJSON(false, "Error resume vhost.");
					$this->chLogData .= "[".$chBotID." - Resume] Error resume vhost.";
				} else {
					$bResume = true;
					$this->chLogData .= "[".$chBotID." - Resume] Ok resume vhost.";
				}
			}
			if ($bResume) {
			    /*
				$slaveResult = $this->getSlaveAccountControl($chBotID);
				if ($slaveResult['bResult']) {
					$aResult = $this->getErrorJSON(true, 'ok', $chBotID);
				} else {
					$aResult = $this->getErrorJSON(false, 'ok', $chBotID);
				}
				*/
				
				// nc apache graceful 실행
				
				$aResult = $this->getErrorJSON(true, 'ok', $chBotID);
			}
			
			$this->getLogWrite($this->chLogData);
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
		    if(trim($chLog)) {
		        $chLog = "[".date("Y-m-d H:i:s")."] ".$chLog;
		        //file_put_contents($this->chLogDir."/account_".date("Ymd").".log", $chLog."\n", FILE_APPEND);
		    }
		}

	}
?>
