<?php
	include('session.php');
	include 'PHP-Stanford-NLP-master/autoload.php';
	include 'spipu/vendor/autoload.php';
	include 'html2text/vendor/autoload.php';
	require_once 'dependency.php';
	function procText($fullText, $name, $code)
	{
		
		$user_id =  $_SESSION['user'];
		
		$pos = new \StanfordNLP\POSTagger(
		   __DIR__.'/PHP-Stanford-NLP-master/src/stanford-postagger-2015-12-09/models/english-left3words-distsim.tagger',
		   __DIR__.'/PHP-Stanford-NLP-master/src/stanford-postagger-2014-08-27/stanford-postagger.jar'
		);
		//echo $fullText;
		$result = $pos->tag(explode(' ', $fullText));
		//var_dump($result);
		//return;
		$result = removeQuestions($result);
		$procText = processText($result);
		
		// echo $procText;
		
		
		
		$ner = new \StanfordNLP\NERTagger(
		  __DIR__.'/PHP-Stanford-NLP-master/src/stanford-ner-2015-04-20/classifiers/english.all.3class.distsim.crf.ser.gz',
		  __DIR__.'/PHP-Stanford-NLP-master/src/stanford-ner-2015-04-20/stanford-ner.jar'
		);
		
		$result_ner = $ner->tag(explode(' ', $procText));
		//var_dump($result_ner);
		insertNERtags($result_ner, $code);
		
		
		$par_length = count($result);
		
		// var_dump($result);
		for($x=0; $x<$par_length; $x++){
			processDependencies($result[$x], $result_ner[$x], $code);
		}
		
		echo "<script type='text/javascript'>
		$(document).ready(function(){
			$('#btn-review').trigger('click'); 
		});
		</script>";
		
	}
	
	function insertNERtags($result, $code){
		$par = 0;
		$sib = 0;
		$coni = 0;
		$last_id = -1;
		
		
		$arr_length = count($result);
		for($i=0; $i<$arr_length; $i++){
			$in_length = count($result[$i]);
			for($j=0; $j<$in_length-1; $j++){
			
				if(strcmp($result[$i][$j][1], 'PERSON')==0) $par = 1;
				else if(strcmp($result[$i][$j][1], 'LOCATION')==0) $par = 2;
				else if(strcmp($result[$i][$j][1], 'ORGANIZATION')==0) $par = 3;
				else if(strcmp($result[$i][$j][1], 'MISC')==0) $par = 4;
				else $sib = 0;
				
				if($par == $sib){
					$coni = (int)$last_id;
					//echo $con;
				}else{
					$coni = 0;	
				}
				
				$node = $result[$i][$j][0];
				$node = addslashes($node);
				if($par != 0){
					if(mysql_query("INSERT INTO class(parent_id,name,code,sib) VALUES('$par','$node','$code','$coni')") or die(mysql_error()))
					{
						$last_id = mysql_insert_id();
					}	
					$sib = $par;
					
					$par=0;
				}
				
			}
		}
		
		
	}
	
	function processText($result){
		// $sentences = splitToSentences($fullText);
		$returnText = "";
		$arr_length = count($result);
		for($i=0; $i<$arr_length; $i++){
			$in_length = count($result[$i]);
			for($j=0; $j<$in_length-1; $j++){
				
				$returnText = $returnText.' '.$result[$i][$j][0];
				
			}
			$returnText = trim($returnText);
			$returnText = $returnText.'.';
		}
		// $returnText = implode(' ', $sentences);
		//end of remove questions
		
		return $returnText;
	}
	
	function removeQuestions($result){
		$arr_length = count($result);
		// echo $arr_length;
		//remove questions
		for($i=0; $i<$arr_length; $i++){
			$end = count($result[$i]);
			if(strcmp($result[$i][$end-1][0], '?')==0){
				array_splice($result, $i, 1);
			}
		}
		
		return $result;
		
	}
	
	function removeTitle($result){
		//Implement!	
	}
	
//start of splitFragments	
	function splitToFragments($result){
		$arr_length = count($result);
		for($i=0;$i<$arr_length;$i++)
		{
			if(strcmp($result[$i][1],"VB") == 0 || strcmp($result[$i][1],"VBD") == 0 || strcmp($result[$i][1],"VBP") == 0 || strcmp($result[$i][1],"VBZ") == 0)
			{
				$pred = array_slice($result, $i);
				$subj = array_slice($result, 0, $i);
				
				$answer = processAnswer($subj);
				$question = processQuestion($pred);
				break;
			}
		}
		
		//insertQuestionsToDB($answer, $question);
	}	
	
	function processAnswer($subj){
		// $subj = checkFragments($subj);
		
		$subj_length = count($subj);
		$j = $subj_length;
		for($i=$subj_length-1; $i>=0; $i--){
			if(strcmp($subj[$i][1],"NN") == 0 || strcmp($subj[$i][1],"NNS") == 0 || strcmp($subj[$i][1],"NNP") == 0 || strcmp($subj[$i][1],"NNPS") == 0 || strcmp($subj[$i][1],"VBG") == 0 || strcmp($subj[$i][1],"PRP") == 0 || strcmp($subj[$i][1],"PRP$") == 0 ){
				
				//|| strcmp($subj[$i][1],"DT") == 0 || strcmp($subj[$i][1],"JJ") == 0 || strcmp($subj[$i][1],"JJR") == 0 || strcmp($subj[$i][1],"JJS") == 0 || strcmp($subj[$i][1],"CC") == 0
				
				
				$answer = $subj[$i][0].' '.$answer;
			}
		}
		return $answer;
	}
	
	function processQuestion($pred){
		
		$pred_length = count($pred);
		for($i=0; $i<$pred_length; $i++){	
			$question = $question.' '.$pred[$i][0];	
		}
		
		return $question;
		
	}
