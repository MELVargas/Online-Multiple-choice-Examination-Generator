<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css">

<?php
	include('session.php');
	include 'pdftotext/vendor/autoload.php';
	require_once 'function.php';	
	include('review-form.php');	

if($_POST['name'])
{
	$text = $_POST['text'];
	$name = $_POST['name'];
	$text = trim($text);
	$name = trim($name);
	$fullText = "";
	
	$pdfToText = XPDF\PdfToText::create();

	if (isset($_POST['checkbox'])) {
	if (is_array($_POST['checkbox'])) {
		foreach($_POST['checkbox'] as $checkbox){
			
			$select = trim($checkbox);
			$sql="SELECT * FROM tbl_uploads WHERE id=".$select;
			$query=mysql_query($sql);
			$fileRow=mysql_fetch_array($query);
			
			if($query) // will return true if succefull else it will return false
			{
				$pdfText = $pdfToText->getText('uploads/'.$fileRow['file']);

				$fullText = $fullText.$pdfText;
					
			}else{
				echo 'error';
				
			}		
		}
	}
	}
	
	$fullText = $fullText.$text;
	$_SESSION['exam_name'] = $name;
	$code = $_SESSION['code'];
	
	$fullText = removeNonSentence($fullText);
	procText($fullText, $name, $code);
	
	}
	
	function removeNonSentence($fullText){
		$arr = explode("\n", $fullText);
		$total_len = 0;
		$arr_len = count($arr);
		for($i=0; $i<$arr_len; $i++){
			$arr[$i] = trim($arr[$i]);
			$total_len = $total_len + strlen($arr[$i]);
		}
		
		$average = $total_len/$arr_len;
		
		
		
		for($i=0; $i<$arr_len; $i++){
			if(strlen($arr[$i]) < ($average * 2 / 3)){
				
				$dot = strrpos($arr[$i], '.', -1);
				if($dot){
					$line = substr($arr[$i], 0, $dot+1);
					if($line) $arr[$i] = $line;
				}else{
					unset($arr[$i]);
					$tmp = $i;
					$check = true;
					while($check){
						$tmp--;
						if($tmp>=0){
							$dot2 = strrpos($arr[$tmp], '.', -1);
							if($dot2){
								$line = substr($arr[$tmp], 0, $dot2+1);
								if($line) $arr[$tmp] = $line;
								$check = false;
							}else{
								unset($arr[$tmp]);
								
							}
							
						}else{
							$check = false;
						}
					}
				}
			}
		}
		
		//array_splice($arr, $i, 1);
		$arr = array_values($arr);
		$text = implode(" ",$arr);
		return $text;
	}
	
	function insertToDB($letter){
		$letter = trim($letter);
		ini_set('max_execution_time', 1000000);
		
		$dir = "library/".$letter;
		$dh  = opendir($dir);
		while (false !== ($filename = readdir($dh))) {
			$files[] = $filename;
		}
		$fruit = array_shift($files);
		$fruit = array_shift($files);
		// var_dump($files);
		
		foreach($files as $value){
			$path = "library/".$letter."/".$value;
			
			// echo $value;
			// echo "<br />";
			// $test = explode('#', $value, 2);
			// $tmp_word = $test[0];
			// $exist = "";
			// $sql="SELECT * FROM entry_".$letter." WHERE word='".$tmp_word."'" ;
			// $result_set=mysql_query($sql) or die(mysql_error());
			// while($row=mysql_fetch_array($result_set))
			// {
				// $exist = $row['word'];
				
			// }
			
			// if($exist == ""){
				// echo $tmp_word;
				// echo "<br />";
			// }
			
			
			
			if (file_exists($path)){
				// $file = nl2br(file_get_contents('library/a/'.$cand[$j].'#n#1.path.wnsim.txt', true));	
				// echo $file;			
				
				
					$count = 0;
					$handle = fopen($path, "r");
				if ($handle) {
					while (($line = fgets($handle)) !== false) {
						// process the line read.
					
						// echo $line;
						// echo "<br />";
						
						if($count>0){
							$line_arr = explode(' ',trim($line));
							$word = explode('#', $line_arr[1], 2);
							//$dist = array();
							//$dist_word = array();
							$word_value = (double)$line_arr[0];
							//$line_value = (int)$line_value;
							// echo $word[0];
							// echo " ";
							// echo $line_value;
							// echo "<br />";
							if($word_value >= (float)0.333333){
								$line_key[] = $word[0];
								$line_value[] = (int)($word_value * 10);
								$tmppp = (int)($word_value * 10);
								$tmooo = mysql_real_escape_string($word[0]);
								mysql_query("INSERT INTO entry_".$letter." (word,value,target) VALUES('$tmooo','$tmppp', '$last_id')") or die(mysql_error());
							}
							
							
						}else{
							$pieces = explode(" ", $line);
							$count++;
							$cut = explode("#", $pieces[2]);
							$key = mysql_real_escape_string($cut[0]);
							echo $key;
							echo "<br />";
							mysql_query("INSERT INTO entry_".$letter." (word,value) VALUES('$key',999)") or die(mysql_error());
							$last_id = (int)mysql_insert_id();
						}
					}

					fclose($handle);
					
					
				} else {
					// error opening the file.
				}
				
				$ar1 = array(10, 100, 100, 0);
				$ar2 = array(1, 3, 2, 4);
				array_multisort($line_value, $line_key);
				$line_value = array_reverse($line_value);
				$line_key = array_reverse($line_key);
				// var_dump($line_value);
				// var_dump($line_key);
				
				unset($line_value);
				unset($line_key);
				
			}
			
		}
		
	}


	include('review-modal.php');
?>

