 <?php
include('session.php');
$user_id = $userRow['user_id'];

include 'graph.php';
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>File Uploading With PHP and MySql</title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<link rel="stylesheet" href="css/highcharts.css" type="text/css" />
</head>
<body>
<?php
include('header-student.php');
?>
<div id="body">
	<!--table id="result" class="table table-striped">
    <tr>
    <th>Exam Name</th>
	<th>Code</th>
    <th>Author</th>
    <th>Date Taken</th>
    <th>Score</th>
    <th>Total</th>
    
	
    </tr-->
    <?php
	
	$user_id = $_SESSION['user'];
	
	$sql="SELECT * FROM result WHERE user_id=".$user_id." ORDER BY exam_id ASC, datetime ASC";
	$result_set=mysql_query($sql) or die(mysql_error());
	
	if(mysql_num_rows($result_set)== 0){
	   echo "No one took this exam.";
	}
	else{
		// $in_sql="SELECT * FROM exam WHERE id=".$id;
		// $in_result_set=mysql_query($in_sql);
		// if ($in_result_set && mysql_num_rows($in_result_set) > 0) {
			// while($in_row=mysql_fetch_array($in_result_set))
			// {
				// $exam_name = $in_row['name'];
				// $author_id = $in_row['user_id'];
			// }
		// } else {
			// $exam_name = "Does Not Exist";
		// }
		
		// $in_sql="SELECT * FROM users WHERE user_id=".$author_id;
		// $in_result_set=mysql_query($in_sql);
		// if ($in_result_set && mysql_num_rows($in_result_set) > 0) {
			// while($in_row=mysql_fetch_array($in_result_set))
			// {
				// $author= ucfirst($in_row['f_name'])." ".ucfirst($in_row['m_name'][0]).". ".ucfirst($in_row['l_name']);
			// }
		// } else {
			// $author = "Does Not Exist";
		// }
		
		// $in_sql="SELECT * FROM result WHERE exam_id=".$id;
		// $in_result_set=mysql_query($in_sql);
		// if ($in_result_set && mysql_num_rows($in_result_set) > 0) {
			// while($in_row=mysql_fetch_array($in_result_set))
			// {
				// $total = $in_row['total'];
			// }
		// } else {
			// $total = 0;
		// }
		// echo "<strong>Exam Name: </strong>".$exam_name;
		// echo "<br />";
		// echo "<strong>Author: </strong>".$author;
		// echo "<br />";
		// echo "<strong>No. of Items: </strong>".$total;
		// echo "<br />";
		// echo "<br />";
		
		echo "<table class='table table-bordered table-hover' id='result'>";

		echo "<tr>";
		echo "<th>Exam Name</th>";
		echo "<th>No. of Times Taken</th>";
		echo "<th>First Take Score</th>";
		echo "<th>Highest Score</th>";
		echo "<th><span class='glyphicon glyphicon-modal-window'></span></th>";
		echo "</tr>";
		
		
		$tmp_id = 0;
		$count = 0;
		$loop = 0;
		$trg = 0;
		$numRow = mysql_num_rows($result_set);
		while($row=mysql_fetch_array($result_set))
		{
			
			$taker_id = $row['exam_id'];

			
			
			
			if($tmp_id != $taker_id && $loop>0){
			echo "<tr>";
			echo "<td colspan='3'>";
			createGraph($tmp_id, $taker, 5, 5, $score);
			unset($score);
			echo "</td>";
			echo "</tr>";
			echo "
				</table>
				</td>
				</tr>";
					
			}
			
			$in_sql="SELECT * FROM exam WHERE id=".$taker_id;
			$in_result_set=mysql_query($in_sql);
			if ($in_result_set && mysql_num_rows($in_result_set) > 0) {
				while($in_row=mysql_fetch_array($in_result_set))
				{
					$taker = $in_row['name'];
				}
			} else {
				$taker = "Does Not Exist";
			}
			
			
			if($tmp_id != $taker_id){
				$count = 0;
				$high = 0;
				$first = -1;
				
				$in_sql="SELECT * FROM result WHERE user_id=".$user_id." AND exam_id=".$taker_id;
				$in_result_set=mysql_query($in_sql);
				$count = mysql_num_rows($in_result_set);
				
				if ($in_result_set && $count > 0) {
					while($in_row=mysql_fetch_array($in_result_set))
					{
						if($in_row['score'] > $high) $high = $in_row['score'];
						if($first == -1) $first = $in_row['score'];
					}
				} else {
					$count = 0;
				}
				
				
				$name[] = $taker;
				$numTake[] = $count;
				$highScore[] = $high;
				$firstScore[] = $first;
				
				
				// $tkr = $taker;
				// $num = $count;
				// $count = 1;
				// $high = $row['score'];
				// $first = $high;
				// $trg = 1;
			// }else{
				// $count++;
				// if($row['score'] > $high) $high = $row['score'];
				
			// }
			
			// if(($trg == 1 && $loop > 0) || $loop == $numRow){
				// $name[] = $tkr;
				// $numTake[] = $num;
				// $highScore[] = $high;
				// $firstScore[] = $first;
				// $trg = 0;
				echo "<tr data-toggle='collapse' data-target='#accordion-".$row['id']."' class='clickable' data-parent='#accordion' aria-expanded='true'>";
				echo "<td>";
				echo $taker;
				echo "</td>";
				echo "<td>";
				echo $count;
				echo "</td>";
				echo "<td>";
				echo $first;
				echo "</td>";
				echo "<td>";
				echo $high;
				echo "</td>";
				echo "<td>";
				echo "<span class='glyphicon glyphicon-chevron-down'></span>";
				echo "</td>";
				echo "</tr>";
				
				echo "<tr  id='accordion-".$row['id']."' class='panel-collapse  collapse active' role='tabpanel'>
					<td colspan='5'>
					<table class='table table-bordered' style='width:100%;'>";
				echo "<tr>";
				echo "<th>Date & Time Taken</th>";
				echo "<th>Score</th>";
				echo "</tr>";
			}
			echo "<tr>";
			echo "<td>";
			echo $row['datetime'];
			echo "</td>";
			echo "<td>";
			echo $row['score'];
			$score[] = (int)$row['score'];			
			echo "</td>";
			echo "</tr>";
			
			
				
			
			
			$loop++;
			$tmp_id = $taker_id;
			
			
				
		}
		echo "<tr>";
		echo "<td colspan='2'>";
		createGraph($tmp_id, $taker, 5, 5, $score);
		unset($score);
		echo "</td>";
		echo "</tr>";
		echo "
			</table>
			</td>
			</tr>";		
		
		echo "</table>";
	}
	
	
	// $count = 0;
	// $user_id = $_SESSION['user'];
	// $sql="SELECT * FROM result WHERE user_id=".$user_id." ORDER BY datetime";
	// $result_set=mysql_query($sql);
	// while($row=mysql_fetch_array($result_set))
	// {
        
		// $exam_id = $row['exam_id'];
		// $in_sql="SELECT * FROM exam WHERE id=".$exam_id."";
		// $user_id = 0;
		// $in_result_set=mysql_query($in_sql);
		// if ($in_result_set && mysql_num_rows($in_result_set) > 0) {
			// while($in_row=mysql_fetch_array($in_result_set))
			// {
				// $exam_name = $in_row['name'];
				// $user_id = $in_row['user_id'];
				// $exam_code = $in_row['code'];
			// }
		// } else {
			// $exam_name = "Does Not Exist";
			// $user_id = 0;
			// $exam_code = "Does Not Exist";
		// }
		
		// $in_sql="SELECT * FROM users WHERE user_id=".$user_id;
		// $in_result_set=mysql_query($in_sql);
		// if ($in_result_set && mysql_num_rows($in_result_set) > 0) {
			// while($in_row=mysql_fetch_array($in_result_set))
			// {
				// $author = ucfirst($in_row['f_name'])." ".ucfirst($in_row['m_name'][0]).". ".ucfirst($in_row['l_name']);
			// }
		// } else {
			// $author = "Does Not Exist";
		// }
		
		// echo "<tr>";
		
		// echo "<td>";
		// echo $exam_name;
		// echo "</td>";
		// echo "<td>";
		// echo $exam_code;
		// echo "</td>";
		// echo "<td>";
		// echo $author;
		// echo "</td>";
		// echo "<td>";
		// echo $row['datetime'];
		// echo "</td>";
		// echo "<td>";
		
		// echo $row['score'];
		// echo "</td>";
		// echo "<td>";
		
		// echo $row['total'];
		// echo "</td>";
		// echo "</tr>";
		
	// }
	?>
    <!--/table-->
	
</div>
<script src="js/highcharts.js"></script>
<script src="js/exporting.js"></script>
 <?php include('footer.php'); ?>
</body>
</html>