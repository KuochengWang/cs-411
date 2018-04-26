<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=us-ascii">
	<title></title>
	<link rel="stylesheet" href="w3.css">
</head>

<?php

$mysqli = new mysqli("cpanel3.engr.illinois.edu", "funproject_funproject", "X5V-tfN-7Yh-nnb", "funproject_Database");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

session_start();
$username = $_SESSION["username"]; 
$cryptoTypes = array("BTC","LTC","ETH");
for($i = 0; $i < count($cryptoTypes); $i++) {
    $cryptoType = $cryptoTypes[$i];
    $walletID = findWallet($cryptoType,$mysqli,$username);
    //$sql_request = "SELECT *, username FROM Request WHERE payee='$walletID'";
    $sql_request = "SELECT datetime, amount, Request.type, username FROM Request, Account WHERE payer='$walletID' and walletID = payee";
    if ($result = $mysqli->query($sql_request)) {
    
        /* fetch associative array */
        while ($row = $result->fetch_assoc()) {
            printf ("%s %s %s %s", $row["datetime"], $row["amount"],$row["type"],$row["username"]);
              
            echo "<br>";
        }
    
        /* free result set */
        $result->free();
    }
}

function findWallet($cryptoType,$mysqli,$username)
{
   
    $sql_wallet = "SELECT walletID FROM Account WHERE username = '$username' AND type='$cryptoType'";
    
    if(!$mysqli->query($sql_wallet))
    {
        echo("cannot find find the correct person");
    }
    
    $wallet_query = $mysqli->query($sql_wallet);
    $walletid = mysqli_fetch_row($wallet_query);
    $wallet = $walletid[0];
    return $wallet;
}

$mysqli->close();
?>













