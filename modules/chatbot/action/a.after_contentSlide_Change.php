<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅 

$result=array();
$result['error']=false;
$mod=$_GET['mod'];

$mod_to_title =array('hot'=>'인기','new'=>'최신','video'=>'영상');

$TMPL['mod'] = $mod;
$TMPL['mod_title'] = $mod_to_title[$mod];
$_file=$mod=='best'?$mod:'default';

// 리스트타입 active 적용 
$TMPL['listType_single_active'] = $list_type == 'single'? 'active':'';
$TMPL['listType_multi_active'] = $list_type == 'multi'? 'active':'';

$skin=new skin('filter/filter_'.$_file);
$result['content']=$skin->make();

echo json_encode($result);
exit;
?>
