<?
	/*
	1. 모든 봇 DB는 하나의 계정 ID로 관리 (예: botadmin/botpass)
	관리용 계정은 아래와 같이 미리 생성되어 있어야 함.
	사용자용 db명의 prefix는 'bot_'

	create user '관리아이디'@'localhost' identified by '비밀번호';
	create user '관리아이디'@'%' identified by '비밀번호';
	grant all privileges on `db prefix%`.* to '관리아이디'@'localhost';
	grant all privileges on `db prefix%`.* to '관리아이디'@'%';
	flush privileges;

	2. bottalks.cloud.sql 파일의 위치는 기본적으로 본 스크립트와 동일 경로

	3. 사용자용 DB 생성은 Rest API Post 방식으로 처리
	사용자 계정 ID는 4~10자의 영문/숫자 혼용
	사용자 계정 ID는 DB명, 서브도메인명으로 사용. 실제 DB명은 'bot_계정ID'로 생성
	Rest API 접속 : API 스크립트 디렉토리 경로/명령모드/사용자계정 ID/인증토큰 (http://DB IP/api 디렉토리/account_create/testID/인증토큰)
	*/

	//error_reporting(E_ALL ^ E_NOTICE);

	class botAccount {
		var $MYSQL_BIN, $chSQLFile, $chLogDir, $chAuthUrl, $chSlaveUrl, $chVendorUrl;
		var $chDB_Host, $chDB_Port, $chDB_AdminID, $chDB_AdminPW, $chDB_Prefix, $chDB_BackupDir, $chDataDir, $pConnection;
		var $chLogData;

		public function __construct() {
		    global $g, $_db_bot;
		    
			// mysql bin 디렉토리
			$this->MYSQL_BIN = "/usr/local/mysql/bin";

			// 봇톡스 기본 SQL 파일 경로
			$this->chSQLFile = dirname(__file__)."/bottalks.cloud.sql";

			// 실행 로그 디렉토리
			$this->chLogDir = dirname(__file__)."/log";

			// 접속 토큰 인증 확인 URL
			$this->chAuthUrl = $g['front_host'];

			// DB Slave Server API URL
			//$this->chSlaveUrl = "http://10.10.0.244:8306";
			
			// Vendor Server API URL
			$this->chVendorUrl = "http://".$g['chatbot_local_host'];

			$this->chDB_Host = $_db_bot['host'];
			$this->chDB_Port = $_db_bot['port'];

			$this->chDB_AdminID = $_db_bot['user'];
			$this->chDB_AdminPW = $_db_bot['pass'];

			$this->chDB_Prefix = "bot_";

			// DB 삭제시 백업 디렉토리
			$this->chDB_BackupDir = "/data/backup/drop_db";
			
			// 파일 기본 저장 디렉토리
			$this->chDataDir = "/data/chatbot/www/files";
			
			$this->chLogData = "";
		}

		public function getDBConnect() {
		    global $DB_CONNECT;
			$this->pConnection = mysqli_connect($this->chDB_Host, $this->chDB_AdminID, $this->chDB_AdminPW);
		}

		public function getDBClose() {
			if ($this->pConnection) {
				mysqli_close($this->pConnection);
			}
		}

		public function getDBCreate($chDBName, $aPostData="") {
			$this->getDBConnect();

			$bDBExists = false;
			$bDBCreate = false;
			
			$this->chLogData = "";

			if (!$this->pConnection) {
				$aResult = $this->getErrorJSON(false, "Error DB not connection.");
				$this->chLogData .= "[".$chDBName." - Add] Error DB not connection.\n";
			} else {
				// DB 생성 여부 체크
				$chQuery = "Select count(*) as nCnt From information_schema.schemata Where schema_name = '".$chDBName."' ";
				$pResult = mysqli_query($this->pConnection, $chQuery);
				$pRow = mysqli_fetch_assoc($pResult);
				if ($pRow['nCnt'] > 0) {
					$bDBExists = true;
				}
				mysqli_free_result($pResult);

				if (!$bDBExists) {
					// DB 없을 경우 생성
					$chQuery = "create database ".$chDBName." default character set utf8";
					if (mysqli_query($this->pConnection, $chQuery)) {
						$bDBExists = true;
						$bDBCreate = true;
						$this->chLogData .= "[".$chDBName." - Add] Ok create database.\n";
					} else {
						$error = mysqli_error($this->pConnection);
						$aResult = $this->getErrorJSON(false, "Error creating database: " . $error);
						$this->chLogData .= "[".$chDBName." - Add] Error creating database: " . $error."\n";
					}
				}

				if ($bDBExists) {
					// DB 연결
					mysqli_select_db($this->pConnection, $chDBName);

					// Table 생성 여부 체크 (전체 테이블수 99)
					$chQuery = "show tables from ".$chDBName." ";
					$pResult = mysqli_query($this->pConnection, $chQuery);

					$aTable = array();
					if ($pResult) {
						while($pRow = mysqli_fetch_array($pResult)){
							$aTable[] = $pRow[0];
						}
						mysqli_free_result($pResult);
					}

					$bRestore = false;
					if (count($aTable) == 0) {
						// Table 없을 경우 DB 데이터 restore
						if (file_exists($this->chSQLFile)) {
							$chCommand = "--user=".$this->chDB_AdminID." --password='".$this->chDB_AdminPW."' --host ".$this->chDB_Host." --database ".$chDBName." < ".$this->chSQLFile;
							exec($this->MYSQL_BIN."/mysql ".$chCommand, $aOutput, $pResult);

							if ($pResult > 0) {
								$aResult = $this->getErrorJSON(false, "Error restore database.");
								$this->chLogData .= "[".$chDBName." - Add] Error restore database.\n";
							} else {
								$bRestore = true;
								$this->chLogData .= "[".$chDBName." - Add] Ok restore database.\n";

								// 회원, venter, bot 정보 Insert
								if (is_array($aPostData) && count($aPostData) > 0) {
									if ($aPostData['mbrid']) {
										$chKey = $chVal = "";
										foreach($aPostData['mbrid'] as $key=>$val) {
											if ($key == "uid") $mbruid = $val;
											$chKey .=$key.", ";
											$chVal .="'".$val."', ";
										}
										$chKey = rtrim($chKey, ", ");
										$chVal = rtrim($chVal, ", ");
										$chQuery = "Insert into rb_s_mbrid (".$chKey.") values (".$chVal.")";
										$pResult = mysqli_query($this->pConnection, $chQuery);
										if (!$pResult) {
											$aResult = $this->getErrorJSON(false, "Error fail insert mbrid.");
											$this->chLogData .= "[".$chDBName." - Add] Error fail insert mbrid.\n";
										}
										$this->chLogData .= "[".$chDBName." - Add] Ok insert mbrid.\n";

										// mbrid uid 값 중복을 막기 위해 인서트 후 다음 인서트 증가값을 변경
										$muid = mysqli_insert_id($this->pConnection);
										mysqli_query($this->pConnection, "alter table rb_s_mbrid auto_increment=".($muid+500));
									}
									if ($aPostData['mbrdata']) {
									    $chKey = $chVal = "";
									    
									    $pResult = mysqli_query($this->pConnection, "Show Columns From rb_s_mbrdata");
									    while ($row = mysqli_fetch_array($pResult)) {
									        if (!$mbruid) {
												if ($row['Field'] == "memberuid") $mbruid = $aPostData['mbrdata']['memberuid'];
											}
									        $chKey .=$row['Field'].", ";
									        $chVal .="'".$aPostData['mbrdata'][$row['Field']]."', ";
									    }
									    
										$chKey = rtrim($chKey, ", ");
										$chVal = rtrim($chVal, ", ");
										$chQuery = "Insert into rb_s_mbrdata (".$chKey.") values (".$chVal.")";
										$pResult = mysqli_query($this->pConnection, $chQuery);
										if (!$pResult) {
											$aResult = $this->getErrorJSON(false, "Error fail insert mbrdata.");
											$this->chLogData .= "[".$chDBName." - Add] Error fail insert mbrdata.\n";
										}
										$this->chLogData .= "[".$chDBName." - Add] Ok insert mbrdata.\n";
									}
									
									if ($aPostData['mbrcomp']) {
										$chKey = $chVal = "";
										foreach($aPostData['mbrcomp'] as $key=>$val) {
											if (!$mbruid) {
												if ($key == "memberuid") $mbruid = $val;
											}
											$chKey .=$key.", ";
											$chVal .="'".$val."', ";
										}
										$chKey = rtrim($chKey, ", ");
										$chVal = rtrim($chVal, ", ");
										$chQuery = "Insert into rb_s_mbrcomp (".$chKey.") values (".$chVal.")";
										$pResult = mysqli_query($this->pConnection, $chQuery);
										if (!$pResult) {
											$aResult = $this->getErrorJSON(false, "Error fail insert mbrcomp.");
											$this->chLogData .= "[".$chDBName." - Add] Error fail insert mbrcomp.\n";
										}
										$this->chLogData .= "[".$chDBName." - Add] Ok insert mbrcomp.\n";
									}

									// Add Vendor
									$chQuery = "Select count(*) as nCnt From rb_chatbot_vendor Where mbruid='".$mbruid."'";
									$pResult = mysqli_query($this->pConnection, $chQuery);
									$pRow = mysqli_fetch_assoc($pResult);
									if ($pRow['nCnt'] == 0) {
										$auth = 1; $type = 1; $display = 1;
										$pResult = mysqli_query($this->pConnection, "Select min(gid) as minGid From rb_chatbot_vendor");
										$pRow = mysqli_fetch_assoc($pResult);
										$gid = $pRow['minGid'] ? ($pRow['minGid']-1) : 1000000000;

									 	$chKey = $chVal = "";
									 	$chKey = "auth, gid, is_admin, display, hidden, type, mbruid, induCat, id, name, service, intro, content, html, tel, tel2, email, logo, upload, d_regis";
									 	$chVal = "'$auth', '$gid', '0', '$display', '0', '$type', '".$mbruid."', '$induCat', ";
									 	$chVal .="'".$aPostData['mbrid']['id']."', '".$aPostData['mbrcomp']['comp_name']."', '', '', '', '', '".$aPostData['mbrdata']['tel1']."', '".$aPostData['mbrdata']['tel2']."', ";
									 	$chVal .="'".$aPostData['mbrdata']['email']."', '".$aPostData['mbrdata']['photo']."', '$upload', '".date("YmdHis")."'";

									 	$chQuery = "Insert into rb_chatbot_vendor (".$chKey.") values (".$chVal.")";
										$pResult = mysqli_query($this->pConnection, $chQuery);
										if (!$pResult) {
											$aResult = $this->getErrorJSON(false, "Error fail insert vendor.");
											$this->chLogData .= "[".$chDBName." - Add] Error fail insert vendor.\n";
										}
										$this->chLogData .= "[".$chDBName." - Add] Ok insert vendor.\n";
										$vendorUID = mysqli_insert_id($this->pConnection);

										$this->getBotCreateRemote($chDBName, $aPostData, $vendorUID);
									}

								}
							}
						} else {
							$aResult = $this->getErrorJSON(false, "Error not exists sql file.");
							$this->chLogData .= "[".$chDBName." - Add] Error not exists sql file.\n";
						}
					} else {
						$this->getBotCreateRemote($chDBName, $aPostData);
						$bRestore = true;
					}

					/* ---------------*/
					if ($bDBCreate) {
						// my.cnf 슬레이브 설정 추가
						//$this->getSlaveControl("add", $chDBName);
					}
					if ($bRestore) {
						$aResult = $this->getErrorJSON(true, 'ok', $chDBName);
					}
					/* ---------------*/
				}
			}

			$this->getDBClose();			
			$this->getLogWrite($this->chLogData);
			return $aResult;
		}
		
		public function getBotCreateRemote($chDBName, $aPostData, $vendorUID="") {
		    if ($aPostData['mbrbot']) {
		        if ($vendorUID) {
					$vendor = $vendorUID;
				} else {
					$pResult = mysqli_query($this->pConnection, "Select uid From rb_chatbot_vendor Where mbruid='".$aPostData['mbrbot']['mbruid']."'");
					$pRow = mysqli_fetch_assoc($pResult);
					$vendor = $pRow['uid'];
				}
				
				// 복사대상 템플릿 정의
				//$template = $aPostData['mbrbot']['scode'];
				//$templateBot_uid = $this->aTemplate[$template];
				
				$targetBot = $aPostData['mbrbot']['t_uid'] < 1 ? 0 : $aPostData['mbrbot']['t_uid'];
				
				$_data = array();
				$_data['dbname'] = $chDBName;
				$_data['vendor'] = $vendor;
				$_data['mbruid'] = $aPostData['mbrbot']['mbruid'];				
				$_data['botid'] = $aPostData['mbrbot']['botid'];
				$_data['botname'] = $aPostData['mbrbot']['botname'];
				//$_data['targetBot'] = $templateBot_uid ? $templateBot_uid : 0;
				$_data['targetBot'] = $targetBot;
				$_data['category'] = $aPostData['mbrbot']['category'];
				$_data['user_uid'] = $aPostData['mbrbot']['user_uid'];
				$_data['c_uid'] = $aPostData['mbrbot']['c_uid'];
				$_data['usetype'] = $aPostData['mbrbot']['usetype'];

    			// web 1차 서버에서 템플릿 복사
    			$lastBot = $this->getCopyBotTemplate($_data);
    			
    			if ($lastBot) {
    			    $this->chLogData .= "[".$_data['botid']." - Add] Ok copy chatbot_template.\n";
    			    return true;
    			} else {
    			    $this->chLogData .= "[".$_data['botid']." - Add] Error fail copy chatbot_template.\n";
    			    return false;
    			}
    		}
		}
		
		public function getCopyBotTemplate($data) {
			$apiURL = $this->chVendorUrl."/api/botAPI/".$data['dbname']."/".$data['mbruid'];
			
			$_data = array();
			$_data['vendor'] = $data['vendor'];
            $_data['botid'] = $data['botid'];
            $_data['botname'] = $data['botname'];
            $_data['targetBot'] = $data['targetBot'];
            $_data['category'] = $data['category'];
            $_data['user_uid'] = $data['user_uid'];
            $_data['c_uid'] = $data['c_uid'];
            $_data['usetype'] = $data['usetype'];
            $aPostData = json_encode($_data, JSON_UNESCAPED_UNICODE);

			$ch = curl_init();			
			curl_setopt($ch, CURLOPT_URL, $apiURL);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $aPostData);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSLVERSION,1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$lastBot = curl_exec($ch);
			curl_close ($ch);
			
			return $lastBot;
		}

		public function getDBDrop($chDBName) {
		    $this->chLogData = "";
			$this->getDBConnect();

			$bDBExists = false;
			$aBotID = array();
			
			if (!$this->pConnection) {
				$aResult = $this->getErrorJSON(false, "Error DB not connection.");
				$this->chLogData .= "[".$chDBName." - Drop] Error DB not connection.\n";
			} else {
				// DB 여부 체크
				$chQuery = "Select count(*) as nCnt From information_schema.schemata Where schema_name = '".$chDBName."' ";
				$pResult = mysqli_query($this->pConnection, $chQuery);
				$pRow = mysqli_fetch_assoc($pResult);
				if ($pRow['nCnt'] > 0) {
					$bDBExists = true;
				}
				mysqli_free_result($pResult);

				if ($bDBExists) {
				    $pResult = mysqli_query($this->pConnection, "Select id From ".$chDBName.".rb_chatbot_bot Where 1>0");
					if ($pResult) {
						while($pRow = mysqli_fetch_assoc($pResult)){
							$aBotID[] = $pRow['id'];
						}
						mysqli_free_result($pResult);
					}
					
					// DB 데이터 백업 후 삭제
					$chBackupFile = "drop_".$chDBName."_".date("Ymd", time()).".sql";
					$chCommand = "--user=".$this->chDB_AdminID." --password='".$this->chDB_AdminPW."' --host ".$this->chDB_Host." ".$chDBName." > ".$this->chDB_BackupDir."/".$chBackupFile;
					exec($this->MYSQL_BIN."/mysqldump ".$chCommand, $aOutput, $pResult);
					if ($pResult > 0) {
						$aResult = $this->getErrorJSON(false, "Error backup database.");
						$this->chLogData .= "[".$chDBName." - Drop] Error backup database.\n";
					} else {
					    $this->chLogData .= "[".$chDBName." - Drop] OK backup database.\n";
						// DB 삭제
						if (!mysqli_query($this->pConnection, "drop database ".$chDBName)) {
							$error = mysqli_error($this->pConnection);
							$aResult = $this->getErrorJSON(false, "Error drop database: " . $error);
							$this->chLogData .= "[".$chDBName." - Drop] Error drop database: " . $error."\n";
						} else {
							// my.cnf 슬레이브 설정 추가
							//$this->getSlaveControl("delete", $chDBName);

							$aResult = $this->getErrorJSON(true, "ok", $chDBName);
							$this->chLogData .= "[".$chDBName." - Drop] Ok drop database.\n";
						}
					}
				}
				
				// DB 삭제 후 업로드 파일, 학습모델 삭제
				$mbruid = str_replace("bot_user", "", $chDBName);
				$this->getDeleteDirectory($this->chDataDir."/chatbot/".$mbruid);
				$this->getDeleteDirectory($this->chDataDir."/trainData/".$mbruid);
			}

			$this->getDBClose();
			$this->getLogWrite($this->chLogData);
			
			// 트래픽 DB 삭제
			if(count($aBotID) > 0) {
			    $pConnection = mysqli_connect($this->chDB_Host, 'traffic', 'traffic@%@&(', 'traffic', 3306);
			    foreach($aBotID as $botid) {
			        mysqli_query($pConnection, "Delete From rb_service_traffic Where category='chatbot' and botid='".$botid."' ");
			        mysqli_query($pConnection, "Delete From rb_service_traffic_summary Where category='chatbot' and botid='".$botid."' ");
			    }
			    mysqli_close($pConnection);
			}
			return $aResult;
		}
		
		public function getDeleteDirectory($dir) {
            if(is_dir($dir)) {
                $it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
                $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
                foreach($it as $file) {
                    if ($file->isDir()) rmdir($file->getPathname());
                    else unlink($file->getPathname());
                }
                return rmdir($dir);
            }
        }

		public function getDBModifyMember($chDBName, $aPostData="") {
		    $this->chLogData = "";
			$this->getDBConnect();

			if (!$this->pConnection) {
				$aResult = $this->getErrorJSON(false, "Error DB not connection.");
			} else {
				// DB 여부 체크
				$chQuery = "Select count(*) as nCnt From information_schema.schemata Where schema_name = '".$chDBName."' ";
				$pResult = mysqli_query($this->pConnection, $chQuery);
				$pRow = mysqli_fetch_assoc($pResult);
				mysqli_free_result($pResult);
				if ($pRow['nCnt'] == 0) {
					$aResult = $this->getErrorJSON(false, "Error not exists db.");
					$this->getLogWrite("[".$chDBName."] Error not exists db.\n");
					return $aResult;
				}

				mysqli_select_db($this->pConnection, $chDBName);

				$mbruid = "";
				if (is_array($aPostData) && count($aPostData) > 0) {
					if ($aPostData['mbrid']) {
						$chVal = "";
						foreach($aPostData['mbrid'] as $key=>$val) {
							if ($key == "uid") {
								$mbruid = $val;
							} else {
								$chVal .=$key." = '".$val."', ";
							}
						}
						$chVal = rtrim($chVal, ", ");
						$chQuery = "Update rb_s_mbrid Set ".$chVal." Where uid = '".$mbruid."'";
						$pResult = mysqli_query($this->pConnection, $chQuery);
						if (!$pResult) {
							$aResult = $this->getErrorJSON(false, "Error fail update mbrid.");
							$this->getLogWrite("[".$chDBName." - Add] Error fail update mbrid.\n");
							return $aResult;
						}
					}
					if ($aPostData['mbrdata']) {
					    $chVal = "";
					    
					    $pResult = mysqli_query($this->pConnection, "Show Columns From rb_s_mbrdata");
					    while ($row = mysqli_fetch_array($pResult)) {
					        if ($row['Field'] == "memberuid") {
					            $mbruid = $aPostData['mbrdata']['memberuid'];
					        } else {
					            $chVal .=$row['Field']." = '".$aPostData['mbrdata'][$row['Field']]."', "
					        }
					    }
					    $chVal = rtrim($chVal, ", ");
					    
					    /*
						$chVal = "";
						foreach($aPostData['mbrdata'] as $key=>$val) {
							if ($key == "memberuid") {
								if (!$mbruid) $mbruid = $val;
							} else {
							    if($key == "is_test" || $key == "d_texpire") continue;
								$chVal .=$key." = '".$val."', ";
							}
						}
						$chVal = rtrim($chVal, ", ");
						*/
						$chQuery = "Update rb_s_mbrdata Set ".$chVal." Where memberuid = '".$mbruid."'";
						$pResult = mysqli_query($this->pConnection, $chQuery);
						if (!$pResult) {
							$aResult = $this->getErrorJSON(false, "Error fail update mbrdata.");
							$this->getLogWrite("[".$chDBName." - Add] Error fail update mbrdata.\n");
							return $aResult;
						}
					}
					if ($aPostData['mbrcomp']) {
						$chVal = "";
						foreach($aPostData['mbrcomp'] as $key=>$val) {
							if ($key == "memberuid") {
								if (!$mbruid) $mbruid = $val;
							} else {
								$chVal .=$key." = '".$val."', ";
							}
						}
						$chVal = rtrim($chVal, ", ");
						$chQuery = "Update rb_s_mbrcomp Set ".$chVal." Where memberuid = '".$mbruid."'";
						$pResult = mysqli_query($this->pConnection, $chQuery);
						if (!$pResult) {
							$aResult = $this->getErrorJSON(false, "Error fail update mbrcomp.");
							$this->getLogWrite("[".$chDBName." - Add] Error fail update mbrcomp.\n");
							return $aResult;
						}
					}

				}
			}

			$aResult = $this->getErrorJSON(true, "ok");
			$this->getDBClose();
			$this->getLogWrite($this->chLogData);
			return $aResult;
		}

		public function getBOTUsable($chDBName, $aPostData="") {
			$this->getDBConnect();

			if (!$this->pConnection) {
				$aResult = $this->getErrorJSON(false, "Error DB not connection.");
			} else {
			    // DB 여부 체크
				$chQuery = "Select count(*) as nCnt From information_schema.schemata Where schema_name = '".$chDBName."' ";
				$pResult = mysqli_query($this->pConnection, $chQuery);
				$pRow = mysqli_fetch_assoc($pResult);
				mysqli_free_result($pResult);
				if ($pRow['nCnt'] == 0) {
					$aResult = $this->getErrorJSON(false, "Error not exists db.");
					$this->getLogWrite("[".$chDBName."] Error not exists db.");
					return $aResult;
				}

				mysqli_select_db($this->pConnection, $chDBName);
				
				if (is_array($aPostData) && count($aPostData) > 0) {
					if ($aPostData['botinfo']['botid'] && $aPostData['botinfo']['buse']) {
						$bHidden = $aPostData['botinfo']['buse'] == "on" ? 0 : 1;
						$chQuery = "Update rb_chatbot_bot Set hidden = '".$bHidden."' Where id = '".$aPostData['botinfo']['botid']."'";
						$pResult = mysqli_query($this->pConnection, $chQuery);
						if (!$pResult) {
							$aResult = $this->getErrorJSON(false, "Error fail update botid.");
							$this->getLogWrite("[".$chDBName." - Add] Error fail update botid.");
							return $aResult;
						}
					}
				}
			}

			$aResult = $this->getErrorJSON(true, "ok");
			$this->getDBClose();
			return $aResult;
		}

		public function getAccessAuth($mod, $sid, $token) {
			$chUrl = $this->chAuthUrl;
			$data = array("r"=>"bts", "m"=>"service", "a"=>"access_auth", "mod"=>$mod, "sid"=>$sid, "token"=>$token);

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

		public function getSlaveControl($mod, $dbname) {
			$apiURL = $this->chSlaveUrl."/api/".$mod."/".$dbname;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, true);
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
		        file_put_contents($this->chLogDir."/account_".date("Ymd").".log", $chLog."\n", FILE_APPEND);
		    }
		}

	}
?>
