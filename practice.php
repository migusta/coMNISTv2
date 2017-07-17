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
				$stmt = $auth_user->runQuery("SELECT l.id, l.name FROM letters l 
												INNER JOIN actions a ON a.lessonid=l.id
												INNER JOIN lessons s
												ON l.lessonid=s.id
												WHERE a.userid=:user_id and a.started=1");
				$stmt->execute(array(":user_id"=>$user_id));
				$count=$stmt->rowCount();
	
	$lessonid = strip_tags($_GET['lessonid']);

	//получаем запись о текущей активности
	$stmt2=$auth_user->runQuery("SELECT * from actions WHERE lessonid=:lesson_id AND userid=:userid");
	$stmt2->execute(array(":lesson_id"=>$lessonid,":userid"=>$user_id));
	$actionsRow=$stmt2->fetch(PDO::FETCH_ASSOC);
	$actionid=$actionsRow["id"];
	
	
				//получаем запрос для слов из известных букв
				// $sql="SELECT word_en, word_ru, img FROM words WHERE ";
				
				// $counter=0;
				
				// while ($lettersRow=$stmt->fetch(PDO::FETCH_ASSOC)) {
				// 	$sql=$sql." word_en REGEXP '".$lettersRow["name"]."'";	
				// 	++$counter;
				// 	if($counter!=$count) $sql=$sql." AND ";
				// }
				// $sql=$sql."ORDER BY RAND() LIMIT 3";
				// //получаем слова 
				// $stmt = $auth_user->runQuery($sql);
				// $stmt->execute();
				


	// if(isset($_POST['btn-finish']))
	// {
	// 	$score = strip_tags($_POST['txt_score']);
	// 	$actionid = strip_tags($actionid);
		
			
	// 	if($auth_user->finishLesson($actionid,$score))		
	// 	{
	// 		$auth_user->redirect('index.php');
	// 	}
	// 	else
	// 	{
	// 		$error = "Wrong Details !";
	// 	}	
	// }
?>  

<!DOCTYPE html">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>learn2write, learning a new alphabet made easy</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Barrio" rel="stylesheet">
	<script type="text/javascript" src="js/rgbcolor.js"></script>
	<script type="text/javascript" src="js/StackBlur.js"></script>
	<script type="text/javascript" src="js/canvg.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/draw.js?v=1.52"></script>
	<link href="css/main.css?v=1.7" rel="stylesheet"></link>
</head>

<body>
	<script type="text/javascript">		
	$(document).ready(	
			function () {
				init();
			}
		)
	</script>
	<div class="wrap">
		<div class="content">
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
				<a href ="https://github.com/migusta/CoMNISTv2"><img src="images/learn2write.png" height="50px" class="logo" /></a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    
                    <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="glyphicon glyphicon-user"></span>&nbsp;Hi, <?php echo $userRow['user_name']; ?>&nbsp;<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="profile.php"><span class="glyphicon glyphicon-user"></span>&nbsp;View Profile</a></li>
                        <li><a href="logout.php?logout=true"><span class="glyphicon glyphicon-log-out"></span>&nbsp;Sign Out</a></li>
                    </ul>
                    </li>
                </ul>
                </div><!--/.nav-collapse -->
            </div>
            </nav>


    <div class="clearfix"></div>
    	
    

			<div class="my-container" id="draw-container">
				<div class="with-small-padding"> <span class="en_info">What you see on the picture?</span></div>
				<div class="success">Great job!</div>
				<div>
					Your score: <span id="user-score">0</span>/<span id="db-length"></span>
				</div>
				<div>Change language:
					<select id="quiz-lang" onchange="changeLanguage(this.value)">
						<option selected value="en">English</option>
						<option  value="ru">Russian</option>
					</select>
				</div>
				<div id="current-word" word="" >
					<img src=""/>
				</div>
				<div id="draw-letter-area">
					<svg></svg>
					<img src="" id="result-image"/>		
				</div>
				<div class="clear"></div>
				<input type="button" value="Erase" id="clearbutton" onclick="clearDrawingArea()" class="main-button"/>
				<input type="button" value="Retry" id="retrybutton" onclick="retryDrawing()" class="main-button"/>
				
				<input type="button" value="Submit" id="submitbutton" onclick="validate_and_save(true)" class="main-button">
				<input type="button" value="Next" id="nextbutton" onclick="showNextImage()" class="main-button">
				<input type="button" value="Finish" id="finishbutton" onclick="finishPractice()" class="main-button">
				
				<input type="button" value="Restart practice" id="restartbutton" onclick="initQuizz()" class="main-button">

				<div class="clear"></div>
				<input type="hidden" class="form-control" name="txt_score" id="hid-score" value="0"/>
				<input type="hidden" class="form-control" id="hid-action" value="<?php echo $actionid  ?>"/>
				
			</div>
			 <!--<form class="form-finish" method="post" id="finish-container">
					
					<button type="submit" name="btn-finish" class="btn btn-default">
						<i class="glyphicon glyphicon-log-in"></i> &nbsp; Finish
					</button>
			</form>-->
			<!--<div id="finish-container" class="my-container">
				The game is finished.Click <a href='/'>here<a> to return to lessons
			</div>-->
			
			<canvas id="canvas" height="240" width="448"/>
		</div>
		<!--<div class="footer">
			<div class="my-container">
				Powered by <a href ="http://github.com/GregVial/CoMNIST"><img src="images/logo-sm.png" height="40px" class="logo-cmnst" /></a>
			</div>
		</div>-->
	</div>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-86523398-3', 'auto');
		ga('send', 'pageview');

	</script>
</body>

</html>