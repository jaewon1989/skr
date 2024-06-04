<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
require_once $g['dir_module'].'includes/movie.class.php';
$chatbot = new Chatbot();
$movie = new Movie();

$content = $movie->addViewLog($_POST);

$result['content'] = $content;
echo json_encode($result);	

exit;
?>
