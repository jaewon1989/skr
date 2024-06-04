<?php
if(!defined('__KIMS__')) exit;
include_once $theme.'main.func.php'; // 테마 함수파일 인클루드 

$category=$uid; // 카테고리 uid 

$result=array();
$result['error']=false;

if($mod=='membership')  $result['content']=Shop_theme_getMembershipList($category,$sort,$orderby,$recnum,$_WHERE);
else $result['content']=Shop_theme_getProductList($category,$sort,$orderby,$recnum,$_WHERE);
echo json_encode($result,true);
exit;
?>

?>