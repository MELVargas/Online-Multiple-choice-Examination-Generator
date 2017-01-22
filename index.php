<?php
include('session.php');

$user_type = $userRow['type'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome</title>

</head>
<body>
<?php


if($user_type == 0) include('header-student.php');
else if($user_type == 1) include('header.php');
else include('header.php');
?>
<div id="body">
	<!--form action="upload.php" method="post" enctype="multipart/form-data">
	<input type="file" name="file" />
	<button type="submit" name="btn-upload">Generate Exam</button>
	</form-->
	<br />
	<br />
	<?php 
	if($user_type == 0){
	?>
	<div id="upload-files" class="jumbotron">
	
		<div class="alert alert-success fade in hidden" id="score">
			<a href='#' class='close'  data-dismiss='alert'>&times;</a>
			<span id="alert-inside"><strong>Success!</strong> You scored</span>
			
			<a href="result.php">Click here to view scores.</a>
		</div>
		
	  
	   
		<h1>Hello, <?php echo $userRow['f_name'];?>!</h1>
		<form method="post" multipart="" enctype="multipart/form-data">
			
			<label for="my-input">Please enter the examination code below.</label>
			<input type="text" class="form-control" id="code" name="code" placeholder="Exam Code" required />   
			<br />			
			<!--input class="btn btn-default" name="take-exam" type="submit"-->
			<button type="button" id="btn" class="btn btn-info" data-toggle="modal" data-target="#examModal">Take Exam</button>
			<br />
			<p class="help-block">Examination code is given by the instructor.</p>
			
		</form><br />
		<div id="icon-cont">
			
		</div>
	</div>
	
	
	<?php 
	}else{
	?>
	
	<div id="upload-files" class="jumbotron">
		<h1>Hello, <?php echo $userRow['f_name'];?>!</h1>
		<form action="upload2.php" method="post" multipart="" enctype="multipart/form-data">
			<label for="my-input">Upload your files here.</label>
			<input type="file" id="my-input" name="img[]" onmousedown="triggerFlag();" onmouseout="showFiles();" multiple>   
			<br />			
			<input class="btn btn-default" type="submit">
			<br />
			<p class="help-block">If your files are already uploaded, <br /><a href="view.php">click here.</a></p>
			
		</form><br />
		<div id="icon-cont">
			
		</div>
	</div>
	
	<?php 
	
	}?>
</div>

<script>
    $(document).ready(function(){
        $("#btn").click(function(){
            var vUserId = $("#code").val();
         if(vUserId=='')
         {
             alert("Please enter code");
         }
         else{
            $.post("takeexam.php", //Required URL of the page on server
               { // Data Sending With Request To Server
                  code:vUserId,
               },
         function(response,status){ // Required Callback Function
             $("#bingo").html(response);//"response" receives - whatever written in echo of above PHP script.
             //$("#form")[0].reset();
          });
        }
     });
   });
   

</script>

<?php
if(isset($_POST['btn-takeExam']))
{
	$code = $_POST['code'];
	$exam_id = $_POST['exam_id'];
	$user_id = $_POST['user_id'];
	
	$answer = $_POST['answer'];
	$q_id = $_POST['q_id'];
	$tmp = array_combine($q_id, $answer);
	$score = 0;
	$total = 0;
	foreach ($tmp as $key => $value) {
		echo "key: ".$key." value: ".$value;
		echo "<br />";
		
		$sql="SELECT * FROM distractor WHERE q_id='".$key."' AND pos='".$value."'" ;
		$result_set=mysql_query($sql) or die(mysql_error());
		$row=mysql_fetch_array($result_set);
		if($row['correct'] == 1){
			//correct
			$score++;
		}else{
			//wrong
			
		}
		$total++;
	}
	if(mysql_query("INSERT INTO result(user_id,exam_id,score,total) VALUES('$user_id','$exam_id','$score','$total')")or die(mysql_error())){}
	$alert_cont = " <strong>".$score."</strong> out of <strong>".$total."</strong>!";
	
	echo "<script>	$('#alert-inside').append('".$alert_cont."');
					$('#score').removeClass('hidden');
		</script>";
}


?>
<div class="modal fade" id="examModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Take Exam</h4>
      </div>
	  <form method="post">
      <div class="modal-body">
        <div id="bingo"></div>

      </div>
	   <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button type="submit" name="btn-takeExam" class="btn btn-primary">Submit</button>
      </div>
	  </form>
    </div>
   </div>
</div>

<?php include('footer.php'); ?>

<script src="js/script.js"></script>
</body>
</html>