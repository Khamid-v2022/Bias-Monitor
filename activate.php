<?php
require('include/connection.php'); 
if(isset($_REQUEST['activation_code']) && isset($_REQUEST['data'])) {
	$received_code = $_REQUEST['activation_code'];
	$decode_code = base64_decode($received_code);
	$email = $_REQUEST['email'];
	$query ="SELECT activation_code,exp_time FROM bias_detail where id=".$_REQUEST['data'];  
	$row = mysqli_query($conn, $query);
	$bias_detail = mysqli_fetch_assoc($row);
	$current_time = time();
	if($bias_detail['activation_code'] == $decode_code) {
		if($current_time <= $bias_detail['exp_time']) {
			$verified = 1;
			$sql = $conn->prepare('UPDATE bias_detail SET verified=? WHERE id=?');
			$sql->bind_param('ii', $verified, $_REQUEST['data']);
			$status = $sql->execute();
			if ($status === false) {
				$message = "Sorry, could not confirm your article. Please try again.";
			} else {
				$message = "Thank you for confirming your article.";
			}
		}
		else{
			$message = "Sorry, this link has expired. Please try again.";
		}
	} else {
		$message = "Sorry, could not confirm your article. Please try again.";
	}
}
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
      	<link rel="apple-touch-icon" sizes="180x180" href=“/images/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/favicon/favicon-16x16.png">
<link rel="manifest" href="/images/favicon/site.webmanifest">


	</head>  
      <body>  
           <br /><br />  
           <div class="container"> 
				<div class="row text-center">
                <div class="col-lg-12">
                   <img src="images/logo.png"/>
                </div>
				</div>
                <p style="font-size: 24px;text-align: center;padding: 20px;">
				<?php  
				echo $message;
				?>
				</p>
                <br /> 
			</div>
							<div class="container" style="text-align:center">
					<span><button class="return-box" id="return"><a href="https://biasmonitor.com/" style="color:#fff">Return to home/article statistics</a></button></span>
				</div>
		</body>
</html>
