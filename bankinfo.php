<?php session_start() ?>


<div class="body-content">
    <div class="alert alert-success"><?= $_SESSION['message'] ?></div><br />
    Welcome <span class="user"><?= $_SESSION['fullname'] ?></span>
</div>

<?php
$mysqli = new mysqli("cpanel3.engr.illinois.edu", "funproject_funproject", "X5V-tfN-7Yh-nnb", "funproject_Database");
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($_POST['bank_password'] == $_POST['bank_confirmpassword']){
        $crypto_username = $_SESSION['username'];
        $bank_type = $_POST['bank_type'];
        $bank_username = $mysqli->real_escape_string($_POST['bank_username']);
        $bank_password = $mysqli->real_escape_string($_POST['bank_password']);
        $sql = "INSERT INTO Bank (bankID, crypto_username, bank_username, password) " . "VALUES ('$bank_type', '$crypto_username', '$bank_username', '$bank_password')";
        $sql_changeInUser = "UPDATE User SET bankID='$bank_type' WHERE username='$crypto_username'";
    }
    if($mysqli->query($sql) === true){
            $mysqli->query($sql_changeInUser);
            $_SESSION['message'] = "Bank information verified!";
            header("location:crypto.html");
        }
        else{
            $_SESSION['message'] = "Information could not be verified";
        }
    
}

$mysqli->close();

?>


<div class="module">
    <h1>Create an account: Step 2</h1>
    <form class="form" action="bankinfo.php" method="post" enctype="multipart/form-data" autocomplete="off">
        
    <div>
	Select Your Bank: <br />
    <input name="bank_type" type="radio" value=0 checked />Bank A<br />
    <input name="bank_type" type="radio" value=1 />Bank B<br />
    <input name="bank_type" type="radio" value=2 />Bank C<br />
    <input name="bank_type" type="radio" value=3 />Bank D<br />
    <input name="bank_type" type="radio" value=4 />Bank E<br />
    <input name="bank_type" type="radio" value=5 />Bank F<br />
    <input name="bank_type" type="radio" value=6 />Bank G<br />
    <input name="bank_type" type="radio" value=7 />Bank H<br />
    <input name="bank_type" type="radio" value=8 />Bank I<br />
    <input name="bank_type" type="radio" value=9 />Bank J<br />
    </div>
      
      <input type="text" placeholder="Bank Username" name="bank_username" required />
      <input type="password" placeholder="Bank Password" name="bank_password" autocomplete="new-password" required />
      <input type="password" placeholder="Confirm Password" name="bank_confirmpassword" autocomplete="new-password" required />
      <input type="number" placeholder="Bank Balance" name="bank_balance" min ="0" required />
      <input type="submit" value="Verify" name="verify" class="btn btn-block btn-primary" />
    </form>
  </div>