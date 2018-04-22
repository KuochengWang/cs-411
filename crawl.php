<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=us-ascii">
	<title></title>
	<link rel="stylesheet" href="w3.css">
</head>

<?php
   
require_once('TwitterAPIExchange.php');

$settings = array(
    'oauth_access_token' => "2781593695-a6llqVfsL1TsYRwkxeJEkC0jwXT6eqE6sU5VGGY",
    'oauth_access_token_secret' => "8RpjoMh8g9AbkaY4DidWVKL3CTWGOGfumcqRDptDEuVDG",
    'consumer_key' => "A42KYcX4xa7k1HOTiPZwWMrRv",
    'consumer_secret' => "pVBq4nJS7nzViayrSAJlJWBqgMeCFm5rnKJcC5lfBZZaOHIwS5"
);

$url = 'https://api.twitter.com/1.1/search/tweets.json';
$requestMethod = 'GET';
$getfield = '?q=#BTC&result_type=recent';

$twitter = new TwitterAPIExchange($settings);
$response = $twitter->setGetfield($getfield)
         ->buildOauth($url, $requestMethod)
         ->performRequest();

$needle = "],\"search_metadata\"";
$substr = stristr($response, $needle, true);
$statuses = substr($substr, 13);

function json_decode_multi($s, $assoc = false, $depth = 512, $options = 0) {
    if(substr($s, -1) == ',')
        $s = substr($s, 0, -1);
    return json_decode("[$s]", $assoc, $depth, $options);
}

$decoded = json_decode_multi($statuses);
#var_dump($decoded[0]);

$map = readDctionary("WordIndex.txt");

Echo "<p><b>Latest Bitcoin Tweets</b></p>";

$totalScore = 0;
foreach($decoded as $item) {
    echo $item->text;
    echo "..........................";
    $label = predict($map,$item->text);
    $label==":)"? $totalScore++ : $totalScore--;
    echo "<strong> $label </strong>";
    echo  nl2br ("\n");
}

printScroe($totalScore);

$url2 = 'https://api.twitter.com/1.1/search/tweets.json';
$requestMethod2 = 'GET';
$getfield2 = '?q=#ETH&result_type=recent';

$twitter2 = new TwitterAPIExchange($settings);
$response2 = $twitter2->setGetfield($getfield2)
         ->buildOauth($url2, $requestMethod2)
         ->performRequest();

$needle2 = "],\"search_metadata\"";
$substr2 = stristr($response2, $needle2, true);
$statuses2 = substr($substr2, 13);

$decoded2 = json_decode_multi($statuses2);
#var_dump($decoded[0]);

#$map2 = readDctionary("WordIndex.txt");

#echo "what";

Echo "<p><b>Latest Ethereum Tweets</b></p>";

$totalScore = 0;
foreach($decoded2 as $item2) {
    echo $item2->text;
    echo "..........................";
    $label = predict($map,$item2->text);
    $label==":)"? $totalScore++ : $totalScore--;
    echo "<strong> $label </strong>";
    echo  nl2br ("\n");
}

printScroe($totalScore);

$url3 = 'https://api.twitter.com/1.1/search/tweets.json';
$requestMethod3 = 'GET';
$getfield3 = '?q=#LTC&result_type=recent';

$twitter3 = new TwitterAPIExchange($settings);
$response3 = $twitter3->setGetfield($getfield3)
         ->buildOauth($url3, $requestMethod3)
         ->performRequest();

$needle3 = "],\"search_metadata\"";
$substr3 = stristr($response3, $needle3, true);
$statuses3 = substr($substr3, 13);

$decoded3 = json_decode_multi($statuses2);
#var_dump($decoded[0]);

#$map2 = readDctionary("WordIndex.txt");

#echo "what";

Echo "<p><b>Latest Litecoin Tweets</b></p>";

$totalScore = 0;
foreach($decoded2 as $item2) {
    echo $item2->text;
    echo "..........................";
    $label = predict($map,$item2->text);
    $label==":)"? $totalScore++ : $totalScore--;
    echo "<strong> $label </strong>";
    echo  nl2br ("\n");
}

printScroe($totalScore);

function readDctionary($dictionary)
{
    $map = array();
    $file = fopen($dictionary,'r');
    $row = 0;
    $sentenceWidth = 113;
   
    while (!feof($file)) 
    {
        $line = fgets($file,4096);
        $line = explode(" ", $line);
        $map[$line[0]] = (int)$line[1];
        $row = $row+1;
    }
    fclose($fh);
    return $map;

}

