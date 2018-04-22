<?php

$mysqli = new mysqli("cpanel3.engr.illinois.edu", "funproject_funproject", "X5V-tfN-7Yh-nnb", "funproject_Database");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

session_start();

date_default_timezone_set('CST6CDT');

$payee_username = $_SESSION["username"];
$payer_username = $mysqli->real_escape_string($_POST['payer_username']);
$cryptoType = $_POST['cryptoType'];
$amount = $mysqli->real_escape_string($_POST['amount']);
$date = date("Y-m-d") . " " . date("h:i:s") . ".";

$payeeWallet = findWallet($cryptoType,$mysqli,$payee_username);
$payerWallet = findWallet($cryptoType,$mysqli,$payer_username);

//Going to need an if statement for insertRequest if wallet isn't found

if(!($payeeWallet == null or $payeeWallet == null or $payeeWallet == '' or $payerWallet == ''))
    insertRequest($date, $amount, $cryptoType, $payeeWallet, $payerWallet, $mysqli);
else
    echo("Request failed. Check your information.");

function findWallet($cryptoType,$mysqli,$username)
{
    $sql_wallet = "SELECT walletID FROM Account WHERE username = '$username' AND type = '$cryptoType'";
    $wallet_query = $mysqli->query($sql_wallet);
    
    if(!$wallet_query)
    {
        echo("Can't find wallet");
    }
    
    $walletid = mysqli_fetch_row($wallet_query);
    $wallet = $walletid[0];
    return $wallet;
}

function insertRequest($date, $amount, $cryptoType, $payeeWallet, $payerWallet, $mysqli)
{
    $sql_request = "INSERT INTO Request VALUES ('$date', '$amount', '$cryptoType', '$payeeWallet', '$payerWallet')";
    
    echo($sql_request);
    
    if(!$mysqli->query($sql_request))
    {
        echo("Request insert failed");
    }
}








