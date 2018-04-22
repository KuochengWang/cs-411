<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=us-ascii">
	<title></title>
	<link rel="stylesheet" href="w3.css">
</head>

<?php session_start(); ?>

<?php
$mysqli = new mysqli("cpanel3.engr.illinois.edu", "funproject_funproject", "X5V-tfN-7Yh-nnb", "funproject_Database");

date_default_timezone_set('CST6CDT');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $sender_username = $_SESSION["username"];
    $recipient_username = $mysqli->real_escape_string($_POST['transaction_username']);
    $currency_type = $_POST['currency_type'];
    $transaction_type = "User";
    $transaction_amount = $mysqli->real_escape_string($_POST['transaction_amount']);
    $transaction_date = date("Y-m-d") . " " . date("h:i:s") . ".";
    
    
    #echo "$currency_type" . "," . "$transaction_date" . "," . $sender_username;
    
    $sql_userbalance = "SELECT * FROM Account WHERE username='$sender_username' AND type='$currency_type'";
    $sql_recipientbalance = "SELECT * FROM Account WHERE username='$recipient_username' AND type='$currency_type'";
    
    if($mysqli->query($sql_userbalance) == true){
        $result1=$mysqli->query($sql_userbalance);
        $row_userbalance = $result1->fetch_assoc();
        $result2=$mysqli->query($sql_recipientbalance);#valid recipient?
        $row_recipientbalance = $result2->fetch_assoc();
        $sender_walletID = $row_userbalance['walletID'];
        $recipient_walletID = $row_recipientbalance['walletID'];
        $sql_transaction1 = "INSERT INTO Transaction (username, datetime, transaction_type, amount, type, to_, from_) " . "VALUES ('$sender_username', '$transaction_date', '$transaction_type', '$transaction_amount', '$currency_type', '$recipient_walletID', '$sender_walletID')";
        $sql_transaction2 = "INSERT INTO Transaction (username, datetime, transaction_type, amount, type, to_, from_) " . "VALUES ('$recipient_username', '$transaction_date', '$transaction_type', '$transaction_amount', '$currency_type', '$sender_walletID', '$recipient_walletID')";
        echo "Able to retrieve balance!\n";
        #$_SESSION['balance'] = $result['balance'];
        if(($mysqli->query($sql_transaction1) == true) && ($mysqli->query($sql_transaction2) == true) && ($transaction_amount <= $row_userbalance['balance']) && ($result2->num_rows > 0)){
            $sender_balance = $row_userbalance['balance'] - $transaction_amount;
            $recipient_balance = $row_recipientbalance['balance'] + $transaction_amount;
            $sql_senderBalanceUpdate = "UPDATE Account SET balance='$sender_balance' WHERE username='$sender_username' AND type='$currency_type'";
            $sql_recipientBalanceUpdate = "UPDATE Account SET balance='$recipient_balance' WHERE username='$recipient_username' AND type='$currency_type'";
            echo "User is present;$sender_balance;  $recipient_balance";
            
            if(($mysqli->query($sql_senderBalanceUpdate) == true) && ($mysqli->query($sql_recipientBalanceUpdate) == true)){
                $_SESSION['transaction_message'] = "Transaction Successful!";
                echo "Transaction Successful!";
                #header("location: crypto.html");
            }
        }
        else{
            echo "Error: Transaction could not be completed!";
        }
    }
    else{
        echo "failed to get balance";
    }
    #echo $result['balance'];
    
    
}
$mysqli->close();
#&& ($transaction_amount > $result)

?>



<h4>Transaction:</h4>
<!--<div class="alert alert-success"><?= $_SESSION['transaction_message'] ?></div> -->
<!--<div class="alert alert-balance"><?= $_SESSION['balance_message'] ?></div>-->
<form action="transaction.php" method="post" target="_blank">
<p align="left">Recipient Username:<br />
<input name="transaction_username" type="text" required/><br />

						
						amount:<br />
						<input name="transaction_amount" type="number" step=0.0001 required/><br />
						
						<div>
						    Cryptocurrency Type: <br />
    <input name="currency_type" type="radio" value="BTC" checked />BTC<br />
    <input name="currency_type" type="radio" value="LTC" />LTC<br />
    <input name="currency_type" type="radio" value="ETH" />ETH<br />
						    
    </div>
    <input name="complete" type="submit" value="complete" />
						</p>
						</form>
<br />

