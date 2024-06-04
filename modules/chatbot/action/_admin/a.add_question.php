<?php
if(!defined('__KIMS__')) exit;

include_once $g['path_core'].'function/string.func.php';
require_once $g['dir_module'].'/includes/excel_reader2.php'; // 엑셀 리더 클래스 인클루드 
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();

$q_array = explode(',',$content);

foreach ($q_array as $question) {
	$q_data = array(
	   "vendor"=>$vendor,
	   "bot" =>$bot,
	   "quesCat"=>$quesCat,
	   "question"=>trim($question)
	);

	$chatbot->regisQuestion($q_data);
}



getLink('reload','parent.parent.','','');

?>
