<?php  
  require('include/header_no_bot.php');
  function getUrlFromPath($path_url){
    $parse = parse_url($path_url);
    $url = preg_replace('#^www\.(.+\.)#i', '$1', $parse['host']);
    return $url;
  }

  $exist_topic2 = true;
?>  

<!-- pie chart -->
<?php 
    $urls = [];
    $count_of_urls = [];
    $query1 ="SELECT * FROM bias_detail where bias_id = " . $_GET['id'] . " AND verified = 1"; 

    $results = mysqli_query($conn, $query1);
    if($results->num_rows > 0){
        while($item = $results->fetch_assoc()) {
              $url = getUrlFromPath($item['article_url']);
              if(in_array($url, $urls)){
                   $count_of_urls[$url]++;
              }else{
                   array_push($urls, $url);
                   $count_of_urls[$url] = 0;
              }                  
        }
    }
    $url_datas = [];
    foreach($count_of_urls as $key  => $value){
        $url_data['value'] = $value;
        $url_data['name'] = $key;
        array_push($url_datas, $url_data);
    }
?>

<?php 
  $query3 = "SELECT * FROM bias_master WHERE id = " . $_GET['id'];
  $results = mysqli_query($conn, $query3);
  if($results->num_rows > 0){
    $item = $results->fetch_assoc();
    if($item['bias_2'])
      $topics = [$item['bias_1'], $item['bias_2'], "neutral"];
    else{
      $topics = [$item['bias_1'], "neutral"];
      $exist_topic2 = false;
    }
  }

  $query2 = "SELECT IFNULL(SUM(bias_1_count), 0) AS bias_1, IFNULL(SUM(bias_2_count), 0) AS bias_2 FROM bias_detail WHERE bias_id = " . $_GET['id'] . " AND verified = 1 AND bias_selected != 'neutral'";
  $results = mysqli_query($conn, $query2);
  if($results->num_rows > 0){
    $item = $results->fetch_assoc();
    if($exist_topic2)
      $topic_data = [$item['bias_1'], $item['bias_2']];
    else
      $topic_data = [$item['bias_1']];
  }

  $query2_2 = "SELECT IFNULL(SUM(bias_1_count), 0) AS bias_neutral FROM bias_detail WHERE bias_id = " . $_GET['id'] . " AND verified = 1 AND bias_selected = 'neutral'";
  $results = mysqli_query($conn, $query2_2);
  if($results->num_rows > 0){
    $item = $results->fetch_assoc();
    array_push($topic_data, $item['bias_neutral']);
  }

  
?>

<!-- histogram chart -->
<?php 
    $query_histogram = "SELECT IFNULL(a.date_of_article, b.date_of_article) article_date, a.bias_1, a.bias_2, b.bias_neutral 
    FROM (
        SELECT date_of_article, IFNULL(SUM(bias_1_count), 0) AS bias_1, IFNULL(SUM(bias_2_count), 0) AS bias_2 
        FROM bias_detail 
        WHERE bias_id = " . $_GET['id'] . " AND bias_selected != 'neutral' AND verified = 1 
        GROUP BY date_of_article) a 
    LEFT JOIN (
        SELECT date_of_article, SUM(bias_1_count) AS bias_neutral 
        FROM bias_detail 
        WHERE bias_id = " . $_GET['id'] . " AND bias_selected = 'neutral' AND verified = 1 
        GROUP BY date_of_article) b 
    ON a.date_of_article = b.date_of_article 
    ORDER BY a.date_of_article";

    $results = mysqli_query($conn, $query_histogram);
    $histogram_dates = [];
    $histogram_bias1 = [];
    if($exist_topic2)
      $histogram_bias2 = [];
    $histogram_neutral = [];
    if($results->num_rows > 0){
        while($item = $results->fetch_assoc()) {
            array_push($histogram_dates, $item['article_date']);
            array_push($histogram_bias1, $item['bias_1']); 
            if($exist_topic2){
              array_push($histogram_bias2, $item['bias_2']); 
            }
            array_push($histogram_neutral, $item['bias_neutral']);         
        }
    }
