<?php
if(isset($_POST['btn-selectquestions']))
{
	if (is_array($_POST['question-box']) && is_array($_POST['dist-box'])) {
		$q_id = $_POST['ques-id-box'];
		$d_id = $_POST['dist-id-box'];
		
		foreach($_POST['question-box'] as $index => $quesbox){
			//$select = mysql_real_escape_string($_POST['select']);
			$question = trim($quesbox);
			$question = addslashes($question);
			$this_id = $q_id[$index];
			mysql_query("UPDATE qa SET question='$question' WHERE id='$this_id'") or die(mysql_error());    
		}
		
		foreach($_POST['dist-box'] as $index => $distbox){
			//$select = mysql_real_escape_string($_POST['select']);
			$dist = trim($distbox);
			$dist = addslashes($dist);
			$this_id = $d_id[$index];
			mysql_query("UPDATE distractor SET dist='$dist' WHERE id='$this_id'") or die(mysql_error());    
		}
	}
	
	
	if (is_array($_POST['modal-checkbox'])) {
		foreach($_POST['modal-checkbox'] as $checkbox){
			//$select = mysql_real_escape_string($_POST['select']);
			$select = trim($checkbox);
			mysql_query("DELETE FROM qa WHERE id = '$select'") or die(mysql_error());  
			mysql_query("DELETE FROM distractor WHERE q_id = '$select'") or die(mysql_error());  
		}
	}
	$code = $_POST['code'];
	$name = $_SESSION['exam_name'];
	
	// echo "<script>alert('".$code."')</script>";
	// echo "<script>alert('".$name."')</script>";
	generatePDF($name, $code);
	

	echo "<script type='text/javascript'>
	$(document).ready(function(){
	$('#myModal').modal('show');
	});
	</script>";
}
?>