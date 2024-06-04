<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php';
$g['dir_include']=$g['dir_module'].'includes/';

$mod = $mod ? $mod : 'best';
if (strstr('[uploader][address][company][coupon][orderinfo][tax]',$mod))
{
	$type = $type ? $type : 'main';
	$iframe = 'Y';
	$g['dir_module_skin'] = $g['dir_module'].'lang.'.$_HS['lang'].'/'.$mod.'/';
	$g['url_module_skin'] = $g['url_module'].'/lang.'.$_HS['lang'].'/'.$mod;
	$g['img_module_skin'] = $g['url_module_skin'].'/image';
	$g['dir_module_mode'] = $g['dir_module_skin'].$type;
	$g['url_module_mode'] = $g['url_module_skin'].'/'.$type;
	$g['main'] = $g['dir_module_mode'].'.php';
}
else {
    
    // 테마 & 
	$d['ad']['skin'] = $d['ad']['skin_main'];
	if($mod=='admin') $_HM['layout'] = 'admin/default.php';

	if ($g['mobile']&&$_SESSION['pcmode']!='Y')
	{
		if ($d['ad']['skin_mobile']!='none')
		{
			if ($C['uid']) $d['ad']['skin'] = $C['skin_mobile'] ? $C['skin_mobile'] : $d['ad']['skin_mobile'];
			else $d['ad']['skin'] = $d['ad']['skin_mobile'];
		}
	} 
    
    $CONF['theme_path']=$g['dir_module'].'theme';
    $CONF['theme_name']=$d['ad']['skin']; 

    // class 인클루드 
    require_once $g['dir_include'].'base.class.php';
    require_once $g['dir_module'].'languages/'.(!empty($_GET['lang']) ? $_GET['lang']:'korean').'.php';
    require_once $g['dir_include'].'module.class.php';
 
    if($mod=='hot'||$mod=='new'||$mod=='video') $pmod='feed';
	else $pmod = $pmod ? $pmod : $mod;
	
	$g['sns_reset']= getLinkFilter($g['s'].'/?'.($_HS['usescode']?'r='.$r.'&amp;':'').($c?'c='.$c:'m='.$m),array($mod?'mod':'',$skin?'skin':'',$iframe?'iframe':'')).($cat!=''?'&amp;cat='.$cat:'');
	$g['sns_list']	= $g['sns_reset'].getLinkFilter('',array('p','sort','orderby','recnum','maker','brand',$type?'type':'',$where&&$keyword?'where,keyword':''));
	$g['sns_view']	= $g['sns_list'].'&amp;uid=';
	$g['pagelink']  = $g['sns_list'];

	$g['dir_module_skin'] = $g['dir_module'].'theme/'.$d['ad']['skin'].'/';
	$g['url_module_skin'] = $g['url_module'].'/theme/'.$d['ad']['skin'];
	$g['img_module_skin'] = $g['url_module_skin'].'/image';
    $g['dir_module_mode'] = $g['dir_module_skin'].$pmod;
  	$g['url_module_mode'] = $g['url_module_skin'].'/'.$pmod;
  	$g['main'] = $g['dir_module_mode'].'.php';
}
?>