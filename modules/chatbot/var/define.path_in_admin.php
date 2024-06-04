<?php

$dir_module = $g['path_module'].$module.'/';

$g['dir_include']=$dir_module.'includes/';

if($g['mobile']&&$_SESSION['pcmode']!='Y'){
    $d['sns']['skin']=$d['sns']['skin_mobile'];
    $d['sns']['layout']=$d['sns']['layout_mobile'];
}else{
    $d['sns']['skin']=$d['sns']['skin_main'];
    $d['sns']['layout']=$d['sns']['layout_main'];
} 
// 모듈 theme 패스  
$g['dir_module_skin'] = $dir_module.'theme/'.$d['sns']['skin'].'/';
$g['url_module_skin'] = $url_module.'/theme/'.$d['sns']['skin'];
$g['img_module_skin'] = $g['url_module_skin'].'/images';

// 레이아아웃 패스 
$g['dir_layout'] = $g['path_layout'].$d['sns']['layout'].'/';
$g['url_layout'] = $g['s'].'/layouts/'.$d['sns']['layout'];
$g['img_layout'] = $g['url_layout'].'/_images';

// bsas.class.php skin class 에 필요 
$CONF['theme_path']=$dir_module.'theme';
$CONF['theme_name']=$d['sns']['skin'];

// class 인클루드 
require_once $g['dir_include'].'base.class.php';
require_once $dir_module.'languages/'.(!empty($_GET['lang']) ? $_GET['lang']:'korean').'.php';
require_once $g['dir_include'].'feed.class.php';
?>