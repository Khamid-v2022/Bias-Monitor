 <?php  
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
 require('include/connection.php'); 
 $query1 ="SELECT * FROM bias_master where active = 1 ORDER BY id DESC";  
 $bias_master = mysqli_query($conn, $query1);
 $query2 ="SELECT bias_id, count(*) as count FROM bias_detail where verified = 1 group by bias_id";  
 $bias_counts_res = mysqli_query($conn, $query2);
 $bias_count = array();
 $bais_detail = "SELECT * FROM bias_detail ORDER BY id DESC";  
 while($row = $bias_counts_res->fetch_assoc()) {
	$bias_count[$row['bias_id']] = $row;
 }
 //mysqli_close($conn);
 ?>  
 <!DOCTYPE html>  
 <html>  
      <head>  
		   <meta name="viewport" content= "width=device-width, initial-scale=1.0">
           <title>Bias Monitor</title>  
           <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
           <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
           <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>  
           <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>            
           <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" /> 
			<link rel="stylesheet" href="css/style.css">
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-CNC43P02RL"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'G-CNC43P02RL');
		</script>
			
      </head>  
      <body>  
           <div class="container"> 
				<div class="row text-center">
                <div class="col-lg-12">
                   <img src="images/logo.png"/>
                </div>
				</div>
                <h1 class="heading">Bias Monitor</h1>
                <br />  
				<div class="container" style="font-size:20px;">
				TEXT
				</div>
                <div class="table-responsive" style="padding:15px;">  
                     <table id="bias_master_data" class="table table-striped table-bordered">  
                          <thead>  
                               <tr>  
                                    <td>Article URL</td>  
										<td></td>
                               </tr>  
                          </thead>  
                          <?php  

						        while($row = mysqli_fetch_array($bias_detail))  
                          {  
                                                        $article_urls = Select * FROM bias_detail where id=".$row['id'].";  


                               <tr>  
                                    <td>'.$article_urls.'</td>  
                               </tr>  
                               ';  
                          }  
                          ?>  
                     </table>  
                </div>  
           </div>  
      </body>  
 </html>  
 <script>  
 $(document).ready(function(){  
      $('#bias_master_data').DataTable();  
 });  
 </script>
