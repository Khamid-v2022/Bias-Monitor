<?php 
require('include/header_no_bot.php'); 

	$query_topic = "SELECT * FROM bias_master WHERE id = " . $_GET['bias_id'];
	$results = mysqli_query($conn, $query_topic);
	if($results->num_rows > 0){
		$item = $results->fetch_assoc();
		$topics = [$item['bias_1'], $item['bias_2']];
	}

	$query_article = "SELECT * FROM bias_detail WHERE id = " . $_GET['id'];
	$results = mysqli_query($conn, $query_article);
	if($results->num_rows > 0){
		$row = $results->fetch_assoc();
		$article_link = $row['article_url'];
	}


 	$query = "SELECT bias_id, article_title, article_text, IFNULL(is_neg, 0) is_neg, IFNULL(is_neu, 0) is_neu, IFNULL(is_pos, 0) is_pos, detail.bias_selected AS main_selected, sentences.bias_selected AS sub_selected, flag FROM bias_detail detail
 	LEFT JOIN article_sentences sentences ON detail.id = sentences.bias_detail_id
 	WHERE detail.id = " . $_GET['id'];
	
	$results = mysqli_query($conn, $query);

	$title = "";
	$text = "";

	$index = 0;
	$total_rows = 0;

	$prev_flag = -1;	//initial flag
	
	if($results->num_rows > 0){
			$total_rows = $results->num_rows;
    	while($row = $results->fetch_assoc()) {
    		$title = $row['article_title'];

    		$row_text = $row['article_text'];
    		$index = 0;
    		
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
  				default:
  					$color = "black";
  					break;
  			}
				$row_text = highlightsSentences($row_text, $color);

				if($prev_flag != $row['flag'])
    			$text .= "<br/>" . $row_text;
    		else
    			$text .= $row_text;
    		
    		$prev_flag = $row['flag'];
    	}
  }


  // Get Article Score
  $score_query = "SELECT IFNULL(SUM(is_neg), 0) bias_neg, IFNULL(SUM(is_neu), 0) bias_neu, IFNULL(SUM(is_pos), 0) bias_pos FROM article_sentences WHERE bias_detail_id = " . $_GET['id'];
  $results = mysqli_query($conn, $score_query);
  if($results->num_rows > 0){
  	$row = $results->fetch_assoc();
  	$score_neg = $row['bias_neg'];
  	$score_neu = $row['bias_neu'];
  	$score_pos = $row['bias_pos'];
  	if($total_rows == 1)
  		$score_no = $total_rows - $score_neg - $score_neu - $score_pos - 1;
  	else
  		$score_no = $total_rows - $score_neg - $score_neu - $score_pos;
  }

	function highlightsWords($text, $words, $color) {
	  	preg_match_all('~\w+~', $words, $m);
	  	if(!$m) return $text;
	  	$re = '~\\b(' . implode('|', $m[0]) . ')\\b~i';
	  	return preg_replace($re, '<span style="color:' . $color . '">$0</span>', $text);
	}

	function highlightsSentences($text, $color) {
	  	return '<span style="color:' . $color . '">' . $text . '</span>';
	}

	function get_sentences_status($row){
		if($row['is_neg'] == 0 && $row['is_neu'] == 0 && $row['is_pos'] == 0)
			return "";
		$max = max(array($row['is_neg'], $row['is_neu'], $row['is_pos']));
		if($row['is_neg'] == $max)
			return "Neg";
		else if($row['is_neu'] == $max)
			return "Neu";
		else if($row['is_pos'] == $max)
			return "Pos";
	}
?>
<style type="text/css">
	p {
		font-size: 1rem;
		color: black;
	}

	.score-item {
		padding: 0 15px;
		color: #999;
    position: relative;
    margin-left: 10px;
	}

	.score-item:before {
		content: "";
    position: absolute;
    width: 10px;
    height: 10px;
    margin-top: 9px;
    border: 5px solid;
    left:  0px;
	}

	.neg-score:before {
		border-color: red;
	}
	.pos-score:before {
		border-color: blue;
	}
	.neu-score:before {
		border-color: #daa732;
	}
	.no-score:before {
		border-color: black;
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
    	<p><b>Scores:</b>
    		<span class="score-item neg-score">Negative: <?=$score_neg?></span>
    		<span class="score-item neu-score">Neutral: <?=$score_neu?></span>
    		<span class="score-item pos-score">Positive: <?=$score_pos?></span>
    		<span class="score-item no-score">No-Score: <?=$score_no?></span>
    	</p>
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