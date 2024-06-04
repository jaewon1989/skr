<?php
/**
 * Database configuration
 */
require_once  Rb_root.'_var/db.info.php';

// DB 상수 지정
define('DB_USERNAME', $DB['user']);
define('DB_PASSWORD', $DB['pass']);
define('DB_HOST', $DB['host']);
define('DB_NAME', $DB['name']);

define('USER_CREATED_SUCCESSFULLY', 0);
define('USER_CREATE_FAILED', 1);
define('USER_ALREADY_EXISTED', 2);
?>
