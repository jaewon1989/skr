<?php
require 'dbconfig.php';
function checkuser($fuid,$ffname,$femail){
    global $connection;
    $check = mysqli_query($connection, "select * from Users where Fuid='$fuid'");
	$check = mysqli_num_rows($check);
	if (empty($check)) { // if new user . Insert a new record		
	$query = "INSERT INTO Users (Fuid,Ffname,Femail) VALUES ('$fuid','$ffname','$femail')";
	mysqli_query($connection, $query);	
	} else {   // If Returned user . update the user record		
	$query = "UPDATE Users SET Ffname='$ffname', Femail='$femail' where Fuid='$fuid'";
	mysqli_query($connection, $query);
	}
}?>
