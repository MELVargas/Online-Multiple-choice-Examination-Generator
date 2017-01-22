<?php
include_once 'dbconfig.php';
include('session.php');
	$id =$_REQUEST['id'];
	
	
	// sending query
	mysql_query("DELETE FROM tbl_uploads WHERE id = '$id'")
	or die(mysql_error());  	
	
	//header("Location: index.php");
?> 