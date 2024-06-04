<?php
if(!defined('__KIMS__')) exit;

$iframe = 'Y';
$page = 'main';

$g['dir_module_skin'] = $g['dir_module'].'pages/';
$g['url_module_skin'] = $g['url_module'].'/pages';
$g['img_module_skin'] = $g['url_module_skin'].'/image';

$g['dir_module_mode'] = $g['dir_module_skin'].$page;
$g['url_module_mode'] = $g['url_module_skin'].'/'.$page;

$g['main'] = $g['dir_module_mode'].'.php';
?>
