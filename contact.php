<?php

$mysqli = new mysqli("cpanel3.engr.illinois.edu", "funproject_funproject", "X5V-tfN-7Yh-nnb", "funproject_Contacts");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}


$sql_prepare = $mysqli->prepare("INSERT INTO Funproject_Contacts (Username1,	Contact,Username2,Name) VALUES(?,?,?,?)");
$sql_prepare->bind_param("ssss", $user1,$contact ,$user2, $name);

$user1 = $_GET["user1"];
$user2 = $_GET["user2"];
$name = $_GET["name"];
$contact = $_GET["contact"];
$sql_prepare->execute();

$result = $mysqli->query($sql);


echo "New records created successfully";
$sql_prepare->close();
$mysqli->close();

?>