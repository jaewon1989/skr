<?php
$g['dir_include']=$g['dir_module'].'includes/';

if($g['mobile']&&$_SESSION['pcmode']!='Y'){
    $d['chatbot']['skin']=$d['chatbot']['skin_mobile'];
    $d['chatbot']['layout']=$d['chatbot']['layout_mobile'];
}else{
    $d['chatbot']['skin']=$d['chatbot']['skin_desktop'];
    $d['chatbot']['layout']=$d['chatbot']['layout_desktop'];
}

if(isset($_POST['theme_name'])){
   $d['chatbot']['skin'] = $_POST['theme_name'];
}

// 모듈 theme 패스  
$g['dir_module_skin'] = $g['dir_module'].'theme/'.$d['chatbot']['skin'].'/';
$g['url_module_skin'] = $g['url_module'].'/theme/'.$d['chatbot']['skin'];
$g['img_module_skin'] = $g['url_module_skin'].'/images';

// 레이아아웃 패스 
$d['layout']['dir'] = dirname($d['chatbot']['layout']);
$g['dir_layout'] = $g['path_layout'].$d['layout']['dir'].'/';
$g['url_layout'] = $g['s'].'/layouts/'.$d['layout']['dir'];
$g['img_layout'] = $g['url_layout'].'/_images';

// bsas.class.php skin class 에 필요 
$CONF['theme_path']=$g['dir_module'].'theme';
$CONF['theme_name']=$d['chatbot']['skin'];

// class 인클루드 
require_once $g['dir_include'].'base.class.php';
require_once $g['dir_module'].'languages/'.(!empty($_GET['lang']) ? $_GET['lang']:'korean').'.php';
require_once $g['dir_include'].'module.class.php';
?>