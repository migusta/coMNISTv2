<?php 
    require_once("session.php");
	
	require_once("class.user.php");
	$auth_user = new USER();
	
	$user_id = $_SESSION['user_session'];
	
	//получаем текущего пользователя
	$stmt = $auth_user->runQuery("SELECT * FROM users WHERE user_id=:user_id");
	$stmt->execute(array(":user_id"=>$user_id));
	
	$userRow=$stmt->fetch(PDO::FETCH_ASSOC);

	//получаем буквы, которые знает пользователь 
	/* $stmt = $auth_user->runQuery("SELECT l.id, l.name FROM letters l 
	  								INNER JOIN actions a ON a.lessonid=l.id
	  								INNER JOIN lessons s
	  								ON l.lessonid=s.id
	 								WHERE a.userid=:user_id and a.started=1");*/

	$lesson_id = strip_tags($_GET['lessonid']);

	$stmt = $auth_user->runQuery("SELECT l.id,l.name FROM letters l 
								INNER JOIN lessons s ON l.lessonid=s.id
								INNER JOIN actions a ON a.lessonid=s.id
								WHERE a.userid=:user_id and a.started=1");
	$stmt->execute(array(":user_id"=>$user_id));
	$count=$stmt->rowCount();
	$letters=$stmt->fetchAll();
	
	$stmt2 = $auth_user->runQuery("SELECT word_ru,word_en, img FROM words ORDER BY RAND()");
	$stmt2->execute(array(":user_id"=>$user_id));

	$knownWords=[];

	while ($wordRow=$stmt2->fetch(PDO::FETCH_ASSOC))
		{
			//массив из слова
			$word_arr = str_split($wordRow["word_en"]);
			$word_length=count($word_arr);	

			$isKnownWord = (bool)False;
			// echo $wordRow["word_en"];
			// echo '<br/>';
			//цикл по всем буквам слова
			for ($i=0;$i<$word_length;$i++)
			{
		     	// echo $word_arr[$i]; // текущая буква 
				 //цикл по буквам, которые знает пользователь
				 $isKnownLetter=(bool)False;
				 foreach($letters as $row) {
					  $letter = $row['name'];
					//   echo " ".$letter."!";
					  if ($letter==$word_arr[$i]){
						  $isKnownLetter=True;
					    //   echo '<br/>';
						//   echo $word_arr[$i]."=".$letter." ";
					 }
				if(!$isKnownLetter) $isKnownWord=(bool)False;
				else $isKnownWord=(bool)True;
				}
				 //если буква незнакомая, выходим из цикла
				 if(!$isKnownWord) {break 1;}
				// echo !$isKnownWord ? 'false' : 'true';
				// echo '<br/>';
			}

			if ($isKnownWord)
			{
				$addedArray = array('word_en' => $wordRow["word_en"],'word_ru' => $wordRow["word_ru"],'img' => $wordRow["img"] ,);
				array_push($knownWords,$addedArray);
			}
		}

			//последняя выученная буква
		$stmt3 = $auth_user->runQuery("SELECT l.id,l.name FROM letters l 
										INNER JOIN lessons s ON l.lessonid=s.id
										WHERE  s.id=:lesson_id");
		$stmt3->execute(array(":lesson_id"=>$lesson_id));
		$lessonLetters=$stmt3->fetch(PDO::FETCH_ASSOC);
		//если буква одна (урок не первый)
		if($stmt3->rowCount()==1) {
			$last_letter = strval($lessonLetters["name"]);
			$filterArray = array_values(array_filter($knownWords,
										function ($var) use ($last_letter)  {	
											if(stripos($var["word_en"], $last_letter)!== false)
											 	return $var;
										}
									));

			echo  json_encode($filterArray);
		}
		else {
			echo  json_encode($knownWords);
		}


		
		
		


  	//получаем запрос для слов из известных букв
	// $sql="SELECT word_en, word_ru, img FROM words WHERE ";
	// $counter=0;
	
	// while ($lettersRow=$stmt->fetch(PDO::FETCH_ASSOC)) {
	// 	$sql=$sql." word_en REGEXP '[".$lettersRow["name"]."]'";	
	// 	++$counter;
	// 	if($counter!=$count) $sql=$sql." AND ";
	// }
	// $sql=$sql."ORDER BY RAND() LIMIT 3";

	// //получаем слова 
	// $stmt = $auth_user->runQuery($sql);
	// $stmt->execute();
    
    // while ($lessonRow = $stmt->fetch(PDO::FETCH_ASSOC))
	// 							$rows[] = $lessonRow;
	// 							echo  json_encode($rows);
	
?>
	