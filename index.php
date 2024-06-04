<?php
header("Content-type:text/html;charset=utf-8");
define('__KIMS__',true);
error_reporting(E_ERROR);
//error_reporting(E_ALL & ~E_NOTICE);
ini_set('session.cookie_httponly', 1);
session_start();

if(!get_magic_quotes_gpc()) {
	if(is_array($_GET)) {
        array_walk_recursive($_GET, function(&$item, $key) {
            $item = addslashes($item);
        });
        foreach($_GET as $key=>$val) ${$key} = $val;
    }
    if(is_array($_POST)) {
        array_walk_recursive($_POST, function(&$item, $key) {
            $item = addslashes($item);
        });
        foreach($_POST as $key=>$val) ${$key} = $val;
    }
} else {
	if (!ini_get('register_globals')) {
		extract($_GET);
		extract($_POST);
	}
}

parse_str($_SERVER['QUERY_STRING'], $aQuery);
if($aQuery['mod']) {
    if($aQuery['mod'] == 'idpwsearch' || $aQuery['mod'] == 'join' || $aQuery['id']) {
        header("Location: /adm"); exit;
    }
}
if($aQuery['r']) {
    if($aQuery['r'] != 'home') {
        header("Location: /adm"); exit;
    }
}
if($aQuery['m']) {
    if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/modules/'.$aQuery['m'])) {
        header("Location: /adm"); exit;
    }
    if($aQuery['m'] == 'admin') {
        header("Location: /adm"); exit;
    }
    if($aQuery['a']) {
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/modules/'.$aQuery['m'].'/action/a.'.$aQuery['a'].'.php')) {
            header("Location: /adm"); exit;
        }
    }
}

$d = array();
$g = array(
	'path_root'   => './',
	'path_core'   => './_core/',
	'path_var'    => './_var/',
	'path_tmp'    => './_tmp/',
	'path_layout' => './layouts/',
	'path_module' => './modules/',
	'path_widget' => './widgets/',
	'path_switch' => './switches/',
	'path_plugin' => './plugins/',
	'path_page'   => './pages/',
	'path_common'   => './common/',
	'path_utils'   => './utils/',
	'path_file'   => './files/',
    'path_blackList' => './blackList/'
);

$chConfDir = './configuration';

// host 도메인 관련 (벤더 시스템에만 적용)
if('localhost' === $_SERVER['SERVER_NAME']){
    require $chConfDir.'/env-local.php';
}
elseif('bottalks.nexuscommunity.kr' === $_SERVER['SERVER_NAME']){
    require $chConfDir.'/env-dev.php';
}
elseif('61.250.39.72' === $_SERVER['SERVER_ADDR']){
    require $chConfDir.'/env-stage.php';
}
else{
    $chConfDir = substr($_SERVER['DOCUMENT_ROOT'], 0, strrpos($_SERVER['DOCUMENT_ROOT'], "/"));
    require $chConfDir.'/bottalksConf.php';
}

$g['time_split'] = explode(' ',microtime());
$g['time_start'] = $g['time_split'][0]+$g['time_split'][1];
$g['time_srnad'] = $g['time_split'][1].substr($g['time_split'][0],2,6);
$g['sm_time'] = ini_get('session.gc_maxlifetime');
$g['https_on'] = $_SERVER['HTTPS']=='on' || stripos($_SERVER['HTTP_X_FORWARDED_PROTO'],'https') !== false ? true : false;

require $g['path_var'].'db.info.php';
require $g['path_var'].'table.info.php';
require $g['path_var'].'switch.var.php';
require $g['path_var'].'plugin.var.php';
require $g['path_module'].'admin/var/var.system.php';

$g['url_file'] = str_replace('/index.php','',$_SERVER['SCRIPT_NAME']);
$g['url_host'] = 'http'.($g['https_on'] ?'s':'').'://'.$_SERVER['HTTP_HOST'];
$g['url_http'] = $g['url_host'].($d['admin']['http_port']&&$d['admin']['http_port']!=80?':'.$d['admin']['http_port']:'');
$g['url_sslp'] = 'https://'.$_SERVER['HTTP_HOST'].(!$g['https_on']&&$d['admin']['ssl_port']?':'.$d['admin']['ssl_port']:'');
$g['url_root'] = $g['url_http'].$g['url_file'];
$g['ssl_root'] = $g['url_sslp'].$g['url_file'];

