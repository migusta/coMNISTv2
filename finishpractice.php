<?php 
    require_once("session.php");
	
	require_once("class.user.php");
	$auth_user = new USER();
	
	$user_id = $_SESSION['user_session'];
	
	//получаем текущего пользователя
	$stmt = $auth_user->runQuery("SELECT * FROM users WHERE user_id=:user_id");
	$stmt->execute(array(":user_id"=>$user_id));
	
	$userRow=$stmt->fetch(PDO::FETCH_ASSOC);

	//получаем параметры
    $actionid = strip_tags($_POST['actionid']);
    $score = strip_tags($_POST['score']);
    
    $stmt=$auth_user->finishPractice($actionid,$score);

    //получаем следующий урок

    $stmt = $auth_user->runQuery("SELECT next_id FROM actions JOIN lessons ON lessons.id=actions.lessonid 
                                    WHERE actions.id=:actionid");
    $stmt->execute(array(":actionid"=>$actionid));
	$lesson=$stmt->fetch(PDO::FETCH_ASSOC);


    //$url='lesson.php?lessonid='.$lesson["next_id"];
    echo $lesson["next_id"];

?>