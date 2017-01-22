<script src="js/jquery-3.1.1.js"></script>
<?php 

include('session.php');
$user_id = $userRow['user_id'];
if($_POST["code"])
{
	$code = $_POST["code"];
	echo "<input type='hidden' name='code' value='".$code."'/>";
	echo "<table class='table table-bordered table-hover' id='question-modal'>";
	// echo "<script>alert(".$user_id.");</script>";
	
	$sql="SELECT * FROM qa WHERE user_id=".$user_id." AND code='".$code."'" ;
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
			if($row['correct'] == 1) $color = "red";
			echo "<span class='upd-dist-".$count."' style='color: ".$color."'>".$tmp."</span>";
			$color = "black";
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
	
	  
}
?>


<script src="js/script.js"></script>