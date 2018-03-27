<?php

	require_once("session.php");
	
	require_once("class.user.php");
	$auth_user = new USER();
	
	
	$user_id = $_SESSION['user_session'];
	
	$stmt = $auth_user->runQuery("SELECT * FROM users WHERE user_id=:user_id");
	$stmt->execute(array(":user_id"=>$user_id));

	$stmt2 = $auth_user->runQuery("SELECT * FROM lessons ");
	$stmt2->execute();

	$stmt3 = $auth_user->runQuery("SELECT * FROM languages ");
	$stmt3->execute();	
	
	$userRow=$stmt->fetch(PDO::FETCH_ASSOC);

	if(isset($_POST['new-letter']))
	{
		$name = strip_tags($_POST['name']);
		$languageid = strip_tags($_POST['language']);
		$lessonid = strip_tags($_POST['lesson']);
			
		try
		{
			// echo $name." ".$lessonid." ".$languageid;
			
			$stmt =$auth_user->runQuery("INSERT INTO `letters`(`name`, `languageid`, `lessonid`) VALUES (:name,:languageid,:lessonid)");
												  
			$stmt->bindparam(":name", $name);
			$stmt->bindparam(":lessonid", $lessonid);
			$stmt->bindparam(":languageid", $languageid);
			
			$stmt->execute();	
			
			echo '<div class="alert alert-success">Successfully added letter '.$name.'!</div>';
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}			
	}
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
	<link href="css/main.css?v=1.52" rel="stylesheet"></link>
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
		<div class2="page-header">Adding new letters</div>
	
		<div class="lessons-wrap">
		<form method="post">
		 <div class="form-group">
			<input type="text" class="form-control" name="name" placeholder="Letter" required />
        </div>
		<div class="form-group">
			   	<select class="form-control" name="lesson">
				<?php 
					while ($lessonRow = $stmt2->fetch(PDO::FETCH_ASSOC))
					{
						echo "<option value=".$lessonRow["id"].">";
						echo $lessonRow["title"];
						echo "</option>";
						
					}
			  	?>
				</select>
        </div>
		<div class="form-group">
			 <select class="form-control" name="language">
			 <?php 
			 	while ($languageRow = $stmt3->fetch(PDO::FETCH_ASSOC))
				{
					echo "<option value=".$languageRow["id"].">";
					echo $languageRow["name"];
					echo "</option>";
					
				}
			  ?>
			 </select>
        </div>
		
		<div class="form-group">
            <button type="submit" class="btn btn-success" name="new-letter">
                	<i class="glyphicon glyphicon-plus"></i> &nbsp; Add
            </button>
        </div>  
		</form>
		
		</div>

</body>

</html>