<?php 
require_once "vendor/autoload.php";
Use Sentiment\Analyzer;
$sentiment = new Sentiment\Analyzer();

$scores = $sentiment->getSentiment("Australian Opposition Leader Anthony Albanese reacts after delivering a speech at a Tasmanian Labor Party campaign launch on Day 27 of the 2022 federal election campaign, in Launceston, Saturday, May 7, 2022 (AAP Image/Lukas Coch) NO ARCHIVING.");
print_r($scores);

?>