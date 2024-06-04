<?php  
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
	header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, X-Bottalks-Bot-Id, X-Bottalks-Role, X-Bottalks-Token");	
	
    define('Rb_root',dirname(dirname(__FILE__)).'../../');
    define('Rb_path','../../');
    //error_reporting(E_ALL & ~E_NOTICE);
    error_reporting(E_ERROR);
    //ini_set("display_errors", 1);
    
    require '../libs/Slim/Slim.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    
    if($app->request->isOptions()) {
        return true;
        exit;
    }

    // class 인클루드 
    $m ='chatbot';
    $g = array(
        'path_root'   => Rb_path,
        'path_core'   => Rb_path.'_core/',
        'path_var'    => Rb_path.'_var/',
        'path_tmp'    => Rb_path.'_tmp/',
        'path_layout' => Rb_path.'layouts/',
        'path_module' => Rb_path.'modules/',
        'path_widget' => Rb_path.'widgets/',
        'path_switch' => Rb_path.'switches/',
        'path_plugin' => Rb_path.'plugins/',
        'path_page'   => Rb_path.'pages/',
        'path_file'   => Rb_path.'files/'
    );
    $g['https_on'] = $_SERVER['HTTPS']=='on' || stripos($_SERVER['HTTP_X_FORWARDED_PROTO'],'https') !== false ? true : false;

    if('localhost' === $_SERVER['SERVER_NAME']){
        $chConfDir = './configuration';
        include_once $chConfDir.'/env.php';
    }
    else{
        $chConfDir = substr($_SERVER['DOCUMENT_ROOT'], 0, strrpos($_SERVER['DOCUMENT_ROOT'], "/"));
        include_once $chConfDir.'/bottalksConf.php';
    }

    require $g['path_var'].'table.info.php';
    require $g['path_core'].'function/db.mysql.func.php';
    require $g['path_core'].'function/sys.func.php';
    require $g['path_core'].'function/socketio.php';
    
    $g['mobile']= isMobileConnect($_SERVER['HTTP_USER_AGENT']);
    $g['device']= $g['mobile'] && $_SESSION['pcmode'] != 'Y';
    $g['dir_module'] = $g['path_module'].$m.'/';
    $g['url_module'] = $g['s'].'/modules/'.$m;
    $g['dir_include'] = $g['dir_module'].'includes/';
    $g['url_host'] = 'http'.($g['https_on'] ? 's' : '').'://'.$_SERVER['HTTP_HOST'];
    $g['socketioUrl'] = 'ssl://'.$_SERVER['HTTP_HOST'];
    $g['socketioPort'] = 3000;

    include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
    require_once $g['dir_module'].'var/define.path.php';
    require_once $g['dir_module'].'includes/reserve.class.php';
    
    class botAPI {
        public $mbruid;
        public $vendor;
        public $bot;
        public $botid;
        public $dialog;
        public $botName;
        public $bot_avatar;
        public $use_chatting;
        public $oSocket;
        public $fromPhone;
        public $roomToken;
        public $accessToken;
        public $bargein;
    	public $next_status;
    	public $r_data;
    	public $aOutputs;
    	public $intentMV;
    	public $aAutoHangup;

    	public function __construct(){
    		global $g, $chatbot;
    		$this->bargein = false;
    		$this->next_status = array('action'=>'recognize');
    		$this->r_data = array();
    		$this->aOutputs = array();
    		$this->aAutoHangup = array('nokeyin'=>3, 'noinput'=>5);
    	}
    	
    	public function pushText($data) {
    	    $data = preg_replace('/<br>|<br \/>|<br\/>/',' ',$data);
    	    $data = preg_replace('/\r\n|\r|\n/',' ', $data);
    		$this->aOutputs[] = addslashes($data);
    	}

        public function pushMenuRespond($_data) {
    		global $chatbot;

    		$result = $_data['result'];
    		$data = $_data['data'];

    		foreach ($result as $item) {
    			for($i=0, $nCnt=count($item); $i<$nCnt; $i++){
    				$type = $item[$i]['type'];
    				$content = $item[$i]['content'];
    				$this->bargein = isset($item[$i]['bargein']) && $item[$i]['bargein'] ? $item[$i]['bargein'] : $this->bargein;
    			    $this->next_status = isset($item[$i]['next_status']) && $item[$i]['next_status'] ? $item[$i]['next_status'] : $this->next_status;
    			    $this->r_data = isset($item[$i]['r_data']) && $item[$i]['r_data'] ? $item[$i]['r_data'] : $this->r_data;
    				if($type=='text' || $type=='form'){
    					$this->pushText($content);
    				}else if($type=='node'){
    					$data['node'] = $content;
    					$this->getNodeRespond($data);
    			    }
    			}
    		}
    	}
    	
    	public function getCallRespond($data) {
    	    global $g, $chatbot;
    	    
    	    $_data = $result = $_response = array();
    	    
    	    $result['result'] = false;
    	    $_response['type'] = "text";
    	    $_response['bargein'] = false;
            
            if($data['msg'] == "nokeyin") {
                // keyin 데이터값이 넘어오지 않았을 경우
                $this->r_data['no_keyin_cnt'] = !isset($data['r_data']['no_keyin_cnt']) ? 2 : ($data['r_data']['no_keyin_cnt']+1);
                if($this->r_data['no_keyin_cnt'] >= $this->aAutoHangup['nokeyin']) {
                    $result['result'] = true;
                    $_response['content'] = "죄송합니다. 응답이 없어 통화를 종료합니다.";
                    $_response['next_status'] = array('action'=>'hangup');
                    $result['data'][] = $_response;
                } else {
                    $result['result'] = true;
                    $_response['content'] = $data['r_data']['content'];
                    $_response['next_status'] = $data['r_data']['next_status'];
                    $result['data'][] = $_response;
                }
                
            } else {
                $_data['vendor'] = $this->vendor;
                $_data['bot'] = $this->bot;
                $_data['bReserve'] = true;
                $_data['clean_input'] = $data['msg'];
                if(isset($data['r_data']['able_weeks']) && count($data['r_data']['able_weeks']) > 0) {
                    $_data['able_weeks'] = $data['r_data']['able_weeks'];
                }
                
                $oDate = new DateParse();
                $DT = $oDate->getDateParse($_data);
                
                $result['temp_sys_date'] = (int)$DT['month'] && (int)$DT['day'] ? $DT['year']."-".$DT['month']."-".$DT['day'] : ($data['r_data']['sys_date'] ? $data['r_data']['sys_date'] : "");
                $result['temp_sys_week'] = $DT['weekday'] ? $DT['weekday'] : ($data['r_data']['sys_week'] ? $data['r_data']['sys_week'] : "");
                $result['temp_sys_time'] = (int)$DT['hour'] ? $DT['hour'].":".$DT['minute'] : ($data['r_data']['step'] != "time" && $data['r_data']['sys_time'] ? $data['r_data']['sys_time'] : "");
            
                $IS = $chatbot->getSimilarityIntent($_data);
                if($IS['intentName']) {
                    $_intentName = $IS['intentName'];
                    $_intentScore = $IS['score'];
                } else {
                    $I = $chatbot->getSentenceIntent($_data);
                    if($I['intentName']) {
                        $_intentName = $I['intentName'];
                        $_intentScore = $I['score'];
                    }
                }
                if($_intentName && $_intentScore >= $chatbot->intentMV) {
                    $result['intentName'] = $_intentName;
                    
                    if($_intentName == "시스템-반복") {
                        $userChat = array('printType'=>'T', 'userId'=>$this->fromPhone, 'content'=>$data['msg']);
                        $userLastChat = $chatbot->addChatLog($userChat);
                
                        $result['result'] = true;
                        $result['is_repeat'] = true;
                        $_response['content'] = $data['r_data']['content'];
                        $_response['next_status'] = $data['r_data']['next_status'];
                    }
                    if($_intentName == "시스템-통화종료" && (!isset($data['r_data']['step']) || $data['r_data']['step'] == "finish")) {
                        $userChat = array('printType'=>'T', 'userId'=>$this->fromPhone, 'content'=>$data['msg']);
                        $userLastChat = $chatbot->addChatLog($userChat);
                        
                        $result['result'] = true;
                        $_response['content'] = "감사합니다. 좋은 하루 보내세요.";
                        $_response['next_status'] = array("action"=>"hangup");
                    }
                    
                    if($userLastChat['last_chat'] && $_response['content']) {
                        $botChat = array();
                        $botChat['content'] = array(array("hform", $_response['content']));
                        $botChat['last_chat'] = $userLastChat['last_chat'];
                        $chatbot->addBotChatLog($botChat);
                    }
                    
                    $result['data'][] = $_response;
                }
            }
            return $result;
    	}

    	public function getNodeRespond($data){
    		global $g, $chatbot, $reserve, $table, $m;
    		
    		$data['roomToken'] = $this->roomToken;
            $data['botId'] = $this->botid;
            $data['userId'] = $this->fromPhone;
            $data['accessToken'] = $this->accessToken;
            $data['r_data'] = $this->r_data;
            $data['r_data']['uphone'] = $this->fromPhone; // 발신폰번호
            
            $unknown = $res_end = $bargein = $is_repeat = $is_reserve = false;
            
            // 반복 요청 등 전화상 문장 확인하기 (반복 의도문은 미리 학습되어 있어야함)
            $_callRespond = $this->getCallRespond($data);
            unset($data['r_data']['able_weeks'], $this->r_data['able_weeks']);
            
            if($_callRespond['result']) {
                $is_repeat = $_callRespond['is_repeat'] ? $_callRespond['is_repeat'] : $is_repeat;
                $result = $_callRespond['data'];
            } else {
                if(isset($data['r_data']) && $data['r_data']['action']) {
                    $data['r_data']['intentName'] = $_callRespond['intentName'];
                    $data['r_data']['temp_sys_date'] = $_callRespond['temp_sys_date'];
                    $data['r_data']['temp_sys_week'] = $_callRespond['temp_sys_week'];
                    $data['r_data']['temp_sys_time'] = $_callRespond['temp_sys_time'];
                    
                    // 예약 시나리오 완료되지 않은 상황일 경우
                    if($data['r_data']['step'] != "finish") {
                        $is_reserve = true;
        		        
        		    // 예약 시나리오 완료된 상황에서 변경이 있을 경우
        		    } else {
        		        if($data['r_data']['action'] == "request" || $data['r_data']['action'] == "modify") {
        		            if($data['r_data']['intentName'] == "시스템-날짜시간변경") {
        		                $is_reserve = true;
        		                $data['r_data']['step'] = "ask_finish_modify";
        		            }
        		            if(preg_match("/확인|조회|검색/u", $data['msg'])) {
        		                $is_reserve = true;
        		                $data['r_data']['step'] = "ask_finish_search";
        		            }
        		        }
        		        if($data['r_data']['action'] == "cancel") {
        		            if(preg_match("/새로|신규|다시/u", $data['msg'])) {
        		                $is_reserve = true;
        		                $data['r_data']['step'] = "ask_finish_new";
        		            }
        		        }
        		    }
        		}
        		
        		//===== call_log input
        		//getCallbotLog('stt_request', $data['msg']);
                
        		if($is_reserve) {
        		    $result = $reserve->getReserveResponse($data);
        		} else {
        		    $result = $chatbot->getApiResponse($data);
        		}
        	}
    		
    		foreach ($result as $resItem) {
    			$type = $resItem['type'];
    			$content = $resItem['content'];
    			$unknown = isset($resItem['unknown']) && $resItem['unknown'] ? $resItem['unknown'] : $unknown;
    			$res_end = isset($resItem['res_end']) && $resItem['res_end'] ? $resItem['res_end'] : $res_end;

    			if($type=='text'){
    			    $this->bargein = isset($resItem['bargein']) && $resItem['bargein'] ? $resItem['bargein'] : $this->bargein;
    			    $this->next_status = isset($resItem['next_status']) && $resItem['next_status'] ? $resItem['next_status'] : $this->next_status;
    			    $this->r_data = isset($resItem['r_data']) && $resItem['r_data'] ? $resItem['r_data'] : $this->r_data;
    				$this->pushText($content);
    			}else if($type=='if'){
    				$_data = array();
    				$_data['data'] = $data;
    				$_data['result'] = $content;
    				$this->pushMenuRespond($_data);
    			}
    		}
    		
    		$response = $r_data_set = "";
    		if (count($this->aOutputs) == 0) {
    			$response .="죄송합니다. 문의하신 내용의 답변을 찾을 수가 없습니다.";
    		} else {
        		foreach($this->aOutputs as $data) {
        		    $response .=$data." ";
        		}
        	}
        	
        	// ARS일 경우 hangup 처리
        	if($this->next_status['action'] == 'ars') {
        	    $ars_link = $this->next_status['value'];
        	    $this->next_status = array('action'=>'hangup');        	    
        	}
        	
            if(!$is_repeat) {
                if(isset($this->r_data['action'])) {
                    $r_data = $this->next_status['action'] == "hangup" ? "" : json_encode($this->r_data, JSON_UNESCAPED_UNICODE);
                    $r_data_set = "r_data='".$r_data."'";
                } else {
                    // 시나리오 아닌 경우 next_status값만 저장
                    $_r_data = array();
                    $_r_data['content'] = $response;
                    $_r_data['bargein'] = $this->bargein;
                    $_r_data['next_status'] = $this->next_status;
                    $r_data = json_encode($_r_data, JSON_UNESCAPED_UNICODE);
                    $r_data_set = "r_data='".$r_data."'";
                }
                getDbUpdate($table[$m.'token'], $r_data_set, "bot='".$this->bot."' and access_mod='callInput' and access_token='".$this->accessToken."'");
            }
        	
        	$_result = array();
        	$_result['response'] = trim($response);
        	$_result['next_status'] = $res_end ? array('action'=>'hangup') : $this->next_status;
        	$_result['bargein'] = $this->bargein;
        	$_result['r_data'] = $this->r_data;
        	if(isset($ars_link) && $ars_link) {
        	    $_result['ars_link'] = $ars_link;
        	}
        	
    		return $_result;
    	}
    	
    	public function removeExpireToken($data) {
    	    global $table, $m;
    	    
    	    if($data['del_type'] == "expire") {
        	    $_now = time();
        	    $del_q = "access_mod='callInput' and expire < '".$_now."'";
        	    $RCD = getDbArray($table[$m.'token'], $del_q, 'uid, roomToken, userId', 'uid', 'asc', '', 1);
        	    while ($R = db_fetch_array($RCD)) {
        	        getDbDelete($table[$m.'token'], "uid='".$R['uid']."'");
        	    }
        	} else {
        	    $bot = $data['bot'] ? $data['bot'] : $this->bot;
        	    $fromPhone = $data['userId'] ? $data['userId'] : $this->fromPhone;
        	    
        	    $R = getDbData($table[$m.'token'], "bot='".$bot."' and access_mod='callInput' and userId='".$fromPhone."'", "uid, roomToken, userId");
        	    if($R['uid']) {
        	        if(!isset($data['no_send']) || $data['no_send'] != true) {
            	        $aSockData = array();
            	        $aSockData['fromPhone'] = $R['userId'];
            	        $aSockData['roomToken'] = $R['roomToken'];
            	        $aSockData['role'] = "disconnect";
            	        sendWebSocket($aSockData);
            	    }
        	        getDbDelete($table[$m.'token'], "uid='".$R['uid']."'");
        	    }
        	}
    	}
    	
    	public function checkToken($data) {
    	    global $table, $m;
    	    
    	    $expire = time()+(60*10); // 10분
    	    $_now = time();
    	    
    	    // 유효기간 지난것 삭제
    	    $data['del_type'] = "expire";
            $this->removeExpireToken($data);
    	    
    	    if($data['mode'] == 'create') {
    	        // 같은 폰 토큰 삭제
    	        $data['del_type'] = "phone";
                $this->removeExpireToken($data);
    	        
        	    $_QKEY = "bot, access_mod, access_token, roomToken, userId, expire, r_data";
                $_QVAL = "'".$data['bot']."','callInput','".$data['accessToken']."', '".$data['roomToken']."', '".$data['userId']."', '".$expire."', ''";
                getDbInsert($table[$m.'token'],$_QKEY,$_QVAL);
                
            } else if($data['mode'] == 'search') {
                $_wh = "A.access_mod='callInput' and A.access_token='".$data['accessToken']."'";
                $_table = $table[$m.'token']." A left join ".$table[$m.'bot']." B on A.bot = B.uid left join ".$table[$m.'botSettings']." C on A.bot = C.bot and C.name = 'use_chatting' ";
                $R = getDbData($_table, $_wh, "A.*, B.vendor, B.name, B.mbruid, B.id as botid, C.value as use_chatting");
                if($R['uid']) {
                    getDbUpdate($table[$m.'token'], "expire='".$expire."'", "uid='".$R['uid']."'");
                }
                
                $_data = array();
                $_data['mbruid'] = $R['mbruid'];
                $_data['name'] = $R['name'];
                $_data['vendor'] = $R['vendor'];
                $_data['bot'] = $R['bot'];
                $_data['botid'] = $R['botid'];
                $_data['roomToken'] = $R['roomToken'];
                $_data['userId'] = $R['userId'];
                $_data['use_chatting'] = $R['use_chatting'];
                $_data['r_data'] = $R['r_data'] ? json_decode($R['r_data'], true) : '';
                return $_data;
            }
    	}
        
        public function getCheckHeader($data) {
            global $table, $m, $g, $_db_bot, $DB, $DB_CONNECT, $chatbot;
            
            $result = array();
            $result["result"] = true;
            
            if($data['mode'] == "auth") {
                $aHost = explode(".", $_SERVER['HTTP_HOST']);
                
                if (!isset($data['headers']['X-Bottalks-Bot-Id']) || $data['headers']['X-Bottalks-Bot-Id'] != $aHost[0]) {
                    $result['result'] = false;
                    $result['message'] = "Invalid bot id.";
                }
                if (!isset($data['headers']['X-Bottalks-Role']) || $data['headers']['X-Bottalks-Role'] != "callbot") {
                    $result['result'] = false;
                    $result['message'] = "Invalid role.";
                }        
                if ($result['result'] == false) {
                    $result['code'] = 400;
                } else {
                    // dev쪽으로 db 정보 문의
                    $botID = $data['headers']['X-Bottalks-Bot-Id'];
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
                        if (!$dbinfo['mbruid']) {
                            $result['result'] = false;
                            $result['message'] = "Invalid botId.";
                            $result['code'] = 401;
                        } else {
                            $_SESSION['mbr_db'] = "bot_user".$dbinfo['mbruid'];
                            require $g['path_var'].'db.info.php';
                            $DB_CONNECT = isConnectedToDB($DB);
                            
                            // botId 로 관련 데이타 추출
                            $B = $chatbot->getBotDataFromId($botID);
                            if(!$B['bot']) {
                                $result['result'] = false;
                                $result['message'] = "Invalid botId.";
                                $result['code'] = 401;
                            } else {
                                $accessToken = "";
                                while (strlen($accessToken) < 64) $accessToken .= chr(mt_rand(0, 255));
                                $accessToken = bin2hex($accessToken).md5(uniqid(mt_rand()))."-".$dbinfo['mbruid'];
                                $roomToken = md5(uniqid(mt_rand()));        
                                $_data = array('mode'=>'create', 'bot'=>$B['bot'], 'accessToken'=>$accessToken, 'roomToken'=>$roomToken, 'userId'=>$data['userId']);
                                $this->checkToken($_data);                                
                                
                                $this->mbruid = $chatbot->mbruid = $B['mbruid'];
                                $this->vendor = $chatbot->vendor = $B['vendor'];
                                $this->bot = $chatbot->botuid = $B['bot'];
                                $this->botid = $chatbot->botid = $botID;
                                $this->dialog = $chatbot->dialog = $B['dialog'];
                                $this->fromPhone = $chatbot->userId = $data['userId'];
                                $this->roomToken = $chatbot->roomToken = $roomToken;
                                $this->botName = $B['name'];
                                $this->use_chatting = $B['use_chatting'];
                                $this->accessToken = $accessToken;
                                
                                $_R = getUidData($table[$m.'bot'], $this->bot);
                                $this->bot_avatar = $chatbot->getBotAvatarSrc($_R);
                                
                                if($this->use_chatting == "on") {
                                    $this->oSocket = new SocketIO($g['socketioUrl'], $g['socketioPort'], '/socket.io/?EIO=3');
                                }
                            }
                        }
                    } else {
                        $result['result'] = false;
                        $result['message'] = "Invalid botId.";
                        $result['code'] = 401;
                    }
                }
                return $result;
                
            } else if($data['mode'] == "chatbot") {
                
                if (!isset($data['headers']['X-Bottalks-Token']) || !$data['headers']['X-Bottalks-Token']) {
                    $result['result'] = false;
                    $result['message'] = "Invalid Access Token.";
                    $result['code'] = 401;
                } else {
                    $accessToken = $data['headers']['X-Bottalks-Token'];
                    
                    $aToken = explode("-", $accessToken);
                    $mbruid = end($aToken);
                    $_SESSION['mbr_db'] = "bot_user".$mbruid;
                    require $g['path_var'].'db.info.php';
                    $DB_CONNECT = isConnectedToDB($DB);
                            
                    $_data = array('mode'=>'search', 'accessToken'=>$accessToken);
                    $_tdata = $this->checkToken($_data);
                    if(!$_tdata['roomToken']) {
                        $result['result'] = false;
                        $result["message"] = "Invalid Access Token.";
                        $result['code'] = 401;
                    } else {
                        $d_wh = "vendor=".$_tdata['vendor']." and bot=".$_tdata['bot']." and gid=0 and active=1 and type='D'";
                        $D = getDbData($table[$m.'dialog'],$d_wh,'uid');
                        
                        $this->mbruid = $chatbot->mbruid = $mbruid;
                        $this->vendor = $chatbot->vendor = $_tdata['vendor'];
                        $this->bot = $chatbot->botuid = $_tdata['bot'];
                        $this->botid = $chatbot->botid = $_tdata['botid'];
                        $this->dialog = $chatbot->dialog = $D['uid'];
                        $this->fromPhone = $chatbot->userId = $_tdata['userId'];
                        $this->roomToken = $chatbot->roomToken = $_tdata['roomToken'];
                        $this->botName = $_tdata['name'];
                        $this->use_chatting = $_tdata['use_chatting'];
                        $this->accessToken = $accessToken;
                        $this->r_data = $_tdata['r_data'];
                        
                        $_R = getUidData($table[$m.'bot'], $this->bot);
                        $this->bot_avatar = $chatbot->getBotAvatarSrc($_R);
                        
                        if($this->use_chatting == "on") {
                            $this->oSocket = new SocketIO($g['socketioUrl'], $g['socketioPort'], '/socket.io/?EIO=3');
                        }
                        
                        //===== call_log input
                        //getCallbotLog('check_header', '');
                    }
                }
                return $result;
            }
        }
        
        function setLogWrite($log) {
            $t = microtime(true);
            $micro = sprintf("%06d",($t - floor($t)) * 1000000);
            $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );            
            $dTime = $d->format("Y-m-d H:i:s.u");
            
            $log = $dTime." ".$_SERVER['REMOTE_ADDR']." ".$log;
            file_put_contents($_SERVER['DOCUMENT_ROOT']."/_tmp/cache/callbot.txt", $log."\n\n", FILE_APPEND);
        }
    }
    //--------------------------------------------------------------------
    $chatbot = new Chatbot();
    $reserve = new Reserve();
    $botAPI = new botAPI();
    
    function echoResponse($status_code, $response) {
        global $botAPI;
        
        $app = \Slim\Slim::getInstance();
        $app->status($status_code);
        if(is_array($response)){
            $app->contentType('application/json');  
            $json_encode = json_encode($response,JSON_UNESCAPED_UNICODE); 
            $json_encode = stripcslashes($json_encode);
            $json_encode = str_replace("[\"",'[',$json_encode);
            $json_encode = str_replace("}\"",'}',$json_encode);
            $json_encode = str_replace("\"{",'{',$json_encode);
            $response = stripcslashes($json_encode);
        }else{
            $app->contentType('text/html');
        }
        
        ob_start();
        echo $response;
        
        ob_flush();
        ob_end_clean();
        flush();
        
        //$log = "response : ".$response;
        //$botAPI->setLogWrite($log);
    }
    
    function sendARSLink($msg) {
        global $g, $table, $m, $botAPI;
        
        if($botAPI->fromPhone) {
            $R = getDbData($table['s_mbrcomp'], "memberuid='".$botAPI->mbruid."'", 'comp_name');
            $subject = $R['comp_name'] ? $R['comp_name'] : $botAPI->botName;
            
            $chByteMsg = iconv("utf-8", "euc-kr", str_replace("\n", "", $msg));
            $nByte = strlen($chByteMsg);
            $chSType = $nByte > 80 ? "L" : "S";
            $smsResult = getSendSMS_Cafe24("get_send_sms", $botAPI->fromPhone, $msg, $subject, $chSType);
        }
    }
    
    function sendWebSocket($data) {
        global $g, $table, $m, $botAPI, $chatbot;
        
        if($botAPI->use_chatting == "on" && $botAPI->oSocket) {
            $_data = array();
            $_data['type'] = "user";
            $_data['bottype'] = "call";
            $_data['vendor'] = $botAPI->vendor;
            $_data['bot'] = $botAPI->bot;
            $_data['roomToken'] = $data['roomToken'] ? $data['roomToken'] : $botAPI->roomToken;
            $_data['phone'] = $data['fromPhone'] ? $data['fromPhone'] : $botAPI->fromPhone;
            $_data['role'] = $data['role'];
            $_data['date'] = date('y.m.d').' '.(date('a') == 'am' ? '오전 ':'오후 ').date('g').':'.date('i');
            $_data['bot_avatar'] = $botAPI->bot_avatar;
            if($data['sender']) $_data['sender'] = $data['sender'];
            if($data['msg']) $_data['msg'] = $data['msg'];
            if($data['log']) $_data['log'] = $data['log'];
            $_data = json_encode($_data);    
            $botAPI->oSocket->emit('send_data', $_data);
        }
    }
    
    function getCallbotLog($utype, $msg) {
        global $g, $table, $m, $botAPI, $chatbot;
        
        $_QKEY = "bot, roomToken, utype, msg, d_regis";
        $_QVAL = "'".$botAPI->bot."','".$botAPI->roomToken."','".$utype."', '".$msg."', now()";
        //getDbInsert("rb_chatbot_calllog",$_QKEY,$_QVAL);
    }
    
    // 챗봇 인증용 : 접속 인증 및 토큰, roomToken 리턴
    $app->post('/auth', function() use ($app) {
        global $chatbot, $botAPI, $reserve, $m, $table, $g, $_db_bot, $DB, $DB_CONNECT;
        
        $response = array();
        $response["result"] = true;
        
        $body = json_decode($app->request->getBody(), true);
        
        $fromPhone = $body['fromPhone'];
        if (!$fromPhone || !is_numeric($fromPhone)) {
            $response["result"] = false;
            $response["message"] = 'Invalid Phone number.';
            echoResponse(401, $response);
            $app->stop();
        }
        
        // header check
        $headers = $app->request->headers;
        $_data = array('mode'=>'auth', 'userId'=>$fromPhone, 'headers'=>$headers);
        $aCheckHeader = $botAPI->getCheckHeader($_data);
        if(!$aCheckHeader['result']) {
            $response["result"] = $aCheckHeader['result'];
            $response["message"] = $aCheckHeader['message'];
            echoResponse($aCheckHeader['code'], $response);
            $app->stop();
        }
        
        //$log = "------------------------------------------------------";
        //$botAPI->setLogWrite($log);
        
        $data = array();        
        $data['api'] = true;
        $data['msg'] = 'hi';
        $data['msg_type'] = 'say_hello';
        $botResult = $botAPI->getNodeRespond($data);
        
        $response["result"] = true;
        $response["accessToken"] = $botAPI->accessToken;
        $response["roomToken"] = $botAPI->roomToken;
        $response['message'] = $botResult['response'];
        $response['next_status'] = $botResult['next_status'];
        $response['barge_in'] = $botResult['bargein'];
        echoResponse(200, $response);
        
        $aSockData = array();        
        $aSockData['role'] = "new_client";
        sendWebSocket($aSockData);
        
        $aSockData = array();
        $aSockData['role'] = "call_log_send";
        $aSockData['log'] = array();
        $aSockData['log'][] = array("sender"=>"bot", "msg"=>$botResult['response']);
        sendWebSocket($aSockData);
    });

    // 챗봇 대화용 : header['access_token'], roomtoken, 메시지
    $app->post('/chatbot', function() use ($app) {
        global $chatbot, $botAPI, $reserve, $g, $_db_bot, $DB, $DB_CONNECT;
        
        $response = array();
        $response["result"] = true;
        
        $body = json_decode($app->request->getBody(), true);
        
        //$log = "request : ".json_encode($body, JSON_UNESCAPED_UNICODE);
        //$botAPI->setLogWrite($log);
        
        // header check
        $headers = $app->request->headers;
        $_data = array('mode'=>'chatbot', 'headers'=>$headers);
        $aCheckHeader = $botAPI->getCheckHeader($_data);
        if(!$aCheckHeader['result']) {
            $response["result"] = $aCheckHeader['result'];
            $response["message"] = $aCheckHeader['message'];
            echoResponse($aCheckHeader['code'], $response);
            $app->stop();
        }
        
        // param check
        $roomToken = $body['roomToken'];
        $message = $chatbot->verifyUserInput($body['message']);
        if($roomToken == "") {
            $response["result"] = false;
            $response["message"] = "roomToken not found.";
            echoResponse(401, $response);
            $app->stop();
        }
        if($message == "") {
            // 사용자 발화 없을 경우 (keyin의 경우는 직전 응답 재전송)
            if(isset($botAPI->r_data['next_status']) && $botAPI->r_data['next_status']['action'] == "keyin") {
                $message = "nokeyin";
            } else {
                unset($botAPI->r_data['no_keyin_cnt']);
                $response["result"] = false;
                $response["message"] = "Message not found.";
                echoResponse(401, $response);
                $app->stop();
            }
        }
        
        // hangup일 경우 종료
        if($message == 'hangup') {
            $_data = array();
            $_data['del_type'] = "phone";
            $botAPI->removeExpireToken($_data);
            exit;
        }
        
        //===== call_log input
        //getCallbotLog('stt_input', $message);
        
        $data = array();
        $data['api'] = true;
        $data['msg'] = $message;
        $data['msg_type'] = ($message == 'noinput' || $message == 'sttfail') ? $message : 'text';
        $botResult = $botAPI->getNodeRespond($data);
        
        if($botResult['response']) {
            $response["result"] = true;
            $response['message'] = $botResult['response'];
            $response['next_status'] = $botResult['next_status'];
            $response['barge_in'] = $botResult['bargein'];
            echoResponse(200, $response);
            
            //===== call_log input
            //getCallbotLog('stt_response', $response['message']);
            
            // ARS 문자 전송
            if(isset($botResult['ars_link']) && trim($botResult['ars_link'])) {
                $ars_msg = "아래의 주소로 접속하여 주세요.\n".trim($botResult['ars_link']);
                sendARSLink($ars_msg);
            }

            // 모니터링 봇응답 로그
            $aSockData = array();
            if($response['next_status']['action'] == 'hangup') {
                $_data = array();
                $_data['del_type'] = "phone";
                $_data['no_send'] = true;
                $botAPI->removeExpireToken($_data);
                
                $aSockData['role'] = "disconnect";
            } else {
                $aSockData['role'] = "call_log_send";
                $aSockData['log'][] = array("sender"=>"user", "msg"=>$message);
                $aSockData['log'][] = array("sender"=>"bot", "msg"=>$botResult['response']);
            }
            sendWebSocket($aSockData);
        }
    });
    
    // 콜봇 옵션 처리
    $app->post('/:action_mod/:type',function($action_mod, $type) use ($app) {
        global $chatbot, $botAPI, $m, $table, $g, $_db_bot, $DB, $DB_CONNECT;
        
        $mod = $action_mod;		
		if(!$mod || !$type) {
		    echo "Not exists parameters."; exit;
		}
		$body = trim($app->request()->getBody());
		if(!$body) {
		    echo "No data."; exit;
		}
		
		$aPostData = json_decode($body, true);
		
		// 콜봇용 TTS 옵션
		if($mod == 'callbot_option') {
		    $result = array();
		    
		    $botid = $aPostData['botId'];
		    if(!$botid) {
		        $result['result'] = false;
		        $result['message'] = "Not exists botId.";
		    } else {
		        if(!isset($_SESSION['mbr_db']) || !$_SESSION['mbr_db']) {
		            // dev쪽으로 db 정보 문의
		            $apiURL = $g['front_host'].'/api/v1/account_info/'.$botid;
		            
		            $ch = curl_init();
		            curl_setopt($ch, CURLOPT_URL, $apiURL);
		            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		            $response = curl_exec($ch);
		            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		            curl_close ($ch);
		            
		            $dbinfo = json_decode($response, true);
		            if (!$dbinfo['mbruid']) {
		                $result['result'] = false;
		                $result['message'] = "Invalid botId.";
		            } else {
		                $_SESSION['mbr_db'] = "bot_user".$dbinfo['mbruid'];
		            }
        	    }
        	    require $g['path_var'].'db.info.php';
                $DB_CONNECT = isConnectedToDB($DB);
                
    		    if($type == "auth") {
        	        $token = $aPostData['accessToken'];
        	        
        	        if(!$token) {
        	            $result['result'] = false;
        	            $result['message'] = "Not exists token.";
        	        } else {        	        
        		        $aToken = getDbData("rb_chatbot_token", "access_mod='".$mod."' and access_token='".$token."'", "*");
        		        if (!$aToken['uid']) {
        	                $result['result'] = false;
        	                $result['message'] = "Invalid token.";
        	            } else {
        	                $result['result'] = true;
        	                $result['botId'] = $botid;
        	                
        	                // tts 옵션 정보
        	                $bot = $aToken['bot'];
        	                
        	                $_wh = "bot=".$bot." and name like 'tts_%'";
        	                $RCD = getDbArray('rb_chatbot_botSettings', $_wh, 'name, value', 'uid', 'asc', '', 1);    	                
        	                while ($R = db_fetch_array($RCD)) {
        	                    if($R['name'] == "tts_audio") {
        	                        $result['ttsWave'] = $R['value'];
        	                    } else if($R['name'] == "tts_pitch") {
        	                        $result['ttsPitch'] = $R['value'];
        	                    } else if($R['name'] == "tts_speed") {
        	                        $result['ttsSpeed'] = $R['value'];
        	                    }
        	                }    	                
                        }
                    }
                }
                
                if($type == "result") {
                    $apiResult = $aPostData['result'];
                    $_result = $apiResult == true ? 1 : -1;
                    
                    $aBot = getDbData("rb_chatbot_bot", "id='".$botid."'", "*");
                    getDbUpdate("rb_chatbot_token", "expire='".$_result."'", "bot='".$aBot['uid']."' and access_mod='callbot_option'");
                }
            }
            
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit;
		}
    });
    
    $app->run();
?>