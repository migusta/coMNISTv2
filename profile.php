<?php

	require_once("session.php");
	
	require_once("class.user.php");
	$auth_user = new USER();
	
	
	$user_id = $_SESSION['user_session'];
	
	$stmt = $auth_user->runQuery("SELECT * FROM users WHERE user_id=:user_id");
	$stmt->execute(array(":user_id"=>$user_id));
	
	$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen"> 
<link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen"> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<link rel="stylesheet" href="main.css" type="text/css"  />
<title>welcome - <?php print($userRow['user_email']); ?></title>
</head>

<body>


<nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        < <nav class="navbar navbar-default navbar-fixed-top">
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

	<div class="clearfix"></div>
	
    <div class="container-fluid" style="margin-top:80px;">
	
    <div class="container">
    
    	<label class="h5">welcome : <?php print($userRow['user_name']); ?></label>
        <hr />
        
        <h1>
        <a href="index.php"><span class="glyphicon glyphicon-home"></span> home</a> &nbsp; 
        <a href="profile.php"><span class="glyphicon glyphicon-user"></span> profile</a></h1>
        <hr />
        
        <p class="h4">Another Secure Profile Page</p> 
        
    
    </div>

</div>




<script src="js/bootstrap.min.js"></script>

</body>
</html>