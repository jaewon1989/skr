<?php

if(!defined('__KIMS__')) exit;

$QVAL="device_id='$deviceid',device_token='$token',device_kind='$dev'"; 

getDbUpdate('fm_member',$QVAL,'member_seq='.$memberuid);

exit;

?>