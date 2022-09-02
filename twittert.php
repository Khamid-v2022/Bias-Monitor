<?php
require_once "vendor/autoload.php";
require('include/connection.php'); 

$query1 = "SELECT * FROM bias_master WHERE id = " . $_GET['id'];
$results = mysqli_query($conn, $query1);
if($results->num_rows > 0){
    $item = $results->fetch_assoc();
    $bias_1 = $item['bias_1'];
    $bias_2 = $item['bias_2'];
}


$query2 = "SELECT SUM(bias_1_count) AS bias_1, SUM(bias_2_count) AS bias_2 FROM bias_detail WHERE bias_id = " . $_GET['id'] . " AND verified = 1 AND bias_selected != 'neutral'";
$results = mysqli_query($conn, $query2);
if($results->num_rows > 0){
    $item = $results->fetch_assoc();
    $bias_1_total = $item['bias_1'];
    $bias_2_total = $item['bias_2'];
}

$query3 = "SELECT SUM(bias_1_count) AS bias_neutral FROM bias_detail WHERE bias_id = " . $_GET['id'] . " AND verified = 1 AND bias_selected = 'neutral'";
$results = mysqli_query($conn, $query3);
if($results->num_rows > 0){
    $item = $results->fetch_assoc();
    $bias_neutral = $item['bias_neutral'];
}
  

// $twiit_message = "Latest updates for the news article <b>" . $bias_1 . "</b> vs <b>" . $bias_2 . "</b>.";
// $twiit_message .= "</br>";
// $twiit_message .= "Total articles " . ($bias_1_total + $bias_2_total + $bias_neutral);
// $twiit_message .= "</br>";
// $twiit_message .= $bias_1 . " " . $bias_1_total . " / " . $bias_2 . " " . $bias_2_total . " / " . "Neutral " . $bias_neutral;
// $twiit_message .= "</br>";
// $twiit_message .= "Check out more at <a href='https://www.biasmonitor.com' target='_black'>www.biasmonitor.com</a>";

$twiit_message = "Latest updates for the news article " . $bias_1 . " vs " . $bias_2 . ".";
$twiit_message .= "\n";
$twiit_message .= "Total mentions " . ($bias_1_total + $bias_2_total);
$twiit_message .= "\n";
$twiit_message .= $bias_1 . " " . $bias_1_total . " / " . $bias_2 . " " . $bias_2_total . " / " . "Neutral " . $bias_neutral;
$twiit_message .= "\n";
$twiit_message .= "Check out more at https://www.biasmonitor.com #auspol";


use Abraham\TwitterOAuth\TwitterOAuth;
  
define('CONSUMER_KEY', 'akF2mBXKdduZ8Adec6sBRaws2');
define('CONSUMER_SECRET', 'szWZlTWPtGvrpCHB0xwtpFqc65DViSEjEUySm4UnPvHg4bT9Z3');
define('ACCESS_TOKEN', '1516236888268365827-ULJOQy5QCT9zon1bvkAAtOZ8BKxKfn');
define('ACCESS_TOKEN_SECRET', 'zduecj4iVo0pSniZddPmtCsZIoeiHvy3VkaVbKozfv8yd');
  
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

	
// $status = 'This is a test tweet. https://artisansweb.net';

$result = $connection->post("statuses/update", ["status" => $twiit_message]);

if ($connection->getLastHttpCode() == 200) {
    $resp['status'] = 'success';
    $resp['msg'] = "Your Tweet posted successfully.";
    echo json_encode($resp);
} else {
    $resp['status'] = 'error';
    $resp['msg'] = $result->errors[0]->message;
    echo json_encode($resp);
}