require $g['path_core'].'function/db.mysql.func.php';
require $g['path_core'].'function/sys.func.php';
foreach(getSwitchInc('start') as $_switch) include $_switch;

//-------------------------------------------------------------
if($GLOBALS['_cloud_'] === true) {
    include $g['path_core'].'function/sso.class.php';
}
//-------------------------------------------------------------

// 파라미터 없이 도메인만으로 접속 시
if($_SERVER['REQUEST_METHOD'] == "GET" && ($_SERVER[ "REQUEST_URI"] == "/" || $_SERVER[ "QUERY_STRING"] == "")) {
    getLink($g['s'].'/adm','','','');
}

require $g['path_core'].'engine/main.engine.php';
if ($keyword) {
	$keyword = trim($keyword);
	$_keyword= stripslashes(htmlspecialchars($keyword));
}
if (!$p) $p = 1;
if (!is_dir($g['path_module'].$m)) $m = $g['sys_module'];
$g['dir_module'] = $g['path_module'].$m.'/';
$g['url_module'] = $g['s'].'/modules/'.$m;

if ($i) require $g['path_core'].'engine/interface.engine.php';
if ($a) require $g['path_core'].'engine/action.engine.php';
if ($_HS['open'] > 1) require $g['path_core'].'engine/siteopen.engine.php';
if (!$s && $m != 'admin') getLink($g['s'].'/?m=admin&module='.$g['sys_module'].'&nosite=Y','','','');
if($modal) $g['main'] = $g['path_module'].$modal.'.php';
else include $g['dir_module'].'main.php';

if ($m=='admin' || $iframe=='Y') $d['layout']['php'] = $_HM['layout'] = '_blank/default.php';
else {
	if (!$g['mobile']||$_SESSION['pcmode']=='Y'){
	   $d['layout']['php'] = $prelayout ? $prelayout.'.php' : ($_HM['layout'] ? $_HM['layout'] : $_HS['layout']);
	}else{
	   $d['layout']['php'] = $prelayout ? $prelayout.'.php' : ($_HM['layout'] ? $_HM['layout']:($_HS['m_layout'] ? $_HS['m_layout'] : $_HS['layout']));
	}
}

$d['layout']['dir'] = dirname($d['layout']['php']);
$g['dir_layout'] = $g['path_layout'].$d['layout']['dir'].'/';
$g['url_layout'] = $g['s'].'/layouts/'.$d['layout']['dir'];
$g['img_layout'] = $g['url_layout'].'/_images';

define('__KIMS_CONTENT__',$g['path_core'].'engine/content.engine.php');

if($my['admin'] && (!$_SERVER['HTTP_REFERER'] || $panel=='Y') && $panel!='N' && !$iframe && !is_file($g['dir_layout'].'_var/nopanel.txt'))
{
	include $g['path_core'].'engine/adminpanel.engine.php';
}
else
{
	foreach($g['switch_1'] as $_switch) include $_switch;

	if ($m!='admin')
	{
		include $g['path_var'].'sitephp/'.$_HS['uid'].'.php';
		if($_HS['buffer'])
		{
			$g['buffer']=true;
			ob_start('ob_gzhandler');
		}
	}

	$g['location']	= getLocation(0);
	$g['browtitle'] = getPageTitile();

	include './layouts/'.$d['layout']['dir'].'/_includes/_import.control.php';
	include $g['path_layout'].$d['layout']['php'];
	foreach($g['switch_4'] as $_switch) include $_switch;
	//echo "\n".'<!-- KimsQ Rb v.'.$d['admin']['version'].' / Runtime : '.round(getCurrentDate()-$g['time_start'],3).' -->';
	if($g['buffer']) ob_end_flush();

}
db_close($DB_CONNECT);
?>