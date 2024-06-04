<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
require_once $g['dir_module'].'includes/movie.class.php';
$chatbot = new Chatbot();
$movie = new Movie();

$result=array();
$result['error']=false;
$chatbot->botid = $_POST['botid'];

$page = $_POST['page'];
$token = $_POST['token'];
$data = array();
$data['page'] = $page;
$data['token'] = $token;
$reply = $movie->getMovieList($data);

// if($page>1){
// 	print_r($reply);
// 	exit;
// }

$result['content'] = $reply['response'];
$result['query'] = $reply['query'];

echo json_encode($result);	

exit;
?>
