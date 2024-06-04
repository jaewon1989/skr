<?php
/*
  #####  request body 추출  ##### 
  $app = new \Slim\Slim();
  $body = $app->request->getBody();

*/

define('Rb_root',dirname(dirname(__FILE__)).'../../');
define('Rb_path','../../');
error_reporting(E_ERROR);

//require_once '../include/DbHandler.php';
require_once '../include/PassHash.php';
require '../libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();


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
$g['https_on'] = $_SERVER['HTTPS']=='on' || stripos($_SERVER['HTTP_X_FORWARDED_PROTO'],'https') !== false ? true : false;
$g['url_host'] = 'http'.($g['https_on']?'s':'').'://'.$_SERVER['HTTP_HOST'];

include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
require_once $g['dir_module'].'var/define.path.php';

require_once $g['dir_include'].'base.class.php';
require_once $g['dir_include'].'module.class.php';

$chatbot = new Chatbot();

/**
 * 사이트 정보  추출
 * method GET
 * url /site/:site          
 */ 

class resAPI {
    
    public function __construct(){
        global $chatbot,$g;
    }
    

    // 유저가 채팅창에 입력해서 보내진 내용 텍스트 얻기
    public function getMessageText($webhook){
        $messageText = $webhook->{"events"}[0]->{"message"}->{"text"}; 
        return $messageText;
    }
    
    // 메세지 타입 얻기 : text, sticker, image(첨부이미지, 사진 찍어서 올린것도 포함),audio
    public function getMessageType($webhook){ 
        $messageText = $webhook->{"events"}[0]->{"message"}->{"type"}; 
        return $messageText;
    }
    
    // postback 데이터 얻기 
    public function getPostBackData($webhook){
        $postback = $webhook->{"events"}[0]->{"postback"}->{"data"}; 
        return $postback;
    }
    
    // 이벤트 타입 얻기 : message, postback,  
    public function getEventType($webhook){
        $eventType = $webhook->{"events"}[0]->{"type"};
        return $eventType;
    } 
    
    public function pushMenuRespond($_data){
        global $chatbot;
        
        $result = $_data['result'];
        $data = $_data['data'];
        $response = array();
        foreach ($result as $item) {
            $itemQty = count($item);
            for($i=0;$i<$itemQty;$i++){
                $type = $item[$i]['type'];
                $content = $item[$i]['content'];                           
                
                if($type =='node'){
                    $data['node'] = $content;
                    $response[] = $this->getNodeRespond($data);

                }else{
                    $response[] = array("type"=>$type,"content"=>$content);
                }
            }
        }

        return $response;

        //$this->echoResponse(200,$response);
    }

    public function getNodeRespond($data){
        global $chatbot;
   
        $result = $chatbot->getApiResponse($data); 
        $response = array();
        foreach ($result as $resItem) {
            $type = $resItem['type'];
            $content = $resItem['content'];
            
            if($type =='if'){
                $_data = array();
                $_data['data'] = $data;
                $_data['result'] = $content;
                $response[] =array("type"=>$type,"content"=>$this->pushMenuRespond($_data)); 
            }else{
                $response[] = array("type"=>$type,"content"=>$content);
            } 
        }

        return $response;

    }
    
    // 리소스로 bot id 추출 
    function getBotIdFromUri($uri){
        $uri_arr = explode('/',$uri);
        $console_id = $uri_arr[1];
        $consoleId_arr = explode('-',$console_id);
        
        return $consoleId_arr[0]; 
    } 


    /**
     * Echoing json response to client
     * @param String $status_code Http response code
     * @param Int $response Json response
     */
    function echoResponse($status_code, $response) {
        $app = \Slim\Slim::getInstance();
        // Http response code
        $app->status($status_code);

        if(is_array($response)){
            $app->contentType('application/json');  
            $json_encode = json_encode($response,JSON_UNESCAPED_UNICODE); 
            $json_encode = stripcslashes($json_encode);
            $json_encode = str_replace("[\"",'[',$json_encode);
            $json_encode = str_replace("}\"",'}',$json_encode);
            $json_encode = str_replace("\"{",'{',$json_encode);

            echo stripcslashes($json_encode);  
        }else{
            $app->contentType('text/html');          
            echo $response;  
        }  
    }
    
    
}

$resAPI = new resAPI();

function authenticate(\Slim\Route $route) {
    global $chatbot,$resAPI;

    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
 
    // Verifying Authorization Header
    if (isset($headers['Authorization']) && isset($headers['X-Bottalks-Token']) ) {
    
        // get the api key
        $data =array();
        $data['headers'] = $headers;
        $data['botId'] = $resAPI->getBotIdFromUri($app->request->getResourceUri());
            
        $verify_apiKey = $chatbot->verify_apiKey($data);

        // validating api key
        if (!$verify_apiKey['valid']) {
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            // $response['client_id'] = $verify_apiKey['client_id'];
            // $response['client_secret'] = $verify_apiKey['client_secret'];
            // $response['access_token'] = $verify_apiKey['access_token'];
            // $response['id_wh'] = $verify_apiKey['id_wh'];
            // $response['data'] = $data;

            $resAPI->echoResponse(401, $response);
            $app->stop();
        } 
    }else {
        // api key is missing in header
        $response["error"] = true;
        if(!isset($headers['Authorization'])) $response["message"] = "Authorization key is misssing";
        else if(!isset($headers['X-Bottalks-Token'])) $response["message"] = "Access Token is misssing";
        $resAPI->echoResponse(400, $response);
        $app->stop();
    }
}

