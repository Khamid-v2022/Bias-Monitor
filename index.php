<?php  
  require('include/header.php');
?>  
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
            <!-- ***** Menu Start ***** -->
            <ul class="nav">
              <li class="scroll-to-section"><a href="#top" class="active">Home</a></li>
              <li class="scroll-to-section"><a href="#topics">Topics</a></li>
              <li class="scroll-to-section"><a href="#footer">Contact Us</a></li>
              <li class="scroll-to-section"></li>
            </ul>        
            <a class='menu-trigger'>
                <span>Menu</span>
            </a>
            <!-- ***** Menu End ***** -->
          </nav>
        </div>
      </div>
    </div>
  </header>
  <!-- ***** Header Area End ***** -->
<?php 
  $query_total_topic = "SELECT COUNT(*) AS total_topics FROM bias_master WHERE published = '1'";
  $query_new_today = "SELECT COUNT(*) AS today_articles FROM bias_detail WHERE date_of_article = '" . date("Y-m-d") . "'";
  $query_articles = "SELECT COUNT(*) AS count_of_articles FROM bias_detail";
  $query_sentences = "SELECT COUNT(*) AS count_of_sentences FROM article_sentences";

  $results = mysqli_query($conn, $query_total_topic);
  if($results->num_rows > 0){
    $item = $results->fetch_assoc();
    $total_topic = $item['total_topics'];
  }

  $results = mysqli_query($conn, $query_new_today);
  if($results->num_rows > 0){
    $item = $results->fetch_assoc();
    $new_today = $item['today_articles'];
  }

  $results = mysqli_query($conn, $query_articles);
  if($results->num_rows > 0){
    $item = $results->fetch_assoc();
    $articles = $item['count_of_articles'];
  }

  $results = mysqli_query($conn, $query_sentences);
  if($results->num_rows > 0){
    $item = $results->fetch_assoc();
    $sentences = $item['count_of_sentences'];
  }

?>


  <div class="main-banner wow fadeIn" id="top" data-wow-duration="1s" data-wow-delay="0.5s">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="row">
            <div class="col-lg-6 align-self-center">
              <div class="left-content show-up header-text wow fadeInLeft" data-wow-duration="1s" data-wow-delay="1s">
                <div class="row">
                  <div class="col-lg-12">
                    <h2>Bias Monitor is an automated engine that searches for keywords and makes a judgement on its bias perspective.</h2>
                    <p>We believe when a journalist is reporting unless an opinion piece should be based solely on the facts. Distorting the facts and the message in a biased way does not give the reader the opportunity uses their own discernment, form their own judgements and opinions. 
                      We believe that many journalists have lost their way.
                      This services intent is to be 100% impartial.
                    Bias Monitor is privately owned and funded.</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="right-image wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.5s">
                <!-- <img src="assets/images/slider-dec.png" alt=""> -->
                <div class="summary">
                  <h3>Total number of topics: <big><?=number_format($total_topic)?></big></h3>
                  <h3>New Articles today: <big><?=number_format($new_today)?></big></h3>
                  <h3>Number of articles: <big><?=number_format($articles)?></big></h3>
                  <h3>Number of sentences: <big><?=number_format($sentences)?></big></h3>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="topics" class="services section">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 offset-lg-2">
          <div class="section-heading  wow fadeInDown" data-wow-duration="1s" data-wow-delay="0.5s">
            <h4>Full automated<em> Media bias monitoring </em>system.<br/> Updates hourly!</h4>
            <img src="assets/images/heading-line-dec.png" alt="">
            <p>This services intent is to be 100% impartial.<br/>
            Bias Monitor is privately owned and funded</p>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="table-responsive" style="padding:15px;"> 
          <?php 
            $query1 ="SELECT * FROM bias_master where published = '1' ORDER BY id DESC";
            $bias_master = mysqli_query($conn, $query1);
           
            $query2 ="SELECT bias_id, count(*) as count FROM bias_detail where verified = 1 group by bias_id";  
            $bias_counts_res = mysqli_query($conn, $query2);
           
            $bias_count = array();
            while($row = $bias_counts_res->fetch_assoc()) {
              $bias_count[$row['bias_id']] = $row;
          }?> 
          <table id="bias_master_data" class="table table-striped table-bordered">  
            <thead>  
              <tr>  
                <td>Bias Topics</td>  
                <td>No. Of Articles</td>  
                <td>Summary</td>  
              </tr>  
            </thead>  
            <?php  
              $topics = [];
              $article_datas = array();
              while($row = mysqli_fetch_array($bias_master)){  
                $query3 ="SELECT bias_selected, count(*) as count FROM bias_detail where bias_id=" . $row['id'] . " and verified = 1 group by bias_selected";  
                $bias_summary_res = mysqli_query($conn, $query3);
                $summary_count = array();
                if($bias_summary_res->num_rows > 0){
                   while($summary = $bias_summary_res->fetch_assoc()) {
                    $summary_count[$summary['bias_selected']] = $summary;
                   }
                }
                $bias1_count = isset($summary_count['bias_1']['count']) ? $summary_count['bias_1']['count'] : '0';
                $bias2_count = isset($summary_count['bias_2']['count']) ? $summary_count['bias_2']['count'] : '0';
                $neutral_count = isset($summary_count['neutral']['count']) ? $summary_count['neutral']['count'] : '0';
                $article_count = isset($bias_count[$row['id']]['count']) ? $bias_count[$row['id']]['count'] : 0;

                if($row["bias_2"])
                  $topic = $row["bias_1"] . ' and ' . $row["bias_2"];
                else
                  $topic = $row["bias_1"];

                array_push($topics, $topic);
                array_push($article_datas, $article_count);
                
                echo '  
                 <tr>  
                      <td><a href="/report.php?id=' . $row['id'] . '" >'. $topic .'</a></td>  
                      <td>'. $article_count . '</td>  
                      <td>'. $bias1_count.' / '.$bias2_count.' / '.$neutral_count.'</td>
                 </tr>  
                 ';  
              }?>  
            </table>  
        </div>
      </div>
    </div>
  </div>

<?php  
  require('include/footer.php');
?>  

<script type="text/javascript">
  $('#bias_master_data').DataTable(); 
  var is_header_nav = true;
</script>