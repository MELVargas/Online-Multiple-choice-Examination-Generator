<?php
	require_once 'inflect.php';
	$singu = new Inflect();
//start of processDependencies
	function processDependencies($result, $ner, $code)
	{
		var_dump($result);
		$code = $_SESSION['code'];
		$clause = getClause($result);
	
		$root = findRoot($clause, $result);
		// echo "<script>alert(".$root.")</script>";
		if($root == null) return;
		
		$because = findIndex($result, "because", $clause);
		$by = findIndex($result, "by", $clause);
		if(strcmp($result[0][1],"VB")==0)return;//imperative
		
		if(in_array_r("because", $result) && ($because>$root)){
			
			detectWhy($result, $ner, $clause, $code, $root);
			
		}
		else if(in_array_r("by", $result) && ($by>$root) && ($result[$by+1][1] == 'VBG')){
			detectHow($result, $ner, $clause, $code, $root);
			//how
		}
		else{
			
		
		// echo "<script>alert(".$root.")</script>";
		//return;
		
		
		$subjFragEnd = findNSubj($clause, $result, $root);
		if($subjFragEnd == -1) return;
		// echo "<script>alert(".$subjFragEnd.")</script>";
		$subjFrag = buildSubjFrag($clause, $result, $subjFragEnd);
		// var_dump($subjFrag);
		if($subjFrag == null) return;
		// echo "<script>alert(".$root.")</script>";
		$subjNERFrag = buildSubjFrag($clause, $ner, $subjFragEnd);
		$ques = idenWH($result, $ner, $subjFragEnd, $root, $clause);
		$answer = buildFrag($subjFrag);
		
		//var_dump($subjNERFrag);
		$dist = getDistractors($subjFrag, $subjNERFrag, 4 - 1, $code);
		$fin_dist = buildDistrators($subjFrag, $dist, 4 - 1);
		// if($fin_dist == null) return;
		
		
		
		// echo $ques;
		// echo "<br />";
		// echo $answer;
		// echo "<br />";
		// echo "<br />";
		// var_dump($fin_dist);
		
		if(verifyAnsQues(trim($answer), trim($ques)) == 0) return;
		insertQuestionsToDB($answer, $ques, $fin_dist, $code);
		}
	}
	
	function detectWhy($result, $ner, $start, $code, $root){
		$index = findIndex($result, "because", $start);
		
		// echo $start;
		// echo "<br />";
		// echo $index;
		// echo "<br />";
		$tmp = array_slice($result, $start, $index-$start);
		$new_root = $root - $start;
		// echo "<br />";
		$question = repoBe($tmp, $new_root, 1);
		$ans = array_slice($result, $index);
		
		$ans_ner = array_slice($ner, $index);
		$answer = buildFrag($ans);

		$dist = getDistractors($ans, $ans_ner, 4 - 1, $code);
		$fin_dist = buildDistrators($ans, $dist, 4 - 1);
		// echo $question;
		// echo "<br />";
		// echo $answer;
		// echo "<br />";
		if(verifyAnsQues(trim($answer), trim($question)) == 0) return;
		insertQuestionsToDB(trim($answer), trim($question), $fin_dist, $code);
	}
	
	function verifyAnsQues($answer, $question){
		if(strlen($answer) >= 1000 || strlen($question) >= 1000){
			return 0;
		}
		return 1;
		
	}
	
	function detectHow($result, $ner, $start, $code, $root){
		$index = findIndex($result, "by", $start);
		$tmp = array_slice($result, $start, $index-$start);
		$new_root = $root - $start;
		$question = repoBe($tmp, $new_root, 2);
		$ans = array_slice($result, $index);
		
		$ans_ner = array_slice($ner, $index);
		$answer = buildFrag($ans);

		$dist = getDistractors($ans, $ans_ner, 4 - 1, $code);
		$fin_dist = buildDistrators($ans, $dist, 4 - 1);
		if(verifyAnsQues(trim($answer), trim($question)) == 0) return;
		insertQuestionsToDB(trim($answer), trim($question), $fin_dist, $code);
	}
	
	function repoBe($result, $root, $type){
		global $singu;
		// var_dump($result);
		if(strcmp($result[$root][0], 'is')==0 || strcmp($result[$root][0], 'was')==0 || strcmp($result[$root][0], 'are')==0 || strcmp($result[$root][0], 'were')==0){
			
			$tmp = $result[$root];
			array_splice($result, $root, 1);
			array_unshift($result, $tmp);
			if($type==1)$text = "Why ".strtolower(buildFrag($result))."?";
			else if($type==2)$text = "How ".strtolower(buildFrag($result))."?";
			return $text;
		}else{
			$handle = "";
			
			$tmp = $result[$root][0];
			$tmp = $singu->singularize($tmp);
			
			$sql="SELECT * FROM verb WHERE past='".$tmp."'";
			$result_set=mysql_query($sql);
			$row=mysql_fetch_array($result_set);
			if($row){
				$handle = "did ";
				$result[$root][0] = $row['present'];
				
			}else{
				if(strcmp($tmp, $result[$root][0])==0){
					$handle = "do ";
				
				}else{
					
					$handle = "does ";
				}
				$result[$root][0] = $tmp;
			}
			
			
			if($type == 1)$text = "Why ".$handle.strtolower(buildFrag($result))."?";
			else if($type == 2)$text = "How ".$handle.strtolower(buildFrag($result))."?";
			return $text;
		}
	}
	
	function in_array_r($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
				return true;
			}
		}

		return false;
	}
	
	function findIndex($result, $word, $clause){
		//first occurence from left to right
		$word = trim($word);
		$start = $clause;
		$end = count($result);
		for($i=$start; $i<$end; $i++){
			if(strcmp($result[$i][0], $word) == 0) return $i;
		}
		
		return;
	}
	
	function buildFrag($result){
		$end = count($result);
		for($i=$end;$i>=0;$i--)
		{
			$frag = $result[$i][0].' '.$frag;
		}
		$frag = str_replace("-LRB-", "(", $frag);
		$frag = str_replace("-RRB-", ")", $frag);
		return mysql_real_escape_string(trim($frag));
		
	}
	
	function shuffleWord($word){
		
		
		return $word;
	}
	
	function getDistractors($result, $ner, $num, $code){
		
		$frag_length = count($result);
		global $singu;
		
		for($i =0; $i<$frag_length; $i++){
			if(strcmp($result[$i][1],"NN") == 0 || strcmp($result[$i][1],"NNS") == 0 || strcmp($result[$i][1],"NNP") == 0 || strcmp($result[$i][1],"NNPS") == 0 ||  strcmp($result[$i][1],"VBG") == 0   ){
			//|| strcmp($result[$i][1],"PRP") == 0 || strcmp($result[$i][1],"PRP$" ) == 0 || strcmp($result[$i][1],"EX" ) == 0
				//$str = $str.' '.$result[$i][0];
				// $str = trim($str);
				$str = trim($result[$i][0]);
				$cand[] = $str; 
				$cand_ner[] = $ner[$i][1];
				$index[] = $i;
			}else{
				$str = '';		
			}
		}
		
		var_dump($cand);
		
		$cand_length = count($cand);
		
		for($j=0; $j<$cand_length; $j++){
			$cap_trig = false;
			$plu_trig = false;
			
			$masorig = $cand[$j];
			$cand[$j] = str_replace(' ','_',$cand[$j]);
			$target = strtolower($cand[$j]);
			$orig = $target;
			$target = $singu->singularize($target);
			//similar_text($orig, $target, $per);
			if(strcmp($orig, $target) != 0) $plu_trig = true;
			
			if(ctype_upper($masorig{0}))
			{
				$cap_trig = true;
			}
			
			if(ctype_alpha($target{0}))$db_name = "entry_".$target{0};
			else $db_name = "entry_0";
			
			$target = mysql_real_escape_string($target);
			$masorig = mysql_real_escape_string($masorig);
			
			$sql="SELECT * FROM ".$db_name." WHERE word='".$target."' AND value='999'" ;
			$result_set=mysql_query($sql);
			
			while($row=mysql_fetch_array($result_set))
			{
				$targ_id = $row['id'];
			}
			
			$sql="SELECT * FROM ".$db_name." WHERE target=".$targ_id." AND value<='5'";
			$result_set=mysql_query($sql);
			//strcmp($cand_ner[$j],"PERSON") != 0 && /
			if($result_set){
				while($row=mysql_fetch_array($result_set))
				{
					
					//$row['word'] = str_replace('_',' ',$cand[$j]);
					if (strpos($row['word'], '_') == false) {
						// echo $row['word'];
					// echo '<br />';
						$dist_word[] = $row['word'];
					}
					
				}
				
			}else{
				$sql="SELECT * FROM class WHERE name='".$masorig."' AND code='".$code."'AND sib='0'" ;
				$result_set=mysql_query($sql) or die(mysql_error());
				$iden = 0;
				while($row=mysql_fetch_array($result_set))
				{
					$iden = $row['parent_id'];
					
					//fetch same parent_id and code
				}
				
				
				if($iden != 0){
					$plu_trig = 0;
					$sql="SELECT * FROM class WHERE parent_id='".$iden."' AND code='".$code."' AND name!='".$masorig."' AND sib='0'" ;
					$result_set=mysql_query($sql) or die(mysql_error());
					
					while($row=mysql_fetch_array($result_set))
					{
						$dist_word[] = $row['name'];
						//fetch same parent_id and code
					}
					
					if(empty($dist_word)){
						$sql="SELECT * FROM class WHERE parent_id='".$iden."' AND name!='".$masorig."' AND sib='0'" ;
						$result_set=mysql_query($sql) or die(mysql_error());
						
						while($row=mysql_fetch_array($result_set))
						{
							$dist_word[] = $row['name'];
							//fetch same parent_id and code
						}
						// var_dump($dist_word);
						
						
					}
					$iden = 0;
				}else{
					//jumbapalooza
					if($j == $cand_length-1){
						$vowels = array('a', 'e', 'i', 'o', 'u');
						$str_len = strlen($target);
						$trgt = $target;
						
						for($z=0; $z<$str_len; $z++){
							if(strcmp($trgt[$z], 'a')==0 || strcmp($trgt[$z], 'e')==0 || strcmp($trgt[$z], 'i')==0 || strcmp($trgt[$z], 'o')==0 || strcmp($trgt[$z], 'u')==0){
								$vows_id[] = $z;
								$vows[] = $trgt[$z];
								
							}else{
								$cons_id[] = $z;
								$cons[] = $trgt[$z];
							}
							
						}
						
						for($z=0; $z<$str_len; $z++){
							if(!empty($cons)){
								shuffle($cons);
								
								for($y=0; $y<count($cons_id); $y++){
									$ind = $cons_id[$y];
									$trgt[$ind] = $cons[$y]; 
								}	
								if(strcmp($target, $trgt)!=0)$dist_word[] = $trgt;
							}else{
								shuffle($vows);
								
								for($y=0; $y<count($vows_id); $y++){
									$ind = $vows_id[$y];
									$trgt[$ind] = $vows[$y]; 
								}	
								if(strcmp($target, $trgt)!=0)$dist_word[] = $trgt;
								
							}
						}
					}
					
					
					
				
				
					
				}
				//fetch same parent_id and code
				
				// if($iden == 0 || count($dist_word)<3){
					
					// unset($dist_word);
					// //last alternative
					// //shuffle consonants
					// $dist_word = shuffleWord($masorig);
				// }
				
			}
			// $path = 'library/'.$target[0].'/'.$target.'#n#1.path.wnsim.txt';
			
			
				if($plu_trig || $cap_trig) {
					
					$dw_length = count($dist_word);
					for($x = 0; $x < $dw_length; $x++){
						if($plu_trig)$dist_word[$x] = $singu->pluralize($dist_word[$x]);
						if($cap_trig)$dist_word[$x] = ucwords($dist_word[$x]);
						
					}
				}
				
				// var_dump($dist_word);
				if (!empty($dist_word)) {
					$tmp[] = $index[$j];
					$dist_word = array_unique($dist_word);
					if($dist_word)shuffle($dist_word);
					$tmp[] = $dist_word;
					$arr[] = $tmp;
					
					
					var_dump($arr);
					return $arr;
				}
				//var_dump($dist);
				unset($dist);
				unset($tmp);
				$dist = array();
				$tmp = array();
				
				// var_dump();
			// }
		}
		
		//var_dump($arr);
		//return;
		
	}
		
	function buildDistrators($result, $dist, $num){
		
		$arr_length = count($dist);
		if(!$dist) echo "true";
		else echo "false";
		var_dump($dist);
		//if($arr_length < $num+1 && $arr_length != 0) $num = $arr_length;
		//if arr_length == 0 shuffle
	
		for($j=0; $j<$num; $j++){
			for($i=0; $i<$arr_length; $i++){
				$index = $dist[$i][0];
				$result[$index][0] = $dist[$i][1][$j];
				
			}
			$tmp = buildFrag($result);
			$dist_col[] = $tmp;
		}
		$dist_col = array_filter($dist_col);
		var_dump($dist_col);
		$dist_col = array_unique($dist_col);
		//if(count($dist_col) == 1) return;
		return $dist_col;
	}
	
	function getClause($result){
		$subj = buildFrag($result);
		
		if (strpos($subj, ',') !== false) {
			
			$arr_length = count($result);
			$in = 0;
			for($i=0;$i<$arr_length;$i++)
			{
				if(strcmp($result[$in][1],"IN") == 0 || strcmp($result[$in][1],"WRB") == 0 || strcmp($result[$in][1],"RB") == 0){
					if(strcmp($result[$i][1],",") == 0){
						$in = $i + 1;
					}
				}
				
			}
			
			return $in;
			// for($j=0;$j<$in;$j++)
			// {
				// if(strcmp($result[$j][1],"NN") == 0 || strcmp($result[$j][1],"NNS") == 0 || strcmp($result[$j][1],"NNP") == 0 || strcmp($result[$j][1],"NNPS") == 0 ||  strcmp($result[$j][1],"VBG") == 0){
					// return $in;
				// }
				
			// }
			
			// return 0;
		}else{
			
			return 0;
		}
		
		
		return 0;
		
	}
	
	function buildSubjFrag($start, $result, $end){
		
		//find clauses
		for($i=$end;$i>=$start;$i--)
		{
			//if(strcmp($result[$i][1],",") == 0) break;
			$frag[] = $result[$i];
			
		}
		
		$frag = array_reverse($frag);
		//if(in_array_r("PRP", $result) || in_array_r("EX", $result) ) return; 
		//|| in_array_r("PRP$", $result) 
		return $frag;
	}
	
	
	
	function findNearestNounRight($result, $pos, $exc){
		$arr_length = count($result);
		
		
		for($i=$pos;$i<$arr_length;$i++)
		{
			if(strcmp($result[$i][1],"NN") == 0 || strcmp($result[$i][1],"NNS") == 0 || strcmp($result[$i][1],"NNP") == 0 || strcmp($result[$i][1],"NNPS") == 0 ||  strcmp($result[$i][1],"VBG") == 0 || strcmp($result[$i][1],"PRP") == 0 || strcmp($result[$i][1],"PRP$") == 0 ){
				$tmp = $i;
				for($j=0; $j<$arr_length; $j++){
					if(strcmp($result[$i][1],$exc[$j]) == 0)return;
					else return $tmp;
				}
			}
			
		}	
	}
	
	function findNSubj($clause, $result, $pos){
		$trig = 0;
		
		if($pos!=$clause){
		for($i=$pos;$i>=$clause;$i--)
		{
			// echo $pos;
			// echo "<br />";
			// echo $clause;
			// echo "<br />";
			if((strcmp($result[$i][1],"NN") == 0 || strcmp($result[$i][1],"NNS") == 0 || strcmp($result[$i][1],"NNP") == 0 || strcmp($result[$i][1],"NNPS") == 0 || strcmp($result[$i][1],"VBG") == 0 ||  strcmp($result[$i][1],"CD") == 0 ||  strcmp($result[$i][1],"''") == 0 ||  strcmp($result[$i][1],"-RRB-") == 0 ||  strcmp($result[$i][1],"JJ") == 0 ) && $trig == 0){
				//strcmp($result[$i][1],"PRP") == 0 || strcmp($result[$i][1],"PRP$") == 0 || strcmp($result[$i][1],"EX") == 0
				//strcmp($result[$i][1],"PRP") == 0 || strcmp($result[$i][1],"PRP$") == 0 || strcmp($result[$i][1],"EX") == 0 ||||  strcmp($result[$i][1],"RB") == 0 
				return $i;
			// }else if(strcmp($result[$i][1],',') == 0){
				// $x = $start;
				// $check = 0;
				// while($x!=($i-1)){
					// if(strcmp($result[$x][1],',') == 0){
					// $check = 1;
					// break;
					// }
					// $x++;
				// }
				// echo $check;
				// if($check == 1){
					// if($trig == 0) $trig = 1;
					// else if($trig == 1) $trig = 0;
					// $check = 0;
				// }
			}
		}}
		return -1;
	}
	
	function findRoot($start, $result){
		$arr_length = count($result);
		$trig = 0;
		$quoteTrig = 0;
		$parenTrig = 0;
		// $quotes = mysql_real_escape_string("'");
		
		for($i=$start;$i<$arr_length;$i++)
		{
			if(strcmp($result[$i][1],"``") == 0) $quoteTrig = 1;
			if(strcmp($result[$i][1],"-LRB-") == 0) $parenTrig = 1;
			
			if($quoteTrig == 1){
				if(strcmp($result[$i][1],"''") == 0) $quoteTrig = 0;
				
			}else if($parenTrig == 1){
				if(strcmp($result[$i][1],"-RRB-") == 0) $parenTrig = 0;		
				
			}else{				
				if(strcmp($result[$i][1],"IN") == 0 && $trig == 1)
				{
					return;
					//|| strcmp($result[$i][1],"VBN") == 0
				}else if(strcmp($result[$i][1],"MD") == 0 || strcmp($result[$i][1],"VB") == 0 || strcmp($result[$i][1],"VBD") == 0 || strcmp($result[$i][1],"VBP") == 0 || strcmp($result[$i][1],"VBZ") == 0|| strcmp($result[$i][1],"VBN") == 0)
				{
					$check = checkRoot($result, $i);
					if($check){	
					
						//echo $i;
						$trig = 1;
						$nSubj = findNSubj($start, $result, $i);	
						//echo $nSubj;
						if($nSubj!= -1){
							//echo $i;
							return $i;
						}else{
							
							return;
						}		
					}else{
						
						return;
					}
				}
			}
		}
		
		return;
		
	}
	
	function checkRoot($result, $rootIndex){
	
		for($i=$rootIndex-1; $i>=0; $i--){
			if(strcmp($result[$i][1],"VB") == 0 || strcmp($result[$i][1],"VBD") == 0 || strcmp($result[$i][1],"VBP") == 0 || strcmp($result[$i][1],"VBN") == 0 || strcmp($result[$i][1],"VBZ") == 0) return true;
			else if(strcmp($result[$i][1],"WDT") == 0)return false;
		}
		
		return true;
	}
	
	function insertQuestionsToDB($answer, $question, $dist, $code)	{
		$code = trim($code);
		if($dist != null){
		foreach (range(0, count($dist)) as $number) {
			$pos[] = $number;
			
		}
		shuffle($pos);
		
		$none = rand(1, 10);
		$answer = str_replace("-LRB-", "(", $answer);
		$answer = str_replace("-RRB-", ")", $answer);
		
		$question = str_replace("-LRB-", "(", $question);
		$question = str_replace("-RRB-", ")", $question);
		
		$answer = (string)trim($answer);
		$question = (string)mysql_real_escape_string(trim($question));
		
		$user_id =  $_SESSION['user'];
		
		
		if($answer == null || $question == null) return;
		if(mysql_query("INSERT INTO qa(answer,question,user_id,code) VALUES('$answer','$question','$user_id', '$code')") or die(mysql_error()))
		{
			$tmp = $pos[0];
			//echo $tmp;
			if($tmp == 3 && $none == 1) $answer = "None of the above";
			$last_id = (int)mysql_insert_id();
			
			if(mysql_query("INSERT INTO distractor(q_id,dist,correct,pos) VALUES('$last_id','$answer','1','$tmp')") or die(mysql_error())){}
			$count = 0;
			//var_dump($dist);
			foreach ($dist as $value){
				$count++;
				$tmp = $pos[$count];
				
				if($tmp == 3 && $none == 1) $value = "None of the above";
				$value = (string)$value;
				if(mysql_query("INSERT INTO distractor(q_id,dist,correct,pos) VALUES('$last_id','$value','0','$tmp')") or die(mysql_error())){}
			}
			
		}
		else
		{
			echo "error in questions";
		}		
		}
	}
	
	function idenWH($result, $ner, $subjEnd, $predStart, $clause)	{
		$type = 0;
		$ans = "";
		// if($subjEnd != 0){
			// for($x = $subjEnd; $x >= 0; $x--){
				// if(strcmp($result[$x][1],"NN") != 0 || strcmp($result[$x][1],"NNS") != 0 || strcmp($result[$x][1],"NNP") != 0 || strcmp($result[$x][1],"NNPS") != 0 ||  strcmp($result[$x][1],"VBG") != 0 || strcmp($result[$x][1],"PRP") != 0 || strcmp($result[$x][1],"PRP$") != 0 )break;
				
			// }
			// $subjEnd = x+1;
		// }
		for($j=$clause;$j<=$subjEnd;$j++)
		{
			$ans = $ans.$result[$j][0].' ';
		}
		$DT = date_parse($ans);
		
		// var_dump($DTtest);	
		// $DT = validateDate($ans);
		//var_dump($DT);
		if(strcmp($ner[$clause][1],"PERSON") == 0){
			$type = 1;
		}else if(strcmp($ner[$clause][1],"LOCATION") == 0){
			$type = 2;
		}else if(($DT['year']!=false && $DT['month']!=false && $DT['day']!=false) || $DT['fraction']===(float)0){
			$type = 3;
		}
		
		$arr_length = count($result);
		
		for($i=$predStart;$i<$arr_length-1;$i++)
		{
			$frag = $frag.$result[$i][0].' ';
		}
		$frag = trim($frag);
		
		$frag = $frag.'?';
		
		if($type == 1) $frag = "Who ".$frag;
		else if($type == 2) $frag = "Where ".$frag;
		else if($type == 3) $frag = "When ".$frag;
		else $frag = "What ".$frag;
		
		
		if($clause != null){
			
			for($i=0;$i<$clause-1;$i++)
			{
				$tmp = $tmp.$result[$i][0].' ';
			}
			
			$tmp = trim($tmp);
			$tmp = $tmp.", ";
			$frag = lcfirst($frag);
			$frag = $tmp.$frag;
		}
		return $frag;
	}
	
	function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}
	
	
//end of processDependencies

?>