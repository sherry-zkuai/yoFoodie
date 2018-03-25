<?php
require './security.php';
require './db.php';


function login()
{
	$info = json_decode(file_get_contents("php://input"), true);
	$username = $info['username_login'];
	$password = $info['password_login'];
	$remember = $info['remember'];
	
	$result = verifyUser($username, $password);
	
	if ($result['code'] == 1)
	{
		//keep the session for one day if "remember"
		$lifeTime = 24*3600;
		session_start();
		$_SESSION['loggedin'] = 1;
		$_SESSION['userId'] = $result['id'];
		$_SESSION['usename'] = $username;
		if ($remember)
		{
			setcookie("token", token($username,$result['id']), time() + $lifeTime, "/", NULL, NULL, true);
		}
		else
		{
			setcookie(session_name(), session_id(), null, "/", NULL, NULL, true);
		}
		echo json_encode(array('code' => 1, 'err' => "",'id'=>$result['id']));
	}
	else if ($result['code'] == 0)
	{
		echo json_encode(array('code' => 0, 'err' => $result['err']));
	}
	else 
	{
		echo json_encode(array('code' => -1, 'err' => $result['err']));
	}
}

function register()
{
	$info = json_decode(file_get_contents("php://input"), true);
	$username = $info['username_register'];
	$password = $info['password_register'];
	$result = register_db($username, $password);
	
	// if succeed
	if ($result['code'] == 1)
	{
		session_start();
		$_SESSION['loggedin'] = 1;
		$_SESSION['userId'] = $result['id'];
		$_SESSION['usename'] = $username;
		
		setcookie(session_name(), session_id(), null, "/", NULL, NULL, true);
		echo json_encode(array('code' => 1, 'err' => ""));
	}
	else if ($result['code'] == 0)
	{
		echo json_encode(array('code' => 0, 'err' => $result['err']));
	}
	else 
	{
		echo json_encode(array('code' => -1, 'err' => $result['err']));
	}
}

function logout()
{
	session_start();
	session_destroy();
	setcookie("token",NULL,time()-3600,NULL,NULL,NULL,true);
	echo "<script>window.location = 'https://yofoodie.com/Foodie_home.php'</script>";
}

function newEvent()
{
	$info = json_decode(file_get_contents("php://input"), true);
	$restId = $info['restId'];
	$startTime = $info['startTime'];
	$userId = $info['userId'];
	echo createEvent($restId, $startTime, $userId);
}

function joinEvent()
{
	$info = json_decode(file_get_contents("php://input"), true);
	$userId = $info['userId'];
	$eventId = $info['eventId'];
	echo addUserToEvent($userId, $eventId);
}

function restEvents()
{
	$info = json_decode(file_get_contents("php://input"), true);
	$restId = $info['rest'];
	$time=$info['time'];
	echo getEventsByRes($restId,$time);
}

function userEvents()
{
	$info = json_decode(file_get_contents("php://input"), true);
	$userId = $info['userId'];
	$time=$info['time'];
	echo getEventsByUsr($userId,$time);
}

function newReview()
{
	$info = json_decode(file_get_contents("php://input"), true);
	$userId = $info['userId'];
	$restId = $info['restId'];
	$comment = $info['review'];
	$score = $info['score'];
	$time=$info['time'];
	echo addReview($userId, $restId, $comment, $score,$time);
}

function restReview()
{
	$info = json_decode(file_get_contents("php://input"), true);
	$restId = $info['rest'];
	$startIndex = $info['start'];
	echo searchReview($restId, $startIndex);
}
?>