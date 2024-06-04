<?php
if(!defined('__KIMS__')) exit;

$json_data=stripslashes($data);
$data_arr=json_decode($json_data,true);

$content=$html=='text'?strip_tags($data_arr['content']):$data_arr['content'];
$uid=$data_arr['uid'];
$theme=$data_arr['theme'];
$parent=$data_arr['parent'];
$c_sort=$data_arr['c_sort'];
$c_recnum=$data_arr['c_recnum'];
$c_page=$data_arr['c_page'];
$c_orderby=$data_arr['c_orderby'];
$fmod=$data_arr['fmod'];

include $theme.'/_var.php';
include $theme.'/function.php';

getDbUpdate($table['s_comment'],"content='".$content."'",'uid='.$uid);

$response=getCommentList($fmod,$theme,$parent,$_where,$c_recnum,$c_sort,$c_orderby,$orderby2,$c_page);
echo $response;

exit;
?>
