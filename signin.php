<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=us-ascii">
	<title></title>
	<link rel="stylesheet" href="w3.css">
</head>

<?php

header("Cache-Control: no cache");
session_cache_limiter("private_no_expire");

$mysqli = new mysqli("cpanel3.engr.illinois.edu", "funproject_funproject", "X5V-tfN-7Yh-nnb", "funproject_Database");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$user = trim($_POST["username"]);
$pass = trim($_POST["password"]);

#$sql = "SELECT * FROM `User`";
$sql = "SELECT * FROM `User` WHERE username='$user' AND password='$pass'";

$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
       $success = true;
}

if($success == true) {
    session_start();
    $_SESSION["username"] = $user;
?>
 <html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title></title>
</head>
<body>
<table border="1" cellpadding="10" width="100%">
	<tbody>
		<tr>
			<td align="center">
			<?php
                echo("<strong>Welcome, " . $_SESSION["username"] . "!<strong>");
            ?>
			</td>
		</tr>
		<tr>

		</tr>
		<tr>
			<td align="center">
			<table border="0" cellpadding="3" width="640">
				<tbody>
					<tr>
						<td width="45%">
						<div class="center">
						    
						<h4>Cryptocurrency Market</h4>
                        <a href="data.html">Buy/Sell Cryptocurrency</a>    
						   
						<h4>Transaction:</h4>

						
						<input name="transaction" onclick="document.location.href='transaction.php'" type="submit" value="Make Transaction" /></p>
						
						</form>
						</div>
						</td>
						
						<td width="45%">
						<div class="center">
						    
						<h4>Visualization:</h4>

						
						<input name="transaction" onclick="document.location.href='get_json.php'" type="submit" value="Click Here" /></p>
						
						</form>
						</div>
						</td>
						
						<td width="41%">
						<div class="left">
							<br/> <br/> <br/>
							<form action="search.php" method="get" target="_blank">
								<h4>Search Transactions (YYYY-MM-DD):</h4>

								<p align= "left">
								<input type="text" name="q" /><br/>
								<input type="submit" value="Search" /><br/>
								</p>

							</form>
						</div>
					</td>
						
					</tr>
					
					<tr>
					    <td with="42%">
					        <div class="center">
					            <h4>Requests:</h4>
						
						        <input name="request" onclick="document.location.href='request.html'" type="submit" value="Request"/></p>
					        </div>
					        
					       <div class="center">
					            <h4>Crawl:</h4>
						
						        <input name="crawl" onclick="document.location.href='crawl.php'" type="submit" value="Crawl"/></p>
					        </div>
					    </td>
					</tr>
					
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>
</body>
</html>

<?php

    $sql2 = "SELECT DISTINCT day
FROM
((SELECT CONVERT(datetime, DATE) AS day, AVG(amount)
FROM Transaction
WHERE type = 'BTC'
GROUP BY day
HAVING AVG(amount) > 5)
UNION ALL
(SELECT CONVERT(datetime, DATE) AS day, AVG(amount)
FROM Transaction
WHERE type = 'ETH'
GROUP BY day
HAVING AVG(amount) > 5)
UNION ALL
(SELECT CONVERT(datetime, DATE) AS day, AVG(amount)
FROM Transaction
WHERE type = 'LTC'
GROUP BY day
HAVING AVG(amount) > 5)) AS productive_days";

    $result2 = $mysqli->query($sql2);

print("<table width=\"100%\" cellpadding=\"10\">
    <tr>
        <td  align=\"center\">
        <h4>Busiest Days</h4>
        </td>
    </tr> ");
    
print("<tr>
        <td align=\"center\"> ");

        #$num_rows = $result2->num_rows;

            #print("<p>There are " . $num_rows . " result(s) available</p>");
            while ($row = $result2->fetch_assoc())
            {
                print("{$row['day']}<br/>");
            }
            $result->free();


        print("</td>
    </tr>

</table> ");

$mysqli->close();
    
?>

<?php
} else {
    echo '<div class="alert alert-danger">It looks like your username and/or password are incorrect. Please try again.</div>';
    echo "User is $user\n";
    echo "Pass is $pass";
}

$mysqli->close();

?>