// 콘솔id/message --> authenticate 함수를 통해서 key 값 체크후 본 프로세스 실행 
$app->post('/:console_id/message','authenticate',function($console_id) use ($app) {
    global $chatbot,$resAPI;
    
    $consoleId_arr = explode('-',$console_id);

    // request body 체크 --> eventType, messageText, postBackData 추출 
    $reqBody = $app->request->getBody();
    $webhook = json_decode($reqBody);
    $eventType = $resAPI->getEventType($webhook); // 이벤트 타입 체크 message or postback
    $messageText = $resAPI->getMessageText($webhook);
    $postBackData = $resAPI->getPostBackData($webhook);

    $data = array();    
    $data['botId'] = $consoleId_arr[0];
    $data['msg'] = $messageText;
    $data['api'] = true;
    $data['botks_api'] = true;

    // 이벤트 타입 분리 
    if($eventType == 'message'){     
        $response = $resAPI->getNodeRespond($data); 
     
    }else if($eventType=='postback'){ // 메뉴 버튼 누른 경우 
        $code_arr = explode('-',$postBackData);
        $resType = $code_arr[0]; // 
        $uid = $code_arr[1];  
        
        if($resType=='hMenu'){ // 버튼 메뉴 클릭한 경우 
            $data['uid'] = $uid;       
            $result = $chatbot->getMenuRespond($data);
            if($result[0]){
                $_data = array();
                $_data['data'] = $data;
                $_data['result'] = $result;
                $response = $resAPI->pushMenuRespond($_data);

            }else{
                $response = $resAPI->getNodeRespond($data); // content 없는 경우 그냥 입력한 것으로 간주 
            }            
        }
    }  
    
    $resAPI->echoResponse(200,$response);
    
}); 

// 문진봇 관리자 페이지 접근허용 컨펌 
$app->post('/:access_token/:access_mod',function($access_token,$access_mod) use ($app) {
    global $chatbot,$resAPI,$g, $_db_bot, $_db_front, $DB, $DB_CONNECT;
    
    $aToken = explode("_", $access_token);
    if($aToken[2]) {
        $_SESSION['mbr_db'] = "bot_user".$aToken[2];
        require $g['path_var'].'db.info.php';
        $DB_CONNECT = mysqli_connect($DB['host'], $DB['user'], $DB['pass'], $DB['name'],$DB['port']);
    }

    $data = array();
    $data['access_mod'] = $access_mod;
    $data['access_token'] = $access_token; 
    $response = $chatbot->getBotDataFromAT($data); // 엑세스 토큰으로 bot 데이타 얻어오기
    
    $dNow = date("Y-m-d H:i:s");
    $log = $dNow." token: ".$access_token.", mod: ".$access_mod."\n";
    $log .=$dNow." ".json_encode($response, JSON_UNESCAPED_UNICODE)."\n";
    $log .="--------------------------------------------------------------------------";    
    file_put_contents($_SERVER['DOCUMENT_ROOT']."/_tmp/cache/admin_api.txt", $log."\n", FILE_APPEND);
    
    $resAPI->echoResponse(200,$response);   
        
});

// 가격문의 테스트  
$app->get('/coffee_price/:coffee_name',function($coffee_name) use ($app) {
    global $chatbot,$resAPI;

    $data = array();
    $data['type'] = 'coffee';
    $data['name'] = $coffee_name; 
    $response = $chatbot->getLegacyProductPrice($data); // 
    //$response = array("result"=>0);
    
    $resAPI->echoResponse(200,$response);   
        
});

// 심플 API  
$app->get('/oasis_api/:message',function($message) use ($app) {
    global $chatbot,$resAPI;

    $data = array();
    $data['botId'] = 'vxO8TIDXK9NHJzP';
    $data['msg'] = $message; 

    //$response = $chatbot->getApiResponse($data); //  
    $response = array("type"=>"text","content"=>"헬로우");
    
    $resAPI->echoResponse(200,$response);   
        
});  

$app->post('/sync_model/:vendor/:bot',function($vendor,$bot) use ($app) {
    global $chatbot,$resAPI;

    if(!isset($_FILES['file'])) exit;
    
    $baseDir = $_SERVER['DOCUMENT_ROOT'].'/files/trainData2/';
    $saveDir = $vendor.'/'.$bot;
    $aSaveDir = explode('/', $saveDir);
    $tempDir = '';
    foreach($aSaveDir as $dir) {
        if($dir == '') continue;
        $tempDir .=$dir.'/';
        if (!is_dir($baseDir.$tempDir)){
            $oldmask = umask(0);
            mkdir($baseDir.$tempDir,0707);
            umask($oldmask);
        }
    }
    
    $aDir = dir($baseDir.$saveDir);
    while ($chFile = $aDir->read() ) {
        if ($chFile != "." && $chFile != "..") @unlink($baseDir.$saveDir."/".$chFile);
    }
    
    for ($i=0, $nCnt=count($_FILES['file']['name']); $i<$nCnt; $i++) {
        $chFileName = $_FILES['file']['name'][$i];
        move_uploaded_file($_FILES['file']['tmp_name'][$i], $baseDir.$saveDir."/".$chFileName);
    }        
});

$app->run();
?>