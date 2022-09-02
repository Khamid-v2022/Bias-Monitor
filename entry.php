<?php
 require('include/connection.php'); 
 if(isset($_GET['id'])){
	$query ="SELECT * FROM bias_master where id=".$_GET['id'];  
	$row = mysqli_query($conn, $query);
	$bias_master = mysqli_fetch_assoc($row);
 }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8" name="viewport" content= "width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<link rel="stylesheet" href="css/style.css">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

<title> Enter a new article </title>

<link rel="apple-touch-icon" sizes="180x180" href=“/images/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/favicon/favicon-16x16.png">
<link rel="manifest" href="/images/favicon/site.webmanifest">


</head>

<body>
 <div class="demo form-bg">
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-lg-12">
                   <img src="images/logo.png"/">
                </div>
            </div>


            <div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <form method="POST" action="entry_submit.php" id="entry_form">
                        <h1 class="heading">Enter New Article</h1>
                        <div class="form-group">
							<label class="control-label" style="padding-bottom:10px;">Topic: <?php echo $bias_master['bias_1'].' VS '.$bias_master['bias_2'] ?></label>
							<span></span>

                            <label class="control-label"style="display:block; font-size:14px;">Email<span class="mand">*<span></label>
                            <input type="text" class="form-control" style="margin-bottom: 0px;" name="email" id="email" required placeholder="" />
							<span class="error" id="invalid_email">Email-id is invalid</span>
							
							<label class="" style="font-size:14px;">Get our email updates</label>
                            <input class="form-check-input" type="checkbox" value="1" id="send_marketing_email" name="send_marketing_email" style="margin-bottom: 30px;" >
							

                            <label class="control-label" style="display:block; font-size:14px">Article URL<span class="mand">*<span></label>
                            <input type="text" class="form-control" name="article_url" id="article_url" required placeholder=""/>
							<span class="error" id="duplicate_url">Sorry.. this article has already been submitted</span>
							<span class="error" id="invalid_url">Please enter a valid URL.</span>
							
							<label class="control-label" style="font-size:14px">Date of article<span class="mand">*<span></label>
                            <input data-date-format="yyyy-mm-dd" id="datepicker" class="form-control" name="date_of_article">
							
							<label class="control-label" style="display:block;font-size:14px">Bias towards<span class="mand">*<span></label>
                            <span><button class="select-box" id="bias_1"><?php echo $bias_master['bias_1']; ?></button></span> <span><button class="select-box" id="bias_2"><?php echo $bias_master['bias_2']; ?></button></span> <span><button class="select-box" id="neutral">Neutral</button></span>
							<span class="error" id="invalid_sel">Please select one option.</span>
							<input type="hidden" id="bias_selected" name="bias_selected" />
							<input type="hidden" id="ip" name="ip" />
							<input type="hidden" id="bias_id" name="bias_id" value="<?php echo $bias_master['id']; ?>" />
							
                            <button id="submitForm" class="btn btn-default">Submit <i class="fa fa-arrow-circle-right fa-2x"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
<script>
$( document ).ready(function() {
	$(".select-box").click(function(e) {
		e.preventDefault();
		$(".select-box").removeClass('selected');
	    $("#bias_selected").val(this.id);
		$(this).addClass('selected');
	});
	
	$("#submitForm").click(function(e) {
		e.preventDefault();
	  var email = $('#email').val();
	  var article_url = $('#article_url').val();
	  var bias_selected = $('#bias_selected').val();
	  if(IsEmail(email)==false){
          $('#invalid_email').show();
		  $("#invalid_email").css("display", "block");
          return false;
      } else{
		  $('#invalid_email').hide();
	  }
	  $.getJSON("https://api.ipify.org/?format=json", function(e) {
			$("#ip").val(e.ip);
	  });
	  
	  if(bias_selected == '') {
		  $('#invalid_sel').show();
		  $("#invalid_sel").css("display", "block");
          return false;
	  } else {
		  $('#invalid_sel').hide();
	  }
	  if(article_url != '' && IsvalidUrl(article_url)==true) {
		  $('#invalid_url').hide();
		  $('#duplicate_url').hide();
		  $.ajax({
			  type: "GET",
			  url: "check_url.php",
			  data: { 
				'param1': article_url,
			},

			success: function(response) {
				if(response == 'stop') {
					$('#duplicate_url').show();
					  $("#duplicate_url").css("display", "block");
					  return false;
				} else if(response == 'go') {
					$( "#entry_form" ).submit();
				}
			}
		});
	  } else {
		  $('#invalid_url').show();
		  $("#invalid_url").css("display", "block");
		  return false;
	  }
	  
	});
	
});
 function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(!regex.test(email)) {
    return false;
  }else{
    return true;
  }
}
function IsvalidUrl(url) {
  //var regex = /^((http|https):/;
  if (url.indexOf("http://") == 0 || url.indexOf("https://") == 0) {
    return true;
  }else{
    return false;
  }
}
    $('#datepicker').datepicker({
        weekStart: 1,
        daysOfWeekHighlighted: "6,0",
        autoclose: true,
        todayHighlight: true,
    });
	$('#datepicker').datepicker("setDate", new Date());
</script>
