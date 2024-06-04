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

$data = $_POST['data'];

$content = $movie->getMovieList($data); 
$result['content'] = $content;

echo json_encode($result);


exit;
?>