//end of splitFragments

	function InsertExamToDB($file, $name, $size, $code){
		$file = trim($file);
		$name = trim($name);
		$size = trim($size);
		$user_id =  $_SESSION['user'];
	
		if(mysql_query("INSERT INTO exam(file,name,size,user_id,code) VALUES('$file','$name','$size','$user_id','$code')") or die(mysql_error()))
		{
			
		}
		else
		{
			echo "error in exam";
		}		
		
	}
	
	function generatePDF($name, $code){
		
		$ind = 0;
		if(file_exists('pdf/'.$code.'.pdf')){ 
			$del_pdf = unlink('pdf/'.$code.'.pdf');
			$del_doc = unlink('doc/'.$code.'.doc'); 
			$ind = 1;
		}
		
		$content = generateHTML($code);
		//INSERT INTO my_table (`date_time`) VALUES (CURRENT_TIMESTAMP)
		ob_start();
		
		$html2pdf = new Html2Pdf('P', 'Letter', 'en', true, 'UTF-8', array(25, 15, 25, 15));
		$html2pdf->pdf->SetDisplayMode('fullpage');
		$html2pdf->setDefaultFont("courier");
		$html2pdf->writeHTML($content);
		$file = $code.'.pdf';
		$html2pdf->Output(__DIR__ . '/pdf/'.$file, 'F'); 
		//or die('File already exists.');
		
		ob_end_flush();
		
		$file_size = filesize(__DIR__ . '/pdf/'.$code.'.pdf');
		
		$fp = fopen(__DIR__ . '/doc/'.$code.'.doc', 'w+');
		
		//$breaks = array("<br />","<br>","<br/>");  
		//$content = str_ireplace($breaks, "\r\n", $content);  
		//$str = strip_tags($content);
		libxml_use_internal_errors(true);
		$str = Html2Text\Html2Text::convert($content);
		libxml_use_internal_errors(false);
		fwrite($fp, $str);
		fclose($fp);
		
		if($ind == 0){
			insertExamToDB($file, $name, $file_size, $code);
		}else{
			mysql_query("UPDATE exam SET size='$file_size' WHERE code='$code'") or die(mysql_error()); 
			
		}
	
	}
	
	function generateHTML($code){
		
		$user_id =  $_SESSION['user'];
			
		$file = $code.".html";
		$myfile = fopen(__DIR__ . '/html/'.$file, "w") or die("Unable to open file!");
		
		$content = processHTMLContent($code);
		
		fwrite($myfile, $content);
		fclose($myfile);
		
		
		
		return $content;
	}
	
	function processHTMLContent($code){
		
		$user_id =  $_SESSION['user'];
		$school =  $_SESSION['school'];
		$fullname = ucfirst($_SESSION['f_name'])." ".ucfirst($_SESSION['m_name'][0]).". ".ucfirst($_SESSION['l_name']);
		 
		$sql_qa="SELECT * FROM qa WHERE user_id=".$user_id." AND code='".$code."'" ;
		$result_set=mysql_query($sql_qa);
		
		
		$header = file_get_contents('resources/head.html', true);
		
		
		$top = "<div>
					<span>School: ".$school."</span><br/>
					<span>Author: ".$fullname."</span><br/>
				</div>";
		
		$content = "<page><br/>".$top.$header;
		if($result_set === FALSE) { 
			die(mysql_error()); // TODO: better error handling
		}
		$q_count = 0;
		$d = array('a. ', 'b. ', 'c. ', 'd. ' );
		// $content = $content.'Instructor: '.$fullname."<br/>";
		while($row=mysql_fetch_array($result_set))
		{
			$q_count++;
			$content = $content.$q_count.". ".$row['question']."<br/>";
			// .$row['answer']."<br/>"."<br/>"
			//echo "<script>alert(".$content.");</script>";
			//$dist[] = stripslashes(stripslashes($row['answer']));
			$sql_dist = "SELECT * FROM distractor WHERE q_id=".$row['id']." ORDER BY pos";
			$result_dist = mysql_query($sql_dist);
			$d_count = 0;
			while($tmp = mysql_fetch_array($result_dist))
			{
				$dist[] = stripslashes($tmp['dist']);
				
			}
			
			foreach($dist as $value){
				
				$content = $content.$d[$d_count].$value."<br/>";
				$d_count++;
			}
			
			$content = $content."<br/>";
			
			unset($dist);
			$dist = array();
		}
		
		$content = $content."</page>";
		return $content;
		
	}
	
	function splitToSentences($fullText){
		$split_sentences = '%(?#!php/i split_sentences Rev:20160820_1800)
		# Split sentences on whitespace between them.
		# See: http://stackoverflow.com/a/5844564/433790
		(?<=          # Sentence split location preceded by
		  [.!?]       # either an end of sentence punct,
		| [.!?][\'"]  # or end of sentence punct and quote.
		)             # End positive lookbehind.
		(?<!          # But don\'t split after these:
		  Mr\.        # Either "Mr."
		| Mrs\.       # Or "Mrs."
		| Ms\.        # Or "Ms."
		| Jr\.        # Or "Jr."
		| Dr\.        # Or "Dr."
		| Prof\.      # Or "Prof."
		| Sr\.        # Or "Sr."
		| T\.V\.A\.   # Or "T.V.A."
					 # Or... (you get the idea).
		)             # End negative lookbehind.
		\s+           # Split on whitespace between sentences,
		(?=\S)        # (but not at end of string).
		%xi';  // End $split_sentences.

		$sentences = preg_split($split_sentences, $fullText, -1, PREG_SPLIT_NO_EMPTY);
		return $sentences;
	}
	
	
	


?>