<?php
    //session_start();
    define('Rb_root',dirname(dirname(__FILE__)).'../../');
    define('Rb_path','../../');
    //error_reporting(E_ALL ^ E_NOTICE);
    error_reporting(E_ERROR);
    ini_set('display_errors', 1);

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
    $date['totime'] = date('YmdHis');

    $chConfDir = './configuration';

    // host 도메인 관련 (벤더 시스템에만 적용)
    if('localhost' === $_SERVER['SERVER_NAME']){
        include_once $chConfDir.'/env-local.php';
    }
    elseif('bottalks.nexuscommunity.kr' === $_SERVER['SERVER_NAME']){
        include_once $chConfDir.'/env-dev.php';
    }
    elseif('61.250.39.72' === $_SERVER['SERVER_ADDR']){
        include_once $chConfDir.'/env-stage.php';
    }
    else{
        $chConfDir = substr($_SERVER['DOCUMENT_ROOT'], 0, strrpos($_SERVER['DOCUMENT_ROOT'], "/"));
        include_once $chConfDir.'/bottalksConf.php';
    }

    function getErrorJSON($bResult, $bResultMsg="", $bResultData="") {
    	$aArray = array("bResult"=>$bResult, "bResultMsg"=>$bResultMsg, "bResultData"=>$bResultData);
    	return json_encode($aArray);
    }
    
    function getTokenAccessAuth($chUrl, $mod, $sid, $token) {
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

    // Slim REST API 체크
    require '../libs/Slim/Slim.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    
    // 봇템플릿 복사
    $app->post('/:dbname/:mbruid',function($dbname,$mbruid) use ($app) {
        global $g, $m, $date, $table, $_db_bot, $_db_sys, $_db_front, $DB_CONNECT;
        
        $aPostData = json_decode($app->request()->getBody(), true);
        $vendor = $aPostData['vendor'];
        $botid = $aPostData['botid'];
        $botname = $aPostData['botname'];
        $targetBot = $aPostData['targetBot'];
        $bottype = $aPostData['category'] == "callbot" ? "call" : "chat";
        $user_uid = $aPostData['user_uid'];
        $c_uid = $aPostData['c_uid'];
        $paid = $aPostData['usetype'] == "paid" ? 1 : 0;
        
        if(!$dbname || !$mbruid || !$vendor || !$botid) {
            echo 'botid not exists.'; exit;
        }
        
        $_SESSION['mbr_db'] = $dbname;
        
        require $g['path_var'].'db.info.php';
        require $g['path_var'].'table.info.php';
        require $g['path_core'].'function/db.mysql.func.php';
        require $g['path_core'].'function/sys.func.php';
      
        $DB_CONNECT = isConnectedToDB($DB);
        
        $g['dir_module'] = $g['path_module'].$m.'/';
        $g['dir_include'] = $g['dir_module'].'includes/';
        
        include $g['dir_module'].'includes/base.class.php';
        include $g['dir_module'].'includes/module.class.php';
        
        $chatbot = new Chatbot();
        $BT = new botTemp();
        
        $_data = array();   	
       	$_data['vendor'] = $vendor;
       	$_data['mbruid'] = $mbruid;
       	$_data['botId'] = $botid;
       	$_data['botname'] = $botname;
       	$_data['user_uid'] = $user_uid;
       	$_data['c_uid'] = $c_uid; // 프론트 사이트 상품분류 번호
       	$_data['paid'] = $paid; // 봇 유무료 여부
       	
       	$lastBot = '';
        
        if(!$targetBot) {
    		$name = $botname ? $botname : '처음부터 시작하기';
    		$avatar = "";
    		$induCat = 11;
    		$intro = "";
    		$service = "";
    		$auth = $type = $display = 1;
    		$hidden = $is_temp	= 0;
    		$language = 'KOR';
    		$d_regis = $date['totime'];
    		
    		// 전체 table column 체크
    		$BT->getSysColsCheck();
    		
    		$mingid = getDbCnt($table[$m.'bot'],'min(gid)','');
    		$gid = $mingid ? $mingid-1 : 1000000000;
    		
    		$QKEY = "bottype, is_temp,gid,type,auth,vendor,induCat,hidden,display,name,service,intro,website,boturl,mbruid,id,content,html,tag,lang,hit,likes,report,point,d_regis,d_modify,avatar,upload,user_uid,c_uid,paid";
            $QVAL = "'$bottype', '$is_temp','$gid','$type','$auth','$vendor','$induCat','$hidden','$display','$name','$service','$intro','$website','$boturl','$mbruid','$botid','$content','$html','$tag','$language','$hit','$likes','$report','$point','$d_regis','$d_modify','$avatar','$upload','$user_uid','$c_uid','$paid'";
            getDbInsert($table[$m.'bot'],$QKEY,$QVAL);
            $lastBot = getDbCnt($table[$m.'bot'],'max(uid)','');
    		
    		// system 리소스 업데이트
    		$BT->updateSysResource($_data);
            
        } else {
            
            // 전체 table column 체크
    		$BT->getSysColsCheck();
            
        	$BT->dbTargetMod = 'sys';
            $BT->dbTargetServer = 'sys.chatbot';
            
        	$_data['targetBot'] = $targetBot;
            $lastBot = $BT->copyBot($_data);
            
            // system 리소스 업데이트
    		$BT->updateSysResource($_data);
            
            // 인텐트 재학습
            $_dd = array("vendor"=>$_data['vendor'], "bot"=>$lastBot);
            $chatbot->getTrainIntentPesoNLP($_dd);
        }
        echo $lastBot;
            
    });
    
    // 봇별 업로드 파일 총용량
    $app->post('/botStorage/:dbname/:mbr_uid/:botid',function($dbname, $mbr_uid, $botid) use ($app) {
        global $g, $m, $date, $table, $_db_bot, $_db_sys, $_db_front, $DB_CONNECT;
        
        if(!$dbname || !$mbr_uid || !$botid) {
            echo ''; exit;
        }
        
        $_SESSION['mbr_db'] = $dbname;
        
        require $g['path_var'].'db.info.php';
        require $g['path_var'].'table.info.php';
        require $g['path_core'].'function/db.mysql.func.php';
      
        $DB_CONNECT = isConnectedToDB($DB);
        
        $nBytesTotal = $nBytesFile = $nBytesModel = 0;
        
        $aBot = getDbData($table[$m.'bot'], "mbruid = '".$mbr_uid."' and id = '".$botid."'", "uid");
        $botUID = $aBot['uid'];
        if($botUID == '') exit;
        
        $fileDirectory = $g['path_file'].'chatbot/'.$mbr_uid.'/'.$botUID;        
        $path = realpath($fileDirectory);
        if($path != '' && file_exists($path)){
            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $file){
                $nBytesFile += $file->getSize();
            }
        }
        
        $modelDirectory = $g['path_file'].'trainData/'.$mbr_uid.'/'.$botUID;
        $path = realpath($modelDirectory);
        if($path != '' && file_exists($path)){
            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $file){
                $nBytesModel += $file->getSize();
            }
        }   
        $nBytesTotal = ($nBytesFile + $nBytesModel);
        echo $nBytesTotal;        
    });
    
    // 봇 유무료 업데이트
    $app->post('/botUseType/:dbname/:user_uid/:usetype',function($dbname, $user_uid, $usetype) use ($app) {
        global $g, $m, $date, $table, $_db_bot, $_db_sys, $_db_front, $DB_CONNECT;
        
        if(!$dbname || !$user_uid || !$usetype) {
            echo ''; exit;
        }
        
        $_SESSION['mbr_db'] = $dbname;
        
        require $g['path_var'].'db.info.php';
        require $g['path_var'].'table.info.php';
        require $g['path_core'].'function/db.mysql.func.php';
      
        $DB_CONNECT = isConnectedToDB($DB);
        
        $paid = $usetype == 'paid' ? 1 : 0;
        getDbUpdate($table[$m.'bot'], "paid='".$paid."'", "user_uid='".$user_uid."'");
        echo true;        
    });
    
    // 임베딩 챗봇 아이콘
    $app->get('/chatBtn/:dbname/:botid',function($dbname,$botid) use ($app) {
        global $g, $m, $date, $table, $_db_bot, $_db_sys, $_db_front, $DB_CONNECT;
        
        if(!$dbname || !$botid) {
            echo ''; exit;
        }
        
        $_SESSION['mbr_db'] = $dbname;
        
        require $g['path_var'].'db.info.php';
        require $g['path_var'].'table.info.php';
        require $g['path_core'].'function/db.mysql.func.php';
        
        $btnChatbot = '/_core/skin/images/btn_chatbot.png';
    	$pc_btn_bottom = '30px';
        $pc_btn_right = '70px';
        $m_btn_bottom = '25px';
        $m_btn_right = '20px';
      
        $DB_CONNECT = isConnectedToDB($DB);
        
        $query = "Select uid From rb_chatbot_bot ";
	    $query .="Where id='".$botid."' ";
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
        echo json_encode($aData);
            
    });
    
    // 봇 인트로, 응답메시지, FAQ
    $app->post('/setting/:dbname/:mbr_uid/:botid',function($dbname, $mbr_uid, $botid) use ($app) {
		global $g, $m, $date, $table, $_db_bot, $_db_sys, $_db_front, $DB_CONNECT;
        
        if(!$dbname || !$botid) {
            echo ''; exit;
        }
        
        $_SESSION['mbr_db'] = $dbname;
        
        require $g['path_var'].'db.info.php';
        require $g['path_var'].'table.info.php';
        require $g['path_core'].'function/db.mysql.func.php';
        require $g['path_core'].'function/sys.func.php';
      
        $DB_CONNECT = isConnectedToDB($DB);
        
        $g['dir_module'] = $g['path_module'].$m.'/';
        $g['dir_include'] = $g['dir_module'].'includes/';
        
        include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
        require_once $g['dir_module'].'var/define.path.php';
        
        $chatbot = new Chatbot();
        
        $aBot = getDbData($table[$m.'bot'], "mbruid = '".$mbr_uid."' and id = '".$botid."'", "*");
        if($aBot['vendor'] && $aBot['uid']) {
            $vendor = $aBot['vendor'];
            $bot = $aBot['uid'];
            
            // dialog Setting
            if($bot) {
                $data= array();
                $data['vendor'] = $vendor;
                $data['bot'] = $bot;
                $dialog = $chatbot->getVendorAdmDialog($data);
            }
            
            // file directory
            if(isset($_FILES) && count($_FILES) > 0) {
                $userBaseDir = $_SERVER['DOCUMENT_ROOT'];
                $saveDir = '/files/'.$m.'/'.$mbr_uid.'/'.$bot.'/';
                if($dialog) $saveDir .=$dialog.'/';
                $folder  = substr($date['totime'],0,4).'/'.substr($date['totime'],4,2).'/'.substr($date['totime'],6,2);
                $savePath = $saveDir.$folder;
                $path_arr = explode('/',$savePath);
                array_pop($savePath);
                $newPath = '';
                foreach($path_arr as $path) {
                    if($path != '') {
                        $newPath .=$path.'/';
                        if (!is_dir($userBaseDir.'/'.$newPath)){
                            mkdir($userBaseDir.'/'.$newPath,0707);
                            @chmod($userBaseDir.'/'.$newPath,0707);
                        }
                    }
                }
            }
            
            // intro
            if(isset($_POST['intro']) && count($_POST['intro']) > 0) {
                $aPostData = json_decode($_POST['intro'], true);
                
                $_data = array();
                $_data['data']['vendor'] = $vendor;
                $_data['data']['bot'] = $bot;
                $_data['nameArray'] = array();
                
                foreach($aPostData as $key=>$val) {
                    $value = trim($val);
                    
                    if($key == 'intro_menu_name') {
                        foreach($val as $menu) {
                            $QKEY = "vendor,bot,name,value";
                            $QVAL = "'$vendor','$bot','$key','$menu'";
                            getDbInsert($table[$m.'botSettings'],$QKEY,$QVAL);
                        }
                    } else if($key == 'reserve_domainkey') {
                        $_data['nameArray'][$key] = $chatbot->getReserveDomainKey();
                    } else {
                        $_data['nameArray'][$key] = addslashes($value);
                    }
                }                
                $chatbot->updateBotSettings($_data);                
                
                if(isset($_FILES['intro_profile_img'])) {
                    for ($i=0, $nCnt=count($_FILES['intro_profile_img']['name']); $i<$nCnt; $i++) {
                        $path = $savePath.'/'.$_FILES['intro_profile_img']['name'][$i];
                        move_uploaded_file($_FILES['intro_profile_img']['tmp_name'][$i], $userBaseDir.'/'.$path);
                        
                        $QKEY = "vendor,bot,name,value";
                        $QVAL = "'$vendor','$bot','intro_profile_img','$path'";
                        getDbInsert($table[$m.'botSettings'],$QKEY,$QVAL);
                    }
                }
                
                if(isset($_FILES['intro_logo_url'])) {
                    $path = $savePath.'/'.$_FILES['intro_logo_url']['name'];
                    move_uploaded_file($_FILES['intro_logo_url']['tmp_name'], $userBaseDir.'/'.$path);
                    
                    $_data['nameArray'] = array();
                    $_data['nameArray']['intro_logo_url'] = $path;
                    $chatbot->updateBotSettings($_data);
                }
            }
            
            // response
            if(isset($_POST['response']) && $_POST['response']) {                
                $aPostData = json_decode($_POST['response'], true);
                
                foreach($aPostData as $response) {
                    $row = json_decode($response, true);
                    $o_uid = trim($row['o_uid']);
                    $itemType = trim($row['itemtype']);
                    $resType = trim($row['restype']);
                    $content = addslashes(trim($row['content']));
                    if(!$content) continue;
                    
                    $tbl_name = $itemType=='RI'?'dialogResItem':'dialogResItemOC';
                    $tbl = $table[$m.$tbl_name];
                    
                    $QVAL = "";
                    
                    if($itemType =='RI'){
                        if($resType =='text') $QVAL= "content='".$content."'";
                        else if($resType =='img') {
                            if(isset($_FILES['response_img'])) {
                                $path = $savePath.'/'.$_FILES['response_img']['name'][$o_uid];
                                $result = move_uploaded_file($_FILES['response_img']['tmp_name'][$o_uid], $userBaseDir.'/'.$path);
                                if(!$result) continue;
                                $QVAL= "img_url='".$path."'";
                            }
                        }

                    }else if($itemType =='OC'){
                        if($resType =='text') $QVAL= "text_val='".$content."'";
                        else if($resType=='link') $QVAL= "varchar_val='".$content."'";
                        else if($resType =='img') {
                            if(isset($_FILES['response_img'])) {
                                $path = $savePath.'/'.$_FILES['response_img']['name'][$o_uid];
                                $result = move_uploaded_file($_FILES['response_img']['tmp_name'][$o_uid], $userBaseDir.'/'.$path);
                                if(!$result) continue;
                                $QVAL= "varchar_val='".$path."'";
                            }
                        }
                    }
                    
                    getDbUpdate($tbl, $QVAL, "bot=".$bot." and resType='".$resType."' and o_uid=".$o_uid);
                }
            }
            
            // FAQ
            if(isset($_POST['faq']) && $_POST['faq']) {
                $d_regis = date('YmdHis');
                
                $aPostData = json_decode($_POST['faq'], true);                
                foreach($aPostData as $faq) {
                    $row = json_decode($faq, true);
                    $category1 = trim($row['category1']);
                    $category2 = trim($row['category2']);
                    $category3 = trim($row['category3']);
                    $question = addslashes(trim($row['question']));
                    $answer = addslashes(trim($row['answer']));
                    if(!$question && !$answer) continue;
                    
                    $QKEY = "vendor, bot, category1, category2, category3, question, answer, d_regis";
                    $QVAL = "'$vendor', '$bot', '$category1', '$category2', '$category3', '$question', '$answer', '$d_regis'";
                    getDbInsert($table[$m.'faq'], $QKEY, $QVAL);
                }
            }
            
            // callbot
            if(isset($_POST['callbot']) && $_POST['callbot']) {                    
                $aPostData = json_decode($_POST['callbot'], true);
                
                $_data = array();
                $_data['data']['vendor'] = $vendor;
                $_data['data']['bot'] = $bot;
                $_data['nameArray'] = array();
                
                foreach($aPostData as $key=>$val) {
                    $value = trim($val);
                    
                    if($key == 'callno' && $value) {
                        getDbUpdate($table[$m.'bot'], "callno='".$value."'", "uid=".$bot);
                    } else {
                        $_data['nameArray'][$key] = addslashes($value);
                    }
                }                
                $chatbot->updateBotSettings($_data);
            }
            
            // callbot no reset
            if(isset($_POST['callno_reset']) && $_POST['callno_reset']) {                    
                if($_POST['callno_reset'] == true) {
                    getDbUpdate($table[$m.'bot'], "callno=''", "uid=".$bot);
                }                
                $chatbot->updateBotSettings($_data);
            }            
            echo true;
        } else {
            echo false;
        }
	});
	
	// 토픽 usable
    $app->post('/topic_usable/:sid/:dbname/:token',function($sid, $dbname, $token) use ($app) {
		global $g, $m, $date, $table, $_db_bot, $_db_sys, $_db_front, $DB_CONNECT;
        
        if(!$sid || !$dbname || !$token) {
            echo ''; exit;
        }
        
        $_SESSION['mbr_db'] = $dbname;
        
        require $g['path_var'].'db.info.php';
        require $g['path_var'].'table.info.php';
        require $g['path_core'].'function/db.mysql.func.php';
        require $g['path_core'].'function/sys.func.php';
        
        // 토큰 확인
        $authResult = getTokenAccessAuth($CENTERHost, 'topic_usable', $sid, $token);        
		if (!$authResult['bResult']) {
			echo false; exit;
		}
		
		$aPostData = json_decode($app->request()->getBody(), true);
		$aTopicInfo = $aPostData['topicinfo'];
        
		if(!$aTopicInfo['t_uid'] || !$aTopicInfo['status']) {
		    echo false; exit;
		}
      
        $DB_CONNECT = isConnectedToDB($DB);
    
        if($aTopicInfo['buse'] == 1) {
            $status = $aTopicInfo['status'] == "off" ? 0 : 1;
            $_set = "active='".$status."'";
        } else {
            $status = 0;
            $_set = "active='0', bot=-bot";
        }
        getDbUpdate($table[$m.'dialog'], $_set, "o_botuid='".$aTopicInfo['t_uid']."'");
        
        echo true;
    });

    $app->run();
?>