?>

<?php 
  $query_score_histogram = "SELECT detail.date_of_article, IFNULL(SUM(bias_neg), 0) bias_neg, IFNULL(SUM(bias_neu), 0) bias_neu, IFNULL(SUM(bias_pos), 0) bias_pos 
  FROM (SELECT * FROM bias_detail detail WHERE bias_id = " . $_GET['id'] . ") detail 
  LEFT JOIN (
    SELECT bias_detail_id, SUM(ROUND(bias_neg)) bias_neg, SUM(ROUND(bias_neu)) bias_neu, SUM(ROUND(bias_pos)) bias_pos 
    FROM article_sentences 
    WHERE  bias_master_id = " . $_GET['id'] . "
    GROUP BY bias_detail_id) sentences 
  ON detail.id = sentences.bias_detail_id 
  GROUP BY date_of_article 
  ORDER BY date_of_article";

  $results = mysqli_query($conn, $query_score_histogram);
    $score_histogram_dates = [];
    $histogram_bias1_neg = [];
    $histogram_bias1_neu = [];
    $histogram_bias1_pos = [];

    if($exist_topic2){
      $histogram_bias2_neg = [];
      $histogram_bias2_neu = [];
      $histogram_bias2_pos = [];
    }
    
    if($results->num_rows > 0){
        while($item = $results->fetch_assoc()) {
            array_push($score_histogram_dates, $item['date_of_article']);
            array_push($histogram_bias1_neg, $item['bias_neg']); 
            array_push($histogram_bias1_neu, $item['bias_neu']); 
            array_push($histogram_bias1_pos, $item['bias_pos']);  
            if($exist_topic2){
              array_push($histogram_bias2_neg, $item['bias2_neg']); 
              array_push($histogram_bias2_neu, $item['bias2_neu']); 
              array_push($histogram_bias2_pos, $item['bias2_pos']);  
            } 
        }
    }

?>
<style type="text/css">
  .main-banner:after {
    background-image: unset;
  }

  .show-up.wow h2 {
    padding:  30px 0;
  }

  .article-text {
      color: black;
  }
