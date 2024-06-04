<?php
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
	header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, X-Bottalks-Bot-Id, X-Bottalks-Role, X-Bottalks-Token");

    define('Rb_root',dirname(dirname(__FILE__)).'../../');
    define('Rb_path','../../');
    //error_reporting(E_ALL & ~E_NOTICE);
    error_reporting(E_ERROR);
    //ini_set("display_errors", 1);
    session_start();

    require '../libs/Slim/Slim.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();

    if($app->request->isOptions()) {
        return true;
        exit;
    }

    // class 인클루드
    $reserve;
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
        'path_file'   => Rb_path.'files/',
        'path_blackList' => Rb_path.'blackList/',
        'path_utils' => Rb_path.'utils/',
        'path_common' => Rb_path.'common/',
    );
    $g['https_on'] = $_SERVER['HTTPS']=='on' || stripos($_SERVER['HTTP_X_FORWARDED_PROTO'],'https') !== false ? true : false;


    $chConfDir = './configuration';
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

    require $g['path_var'].'table.info.php';
    require $g['path_core'].'function/db.mysql.func.php';
    require $g['path_core'].'function/sys.func.php';

    $g['mobile']= isMobileConnect($_SERVER['HTTP_USER_AGENT']);
    $g['device']= $g['mobile'] && $_SESSION['pcmode'] != 'Y';
    $g['dir_module'] = $g['path_module'].$m.'/';
    $g['url_module'] = $g['s'].'/modules/'.$m;
    $g['dir_include'] = $g['dir_module'].'includes/';
    $g['url_host'] = 'http'.($g['https_on'] ? 's' : '').'://'.$_SERVER['HTTP_HOST'];

    include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
    require_once $g['dir_module'].'var/define.path.php';
    require_once $g['dir_include'].'callbot.class.php';
    require_once $g['dir_include'].'reslog.class.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/blackList/controller/BlackListController.php'; // 블랙리스트 설정

    //--------------------------------------------------------------------
    $chatbot = new Chatbot();
    $callbot = new Callbot();
    //$resLog = new Responselog();

    // 챗봇 인증용 : 접속 인증 및 토큰, roomToken 리턴
    $app->post('/auth', function() use ($app) {
        global $chatbot, $callbot, $reserve, $m, $table, $g, $_db_bot, $DB, $DB_CONNECT;

        $response = array();
        $response["result"] = true;

        $body = json_decode($app->request->getBody(), true);

        $fromPhone = $body['fromPhone'];
        if (!$fromPhone || !is_numeric($fromPhone)) {
            $response["result"] = false;
            $response["message"] = 'Invalid Phone number.';
            $callbot->echoResponse(401, $response);
            $app->stop();
        }

        // header check
        $headers = $app->request->headers;
        $_data = array('mode'=>'auth', 'userId'=>$fromPhone, 'headers'=>$headers);
        $aCheckHeader = $callbot->getCheckHeader($_data);
        if(!$aCheckHeader['result']) {
            $response["result"] = $aCheckHeader['result'];
            $response["message"] = $aCheckHeader['message'];
            $callbot->echoResponse($aCheckHeader['code'], $response);
            $app->stop();
        }

        $data = array();
        $data['api'] = true;
        $data['msg'] = 'hi';
        $data['msg_type'] = 'say_hello';

        $isError = false;
        $botResult = [];
        try {
            $botResult = $callbot->getNodeRespond($data);
        } catch (Exception $exception) {
            $isError = true;
        }

        $response["result"] = true;
        $response["accessToken"] = $callbot->accessToken;
        $response["roomToken"] = $callbot->roomToken;
        $response['message'] = $isError ? $callbot->errorMsg : $botResult['response'];
        $response['next_status'] = $botResult['next_status'];
        $response['barge_in'] = $botResult['bargein'];
        $response['language'] = $botResult['language'];
        $callbot->echoResponse(200, $response);

        if($botResult['next_status']['action'] == "hangup") {
            // 콜봇 응답 카운트
            $_data = array('cstarttime' => 0, 'cendtime' => 0);
            $callbot->getCallResCounter($_data);

            if(isset($GLOBALS['resLog'])) {
                $GLOBALS['resLog']->setLogWrite(['botid'=>$callbot->botid, 'bot'=>$callbot->bot, 'roomToken'=>$callbot->roomToken]);
            }
        }

        $aSockData = array();
        $aSockData['role'] = "new_client";
        $aSockData['sockid'] = uniqid();
        $callbot->sendWebSocket($aSockData);

        // 모니터링 봇응답 로그
        $aSockData = array();
        $aSockData['role'] = "call_log_send";
        $aSockData['log'] = array(
            array("sender"=>"bot", "msg"=>$botResult['response'])
        );
        $callbot->sendWebSocket($aSockData);
    });

    // 챗봇 대화용 : header['access_token'], roomtoken, 메시지
    $app->post('/chatbot', function() use ($app) {
        global $chatbot, $callbot, $reserve, $g, $_db_bot, $DB, $DB_CONNECT;

        $response = array();
        $response["result"] = true;

        $body = json_decode($app->request->getBody(), true);
        // header check
        $headers = $app->request->headers;
        $_data = array('mode'=>'chatbot', 'headers'=>$headers);
        $aCheckHeader = $callbot->getCheckHeader($_data);
        if(!$aCheckHeader['result']) {
            $response["result"] = $aCheckHeader['result'];
            $response["message"] = $aCheckHeader['message'];
            $callbot->echoResponse($aCheckHeader['code'], $response);
            $app->stop();
        }

        $message = $chatbot->verifyUserInput($body['message']);

        // 블랙리스트 적용
        $blackListPostData = ['botUid' => $callbot->bot, 'cleanMessage' => $message];

        $controller = new BlackListController();
        $message = $controller->getCleanMessageForBlacklist($blackListPostData);

        // param check
        if($message == "") {
            // 사용자 발화 없을 경우 (keyin의 경우는 직전 응답 재전송)
            if(isset($callbot->r_data['next_status']) && $callbot->r_data['next_status']['action'] == "keyin") {
                $message = "nokeyin";
            } else {
                if($callbot->r_data['no_keyin_cnt']) {
                    unset($callbot->r_data['no_keyin_cnt']);
                }
                $response["result"] = false;
                $response["message"] = "Message not found.";
                $callbot->echoResponse(401, $response);
                $app->stop();
            }
        }

        // hangup일 경우 종료
        if($message == 'hangup') {
            // 콜봇 응답 카운트
            $_data['cstarttime'] = $body['start_time'] ? $body['start_time'] : 0;
            $_data['cendtime'] = $body['end_time'] ? $body['end_time'] : 0;
            $callbot->getCallResCounter($_data);

            $aSockData = array();
            $aSockData['role'] = "disconnect";
            $callbot->sendWebSocket($aSockData);

            $_data = array();
            $_data['del_type'] = "phone";
            if($callbot->removeExpireToken($_data)) {
                exit;
            }
            exit;
        }

        // 직전 발화문과 동일한 문장 체크
        if(isset($callbot->r_data['last_msg']) && $callbot->r_data['last_msg']) {
            if(getEtcStrReplace($callbot->r_data['last_msg']) == getEtcStrReplace($message)) {
                $callbot->sameq = true;
            }
        }

        // 모니터링 봇응답 로그
        $aSockData = array();
        $aSockData['role'] = "call_log_send";
        $aSockData['log'] = array(
            array("sender"=>"user", "msg"=>$message),
        );
        $callbot->sendWebSocket($aSockData);

        $data = array();
        $data['api'] = true;
        $data['msg'] = $message;
        $data['msg_type'] = ($message === 'noinput' || $message === 'sttfail' || $message === 'nomatch ') ? $message : 'text';

        $isError = false;
        $botResult = [];
        try {
            $botResult = $callbot->getNodeRespond($data);

            // TODO: 장애발생 강제 테스트 제거 필요
            if ('장애 발생' === $message){
                throw new Exception($callbot->errorMsg);
            }
        } catch (Exception $exception) {
            $isError = true;
        }

        if($botResult['response']) {
            // ARS 문자 전송
            if(isset($botResult['ars_link']) && trim($botResult['ars_link'])) {
                $ars_msg = "아래의 주소로 접속하여 주세요.\n".trim($botResult['ars_link']);
                $callbot->sendARSLink($ars_msg);
            }

            // 모니터링 봇응답 로그
            $aSockData = array();
            $aSockData['role'] = "call_log_send";
            $aSockData['log'] = array(
                array("sender"=>"bot", "msg"=>$botResult['response'], "sameq"=>$botResult['sameq'], "unknown"=>$botResult['unknown'])
            );
            $callbot->sendWebSocket($aSockData);

            $response["result"] = true;
            $response['message'] = $isError ? $callbot->errorMsg : (string)$botResult['response'];
            $response['next_status'] = $botResult['next_status'];
            $response['barge_in'] = $botResult['bargein'];
            $response['language'] = $botResult['language'];
            $response['timeout'] = (string)$botResult['timeout'];
            $response['timeoutMsg'] = (string)$botResult['timeoutMsg'];
            $response['ttsSpeed'] = (string)$botResult['ttsSpeed'];

            $callbot->echoResponse(200, $response);
        }

        if($botResult['next_status']['action'] == "hangup") {
            if(isset($GLOBALS['resLog'])) {
                $GLOBALS['resLog']->setLogWrite(['botid'=>$callbot->botid, 'bot'=>$callbot->bot, 'roomToken'=>$callbot->roomToken]);
            }
        }
    });

    $app->run();
?>