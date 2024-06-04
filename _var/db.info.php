<?php
$_DB['host'] = $GLOBALS['_db_bot']['host'];
$_DB['user'] = $GLOBALS['_db_bot']['user'];
$_DB['pass'] = $GLOBALS['_db_bot']['pass'];
$_DB['port'] = $GLOBALS['_db_bot']['port'];
$_DB['name'] = isset($_SESSION['mbr_db']) && $_SESSION['mbr_db'] ? $_SESSION['mbr_db'] : $GLOBALS['_db_bot']['name'];

if ($_SESSION['mbr_db'] || $GLOBALS['_cloud_'] === false) {
	$DB = $_DB;
} else {
	$DB['host'] = $_db_front['host'];
	$DB['name'] = $_db_front['name'];
	$DB['user'] = $_db_front['user'];
	$DB['pass'] = $_db_front['pass'];
	$DB['port'] = $_db_front['port'];
}

$DB['head'] = 'rb';
$DB['type'] = 'InnoDB';

$CENTERHost = $g['front_host'];
?>