<?php

require_once('dbconfig.php');

class USER
{	

	private $conn;
	
	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
    }
	
	public function runQuery($sql)
	{
		$stmt = $this->conn->prepare($sql);
		return $stmt;
	}
	
	public function register($uname,$umail,$upass)
	{
		try
		{
			$new_password = password_hash($upass, PASSWORD_DEFAULT);
			
			$stmt = $this->conn->prepare("INSERT INTO users(user_name,user_email,user_pass) 
		                                               VALUES(:uname, :umail, :upass)");
												  
			$stmt->bindparam(":uname", $uname);
			$stmt->bindparam(":umail", $umail);
			$stmt->bindparam(":upass", $new_password);										  
				
			$stmt->execute();	
			
			return $stmt;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}				
	}

	public function finishLesson($actionid,$score)
	{
		try
		{
			
			// $stmt = $this->conn->prepare("UPDATE  `actions` SET `finished`=1, `score`=:score
		                                //    WHERE userid=:id AND lessonid=:lessonid");
										   //UPDATE `actions` SET `finished`=1,`score`=2 WHERE userid=1 AND lessonid=2
			$stmt = $this->conn->prepare("UPDATE  `actions` SET `finished`=1, `score`=:score
		                                   WHERE id=:actionid");
												  
			// $stmt->bindparam(":id", $id);
			// $stmt->bindparam(":lessonid", $lessonid);			
			$stmt->bindparam(":score", $score);
			$stmt->bindparam(":actionid", $actionid);
			
			$stmt->execute();	
			
			return $stmt;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}				
	}
	 
	public function startLesson($id,$lessonid)
	{
		try
		{
			
			$stmt = $this->conn->prepare("INSERT INTO actions(userid,lessonid,finished,score) 
		                                               VALUES(:id, :lessonid, 0, 0)");
												  
			$stmt->bindparam(":id", $id);
			$stmt->bindparam(":lessonid", $lessonid);			
				
			$stmt->execute();
			$id = $this->conn->lastInsertId();
	
			return $id;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}				
	}
	
	
	public function doLogin($uname,$umail,$upass)
	{
		try
		{
			$stmt = $this->conn->prepare("SELECT user_id, user_name, user_email, user_pass FROM users WHERE user_name=:uname OR user_email=:umail ");
			$stmt->execute(array(':uname'=>$uname, ':umail'=>$umail));
			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
			if($stmt->rowCount() == 1)
			{
				if(password_verify($upass, $userRow['user_pass']))
				{
					$_SESSION['user_session'] = $userRow['user_id'];
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
	
	public function is_loggedin()
	{
		if(isset($_SESSION['user_session']))
		{
			return true;
		}
	}
	
	public function redirect($url)
	{
		header("Location: $url");
	}
	
	public function doLogout()
	{
		session_destroy();
		unset($_SESSION['user_session']);
		return true;
	}
}
?>