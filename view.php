<?php
include('session.php');
require_once 'function.php';
$user_id = $userRow['user_id'];
$user_type = $userRow['type'];
$code = uniqid();
$_SESSION['code'] = $code;
$_SESSION['school'] = $userRow['school']; 
$_SESSION['f_name'] = $userRow['f_name']; 
$_SESSION['m_name'] = $userRow['m_name']; 
$_SESSION['l_name'] = $userRow['l_name']; 
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Examination Generator - Files</title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body>

<?php

if($user_type == 0) include('header-student.php');
else if($user_type == 1) include('header.php');
else include('header.php');
?>
<div id="body">
<!--a class="btn" data-toggle="modal" href="#testModal" >Launch Modal</a-->
	<table id="files" class="table table-striped">
    <tr>
    <th colspan="5"><a href="index.php" target="_blank"><span class="glyphicon glyphicon-upload"  title="upload"></span>Upload New Files</a></th>
    </tr>
    <tr>
	<th><input type="checkbox" class='form' name="checkbox[]" /></th>
    <th>File Name</th>
    <th>File Type</th>
    <th>File Size(KB)</th>
    <th>Action</th>
    </tr>
	<form method="post">
    <?php
	if(isset($_GET['delete'])) {
	$result = mysql_query('DELETE FROM tbl_uploads WHERE id = '.(int)$_GET['delete']);
	}
	
	
	
	$sql="SELECT * FROM tbl_uploads WHERE user_id=".$user_id." ORDER BY id DESC" ;
	$result_set=mysql_query($sql);
	while($row=mysql_fetch_array($result_set))
	{
		?>
        <tr>
        <td><input type="checkbox" class='form' value="<?php echo $row['id'] ?>" name="checkbox" /></td>
        <td><?php echo $row['name'] ?></td>
        <td><?php echo $row['type'] ?></td>
        <td><?php echo $row['size'] ?></td>
        <td class="action">
		<a href="uploads/<?php echo $row['file'] ?>" target="_blank"  class='btn btn-primary btn-xs' >View</a>
		<a href="?delete=<?php echo $row['id'] ?>" class='btn btn-danger btn-xs' target="" onClick="deleteView(this)">Delete</a>
		<a href="uploads.php?download=<?php echo $row['id'] ?>" target=""  class='btn btn-success btn-xs'><span class="glyphicon glyphicon-save"></span>Download</a>
		
		</td>
        </tr>
        <?php
	}
	?>
    </table>
	<br />
	<div id="add-view">
	<label for="add-text">Insert (Additional) Text:</label>
	<textarea class="form-control" rows="5" id="text" name="text"></textarea>
	<!--input type="text" name="text" placeholder="Insert Text" /><br-->
	<!--label for="usr">Number of Items:</label>
	<input type="text" class="form-control" name="numberOfItems" id="usr">
	<!--input type="text" placeholder="Please enter the no. of items." /><br-->
	<div class="input-group">
		<div class="input-group-addon"></div>
		<input type="text" class="form-control" id="name" name="examName" placeholder="filename">
		<div class="input-group-addon">.pdf</div>
    </div>
	<!--input type="text" name="examName" placeholder="Enter the name of exam" data-toggle="modal" data-target=".modal-loading"/><br-->
	<button type="button" name="btn-selectfiles" id="btn-submit" class="btn btn-info" >Generate Examination</button>
	</div>
    </form>
</div>
<script>
    $(document).ready(function(){
		
        $("#btn-submit").click(function(){
			var array = [];
			$("input:checkbox[name=checkbox]:checked").each(function(){
				array.push($(this).val());
			});
            var text = $("#text").val();
            var name = $("#name").val();
            
         if(name=='')
         {
             alert("Please enter name");
         }
         else{
			showLoading();
            $.post("process.php", //Required URL of the page on server
               { // Data Sending With Request To Server
                  text:text,
                  name:name,
                  checkbox:array,
               },
         function(response,status){ // Required Callback Function
             $("#body").html(response);//"response" receives - whatever written in echo of above PHP script.
             //$("#form")[0].reset();
          });
        }
     });
   });
   

</script>
<?php
include('select-question.php');
?>

