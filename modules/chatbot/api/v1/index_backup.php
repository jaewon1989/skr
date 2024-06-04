<?php

define('Rb_root',dirname(dirname(__FILE__)).'../../');
require_once Rb_root.'/api/v1/include/DbHandler.php';
require_once '../include/PassHash.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();


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
 * User Registration
 * url - /register
 * method - POST
 * params - name, email, password
 */
$app->post('/register', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('name', 'email', 'password'));

    $response = array();

    // reading post params
    $name = $app->request->post('name');
    $email = $app->request->post('email');
    $password = $app->request->post('password');

    // validating email address
    validateEmail($email);

    $db = new DbHandler();
    $res = $db->createUser($name, $email, $password);

    if ($res == USER_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["message"] = "You are successfully registered";
    } else if ($res == USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while registereing";
    } else if ($res == USER_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["message"] = "Sorry, this email already existed";
    }
    // echo json response
    echoRespnse(201, $response);
});

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
 * 사이트 정보  추출
 * method GET
 * url /site/:site          
 */ 
$app->get('/site/:site', function($site) {
            $db = new DbHandler();       
            
            $response = array(); 
            $response["error"] = false;
            $response['site'] = array();
            
            $table=getTableName('s_site');
            $cols = $db->getTableColums($table); 
            $result = $db->getSiteInfo($table,$site);
            
            foreach ($result as $site_info) {
                $tmp=array();
                foreach ($cols as $val) {
                    $tmp[$val]=$site_info[$val];
                }
                array_push($response['site'], $tmp);
            }

            echoRespnse(200, $response);
 });

/**
 * 사이트별 메뉴 데이타 추출
 * method GET
 * url /site/:site/menu          
 */
$app->get('/site/:site/menu', function($site) {
            $db = new DbHandler();
           
            $response = array();
            $response["error"] = false;
            $response['menu'] = array();  
            
            $table=getTableName('s_menu');         
            $cols = $db->getTableColums($table);
            $result = $db->getAllmenu($table,$site); 
            
            $response['menu']=getResponseData($cols,$result,$response['menu']); 
    
            echoRespnse(200, $response);
 });

/**
 * 메뉴 uid 데이타 추출
 * method GET
 * url /site/:site/menu/:uid          
 */
$app->get('/menu/:type', function($type) {
            $db = new DbHandler();
           
            $response = array();
            $response["error"] = false;
            $response['menu'] = array();  

            $table=getTableName('s_menu');         
            $cols = $db->getTableColums($table);

            // type 분리
            $type_arr=explode(':', $type);
            $uid_id=$type_arr[1]; // uid 혹은 id
            if($type_arr[0]=='uid') $result = $db->getUiddata($table,$uid_id);  
            else if($type_arr[0]=='id') $result = $db->getIDdata($table,$uid_id);           
            
            $response['menu']=getResponseData($cols,$result,$response['menu']); 
             
            echoRespnse(200, $response);
 });


/**
 * bbsdata 추출
 * method GET
 * site,bbs,category 를 필터링하지 않을 경우 0 으로 세팅            
 */
$app->get('/site/:site/bbs/:bbs/where/:where/filter/:filter', function($site,$bbs,$where,$filter) {
            $db = new DbHandler();
            $filter_arr=explode('-', $filter);
            $sort=$filter_arr[0];
            $orderby=$filter_arr[1];
            $limit=$filter_arr[2];
            $page=$filter_arr[3];
            $where=getSqlFilter($where); // where 절 필터링 : update,delete 등 삭제
            
            $response = array(); 
            $response["error"] = false;
            $response["bbsdata"] = array();
            
            $table=getTableName('bbsdata');         
            $cols = $db->getTableColums($table);
            $result = $db->getBbsdata($table,$site,$bbs,$where,$sort,$orderby,$limit,$page); 
             
            $response['bbsdata']=getResponseData($cols,$result,$response['bbsdata']); 
  
            echoRespnse(200, $response);
});

