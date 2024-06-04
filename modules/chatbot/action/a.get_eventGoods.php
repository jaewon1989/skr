<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

//movie class 
require_once $g['dir_module'].'includes/movie.class.php';
$movie = new Movie();

$result=array();
$result['error']=false;

$vendor = $_POST['vendor'];
$bot = $_POST['bot'];
$cmod = $_POST['cmod'];
$eventGoods = $_POST['eventGoods'];

$chatbot->vendor=$vendor;
$chatbot->botuid=$bot;
$chatbot->cmod=$cmod;

$data = array(
    "vendor"=>$vendor,
    "bot"=>$bot,
    "eventGoods"=>$eventGoods
);

$reply = $movie->getMovieList($data);
$content = $reply['response'];
$eventGoods = ""; 	

$result['content'] = $content;
$result['eventGoods'] = $eventGoods;

echo json_encode($result);


exit;
?>
