<?php

$mysqli = new mysqli("cpanel3.engr.illinois.edu", "funproject_funproject", "X5V-tfN-7Yh-nnb", "funproject_Database");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$user = trim($_POST["username"]);
$pass = trim($_POST["password"]);

$sql = "SELECT * FROM `User` WHERE username='$user' AND password='$pass'";

$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
       $success = true;
}

if($success == true) {
    $sql2 = "DELETE FROM `User` WHERE username='$user' AND password='$pass'";
    $mysqli->query($sql2);
    $sql3 = "DELETE FROM `Account` WHERE username='$user'";
    $mysqli->query($sql3);
    echo 'User deleted.';
} else {
    echo '<div class="alert alert-danger">It looks like your username and/or password are incorrect. Please try again.</div>';
    echo "User is $user\n";
    echo "Pass is $pass";
}

$mysqli->close();

?>