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


$q = $_GET["q"];
$attr = $_GET["attr"];

session_start();
$user = $_SESSION["username"];

$sql = "SELECT * FROM `Transaction` WHERE username='$user' AND datetime LIKE '%$q%'";

$result = $mysqli->query($sql);

print("<table width=\"100%\" border=\"1\" cellpadding=\"10\">
    <tr>
        <td  align=\"center\">
        <h1>Transaction Database</h1>
        <h4>Transaction Search Result(s)</h4>
        </td>
    </tr> ");

print("<td>&nbsp;<a href=\"index.html\">Home</a> &nbsp;
     </td> ");

print("<tr>
        <td align=\"center\"> ");

        $num_rows = $result->num_rows;
        if ($num_rows > 0)
        {
            print("<p>There are " . $num_rows . " result(s) available</p>");
            while ($row = $result->fetch_assoc())
            {
                print("<p><b> Username: {$row['username']} </b>");

                print("<br><br>");

                print("<b><u>Datetime:</u></b> {$row['datetime']}<br/>");
                print("<b><u>Amount:</u></b> {$row['amount']}<br/>");
                print("<b><u>Type:</u></b> {$row['type']}<br/>");
                print("<b><u>To:</u></b> {$row['to_']}<br/>");
                print("<b><u>From:</u></b> {$row['from_']}<br/>");
                print("<br><br>");
            }
            $result->free();
        }
        else
        {
            print("There is no transaction found with your current search criterion  :-  $attr = \"$q\" <br> Please recheck your searching criteria! <br\> <br> Thanks! <br/>");
        }

        print("</td>
    </tr>

</table> ");

$mysqli->close();

?>