</style>
<link href="assets/css/components.min.css" rel="stylesheet" type="text/css">

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
              <li><div class="gradient-button"><a id="modal_trigger" href="/" class="active"><i class="fas fa-arrow-left"></i> Back</a></div></li> 
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </header>
  <!-- ***** Header Area End ***** -->

  <?php 
  if(isset($topics)) {
  ?>
  <div class="container" style="padding-top: 140px;">
      <div class="text-center mb-5">
        <h1>
          <?php 
            if($exist_topic2)
              echo $topics[0] . " and " . $topics[1];
            else
              echo $topics[0];
          ?>
        </h1>
      </div>

      <div class="left-content show-up wow fadeInLeft" data-wow-duration="1s" data-wow-delay="1s">
        <div class="row">
          <div class="col-lg-12 text-center">
            <h2>News Site Analysis</h2>
            <div class="chart-container">
              <div class="chart has-fixed-height" id="pie_basic"></div>
              <div class="no-data text-center">
                No data
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="right-content show-up wow fadeInRight" data-wow-duration="1s" data-wow-delay="1s">
        <div class="row">
          <div class="col-lg-12  text-center">
            <h2>Total mentions in articles</h2>
            <div class="chart-container">
              <div class="chart has-fixed-height" id="column_basic"></div>
              <div class="no-data text-center">
                No data
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="left-content show-up wow fadeInLeft" data-wow-duration="1s" data-wow-delay="1s">
        <div class="row">
          <div class="col-lg-12 text-center">
            <h2>
              <?php 
                if($exist_topic2)
                  echo $topics[0] . " and " . $topics[1];
                else
                  echo $topics[0];
              ?> Mentions
            </h2>
            <div class="chart-container">
              <div class="chart has-fixed-height" id="line_zoom"></div>
              <div class="no-data text-center">
                No data
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="right-content show-up wow fadeInLeft" data-wow-duration="1s" data-wow-delay="1s">
        <div class="row">
          <div class="col-lg-12 text-center">
            <h2>Article Score Histogram</h2>
            <div class="chart-container">
              <div class="chart has-fixed-height" id="score_histogram"></div>
              <div class="no-data text-center">
                No data
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
  <?php }?>

  <div id="topics" class="services section">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 offset-lg-2">
          <div class="section-heading  wow fadeInDown" data-wow-duration="1s" data-wow-delay="0.5s">
            <h2>Article Search</h2>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <?php 
          if(!isset($_GET['order_by'])){
            $order_by = "date_of_article";
          }else{
            $order_by = $_GET['order_by'];
          }
        ?>
        <input id="selected_id" type="hidden" value="<?=$_GET['id']?>">
        <div style="display: flex; align-items: center; justify-content: end;">
          <label for="sort_select" style="margin-right: 10px;">Sort By:</label>
          <select class="form-control" id="sort_select" style="width: 100px">
            <option value="date_of_article" <?=$order_by=="date_of_article"?"selected":""?>>Date</option>
            <option value="bias_pos" <?=$order_by=="bias_pos"?"selected":""?>>Positive Score</option>
            <option value="bias_neg" <?=$order_by=="bias_neg"?"selected":""?>>Negative Score</option>
          </select>
        </div>
        <div class="table-responsive" style="padding:15px;"> 
           <table id="bias_master_data" class="table table-striped table-bordered">  
              <thead>  
                <tr>  
                  <td class="text-center">Article Date</td>  
                  <td class="text-center">Domain</td>
                  <td class="text-center">Article Title <span class="text-muted">(Total sentences)</span></td>   
                  <td class="text-center">
                    <?php 
                    if($exist_topic2){
                      echo 'Bias<br/>Mentions<br/>Selected/Count';
                    }else{
                      echo 'Bias Mentions<br/>Count';
                    }
                    ?>
                  </td>
                  <td >Article Score</td>
                </tr>  
              </thead>  
              <?php  
                  $query4 = "SELECT detail.*, sentences.* FROM (
                    SELECT * FROM bias_detail detail WHERE bias_id = " . $_GET['id'] . ") detail 
                  LEFT JOIN (
                      SELECT bias_detail_id, IFNULL(SUM(ROUND(bias_neg)), 0) bias_neg, IFNULL(SUM(ROUND(bias_neu)), 0) bias_neu, IFNULL(SUM(ROUND(bias_pos)), 0) bias_pos, COUNT(id) AS number_of_count 
                      FROM article_sentences 
                      WHERE bias_master_id = " . $_GET['id'] . " 
                      GROUP BY bias_detail_id
                  ) sentences 
                  ON detail.id = sentences.bias_detail_id 
                  ORDER BY " . $order_by . " DESC, article_url, article_title";

                  $results = mysqli_query($conn, $query4);
                    
                  if($results->num_rows > 0){
                    while($row = $results->fetch_assoc()) {
                      echo '<tr>'; 
                        echo '<td class="text-center">'. $row['date_of_article'] .'</td>'; 
                        echo '<td><a href="' . $row['article_url'] . '" target="_black">'. getUrlFromPath($row['article_url']) . '</a></td>';
                        echo '<td title="' . $row['article_title'] . '"><a  class="article-text" href="view_article.php?id=' . $row['id'] . '&bias_id=' . $row['bias_id'] . '" target="_black">' . $row['article_title'] . ' <span class="text-muted">(' . $row['number_of_count'] . ')' . '</span></td>';

                        if($exist_topic2){
                          echo '<td><b>';
                          if($row['bias_selected'] == 'bias_1')
                            echo $topics[0];
                          else if($row['bias_selected'] == 'bias_2')
                            echo $topics[1];
                          else if($row['bias_selected'] == 'neutral')
                            echo 'neutral';
                          echo '</b><br/>';
                          echo $topics[0] . ": " . $row['bias_1_count'] . '<br/>';
                          echo $topics[1] . ": " .$row['bias_2_count'] . '</td>';                          
                        }
                        else{
                          echo '<td>' . $topics[0] . ": " . $row['bias_1_count'] . '</td>';
                        }
                        
                        echo '<td>'; 
                        // echo 'Total sentences: ' . $row['number_of_count'] . '</br>';
                        echo 'Neg: ' . $row['bias_neg'] . '<br/>';
                        echo 'Neu: ' . $row['bias_neu'] . '</br>';
                        echo 'Pos: ' . $row['bias_pos'] . '</td>';
                      echo '</tr>';  
                    }
                  }
               ?>  
          </table>   
        </div>

        <div class="gradient-button text-end mt-5">
          <a id="modal_trigger" href="/" class="active"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/js/echarts.min.js"></script>
