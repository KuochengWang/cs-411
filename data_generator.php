<?php

$mysqli = new mysqli("cpanel3.engr.illinois.edu", "funproject_funproject", "X5V-tfN-7Yh-nnb", "funproject_Database");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

date_default_timezone_set('CST6CDT');

$time = getdate();
$mon = $time[mon];
$day = $time[mday]; 
$cryptoType = $_POST["cryptoType"];
$amount = $_POST["amount"];

$BTCrate = 173.3 * (($mon-3)*30+$day) + 7530.9 + 10*(rand(0,1))+2*rand(0,1);
$ETHrate = 19.014 * (($mon-3)*30+$day) + 493.7 + 10*(rand(0,1))+2*rand(0,1);
$LTCrate = 3.0108 * (($mon-3)*30+$day) + 148.84 + 10*(rand(0,1))+2*rand(0,1);

session_start();
echo("<strong>Welcome, " . $_SESSION["username"] . "!<strong>");

$BTCrequest = $BTCrate * $amount;
$ETHrequest = $ETHrate*$amount;;
$LTCrequest = $LTCrate*$amount;
$request = 0;

$username = $_SESSION["username"];
$sql = "SELECT balance FROM User WHERE username = '$username'";
if(!$mysqli->query($sql))
{
    echo("cannot find find the correct person");
}

$balance_query = $mysqli->query($sql);
$account_amount = mysqli_fetch_row($balance_query);
echo ($account_amount[0]);

echo("test0");

echo($cryptoType);

if($cryptoType=="btc")
{
    if($BTCrequest>$account_amount[0])
        $request = $BTCrequest;
      
}
else if($cryptoType=="eth")
{
    if($ETHrequest>$account_amount[0])
        $request = $ETHrequest;
}
else
{
    if($LTCrequest>$account_amount[0])
        $request = $LTCrequest;
}


$wallet_ID = findWallet($cryptoType,$mysqli,$username);
if(checkBalance($mysqli,$username,$request))
{
    updateTransaction($username,$amount,$cryptoType,$mysqli,$wallet_ID);
    updateAccount($mysqli,$wallet_ID,$amount);
    updateUser($mysqli,$username,$request);
}


function checkBalance($mysqli,$username,$request)
{
    $sql_balance =   
        "SELECT balance from User Where username = '$username'";

    $balance_query = $mysqli->query($sql_balance);
    if(!$balance_query)
    {
        echo("cannot find find walletid");
    }
    
    $user_balance = mysqli_fetch_row($balance_query);
    echo($user_balance[0]);
    if($user_balance[0]<$request) {
        echo("balance not enough");
        return false;
    }
    else
        return true;
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


function updateTransaction($username,$amount,$cryptoType,$mysqli,$wallet)
{
    if($cryptoType=="btc")
    {
        $supply = "Bitcoin Supply";
    }
    else if($cryptoType=="eth")
    {
        $supply = "Ethereum Supply";
    }
    else 
    {
        $supply = "Litecoin Supply";
    }
    
    $date = date("Y-m-d") . " " . date("h:i:s") . ".";
    $sql_transaction = "INSERT INTO Transaction " . "VALUES ('$username', '$date', 'Market', $amount, '$cryptoType', '$wallet', '$supply')";
    
    echo($sql_transaction);
    
    if(!$mysqli->query($sql_transaction))
    {
        echo("cannot find find the transaction");
    }
}

function updateAccount($mysqli,$wallet,$amount)
{
    $sql_account = "UPDATE Account
    SET balance = balance + '$amount'
    WHERE walletid = '$wallet'";
    if(!$mysqli->query($sql_account))
    {
        echo("cannot Update the transaction");
    }
}

function updateUser($mysqli,$username,$request)
{
    $sql_user = "UPDATE User
SET balance = balance - '$request'
WHERE username = '$username'";
    if(!$mysqli->query($sql_user))
    {
        echo("cannot Update the balance");
    }
}
?>