<?php
// /chatapi_line 주소와 맵핑
// 테스트 방법 : 네이버 라인 검색 - "페소" 검색 후 추가해서 채팅 진행

// "이미지", "푸시", "동영상", "지도" 일반 텍스트 기능 사용이 가능합니다.
// https://developers.line.me/en/docs/messaging-api/reference/#send-reply-message 참고 


// $version = $_GET['version'];
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

// // 로그를 납깁니다. (작업시 임시 파일)
// $myfile = fopen($g['dir_module']."includes/sns_log/navertalk_log.txt", "a") or die("Unable to open file!");
// $json_str = file_get_contents('php://input');
// $json_obj = json_decode($json_str);
// fwrite($myfile, $json_str."\r\n");  
// fclose($myfile);

// require_once $g['dir_module'].'/api/v1/include/PassHash.php';
// require_once $g['dir_module'].'/api/v1/libs/Slim/Slim.php';

// \Slim\Slim::registerAutoloader();

// $app = new \Slim\Slim();

// // // *
// // //  * 사이트 정보  추출
// // //  * method GET
// // //  * url /site/:site          
  
// $app->get('/', function() use ($app) {
//     $res = array(
// 	   "a"=>"A",
// 	   "b"=>"B",
// 	);   

//     echoRespnse(200, $response);
//  });

// $app->run();

// $data = array();
// $data['vendor'] = 45;
// $data['bot'] = 19;
// $data['dialog'] = 112;
// $response = $chatbot->getNodeList($data);  

// $R = getDbData($table['s_menu'],'uid=1','*');

$data = array();
$data['botId'] = 'vxO8TIDXK9NHJzP';
$data['msg'] = $_GET['message']; 

$response = $chatbot->getApiResponse($data); //  
$res = array(
   "a"=>"A",
   "b"=>"B",
   "a"=>"A",
   "a"=>"A",
   "a"=>"A",
   "a"=>"A",
   "a"=>"A",
);

header("Content-Type: application/json");

echo json_encode($response,JSON_UNESCAPED_UNICODE);


exit;

?>