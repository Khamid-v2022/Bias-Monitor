\<?php 
require('include/connection.php'); 
function getUrlFromPath($path_url){
    $parse = parse_url($path_url);
    $url = preg_replace('#^www\.(.+\.)#i', '$1', $parse['host']);
    return $url;
}
?>

<!DOCTYPE html>  
<html>  
    <head>  
		<meta name="viewport" content= "width=device-width, initial-scale=1.0">
        <title>Bias Monitor</title>  
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

        <link href="css/components.min.css" rel="stylesheet" type="text/css">

        <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>  
        <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>    
        <script src="js/echarts.min.js"></script>
          

        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" /> 
		<link rel="stylesheet" href="css/style.css">
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-CNC43P02RL"></script>


		<script>
		   window.dataLayer = window.dataLayer || [];
		   function gtag(){dataLayer.push(arguments);}
		   gtag('js', new Date());

		   gtag('config', 'G-CNC43P02RL');
     		</script>
                  <link rel="apple-touch-icon" sizes="180x180" href="/images/favicon/apple-touch-icon>
                  <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon/favicon-32x32.png">
                  <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon/favicon-16x16.png">
                  <link rel="manifest" href="/images/favicon/site.webmanifest"> 

        <style type="text/css">
        	.dataTables_length select {
        		height: 3rem;
        		padding: ;
        		font-size: 1.5rem;
        	}
        	.dataTables_wrapper .table-bordered {
        		border: 1px solid #ddd!important;
        	}
        	.dataTables_filter input {
        		font-size: 1.5rem;
        	}
        	.dataTables_length {
        		float: none;
        		margin:  0;
        	}
        	.dataTables_filter {
        		float: none;
        	}
        </style>
    </head>  
    <body>  
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
           	$query2 = "SELECT SUM(bias_1_count) AS bias_1, SUM(bias_2_count) AS bias_2 FROM bias_detail WHERE bias_id = " . $_GET['id'] . " AND verified = 1 AND bias_selected != 'neutral'";
			$results = mysqli_query($conn, $query2);
			if($results->num_rows > 0){
				$item = $results->fetch_assoc();
				$topic_data = [$item['bias_1'], $item['bias_2']];
			}

			$query2_2 = "SELECT SUM(bias_1_count) AS bias_neutral FROM bias_detail WHERE bias_id = " . $_GET['id'] . " AND verified = 1 AND bias_selected = 'neutral'";
			$results = mysqli_query($conn, $query2_2);
			if($results->num_rows > 0){
				$item = $results->fetch_assoc();
				array_push($topic_data, $item['bias_neutral']);
			}

			$query3 = "SELECT * FROM bias_master WHERE id = " . $_GET['id'];
			$results = mysqli_query($conn, $query3);
			if($results->num_rows > 0){
				$item = $results->fetch_assoc();
				$topics = [$item['bias_1'], $item['bias_2'], "neutral"];
			}

        ?>

        <div class="container">  
        	<div class="text-center my-5">
        		<h1><?=$topics[0] . " and " . $topics[1]?></h1>
        	</div>  
        	<div class="text-center my-5">
                <h2>News Site Analysis</h2>
                 </div>
                <div class="text-right">
        		<a href="/" class="btn btn-primary"><i class="bi bi-arrow-left"></i>Back</a>
        	</div>       	
           	<div class="chart-container">
                <div class="chart has-fixed-height" id="pie_basic">
                </div>
           	</div>
                <div class="text-center my-5">
                <h2>Total mentions in articles</h2>
                 </div>
           	<div class="chart-container">
                <div class="chart has-fixed-height" id="column_basic">
                </div>
           	</div>

           	<div class="no-data text-center">
                No data
           	</div>
                 </div>
           	<div class="table-responsive">  
                 <br></br>
                <div class="text-center my-5">
                <h2>Article Search</h2>
                 </div>

                <table id="bias_master_data" class="table table-striped table-bordered">  
                    <thead>  
                        <tr>  
                            <td>Article Date</td>  
                            <td>Domain</td>
                            <td>Article Title</td>   
                            <td>Bias Selected</td>  
							<td><?=$topics[0]?></td>
							<td><?=$topics[1]?></td>
                        </tr>  
                    </thead>  
                    <?php  
						$query4 = "SELECT * FROM bias_detail WHERE bias_id = " . $_GET['id'] . " ORDER BY date_entered DESC, article_url,article_title";
						
						$results = mysqli_query($conn, $query4);
												
						if($results->num_rows > 0){
							while($row = $results->fetch_assoc()) {

								echo '<tr>'; 
	                           	 	echo '<td>'. $row['date_of_article'] .'</td>'; 
	                            	echo '<td><a href="' . $row['article_url'] . '" target="_black">'. getUrlFromPath($row['article_url']) . '</a></td>';
	                            	echo '<td>' . $row['article_title'] . '</td>';
	                            	echo '<td>';
	                            	if($row['bias_selected'] == 'bias_1')
	                            		echo $topics[0];
	                            	else if($row['bias_selected'] == 'bias_2')
	                            		echo $topics[1];
	                            	else if($row['bias_selected'] == 'neutral')
	                            		echo 'neutral';
	                            	echo '</td>';
									echo '<td>' . $row['bias_1_count'] . '</td>';
									echo '<td>' . $row['bias_2_count'] . '</td>';
	                           echo '</tr>';  
							}
						}
                     ?>  
                </table>  
            </div>
            <div class="text-right" >
            	<a href="/" class="btn btn-primary"><i class="bi bi-arrow-left"></i>Back</a>
            </div>
       	</div>
       	<footer>
               <!-- Copyright -->
               <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0);">
              © <?php echo date("Y"); ?> Copyright:
                    <a class="text-white" href="https://biasmonitor.com/">biasmonitor.com</a>
               </div>
               <!-- Copyright -->
        </footer>
    </body>  
</html>  

<script type="text/javascript">
	var legend_data = [];
	var url_datas = [];
	var topics = [];
	var topic_data = [];
	
	<?php if($url_datas) { ?>
		legend_data = <?php echo json_encode($urls); ?>;
    	url_datas = <?php echo json_encode($url_datas); ?>;
	<?php } ?>
    
	<?php if($topic_data){ ?>
		topic_data = <?php echo json_encode($topic_data); ?>;
	    topics = <?php  echo json_encode($topics); ?>;
	<?php } ?>
</script>

<script src="js/main.js"></script>