<!-- Modal -->


<div class="modal modal-loading" data-keyboard="false" data-backdrop="static"><!-- Place at bottom of page --></div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">View Examination</h4>
      </div>
      <div class="modal-body">
		<div class="alert alert-success" id="score">
			<span id="alert-inside"><strong>Success!</strong> You can view and download the file by <a href="exam.php">clicking here.</a></span>
			
		</div>
		
		<?php
		// $user_id = $_SESSION['user'];
		
		// $sql="SELECT * FROM exam WHERE user_id=".$user_id." AND (date_time IN (SELECT max(date_time) FROM exam))" ;
		// $result_set=mysql_query($sql) or die(mysql_error());
		// while($row=mysql_fetch_array($result_set))
		// {
			// $iden = $row['file'];
			
			// //fetch same parent_id and code
		// }
		// echo $user_id;
		
		?>
        <!--a href="pdf/<?php //echo $iden ?>" target="_blank"><span class="glyphicon glyphicon-folder-open"  title="view" ></span>Click here to view files</a-->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="testModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
	  <form method="post">
      <div class="modal-body">
	  
	  <div id="review-body">
	  <?php
	  echo "<input type='hidden' name='code' value='".$code."'/>";
	echo "<table class='table table-bordered table-hover' id='question-modal'>";
	// echo "<script>alert(".$user_id.");</script>";
	
	$test_id = '1';
	$test_code = '580c99f255038';
	
	$sql="SELECT * FROM qa WHERE user_id=".$test_id." AND code='".$test_code."'" ;
	$result_set=mysql_query($sql) or die(mysql_error());
	
	if(mysql_num_rows($result_set)== 0){
	   echo "No question can be generated from the input.";
	}
	else{
	while($row=mysql_fetch_array($result_set))
	{
		
		$question = $row['question'];
		$id = $row['id'];
		//fetch same parent_id and code
		echo "<tr id=".$id."><td>";
		echo "<input type='hidden' class='form' value='".$id."' name='ques-id-box[]'/>";
		echo "<textarea type='text' class='form-control hidden ques' name='question-box[]'>".$question."</textarea>";
		echo "<span class='upd-ques'>".$question."</span>";
		echo "<br />";
		
		$in_sql="SELECT * FROM distractor WHERE q_id='".$id."' ORDER BY pos" ;
		$result_set_in=mysql_query($in_sql) or die(mysql_error());
		$count = 0;
		while($row=mysql_fetch_array($result_set_in))
		{
			$tmp = stripslashes($row['dist']);
			$dist_id = $row['id'];
			
			echo "<input type='hidden' class='form hidden' value='".$dist_id."' name='dist-id-box[]'/>";
			echo "<span>";
			if($count==0)echo "a. ";
			else if($count==1)echo "b. ";
			else if($count==2)echo "c. ";
			else if($count==3)echo "d. ";
			echo "</span>";
			
			echo "<textarea type='text' class='form-control hidden dist-".$count."' name='dist-box[]'>".$tmp."</textarea>";
			echo "<span class='upd-dist-".$count."'>".$tmp."</span>";
			echo "<br />";
			$count++;
			//fetch same parent_id and code
		}
		
		echo"</td>"
		
		?>
		<td width="2em" style="padding:2em;">
		<span name="<?php echo $id ?>" class="edt" unchecked></span>
		</td>
		
		
		
		<td width="2em" style="padding:2em;">
		<span name="<?php echo $id ?>" class="chk" unchecked></span>
		<input type="checkbox" class='form modal-checkbox' id="<?php echo $id ?>" value="<?php echo $id ?>" name="modal-checkbox[]"/></td>
		
		</tr>
		
		
		<?php
	}
	}
	
	echo "</table>";
	?>
	  
	  </div>
	  
      </div>
      <div class="modal-footer">
		<a href="view.php" class="btn btn-default">
		  <!--span class="glyphicon glyphicon-plus"></span--> Close
		</a>
		<button type="submit" name="btn-selectquestions" class="btn btn-primary">Save</button>
      </div>
	  </form>
    </div>
  </div>
</div>


<?php include('footer.php'); ?>
</body>
</html>