function predict($map,$sentence)
{
    $line = explode(" ", $sentence);
    $wordList = array();
    $setenceVector = array();
    $score = 0;
    $index = 0;
    $weight = weightMatrix();
    $maxLength = 113;
    foreach ($line as $word)
    {
        $wordList[] = $word;
    }
    foreach ($wordList as $word)
    {
        if($index>=113)
            break;
        $my_variable = array_key_exists($word, $map)? $map[$word] : 0;
        $setenceVector[] = $my_variable;
        $index++;
    }
    
    $index = 0;
    foreach($setenceVector as $vec)
    {
        if($index>=113)
            break;
        $score+=$vec*$weight[$index++];
    }
    
    
    if($score>0)
        return ":)";
    else
        return ":(";
}

# trained using svm.py and got the weighting vector offline
function weightMatrix()
{
   $wei = array(3.81200000e-03,2.21090000e-02, -5.48385556e-02,  9.99220000e-02,
   4.03217778e-02,-1.82355556e-02,  3.13552222e-02, -9.59958889e-02,
   3.65364444e-02,  7.23127778e-02, -2.08397778e-02,  5.13561111e-02,
   1.27491889e-01,  4.62276667e-02, -2.34114444e-02, -2.02915556e-02,
   7.60330000e-02, -3.31573333e-02,  1.22940556e-01,  5.50031111e-02,
  -3.71055556e-02,  1.50540000e-02,  2.51280000e-02, -4.32664444e-02,
   2.91650000e-02, -3.67455556e-03,  9.31358889e-02,  5.32631111e-02,
  -6.74972222e-02, -1.46671556e-01, -1.47285667e-01, -1.18879111e-01,
   6.11838889e-02,  8.05844444e-02,  4.23274444e-02,  2.76174333e-01,
  -2.19086222e-01, -9.27222222e-04, -1.09189222e-01,  1.79537000e-01,
  -2.46616667e-02, -2.04854667e-01,  2.15179222e-01,  1.69029444e-01,
   8.23183333e-02,  1.51114111e-01, -1.54765556e-01,  1.47804111e-01,
   4.14370556e-01,  1.09035000e-01,  8.99544444e-02, 1.99609667e-01,
  -3.12484667e-01,  1.35418333e-01, -1.70223778e-01,  1.88661556e-01,
   2.32378222e-01,  1.37106000e-01, -3.48038889e-01,  4.98026667e-02,
  -1.07844667e-01,  6.10244444e-03, -2.21672667e-01,  2.39041778e-01,
   1.93200333e-01,  1.29302222e-01,  1.27015111e-01,  1.29302222e-01,
   1.29302222e-01,  3.65061111e-02,  5.06477778e-02, -9.86926667e-02,
  -2.14896667e-02, -2.90093333e-02, -2.14831556e-01, -4.76334778e-01,
  -2.30343333e-01,  8.01373333e-02, -4.28181778e-01, -4.39860000e-02,
   1.42440667e-01, -8.98371111e-02,  9.82822222e-02,  2.69284000e-01,
   2.69062222e-02,  2.69062222e-02,  3.67844444e-03, -4.98043333e-02,
   1.19817333e-01,  1.19817333e-01,  1.19817333e-01,  2.69062222e-02,
   2.69062222e-02,  1.25309111e-01,  7.23945556e-02, -1.35688222e-01,
  -1.35688222e-01, -1.35688222e-01,  2.37165111e-01,  1.87131333e-01,
   8.91837778e-02, -6.11175556e-02, -6.11175556e-02, -6.11175556e-02,
  -6.11175556e-02, -6.11175556e-02, -2.23712000e-01, -2.23712000e-01,
  -2.23712000e-01, -2.23712000e-01, -2.23712000e-01, -2.23712000e-01,
  -2.23712000e-01, -1.41900000e-04);
   return $wei;
}

function printScroe($totalScore)
{
    if($totalScore>0)
    {
        echo '<span style="color:RED;text-align:center;">Overall evaluation:   :)</span>';
        echo  nl2br ("\n");
    }
    else
    {
        echo '<span style="color:RED;text-align:center;">Overall evaluation:   :(</span>';
        echo  nl2br ("\n");
    }
}







