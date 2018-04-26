<?php

$mysqli = new mysqli("cpanel3.engr.illinois.edu", "funproject_funproject", "X5V-tfN-7Yh-nnb", "funproject_Database");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

session_start();
date_default_timezone_set('CST6CDT');
$payer_username = $_SESSION["username"];
$date = $_POST['request_time'];
$mon = substr($date, 5, 2);
$day = substr($date, 8, 2);

$BTCrate = 173.3 * (($mon-3)*30+$day) + 7530.9 + 10*(rand(0,1))+2*rand(0,1);
$ETHrate = 19.014 * (($mon-3)*30+$day) + 493.7 + 10*(rand(0,1))+2*rand(0,1);
$LTCrate = 3.0108 * (($mon-3)*30+$day) + 148.84 + 10*(rand(0,1))+2*rand(0,1);

$requested_type = $_POST["cryptoType"];
$crypto = findCrypto($mysqli,$date,$payer_username);
$cryptoType = $crypto[1];
$amount = $crypto[0];
if($cryptoType!=$requested_type)
   echo("You do not have any crypto request on this type");
else
{
    $payeeWallet = findWallet($requested_type,$mysqli,$payer_username);

    if(($payeeWallet == null or  $payeeWallet == ''))
        echo("The user your requested does not have this coin. Check again.");
    
    
    $BTCrequest = $BTCrate * $amount;
    $ETHrequest = $ETHrate*$amount;;
    $LTCrequest = $LTCrate*$amount;
    $request = 0;
    
    $sql = "SELECT balance FROM User WHERE username = '$payer_username'";
    if(!$mysqli->query($sql))
    {
        echo("cannot find find the correct person");
    }
    
    $balance_query = $mysqli->query($sql);
    $account_amount = mysqli_fetch_row($balance_query);
    
    
    if($cryptoType=="btc")
    {
        $request = $BTCrequest;
    }
    else if($cryptoType=="eth")
    {
        $request = $ETHrequest;
    }
    else
    {
        $request = $LTCrequest;
    }
    
    $wallet_ID = findWallet($cryptoType,$mysqli,$payer_username);
    
    if(checkBalance($wallet_ID,$amount,$mysqli))
    {
        $payee_walletID = $crypto[2];
        $payer_walletID = $crypto[3];
        updateTransaction($payer_username,$payee_walletID,$payer_walletID,$amount,$cryptoType,$mysqli);
        updateAccount($mysqli,$payee_walletID,$payer_walletID,$amount);
        updateUser($mysqli,$payer_username,$request,$payee_walletID,$payer_walletID);
        updateRequest($mysqli,$date);
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

function findCrypto($mysqli,$time,$payer)
{
    $query = "SELECT amount,type,payee,payer FROM Request WHERE datetime <= '$time' + interval 1 second AND datetime >= '$time' - interval 1 second";
    if(!$mysqli->query($query))
    {
        echo("cannot find the request");
    }
    
    $type = $mysqli->query($query);
    $crypto_type = mysqli_fetch_row($type);
    return $crypto_type;
}   

function checkBalance($wallet,$amount,$mysqli)
{
    $sql_balance =   
        "SELECT balance
        FROM Account
        WHERE walletID = '$wallet'";
    
    $balance_query = $mysqli->query($sql_balance);
    
    if(!$balance_query)
    {
        echo("cannot find find walletid");
    }
    
    $wallet_balance = mysqli_fetch_row($balance_query);
    echo($wallet_balance[0]);
    if($wallet_balance[0]<$amount) {
        echo("balance not enough");
        return false;
    }
    else
        return true;
}

function updateTransaction($payer_username,$payee_walletID,$payer_walletID,$amount,$cryptoType,$mysqli)
{
    $date = date("Y-m-d") . " " . date("h:i:s") . ".";
    
    $sql_transaction = "INSERT INTO Transaction " . "VALUES ('$payer_username', '$date', 'User', $amount, '$cryptoType', '$payee_walletID','$payer_walletID')";
    
    echo("Request accepted!");
    
    if(!$mysqli->query($sql_transaction))
    {
        echo("cannot find find the transaction");
    }
}

function updateAccount($mysqli,$payee_walletID,$payer_walletID,$amount)
{
    $sql_account = "UPDATE Account
    SET balance = balance - '$amount'
    WHERE walletid = '$payer_walletID'";
    
    if(!$mysqli->query($sql_account))
    {
        echo("cannot Update payer account");
    }
    
    $sql_account = "UPDATE Account
    SET balance = balance + '$amount'
    WHERE walletid = '$payee_walletID'";
    
    if(!$mysqli->query($sql_account))
    {
        echo("cannot Update payee account");
    }
}

function updateUser($mysqli,$payer_username,$request,$payee_walletID,$payer_walletID)
{
    $sql_payer = "UPDATE User
SET balance = balance - '$request'
WHERE username = '$payer_username'";

    if(!$mysqli->query($sql_payer))
    {
        echo("cannot Update user balance");
    }
    
  # UPDATE `User` AS u INNER JOIN Account as a ON (a.username=u.username) SET u.balance = u.balance-0 WHERE a.walletID = '1HmshwN3aVtraQcUVTCxFUdfyVgLxmShRo'
  
    $payee_username = findUsername($mysqli,$payee_walletID);
    $sql_payee = "UPDATE User
SET balance = balance + '$request'
WHERE username = '$payee_username'";
    if(!$mysqli->query($sql_payee))
    {
        echo("cannot Update user balance");
    }

}

function findUsername($mysqli,$payee_walletID)
{
    $sql_user = "SELECT username FROM Account WHERE walletID = '$payee_walletID'";
    $sql_query = $mysqli->query($sql_user);
    if(!$sql_query)
    {
        echo("cannot find username");
    }
    $username = mysqli_fetch_row($sql_query);
    return $username[0];
}

function updateRequest($mysqli,$date)
{
    $delete_sql = "DELETE FROM Request WHERE datetime <= '$date' + interval 1 second AND datetime >= '$date' - interval 1 second";
    if(!$mysqli->query($delete_sql))
    {
        echo("cannot Update the request");
    }
}
$mysqli->close();
?>


