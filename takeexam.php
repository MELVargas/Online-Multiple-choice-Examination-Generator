<?php

include('session.php');
$user_id = $userRow['user_id'];
if($_POST["code"])
{
	$code = $_POST["code"];
	
   // Here, you can also perform some database query operations with above values.
   
	//$code = mysql_real_escape_string($_POST['code']);
	$sql="SELECT * FROM exam WHERE code='".$code."'" ;
	if(mysql_query($sql)){
		$result_set=mysql_query($sql);
		$row=mysql_fetch_array($result_set);
		
		
		if(mysql_num_rows($result_set))
		{
			
			$code = $row['code'];
			$exam_id = $row['id'];
			$count = 0;
			echo "<table class='table table-bordered table-hover' id='question-modal'>";
			$sql="SELECT * FROM qa WHERE code='".$code."'" ;
			$result_set=mysql_query($sql) or die(mysql_error());
			while($row=mysql_fetch_array($result_set))
			{
				echo "<tr><td><strong>".$row['question']."</strong>";
				echo "<br />";
				echo "<br />";
				$q_id = $row['id'];
				echo "<input type='hidden' name='q_id[".$count."]' value='".$q_id."' />";
				$in_sql="SELECT * FROM distractor WHERE q_id='".$q_id."' ORDER BY pos" ;
				$in_result_set=mysql_query($in_sql) or die(mysql_error());
				while($in_row=mysql_fetch_array($in_result_set))
				{
					
					$pos = $in_row['pos'];
					$line = $in_row['pos']+1;
				
					$dist[] = $in_row['dist'];
					$tmp_count[] = $count;
					$tmp_line[] = $line;
					$tmp_pos[] = $pos;
					
					// echo "<label class='radio-inline'>
						// <input type='radio' name='answer[".$count."]' id='inlineRadio".$line."' value='".$pos."' required>".$dist."</label>";
						// echo "<br />";
					
				}
				
				$arr_length = count($dist);
				$num_arr = range(0, $arr_length-1);
				
				shuffle($num_arr);
				// var_dump($num_arr);
				for($i=0; $i<$arr_length; $i++){
					echo "<label class='radio-inline'>
						<input type='radio' name='answer[".$tmp_count[$num_arr[$i]]."]' id='inlineRadio".$tmp_line[$num_arr[$i]]."' value='".$tmp_pos[$num_arr[$i]]."' required>".$dist[$num_arr[$i]]."</label>";
						echo "<br />";
				}
				
				unset($dist);
				unset($tmp_count);
				unset($tmp_line);
				unset($tmp_pos);
				echo "</td></tr>";
				$count++;
			}
			echo $file;
			echo "<input type='hidden' name='code' value='".$code."'/>";
			echo "<input type='hidden' name='exam_id' value='".$exam_id."'/>";
			echo "<input type='hidden' name='user_id' value='".$user_id."'/>";
			echo "The examination code is: ". $code;
			echo "</table>";
		}else{
			echo "Input code does not exist.";
		}
		
		
	}
   
   
}
?>