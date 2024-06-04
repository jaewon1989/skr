<?php

// require_once( 'mysqli.class.php' );

function getMenuData($table,$site_uid)
{
    require_once 'DbConnect.php';
   $db = new DbConnect();
   $DB_CONNECT= $db->connect();
   $result =mysqli_query($DB_CONNECT,"SELECT * FROM ".$table." where hidden=0 and site=".$site_uid);
   $row = array();
   while( $r = mysqli_fetch_row($result)) 
   {
        $row[] = $r;
    }
   return $row;
}




?>