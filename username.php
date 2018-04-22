<?php

#$mysqli = new mysqli("localhost", "cs411demo_dude", "letmein!", "cs411demo_genes");
$mysqli = new mysqli("cpanel3.engr.illinois.edu", "funproject_funproject", "X5V-tfN-7Yh-nnb", "funproject_Database");

header('Location: signup.php');
exit;

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}



$mysqli->close();

?>