<?php 
require('include/header.php'); 

	$query_topic = "SELECT * FROM bias_master WHERE id = " . $_GET['bias_id'];
	$results = mysqli_query($conn, $query_topic);
	if($results->num_rows > 0){
		$item = $results->fetch_assoc();
		$topics = [$item['bias_1'], $item['bias_2']];
	}
	
	$negative_words = [];
	$query_word = "SELECT word FROM negative_words";
	$results = mysqli_query($conn, $query_word);
	if($results->num_rows > 0){
    while($item = $results->fetch_assoc()) {
      array_push($negative_words, $item['word']);
    }
	}

	$query_article = "SELECT * FROM bias_detail WHERE id = " . $_GET['id'];
	$results = mysqli_query($conn, $query_article);
	if($results->num_rows > 0){
		$row = $results->fetch_assoc();
		$article_link = $row['article_url'];
	}


 	$query = "SELECT bias_id, article_title, article_text, bias_neg, bias_neu, bias_pos, detail.bias_selected FROM bias_detail detail
 	LEFT JOIN article_sentences sentences ON detail.id = sentences.bias_detail_id
 	WHERE detail.id = " . $_GET['id'];
	
	$results = mysqli_query($conn, $query);

	$title = "";
	$text = "";

	$index = 0;
	
	if($results->num_rows > 0){
    	while($row = $results->fetch_assoc()) {
    		$title = $row['article_title'];

    		$row_text = $row['article_text'];
    		$index = 0;
    		
    		while($index < count($negative_words)){
    			$end = $index + 2999;
    			if( $index + 2999 >= count($negative_words)){
    				$end = count($negative_words) - 1;
    			}
    			$keys = array_slice($negative_words, $index, 3000);

    			switch(get_sentences_status($row)){
    				case "Neg":
    					$color = "red";
    					break;
    				case "Neu":
    					$color = "#daa732";
    					break;
    				case "Pos":
    					$color = "blue";
    					break;
    			}
    			$row_text = highlightsWords($row_text, implode(" ", $keys), $color);
    			$index += 3000;
    		}
    		switch($row['bias_selected']){
  				case "bias_1":
  					$color = "red";
  					$row_text = highlightsWords($row_text, $topics[0], "blue");
  					$row_text = highlightsWords($row_text, $topics[1], "red");
  					break;
  				case "bias_2":
  					$row_text = highlightsWords($row_text, $topics[1], "blue");
  					$row_text = highlightsWords($row_text, $topics[0], "red");
  					break;
  				case "neutral":
  					$color = "blue";
  					$row_text = highlightsWords($row_text, implode(" ", $topics), "#daa732");
  					break;
  			}
    		$text .= "<br/>" . $row_text;
    	}
  }


  // Get Article Score
  $score_query = "SELECT IFNULL(SUM(ROUND(bias_neg)), 0) bias_neg, IFNULL(SUM(ROUND(bias_neu)), 0) bias_neu, IFNULL(SUM(ROUND(bias_pos)), 0) bias_pos FROM article_sentences WHERE bias_detail_id = " . $_GET['id'];
  $results = mysqli_query($conn, $score_query);
  if($results->num_rows > 0){
  	$row = $results->fetch_assoc();
  	$score_neg = $row['bias_neg'];
  	$score_neu = $row['bias_neu'];
  	$score_pos = $row['bias_pos'];
  }

	function highlightsWords($text, $words, $color) {
	  	preg_match_all('~\w+~', $words, $m);
	  	if(!$m) return $text;
	  	$re = '~\\b(' . implode('|', $m[0]) . ')\\b~i';
	  	return preg_replace($re, '<span style="color:' . $color . '">$0</span>', $text);
	}

	function get_sentences_status($row){
		$max = max(array($row['bias_neg'], $row['bias_neu'], $row['bias_pos']));
		if($row['bias_neg'] == $max)
			return "Neg";
		else if($row['bias_neu'] == $max)
			return "Neu";
		else if($row['bias_pos'] == $max)
			return "Pos";
		return "";
	}
?>
<style type="text/css">
	p {
		font-size: 1rem;
		color: black;
	}

	.score-item {
		padding: 0 10px;
		color: #999;
	}
</style>

<!-- ***** Header Area Start ***** -->
  <header class="header-area header-sticky wow slideInDown" data-wow-duration="0.75s" data-wow-delay="0s">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <nav class="main-nav">
            <!-- ***** Logo Start ***** -->
            <a href="index.php" class="logo">
              <img class="logo-img" src="assets/images/logo.png" alt="Chain App Dev">
            </a>
            <!-- ***** Logo End ***** -->
            <ul class="nav">
              <li>
              	<div class="gradient-button">
              		<a href="javascript:window.close();" class="active"><i class="fa fa-sign-in-alt"></i> Close</a>
              	</div>
              </li> 
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </header>
  <!-- ***** Header Area End ***** -->
  <div class="container" style="padding-top: 140px;">  
  	<div class="text-end">
  		<a href="<?=$article_link?>" target="_blank">Go to article <i class="fas fa-arrow-right"></i></a>
  	</div>
    <div class="text-center my-5">
      <h2><?=$title?></h2>
    </div>
    <div class="text-center my-5">
    	<p><b>Scores:</b><span class="score-item">Negative: <?=$score_neg?></span><span class="score-item">Neutral: <?=$score_neu?></span><span class="score-item">Positive: <?=$score_pos?></span></p>
    </div>
           
    <div class="mt-4">
        <p>
        	<?=$text?>
        </p>
    </div>
  </div>

<?php  
  require('include/footer.php');
?>  