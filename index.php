<?php

	require_once("session.php");
	
	require_once("class.user.php");
	$auth_user = new USER();
	
	
	$user_id = $_SESSION['user_session'];
	
	$stmt = $auth_user->runQuery("SELECT * FROM users WHERE user_id=:user_id");
	$stmt->execute(array(":user_id"=>$user_id));
	
	$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
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
	<script src="js/bootstrap.min.js"></script>
	<script src="js/draw.js?v=1.94"></script>
	<link href="css/main.css?v=1.51" rel="stylesheet"></link>
</head>

<body>
	<div class="wrap">
		<div class="content">
        <nav class="navbar navbar-default">
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
                <!--<ul class="nav navbar-nav">
                    <li class="active"><a href="http://www.codingcage.com/2015/04/php-login-and-registration-script-with.html">Back to Article</a></li>
                    <li><a href="http://www.codingcage.com/search/label/jQuery">jQuery</a></li>
                    <li><a href="http://www.codingcage.com/search/label/PHP">PHP</a></li>
                </ul>-->
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


    <!--<div class="clearfix"></div>-->
    	
    <div class="container">
		<div class="page-header">Your lessons</div>
		<ul class="nav nav-tabs">
			<li role="presentation" class="active"><a href="javascript:activateTab('all')">All</a></li>
			<li role="presentation"><a href="javascript:activateTab('active')">Active</a></li>
			
			<li role="presentation"><a href="javascript:activateTab('finished')">Finished</a></li>
		</ul>
		<div class="lessons-wrap">
			<?php

				//текущие уроки
				$stmt = $auth_user->runQuery("SELECT * FROM `actions` INNER JOIN lessons ON actions.lessonid=lessons.id WHERE userid=:user_id AND finished=0 ORDER BY lessons.id DESC");
				$stmt->execute(array(":user_id"=>$user_id));
				$active_lesson_num=0;
				
				while ($lessonRow = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					?>
					<div class="active lesson">
					<?php 
					// echo "<div ".$class.">";
					echo $lessonRow['title'];
					?>
					<a class="btn btn-default" href="lesson.php?lessonid=<?php echo $lessonRow['id'];?>" role="button">Go</a>
					
					</div>
				<?php	
					$active_lesson_num=$stmt->rowCount(); 
				}	
				
				//законченные уроки 
				$stmt = $auth_user->runQuery("SELECT * FROM `actions` INNER JOIN lessons ON actions.lessonid=lessons.id WHERE userid=:user_id AND finished=1 ORDER BY lessons.id DESC");
				$stmt->execute(array(":user_id"=>$user_id));
				$count=$stmt->rowCount(); 
				//echo $active_lesson_num;
				while ($lessonRow = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					// if 
					// $class = ($lessonRow['finished']!=1) ? 'class="active lesson"' : 'class="finished lesson"';
				//	echo $active_lesson_num;
					if ($active_lesson_num===0){  
						
					?>
						<a class="btn btn-default" href="lesson.php?lessonid=<?php echo $lessonRow['next_id'];?>" role="button">Next Lesson</a>				
					<?php $active_lesson_num=1; //todo: убрать
					 } ?>
				    <div class="finished lesson">
					<?php 
					// echo "<div ".$class.">";
					echo $lessonRow['title'];
					// if ($lessonRow['finished']!='1') {?>
					<a class="btn btn-default" href="lesson.php?lessonid=<?php echo $lessonRow['id'];?>" role="button">Go</a>
					
					</div>
				<?php	
				}
				
				if (count($lessonRow["id"])===0 and $active_lesson_num===0 ){
						
				echo "Welcome to Learn2Write"?>

				<a class="btn btn-default" href="lesson.php?lessonid=1" role="button">Start</a>	
				<?php } ?>
		
		<div>
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