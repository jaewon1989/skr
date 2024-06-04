<?php
$g['layoutVarForSite'] = $g['dir_layout'].'_var/_var.'.$r.'.php';
include is_file($g['layoutVarForSite']) ? $g['layoutVarForSite'] : $g['dir_layout'].'_var/_var.php';

// 언어셋
include getLangFile($g['dir_layout'].'_languages/lang.',($_HS['lang']?$_HS['lang']:$d['admin']['syslang']),'.php');

// 챗봇모듈 chatbot 관련 설정 
// SNS 클래스 설정 추가
$CONF=array();
$CONF['module']=$g['path_module'].'chatbot';
require_once $CONF['module'].'/var/var.php';
$CONF['theme_path']=$CONF['module'].'/theme';
$CONF['theme_name']=$d['chatbot']['skin_desktop']; // 테마 
$CONF['dir_include']=$CONF['module'].'/includes/';
require_once $CONF['dir_include'].'base.class.php';
require_once $CONF['module'].'/languages/'.(!empty($_GET['lang']) ? $_GET['lang']:'korean').'.php';
require_once $CONF['dir_include'].'module.class.php';
$chatbot = new Chatbot();


// 현재 bot 데이타 가져오기 
$_data = array();
$_data['bot'] = $bot;
$_data['mod'] ='list'; // 사이즈 다르게 
$getListBot = $chatbot->getAdmBot($_data);

$menu_icon = array(
    "dashboard"=>"fa-dashboard",
    "make"=>"fa-wrench",
    "template"=>"fa-wrench",
    "graph"=>"fa-share-alt",
    "intentSet"=>"fa-hashtag",
    "entitySet"=>"fa-at",
    "chanel"=>"fa-share-square",
    "analysis"=>"fa-chart-bar",
    "report"=>"fa-newspaper",
    "user"=>"fa-user",
    "intent"=>"fa-hashtag",
    "entity"=>"fa-at",
    "context"=>"fa-map-signs",
    "node"=>"fa-sitemap",
    "conversation"=>"fa-comments",
    "learning"=>"fa-graduation-cap",   
    "settings"=>"fa-gear",
    "config"=>"fa-list-alt",
    "api"=>"fa-plug",
    "legacy"=>"fa-database",
    "sysData"=>"fa-database",
    "voice"=>"fa-microphone",

);

$callIntent = '인텐트';
$callEntity = '엔터티';

// 페이지 Title
$page_arr = explode('/',$page);
$menu_id = $page_arr[1];
$MN = getDbData($table['s_menu'],"id='".$menu_id."' and hidden=0",'depth,parent,name');
if($MN['depth']==2){
     $pageTitle = $MN['name'];
}else if($MN['depth']==3){
    $PM = getDbData($table['s_menu'],"uid='".$MN['parent']."' and hidden=0",'name');
    $pageTitle = $PM['name'].' > '.$MN['name'];
}

// scroll 컨트롤 
if($page=='adm/intentSet'||$page=='adm/entitySet' || $page=='adm/conversation') $no_scroll = true;
else $no_scroll = false;

// 스펨방지 코드 
if (!$_SESSION['upsescode']) $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
$sescode = $_SESSION['upsescode'];

// 소셜 로그인 체크 
$g['mdl_slogin'] = 'slogin';
$g['use_social'] = is_file($g['path_module'].$g['mdl_slogin'].'/var/var.php');
if ($g['use_social'])
{
    include_once $g['path_module'].$g['mdl_slogin'].'/action/a.slogin.check.php';
}

?>