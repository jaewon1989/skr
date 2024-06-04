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
    require_once $g['dir_module'].'includes/reserve.class.php';
    
    // 주소봇 데이터 컨트롤
    function controlAddrBotData($data){
        $tbl ='rb_chatbot_addrBot';
        
        $bot = $data['bot'];
        $access_token = $data['access_token'];
        $room_token = $data['room_token'];
        $act = $data['act'];
        
        $_wh = "bot='".$bot."' and access_token='".$access_token."' and room_token='".$room_token."'";
        $R = getDbData($tbl,$_wh,'uid');

        if($act =='update'){ // 추가

            // context 값 json 인코딩
            $context = json_encode($data['context'],JSON_UNESCAPED_UNICODE);
      
            if($R['uid']){
                $up_qry = "context='$context'";
                getDbUpdate($tbl,$up_qry,'uid='.$R['uid']);

            }else{
                $QKEY = "bot,access_token,room_token,context";
                $QVAL ="'$bot','$access_token','$room_token','$context'";
                getDbInsert($tbl,$QKEY,$QVAL);

            }

        }else if($act =='get'){
            $R = getDbData($tbl,$_wh,'*');
            
            $result = $data;
            $result['context'] = json_decode($R['context'],true);
        }

        return $result;        
    }

    // 주소봇 발화 처리
    function processAddrBot($data){
        global $g;

        require_once $g['dir_module'].'lib/addrBot/client.php'; // addrBot 패키지 
        
        // 저장된 컨텍스트 추출   
        $data['room_token'] = $data['roomToken'];
        $data['access_token'] = $data['accessToken'];//'access_token';
        $data['act'] = 'get';    
        $getAddrData = $this->controlAddrBotData($data);

        $params = [
           "context" =>$getAddrData['context'], // 컨텍스트 값 array (DB 저장값)
           "user_utt" => $data['user_utt'] // 사용자 발화
        ];
        
        $addrBot = new Peso\addrbot\Client(); 
        $addr_result = $addrBot->getAddrTalk($params);

        // 컨텍스트 추가 or 업데이트 
        $data['act'] = 'update';
        $data['context'] = $addr_result['context'];
        $this->controlAddrBotData($data);

        $result =[];
        $result['addr_result'] = $addr_result;
        $result['res_type'] ='text';
        $result['response'] = $addr_result['bot_utt'];        

        return $result;
        
    }
    
    if($_POST['mode'] == 'juso') {
        $data = [];
        $data['bot'] = 193;
        $data['roomToken'] = 'asa23658fdfdfi451af';
        $data['accessToken'] = '124a5s4a8s7d5as4d8a9asd5af4af4af4af4a4fa';
        $result = processAddrBot($data);
        print_r($result);
    }