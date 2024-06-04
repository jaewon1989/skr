<?php

require_once $g['dir_module'].'api/'.$version.'/include/PassHash.php';
require_once $g['dir_module'].'api/'.$version.'/libs/Slim/Slim.php';
require_once $g['dir_module'].'api/'.$version.'/include/func.class.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

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

$app->run();

?>