<?php
    define('Rb_root', $_SERVER['DOCUMENT_ROOT']);
    define('Rb_path',$_SERVER['DOCUMENT_ROOT'].'/');
    //error_reporting(E_ALL & ~E_NOTICE);
    error_reporting(E_ERROR);
    //ini_set("display_errors", 1);
    
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
    
    
    $_SESSION['mbr_db'] = "bot_user258";
    $bot = 6;
    $botid = '7e3d11b5d7caecf';
    
    
    require $g['path_var'].'db.info.php';
    require $g['path_var'].'table.info.php';
    require $g['path_core'].'function/db.mysql.func.php';
    require $g['path_core'].'function/sys.func.php';
    $DB_CONNECT = isConnectedToDB($DB);
    
    $g['mobile']= isMobileConnect($_SERVER['HTTP_USER_AGENT']);
    $g['device']= $g['mobile'] && $_SESSION['pcmode'] != 'Y';
    $g['dir_module'] = $g['path_module'].$m.'/';
    $g['url_module'] = $g['s'].'/modules/'.$m;
    $g['dir_include'] = $g['dir_module'].'includes/';
    $g['url_host'] = 'http'.($g['https_on'] ? 's' : '').'://'.$_SERVER['HTTP_HOST'];
    $g['socketioUrl'] = ($g['https_on'] ? 'ssl://' : '').$_SERVER['HTTP_HOST'];
    $g['socketioPort'] = 3000;    

    include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
    require_once $g['dir_module'].'var/define.path.php';
    
    $body = json_decode(file_get_contents('php://input'), true);
    $roomToken = $body['roomToken'];
    $message = $body['message'];
    
    if($roomToken) {    
        $data = [];
        $data['bot'] = $bot;
        $data['roomToken'] = $roomToken;
        $data['accessToken'] = '124a5s4a8s7d5as4d8a9asd5af4af4af4af4a4fa';
        $data['msg'] = $message ? $message : "init";
        $data['user_utt'] = $data['msg'];
        
        if($data['msg'] == 'init') {
            $_wh = "bot='".$bot."' and access_token='".$data['accessToken']."' and room_token='".$data['roomToken']."'";
            getDbDelete('rb_chatbot_addrBot', $_wh);
        }
        
        require_once $g['dir_module'].'lib/addrBot/client.php'; // addrBot 패키지 
        $addrBot = new Peso\addrbot\Client(); 
        $addr_result = $addrBot->processAddrBot($data);
        print_r($addr_result);
    }