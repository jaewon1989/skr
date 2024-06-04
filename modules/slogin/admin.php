<?php
if(!defined('__KIMS__')) exit;

$g['url_module_asset']=$g['path_module'] .$module.'/asset';
$g['url_module_img'] = $g['url_module_asset'].'/img';

include $g['path_module'].$module.'/admin/'.$front.'.php';

?>
