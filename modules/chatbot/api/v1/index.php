<?php

define('Rb_root',dirname(dirname(__FILE__)).'../../');
define('Rb_path','../../');

require_once '../include/DbHandler.php';
require_once '../include/PassHash.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// class 인클루드 
$m ='chatbot';
$d = array();
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
$g['dir_module'] = $g['path_module'].$m.'/';
$g['url_module'] = $g['s'].'/modules/'.$m;
$g['dir_include'] = $g['dir_module'].'includes/';

require_once $g['dir_include'].'base.class.php';
require_once $g['dir_include'].'module.class.php';
$chatbot = new Chatbot();


echo $g['dir_include'].'module.class.php';
print_r($chatbot);

/**
 * 사이트 정보  추출
 * method GET
 * url /site/:site          
 */ 
$app->get('/node', function() use ($app) {
    $chatbot = new Chatbot();
    $db = new DbHandler();       
    $data = array();
    $data['vendor'] = 45;
    $data['bot'] = 19;
    $data['dialog'] = 112;

    $response = $chatbot->getNodeList($data);    


    echoRespnse(200, $response);
 });


// User id from db - Global Variable
$user_id = NULL;

/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        $db = new DbHandler();

        // get the api key
        $api_key = $headers['Authorization'];
        // validating api key
        if (!$db->isValidApiKey($api_key)) {
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoRespnse(401, $response);
            $app->stop();
        } else {
            global $user_id;
            // get user primary key id
            $user_id = $db->getUserId($api_key);
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * ----------- METHODS WITHOUT AUTHENTICATION ---------------------------------
 */

/**
 * User Login
 * url - /login
 * method - POST
 * params - email, password
 */
$app->post('/login', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('email', 'password'));

    // reading post params
    $email = $app->request()->post('email');
    $password = $app->request()->post('password');
    $response = array();

    $db = new DbHandler();
    // check for correct email and password
    if ($db->checkLogin($email, $password)) {
        // get the user by email
        $user = $db->getUserByEmail($email);

        if ($user != NULL) {
            $response["error"] = false;
            $response['name'] = $user['name'];
            $response['email'] = $user['email'];
            $response['apiKey'] = $user['api_key'];
            $response['createdAt'] = $user['created_at'];
        } else {
            // unknown error occurred
            $response['error'] = true;
            $response['message'] = "An error occurred. Please try again";
        }
    } else {
        // user credentials are wrong
        $response['error'] = true;
        $response['message'] = 'Login failed. Incorrect credentials';
    }

    echoRespnse(200, $response);
});




/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}
    // 테이블 이름 추출 
function getTableName($subject){
   require_once Rb_root.'_var/table.info.php';
   return $table[$subject];
}

/**
  * 최종 출력될 Response 벼열 추출
  * result : 해당 테이블 쿼리 적용 결과값 
  * cols : 해당 테이블 컬럼명 배열 
  * response_subject : response 배열 줄 다루는 주제에 대한 키값. ex) response['menu'], response['bbsdata'] 등   
*/ 
function getResponseData($cols,$result,$response_subject){
 
    foreach ($result as $subject) {
       $tmp=array();
       foreach ($cols as $col) {
           $tmp[$col]=$subject[$col];
       }
          array_push($response_subject, $tmp);
    }
     return $response_subject;
 }   
  
 //SQL필터링
function getSqlFilter($sql)
{
    return preg_replace("( union| update| insert| delete| drop|\/\*|\*\/|\\\|\;)",'',$sql);
}

$app->run();
?>