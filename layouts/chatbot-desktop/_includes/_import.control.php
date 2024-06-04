<?php
// if($m!='hbot'&& $m!='chatbot'){
//     echo '잘못된 접근입니다.';  
//     exit;
// } 

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
//require_once $CONF['dir_include'].'base.class.php';
require_once $CONF['module'].'/languages/'.(!empty($_GET['lang']) ? $_GET['lang']:'korean').'.php';
//require_once $CONF['dir_include'].'module.class.php';
// $chatbot = new Chatbot();

// 해당 벤더 logo 설정 
if($V['uid']){
   $vendor_logo = $V['logo']?$V['logo']:$g['img_layout'].'/dice.jpg';
}

if($_CA[0]=='intro') $intro_menu = '#';
else $intro_menu = 'c=intro/bottalks';

$top_menu = array(
  "챗봇소개"=>$intro_menu,
  "챗봇만들기"=>"c=build",
  "내가 대화한 챗봇"=>"c=talked",
  "ADD 챗봇"=>"c=added",
  "나의 챗봇"=>"c=mybot",
  "고객센터"=>"c=support"
);
if($_CA[0]) $M1=getDbData($table['s_menu'],"id='".$_CA[0]."' and depth=1",'uid,id,is_child');
if($M1['uid']) $M2=getDbArray($table['s_menu'],'hidden=0 and parent='.$M1['uid'].' and depth=2','name,id','gid','asc','',1);

// 서브 메뉴 출력 여부 
if($c=='added' || $c=='talked' || $c=='build' ||  $front=='join' || $front=='login' || $front=='profile' || substr($page,0,5) == 'build' || $page=='view' || $page=='search' || $page=='vendor/main' ) $has_submenu = false;
else $has_submenu = true;

// 카테고리 링크 
$catLink = $g['s'].'/?r='.$r.'&m=chatbot&page=list&cat=';

// oneFrame 페이지 여부 
if($page=='vendor/main' || ($m=='chatbot' && ($page=='view'||$page=='chat')) || $_CA[0]=='intro') $is_oneFrame = true;
else $is_oneFrame = false;

// no nav 페이지 
if(($m=='chatbot' && $page=='chat'||$page=='chat_test')||$page=='vendor/main') $no_nav = true;
else $no_nav = false;

// 마이페이지 여부값 
if($_CA[0]=='mybot'||($m=='chatbot'&&substr($page,0,6) == 'vendor')) $is_vendorPage = true;
else $is_vendorPage = false;

// 소셜 로그인 체크 
$g['mdl_slogin'] = 'slogin';
$g['use_social'] = is_file($g['path_module'].$g['mdl_slogin'].'/var/var.php');
if ($g['use_social'])
{
    include_once $g['path_module'].$g['mdl_slogin'].'/action/a.slogin.check.php';
}

?>