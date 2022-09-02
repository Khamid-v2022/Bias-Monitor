<?php

require('include/connection.php');

if(isset($_REQUEST['param1'])) {
	$query ="SELECT count(*) as count FROM bias_detail where article_url='".$_REQUEST['param1']."'";  
	$row = mysqli_query($conn, $query);
	$bias_master = mysqli_fetch_assoc($row);
	if($bias_master['count'] > 0) {
		echo "stop";	
		exit;
	} else{
		echo "go";
		exit;
	}
}
?>