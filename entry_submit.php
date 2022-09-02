<?php
 require('include/connection.php'); 
	$success = 0;
 if(isset($_REQUEST)){
	$insert_id = 0;
	$bias_id = $_REQUEST['bias_id'];
	$entered_date = date("Y-m-d");	
	$date_of_article = $_REQUEST['date_of_article'];
	$article_url = $_REQUEST['article_url'];
	$bias_selected = $_REQUEST['bias_selected'];
	$ip = $_REQUEST['ip'];
	$email = $_REQUEST['email'];
	$send_marketing_email = $_REQUEST['send_marketing_email'] ? $_REQUEST['send_marketing_email'] : 0;
	$verified = 0;
	$bias_1_count = '';
	$bias_2_count = '';
	$activation_code = rand(10,1000);
	$timestamp = time();
	$exp_time = strtotime('+1 day', $timestamp);

	$sql = $conn->prepare('INSERT INTO bias_detail (bias_id, date_entered, date_of_article,article_url,bias_selected,bias_1_count,bias_2_count,ip_address,email_id,send_marketing_email,verified,activation_code,exp_time) VALUES(?, ?, ?,?,?,?,?,?,?,?,?,?,?)');
	$sql->bind_param('issssssssiiss', $bias_id, $entered_date,$date_of_article,$article_url,$bias_selected,$bias_1_count,$bias_2_count,$ip,$email,$send_marketing_email,$verified,$activation_code,$exp_time);
	$sql->execute();
	$insert_id = $sql->insert_id;
	if($insert_id > 0) {
			$success = 1;
			$encoded_activation_code = base64_encode($activation_code);
			$data = array(
			"sender" => array(
				"email" => 'admin@biasmonitor.com',
				"name" => 'Bias Monitor'         
			),
			"to" => array(
				array(
					"email" => $email,
					"name" => "Your - name" 
				)

			),
			"subject" => 'Confirm your Bias Monitor article',
			"name" => 'Link Authorisation',
			"htmlContent" => '<html><head></head><body><p>Hi!</p>Thanks for submitting the article with your opinion on it\'s possible bias.<br/><br/>Please click on the link below to confirm. The article will not be included in our statistics until it\'s confirmed.<br/><br/><span><a href="https://biasmonitor.com/activate.php?activation_code='.$encoded_activation_code.'&data='.$insert_id.'">Click here</span><br/><br/>Regards,<br/>Bias Monitor Team <br/><img src="https://biasmonitor.com/images/logo.png"/></body></html>'

		);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://api.sendinblue.com/v3/smtp/email');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		$headers = array();
		$headers[] = 'Accept: application/json';
		$headers[] = 'Api-Key: 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';
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);

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
				<?php  if($success == 1) { 
				echo 'Thank you. Your article has been submitted. Please click on the link sent to you on your email id to confirm your submission.';
				}
				else {
					echo "Could not submit your article. Please try again.";
				}
				?>
				</p>
                <br />
				<div class="container" style="text-align:center">
					<span><button class="return-box" id="return"><a href="https://biasmonitor.com/" style="color:#fff">Return to home/article statistics</a></button></span>
				</div>
			</div>
		</body>
</html>
