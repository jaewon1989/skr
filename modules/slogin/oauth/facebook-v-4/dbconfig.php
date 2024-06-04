<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'username');    // DB username
define('DB_PASSWORD', 'password');    // DB password
define('DB_DATABASE', 'database');      // DB name
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
?>