/**
 * bbsdata uid 값으로 게시글 1 개 추출
 * method GET
 */
$app->get('/bbs/:uid', function($uid) {
            $db = new DbHandler();
            
            $response = array(); 
            $response["error"] = false;
            $response["bbsdata"] = array();
            
            $table=getTableName('bbsdata');         
            $cols = $db->getTableColums($table);
            $result = $db->getUiddata($table,$uid); 
             
            $response['bbsdata']=getResponseData($cols,$result,$response['bbsdata']); 
  
            echoRespnse(200, $response);
});



/*
 * ------------------------ METHODS WITH AUTHENTICATION ------------------------
 */

/**
 * Listing all tasks of particual user
 * method GET
 * url /tasks          
 */
$app->get('/tasks', 'authenticate', function() {
            global $user_id;
            $response = array();
            $db = new DbHandler();

            // fetching all user tasks
            $result = $db->getAllUserTasks($user_id);

            $response["error"] = false;
            $response["tasks"] = array();

            // looping through result and preparing tasks array
            while ($task = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $task["id"];
                $tmp["task"] = $task["task"];
                $tmp["status"] = $task["status"];
                $tmp["createdAt"] = $task["created_at"];
                array_push($response["tasks"], $tmp);
            }

            echoRespnse(200, $response);
        });

/**
 * Listing single task of particual user
 * method GET
 * url /tasks/:id
 * Will return 404 if the task doesn't belongs to user
 */
$app->get('/tasks/:id', 'authenticate', function($task_id) {
            global $user_id;
            $response = array();
            $db = new DbHandler();

            // fetch task
            $result = $db->getTask($table['bbsdata'],$task_id, $user_id);

            if ($result != NULL) {
                $response["error"] = false;
                $response["id"] = $result["id"];
                $response["task"] = $result["task"];
                $response["status"] = $result["status"];
                $response["createdAt"] = $result["created_at"];
                echoRespnse(200, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "The requested resource doesn't exists";
                echoRespnse(404, $response);
            }
        });

/**
 * Creating new task in db
 * method POST
 * params - name
 * url - /tasks/
 */
$app->post('/tasks', 'authenticate', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('task'));

            $response = array();
            $task = $app->request->post('task');

            global $user_id;
            $db = new DbHandler();

            // creating new task
            $task_id = $db->createTask($user_id, $task);

            if ($task_id != NULL) {
                $response["error"] = false;
                $response["message"] = "Task created successfully";
                $response["task_id"] = $task_id;
                echoRespnse(201, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to create task. Please try again";
                echoRespnse(200, $response);
            }            
        });

/**
 * Updating existing task
 * method PUT
 * params task, status
 * url - /tasks/:id
 */
$app->put('/tasks/:id', 'authenticate', function($task_id) use($app) {
            // check for required params
            verifyRequiredParams(array('task', 'status'));

            global $user_id;            
            $task = $app->request->put('task');
            $status = $app->request->put('status');

            $db = new DbHandler();
            $response = array();

            // updating task
            $result = $db->updateTask($user_id, $task_id, $task, $status);
            if ($result) {
                // task updated successfully
                $response["error"] = false;
                $response["message"] = "Task updated successfully";
            } else {
                // task failed to update
                $response["error"] = true;
                $response["message"] = "Task failed to update. Please try again!";
            }
            echoRespnse(200, $response);
        });

/**
 * Deleting task. Users can delete only their tasks
 * method DELETE
 * url /tasks
 */
$app->delete('/tasks/:id', 'authenticate', function($task_id) use($app) {
            global $user_id;

            $db = new DbHandler();
            $response = array();
            $result = $db->deleteTask($user_id, $task_id);
            if ($result) {
                // task deleted successfully
                $response["error"] = false;
                $response["message"] = "Task deleted succesfully";
            } else {
                // task failed to delete
                $response["error"] = true;
                $response["message"] = "Task failed to delete. Please try again!";
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