<?php  
  require('include/footer.php');
?>  


<script type="text/javascript">
  var legend_data = [];
  var url_datas = [];
  var topics = [];
  var topic_data = [];

  var histogram_dates = [];
  var histogram_bias1 = [];
  var histogram_bias2 = [];
  var histogram_neutral = [];

  var exist_topic2 = <?=$exist_topic2?"true":"false" ?>;
  
  <?php if($url_datas) { ?>
    legend_data = <?php echo json_encode($urls); ?>;
    url_datas = <?php echo json_encode($url_datas); ?>;
  <?php } ?>
    
  <?php if($topic_data){ ?>
    topic_data = <?php echo json_encode($topic_data); ?>;
    topics = <?php  echo json_encode($topics); ?>;
  <?php } ?>

  <?php if(count($histogram_dates) > 0){ ?>
      histogram_dates = <?php echo json_encode($histogram_dates); ?>;
      histogram_bias1 = <?php  echo json_encode($histogram_bias1); ?>;
      <?php 
        if($exist_topic2){
      ?>
        histogram_bias2 = <?php  echo json_encode($histogram_bias2); ?>;
      <?php } ?>
      histogram_neutral = <?php  echo json_encode($histogram_neutral); ?>;
  <?php } ?>

  var score_histogram_dates = [];
  var histogram_bias1_neg = [];
  var histogram_bias1_neu = [];
  var histogram_bias1_pos = [];

  var histogram_bias2_neg = [];
  var histogram_bias2_neu = [];
  var histogram_bias2_pos = [];

  var score_legend = [];
  score_legend[0] = '<?php echo $topics[0] . " Negative"; ?>';
  score_legend[1] = '<?php echo $topics[0] . " Neutral"; ?>';
  score_legend[2] = '<?php echo $topics[0] . " Positive"; ?>';

  <?php 
    if($exist_topic2){
  ?>
    score_legend[3] = '<?php echo $topics[1] . " Negative"; ?>';
    score_legend[4] = '<?php echo $topics[1] . " Neutral"; ?>';
    score_legend[5] = '<?php echo $topics[1] . " Positive"; ?>';
  <?php } ?>

  <?php if(count($score_histogram_dates) > 0){ ?>
      score_histogram_dates = <?php echo json_encode($score_histogram_dates); ?>;
      histogram_bias1_neg = <?php  echo json_encode($histogram_bias1_neg); ?>;
      histogram_bias1_neu = <?php  echo json_encode($histogram_bias1_neu); ?>;
      histogram_bias1_pos = <?php  echo json_encode($histogram_bias1_pos); ?>;
      <?php 
        if($exist_topic2){
      ?>
        histogram_bias2_neg = <?php  echo json_encode($histogram_bias2_neg); ?>;
        histogram_bias2_neu = <?php  echo json_encode($histogram_bias2_neu); ?>;
        histogram_bias2_pos = <?php  echo json_encode($histogram_bias2_pos); ?>;
  <?php }} ?>
</script>

<script src="assets/js/report.js"></script>