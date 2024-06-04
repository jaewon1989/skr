<?php
if(!defined('__KIMS__')) exit;


$result=array();
$result['error']=false;

$data = $_POST['data'];

$uid_arr = json_decode(stripcslashes($data['uid_arr']),true);
$price_arr = json_decode(stripcslashes($data['price_arr']),true);

foreach ($uid_arr as $index => $uid) {
    $price = $price_arr[$index];
    getDbUpdate('legacy_products',"price='$price'",'uid='.$uid);
}


echo json_encode($result);  
exit;
?>
