<?php
if(!defined('__KIMS__')) exit;
include $theme.'/function.php';
if($mod=='issue-new') include $theme.'/write.php';
else echo getFeedModal($uid,$mod,$fmod);
exit;
?>
