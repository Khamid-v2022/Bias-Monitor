<?php 
require('include/connection.php'); 
require_once "vendor/autoload.php";

$negative_words = [];
$query_word = "SELECT word FROM negative_words";
$results = mysqli_query($conn, $query_word);
if($results->num_rows > 0){
    while($item = $results->fetch_assoc()) {
      	array_push($negative_words, $item['word']);
    }
}



Use Sentiment\Analyzer;
$sentiment = new Sentiment\Analyzer();

// Get Uncounted Articles
// $query_article = "SELECT id, bias_master_id, article_text FROM article_sentences WHERE (bias_selected = '') OR (bias_neg = 0 AND bias_neu = 0 AND bias_pos = 0) OR (bias_1_negativity IS NULL AND bias_2_negativity IS NULL) OR (bias_1_negativity = 0 AND bias_2_negativity = 0)";

$query_article = "SELECT id, article_text FROM article_sentences WHERE (bias_neg = 0 AND bias_neu = 0 AND bias_pos = 0) OR (bias_neg IS NULL AND bias_neu  IS NULL AND bias_pos IS NULL)";

$results = mysqli_query($conn, $query_article);

if($results->num_rows > 0){
    while($item = $results->fetch_assoc()) {
      	
      	// get topic according to the article

  //     	$query_bias = "SELECT * FROM bias_master WHERE id = " . $item['bias_master_id'];
		// $results_bias = mysqli_query($conn, $query_bias);
		// if($results_bias->num_rows > 0){
		// 	$item_bias = $results_bias->fetch_assoc();
			
		// 	$bias_1 = $item_bias['bias_1'];
		// 	$bias_2 = $item_bias['bias_2'];
		// }

		// // remove special charecters and to lowercase
		// $text = preg_replace("/[^A-Za-z0-9' -]/", "", strtolower($item['article_text']));
		// $count = count(array_intersect($negative_words, explode(" ", $text)));

		// $bias_1_negativity = 0; $bias_2_negativity = 0;
      	
  //     	if(isset($bias_1) && strpos($text, strtolower($bias_1)) !== false) {
		//     $bias_selected = "bias_1";
		//     $bias_1_negativity = $count + 1;
		// } else if(isset($bias_2) && strpos($text, strtolower($bias_2)) !== false) {
		//     $bias_selected = "bias_2";
		//     $bias_2_negativity = $count + 1;
		// }else {
		// 	$bias_selected = "neutral";
		// }

      	
      	$scores = $sentiment->getSentiment($item['article_text']);

		// Update 
		// $query_update = "UPDATE article_sentences SET bias_1_negativity = " . $bias_1_negativity . ", bias_2_negativity = " . $bias_2_negativity . " , bias_neg = " . $scores['neg'] . ", bias_neu = " . $scores['neu'] . ", bias_pos = " . $scores['pos'] . ", bias_compound = " . $scores['compound'] . ", bias_selected = '" . $bias_selected . "' WHERE id = " . $item['id'];

		$query_update = "UPDATE article_sentences SET bias_neg = " . $scores['neg'] . ", bias_neu = " . $scores['neu'] . ", bias_pos = " . $scores['pos'] . ", bias_compound = " . $scores['compound'] . " WHERE id = " . $item['id'];

      	$results_bias = mysqli_query($conn, $query_update);

    }
}

?>