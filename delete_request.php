<?php

$mysqli = new mysqli("cpanel3.engr.illinois.edu", "funproject_funproject", "X5V-tfN-7Yh-nnb", "funproject_Database");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

session_start();

$payee_username = $mysqli->real_escape_string($_POST["payee_username"]);
$payer_username = $_SESSION["username"];
$cryptoType = $_POST['cryptoType'];

$payeeWallet = findWallet($cryptoType,$mysqli,$payee_username);
$payerWallet = findWallet($cryptoType,$mysqli,$payer_username);

if(!($payeeWallet == null or $payeeWallet == null or $payeeWallet == '' or $payerWallet == ''))
    deleteRequest($cryptoType, $payeeWallet, $payerWallet, $mysqli);
else
    echo("Deleting failed. Check your information.");

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

function deleteRequest($cryptoType, $payeeWallet, $payerWallet, $mysqli)
{
    $sql_delete = "DELETE FROM Request WHERE payee='$payeeWallet' AND payer='$payerWallet'";
    $delete_query = $mysqli->query($sql_delete);
    
    echo($sql_delete);
    
    if(!$mysqli->query($sql_delete))
    {
        echo("Delete failed");
    }
}

?>