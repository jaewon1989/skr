<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
require_once $g['dir_module'].'includes/movie.class.php';
$chatbot = new Chatbot();
$movie = new Movie();

$result=array();
$result['error']=false;
parse_str($_POST['data'],$data);

$movie->updateLearningData($data);

$result['msg'] = '학습되었습니다.';
echo json_encode($result);
exit;
?>
