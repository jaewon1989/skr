<?php
// 사이트별 변수 셋팅
$g['layoutVarForSite'] = $g['dir_layout'].'_var/_var.'.$r.'.php';
include is_file($g['layoutVarForSite']) ? $g['layoutVarForSite'] : $g['dir_layout'].'_var/_var.php';

// 레이아웃에 포함된 메인페이지 사용할 경우
if (strstr($g['main'],$g['dir_layout']) && !$prelayout) $d['layout']['php'] = $d['layout']['dir'].'/chat.php';
else if($m=='sns' && $mod=='profile') $d['layout']['php'] = $d['layout']['dir'].'/blank.php';

if (isset($layoutPage))
{
	$g['dir_module_mode'] = $g['dir_layout'].'/_pages/'.$layoutPage;
	$g['url_module_mode'] = $g['url_layout'].'/_pages/'.$layoutPage;
	$g['main'] = $g['dir_layout'].'/_pages/'.$layoutPage.'.php';
}

// SNS 클래스 설정 추가
$CONF['module']=$g['path_module'].'chatbot';
require_once $CONF['module'].'/var/var.php';
$CONF['theme_path']=$CONF['module'].'/theme';
$CONF['theme_name']=$d['chatbot']['skin_mobile']; // 모바일 테마 
$CONF['dir_include']=$CONF['module'].'/includes/';
require_once $CONF['dir_include'].'base.class.php';
require_once $CONF['module'].'/languages/'.(!empty($_GET['lang']) ? $_GET['lang']:'korean').'.php';
require_once $CONF['dir_include'].'module.class.php';
$chatbot = new Chatbot();

// 카테고리 링크 
$catLink = $g['s'].'/?r='.$r.'&m=chatbot&page=list&cat=';

// 소셜 로그인 체크 
$g['mdl_slogin'] = 'slogin';
$g['use_social'] = is_file($g['path_module'].$g['mdl_slogin'].'/var/var.php');
if ($g['use_social'])
{
    include_once $g['path_module'].$g['mdl_slogin'].'/action/a.slogin.check.php';
}
// 메인 홈페이지 여부 체크 
if(strstr($g['main'],$g['dir_layout'])) $is_home = true;
else $is_home = false;

if($my['uid']){
	$MVD = $chatbot->getVendorData($my['uid']); // My Vendor Data
	$my_vendor = $MVD['my_vendor']; // 내가 등록한 벤더가 있는가 ?
	$my_vendor_type = $MVD['my_vendor_type']; // 내가 등록한 벤더 타입 (1: 일반, 2: 프리미엄)
    $is_manager = $MVD['is_manager']; // 내가 메니져인지 여부
    $is_manager_auth = $MVD['is_manager_auth']; // 내가 메니져인 경우 승인 상태 (1: 승인, 2:미승인)  
    $our_manager_type = $MVD['our_manager_type']; // 내가 메니져로 있는 벤더 타입 (1: 일반, 프리미엄)    
}
if($my_vendor_type==1 || $our_manager_type==1) $can_build = false;
else $can_build = true;

?>
