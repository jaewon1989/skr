<?php
if(!defined('__KIMS__')) exit;
include_once $theme.'main.func.php'; // 테마 함수파일 인클루드 

$R=getUidData($table[$m.'product'],$uid);
$content_arr=Shop_theme_getContentArray($R['content']);
$result=array();
$result['error']=false;
$result['label']=Shop_theme_getGoodsLabel($R);
$result['content01']=$content_arr[0];
$result['content02']=$content_arr[1];
$result['content03']=Shop_theme_getGoodsGallery($R);

echo json_encode($result,true);
exit;
?>

?>