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
$query_article = "SELECT id, bias_master_id, bias_detail_id, article_text FROM article_sentences WHERE (bias_neg = 0 AND bias_neu = 0 AND bias_pos = 0) OR (bias_neg IS NULL AND bias_neu  IS NULL AND bias_pos IS NULL)";

$results = mysqli_query($conn, $query_article);


if($results->num_rows > 0){
    while($item = $results->fetch_assoc()) {
      	
      	// get 2 Topic from Master table
    	$query_bias = "SELECT * FROM bias_master WHERE id = " . $item['bias_master_id'];
		$results_bias = mysqli_query($conn, $query_bias);
		
		if($results_bias->num_rows > 0){
			$item_bias = $results_bias->fetch_assoc();
			
			$bias_1 = $item_bias['bias_1'];
			$bias_2 = $item_bias['bias_2'];
		}

      	// get title....ect according to the article
    	$query_detail = "SELECT * FROM bias_detail WHERE id = " . $item['bias_detail_id'];
		$results_detail = mysqli_query($conn, $query_detail);
		if($results_detail->num_rows > 0){
			$item_detail = $results_detail->fetch_assoc();

			$default_bias_selected = trim($item_detail['bias_selected']);
			$title = preg_replace("/[^A-Za-z0-9' -]/", "", strtolower($item_detail['article_title']));
			
			if(!$default_bias_selected){
				if(!isset($bias_2) || (isset($bias_2) && !$bias_2)){
					$default_bias_selected = "bias_1";
				} else {
					if(isset($bias_1) && strpos($title, strtolower($bias_1)) !== false){
						$default_bias_selected = "bias_1";
					}else if(isset($bias_2) && $bias_2 &&  strpos($title, strtolower($bias_2)) !== false){
						$default_bias_selected = "bias_2";
					}
				}
			}
			
			$bias_1_mentioned_count = (int)(trim($item_detail['bias_1_count']) ? $item_detail['bias_1_count'] : 0);
			$bias_2_mentioned_count = (int)(trim($item_detail['bias_2_count']) ? $item_detail['bias_2_count'] : 0);
		}


		// remove special charecters and to lowercase
		$text = preg_replace("/[^A-Za-z0-9' -]/", "", strtolower($item['article_text']));
		$count = count(array_intersect($negative_words, explode(" ", $text)));

		$bias_1_count = 0;
		$bias_2_count = 0;

		if(isset($bias_1))
			$bias_1_mentioned_count += substr_count($text, strtolower($bias_1));
		if(isset($bias_2) && $bias_2)
			$bias_2_mentioned_count += substr_count($text, strtolower($bias_2));

		// echo $item['id'] . " :: " . $text . ":::", $bias_1 . " ::: " . $bias_1_mentioned_count, "\r\n";

		$update_detail_sql = "UPDATE bias_detail SET bias_selected = '" . $default_bias_selected . "', bias_1_count = " . $bias_1_mentioned_count . ", bias_2_count = " . $bias_2_mentioned_count . " WHERE id = " . $item_detail['id'];

		mysqli_query($conn, $update_detail_sql);

		$bias_1_negativity = 0; $bias_2_negativity = 0;
      	
      	if(isset($bias_1) && strpos($text, strtolower($bias_1)) !== false) {
		    $bias_selected = "bias_1";
		    $bias_1_negativity = $count + 1;
		} else if(isset($bias_2) && $bias_2 &&  strpos($text, strtolower($bias_2)) !== false) {
		    $bias_selected = "bias_2";
		    $bias_2_negativity = $count + 1;
		} else {
			$bias_selected = $default_bias_selected;
		}

      	
      	$scores = $sentiment->getSentiment($item['article_text']);

      	$is_neg = 0;
      	$is_neu = 0;
      	$is_pos = 0;
      	if($scores['neu'] > 0.8){
      		$is_neu = 1;
      		$is_neg = round($scores['neg']);
      		$is_pos = round($scores['pos']);
      	}else{
      		$is_neu = 0;
      		if($scores['neg'] > $scores['pos']){
				$is_neg = 1;
				$is_pos = round($scores['pos']);
      		}else {
				$is_pos = 1;
				$is_neg = round($scores['neg']);
      		}
      	}

		// Update 
		$query_update = "UPDATE article_sentences SET bias_1_negativity = " . $bias_1_negativity . ", bias_2_negativity = " . $bias_2_negativity . " , bias_neg = " . $scores['neg'] . ", bias_neu = " . $scores['neu'] . ", bias_pos = " . $scores['pos'] . ", bias_compound = " . $scores['compound'] . ", bias_selected = '" . $bias_selected . "', is_neg = " . $is_neg . ", is_neu = " . $is_neu . ", is_pos = " . $is_pos . " WHERE id = " . $item['id'];

      	$results_bias = mysqli_query($conn, $query_update);

    }
}

?>