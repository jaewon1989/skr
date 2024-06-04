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
$parent = $_POST['parent']?$_POST['parent']:'';
$depth = $_POST['depth']?$_POST['depth']:'';
$issun = $_POST['issun']?$_POST['issun']:'';
$keyword = $_POST['keyword']?$_POST['keyword']:'';
$parent_keyword = $_POST['parent_keyword']?$_POST['parent_keyword']:'';
$role = $_POST['role']?$_POST['role']:'';
$cmod = $_POST['cmod'];
$mbruid = $_POST['mbruid'];
$token = $_POST['token'];
$chatbot->qry_token = $_POST['token'];
$chatbot->cmod=$cmod;
$chatbot->vendor=$vendor;
$chatbot->botuid=$bot;

$kwd_data = array(
    "vendor"=>$vendor,
    "bot"=>$bot,
    "parent"=>$parent,
    "depth"=>$depth,
    "issun"=>$issun,
    "keyword"=>$keyword,
    "parent_keyword"=>$parent_keyword,
    "mbruid"=>$mbruid,
    "role"=>$role
);

// 봇 변경 버튼 요청(캠퍼스) 지정 과정  
if($_POST['role']=='showBotListKwd') $content = $chatbot->getChatBotKeywordList($kwd_data);
else $content = $chatbot->getChatKeywordList($kwd_data);

$result['content'] = $content;
$result['eventGoods'] = $eventGoods;
$result['query'] = $reply['query'];

echo json_encode($result);


exit;
?>
