<?php
function getAccess()
{
	$dbConnection = new PDO("mysql:host=localhost;dbname=comp307_project;charset=utf8;","root", "admin307");
	$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbConnection;
}

function register_db($username, $password)
{
	try
	{
		// 连接到数据库
		$con = getAccess();
		// check if the user already exists
		$stmt = $con -> query("SELECT * FROM user WHERE user_name = '$username'");
		if ($stmt -> rowCount() == 0)
		{
			$pswd_hash = password_hash($password, PASSWORD_DEFAULT);
			$con -> query("INSERT INTO user VALUES (UUID_SHORT(),'$username', '$pswd_hash')");
			$stmt=$con->query("select `user_id` from `user` where `user_name`='$username'");
			$id=($stmt->fetch(PDO::FETCH_NUM))[0];
			return array('code' => 1, 'id' =>$id, 'err' => "",'usr'=>$username);
		}
		else
		{
			return array('code' => 0, 'id' => "", 'err' => "Username already exists");
		}
		
	}
	catch (PDOException $e)
	{
		return array('code' => -1, 'id' => "", 'err' => $e->getMessage());
	}
	
}

function verifyUser($username, $password)
{
	try
	{	
		$con = getAccess();
		// check if the user exists
		$stmt = $con -> query("SELECT * FROM `user` WHERE `user_name` = '$username'");
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		
		if ($stmt -> rowCount() == 0)
		{	
			return array('code' => 0, 'id' => "", 'err' => "Username or password incorrect");
		}
		else if ($stmt -> rowCount() == 1)
		{	
			$hash=$row['password'];
			if(password_verify($password,$hash)){
				$id=$row['user_id'];
				return array('code' => 1, 'id' =>$id,'err' => "");
			}else{
				return array('code' => 0, 'id' => "", 'err' => "Username or password incorrect");
			}
		}
		else
		{
			return array('code' => -1, 'id' => "", 'err' => "Database error");
		}
	}
	catch (PDOException $e)
	{
		return array('code' => -1, 'id' => "", 'err' => $e->getMessage());
	}
}

function createEvent($restId, $startTime, $userId)
{
	try
	{
		$con = getAccess();
		$stmt = $con -> query("INSERT INTO event (event_id, restaurant_id, time, user1) VALUES (UUID_SHORT(),'$restId', '$startTime', '$userId')");
		return json_encode(array('code' => 1, 'err' => ""));
	}
	catch (PDOException $e)
	{
		return json_encode(array('code' => -1, 'err' => $e->getMessage()));
	}
}

function addUserToEvent($userId, $eventId) 
{
	try
	{
		$con = getAccess();
		$stmt = $con -> query("SELECT * FROM `event` where `event_id` = '$eventId'");
		$event = $stmt -> fetch(PDO::FETCH_ASSOC);
		if($event['user1']===Null){
			$con->query("UPDATE `event` SET `user1`='$userId' where `event_id`='$eventId' ");
			return json_encode(array('code'=>1,'err'=>""));
		}else if($event['user2']===null){
			if(strcmp($event['user1'],$userId)==0){
				return json_encode(array('code'=>-1,'err'=>"User already in event"));
			}else{
				$con->query("UPDATE `event` SET `user2`='$userId' where `event_id`='$eventId' ");
				return json_encode(array('code'=>1,'err'=>""));
			}
		}else if($event['user3']===null){
			if(strcmp($event['user1'],$userId)==0){
				return json_encode(array('code'=>-1,'err'=>"User already in event"));
			}else if(strcmp($event['user2'],$userId)==0){
				return json_encode(array('code'=>-1,'err'=>"User already in event"));
			}else{
				$con->query("UPDATE `event` SET `user3`='$userId' where `event_id`='$eventId' ");
				return json_encode(array('code'=>1,'err'=>""));
			}
		}else if($event['user4']===null){
			if(strcmp($event['user1'],$userId)==0){
				return json_encode(array('code'=>-1,'err'=>"User already in event"));
			}else if(strcmp($event['user2'],$userId)==0){
				return json_encode(array('code'=>-1,'err'=>"User already in event"));
			}else if(strcmp($event['user3'],$userId)==0){
				return json_encode(array('code'=>-1,'err'=>"User already in event"));
			}else{
				$con->query("UPDATE `event` SET `user4`='$userId' where `event_id`='$eventId' ");
				return json_encode(array('code'=>1,'err'=>""));
			}
		}else if($event['user5']===null){
			if(strcmp($event['user1'],$userId)==0){
				return json_encode(array('code'=>-1,'err'=>"User already in event"));
			}else if(strcmp($event['user2'],$userId)==0){
				return json_encode(array('code'=>-1,'err'=>"User already in event"));
			}else if(strcmp($event['user3'],$userId)==0){
				return json_encode(array('code'=>-1,'err'=>"User already in event"));
			}else if(strcmp($event['user4'],$userId)==0){
				return json_encode(array('code'=>-1,'err'=>"User already in event"));
			}else{
				$con->query("UPDATE `event` SET `user5`='$userId' where `event_id`='$eventId' ");
				return json_encode(array('code'=>1,'err'=>""));
			}
		}else{
			return json_encode(array('code'=>-1,'err'=>"Event full")); //error
		}
	}
	catch (Exception $e)
	{
		//error
		return json_encode(array('code' => -1, 'err' => $e->getMessage()));
	}
}


function addReview($userId, $restId, $comment, $score, $time)
{
	try
	{
		$con = getAccess();
		$stmt = $con -> query("INSERT INTO `review` (`id`, `restaurant_id`, `user_id`, `time`, `content`, `score`) VALUES (UUID_SHORT(), '$restId', '$userId',date('$time'), '$comment', '$score')");
		//$stmt1=$con->query("select `score` from `event` where `restaurant_id` = '$restId'");
		// $num=rowCount($stmt1);
		// $sum=0;
		// while($row=$stmt1->fetch(PDO::FETCH_ASSOC)){
		// 	$sum=$sum+$row['score'];
		// }
		// if($num!=0){
		// 	$s=$sum/$num;
		// }else{
		// 	$s=0;
		// }
		
		// $sql="update `restaurant` set `score`=$s where `id`='$restId'";
		// $con->query($sql);
		// 返回操作状态代号和错误信息
		return json_encode(array('code' => 1, 'err' => ""));
	}
	catch (PDOException $e)
	{
		// 返回错误信息 $e->getMessage()
		return json_encode(array('code' => 0, 'err' => $e->getMessage()));
	}
}

function getEventsByRes($res_id,$time){
	try{
		$db=getAccess();
		$sql="select * from `event`";
		if($res_id!=null){
			$sql=$sql." where `restaurant_id` = '$res_id'";
			if($time!=null){
				$sql=$sql." and date(`time`)=date('$time')";
			}
		}else if($time!=null){
			$sql=$sql." where date(`date`)=date('$time')";
		}
		$sql=$sql." and `time`+interval 1 day >= now()";
		$stmt=$db->query($sql);
		$result=array();
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$obj=array(
			'event_id'=>$row['event_id'],
			'restaurant_id'=>$row['restaurant_id'],
			'time'=>$row['time'],
			'userid1'=>$row['user1'],
			'userid2'=>$row['user2'],
			'userid3'=>$row['user3'],
			'userid4'=>$row['user4'],
			'userid5'=>$row['user5']
			);
			$res=$obj['restaurant_id'];
			$name1=(($db->query("select `user_name` from `user` where `user_id`='".$obj['userid1']."'"))->fetch(PDO::FETCH_NUM))[0];
			$obj['name1']=$name1;
			$name2=(($db->query("select `user_name` from `user` where `user_id`='".$obj['userid2']."'"))->fetch(PDO::FETCH_NUM))[0];
			$obj['name2']=$name2;
			$name3=(($db->query("select `user_name` from `user` where `user_id`='".$obj['userid3']."'"))->fetch(PDO::FETCH_NUM))[0];
			$obj['name3']=$name3;
			$name4=(($db->query("select `user_name` from `user` where `user_id`='".$obj['userid4']."'"))->fetch(PDO::FETCH_NUM))[0];
			$obj['name4']=$name4;
			$name5=(($db->query("select `user_name` from `user` where `user_id`='".$obj['userid5']."'"))->fetch(PDO::FETCH_NUM))[0];
			$obj['name5']=$name5;
			$sql2="select `name` from `restaurant` where `id`='$res'";
			$stmt2=$db->query($sql2);
			$res_name=($stmt2->fetch(PDO::FETCH_NUM))[0];
			$obj['res_name']=$res_name;
			$obj['time1']=$time;
			$result[]=$obj;
		}
		return json_encode(array('code'=>0,'data'=>$result));
	}catch(Exception $e){
		return json_encode(array('code'=>1001,'err'=>$e->getMessage()));
	}
}

function getEventsByUsr($usrid,$time){
	try{
		$db=getAccess();
		$sql="select * from `event` where (`user1` = '$usrid' or `user2` = '$usrid' or `user3` = '$usrid' or `user4` = '$usrid' or `user5` = '$usrid')";
		if($time!=null){
			$sql=$sql." and date(`time`)=date('$time')";
		}
		$stmt=$db->query($sql);
		$result=array();
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$obj=array(
			'event_id'=>$row['event_id'],
			'restaurant_id'=>$row['restaurant_id'],
			'time'=>$row['time'],
			'userid1'=>$row['user1'],
			'userid2'=>$row['user2'],
			'userid3'=>$row['user3'],
			'userid4'=>$row['user4'],
			'userid5'=>$row['user5']
			);
			$res=$obj['restaurant_id'];
			$name1=(($db->query("select `user_name` from `user` where `user_id`='".$obj['userid1']."'"))->fetch(PDO::FETCH_NUM))[0];
			$obj['name1']=$name1;
			$name2=(($db->query("select `user_name` from `user` where `user_id`='".$obj['userid2']."'"))->fetch(PDO::FETCH_NUM))[0];
			$obj['name2']=$name2;
			$name3=(($db->query("select `user_name` from `user` where `user_id`='".$obj['userid3']."'"))->fetch(PDO::FETCH_NUM))[0];
			$obj['name3']=$name3;
			$name4=(($db->query("select `user_name` from `user` where `user_id`='".$obj['userid4']."'"))->fetch(PDO::FETCH_NUM))[0];
			$obj['name4']=$name4;
			$name5=(($db->query("select `user_name` from `user` where `user_id`='".$obj['userid5']."'"))->fetch(PDO::FETCH_NUM))[0];
			$obj['name5']=$name5;
			$sql2="select `name` from `restaurant` where `id`='$res'";
			$stmt2=$db->query($sql2);
			$res_name=($stmt2->fetch(PDO::FETCH_NUM))[0];
			$obj['res_name']=$res_name;
			$obj['time1']=$time;
			$result[]=$obj;
		}
		return json_encode(array('code'=>0,'data'=>$result));
	}catch (Exception $e){
		return json_encode(array('code'=>1002,'err'=>$e->getMessage()));
	}
}


/*// implemented in Python
function searchRest($type, $restName)
{
	try
	{
		$con = getAccess();
		// 只取前。。个信息
		$stmt = $con -> query("SELECT * FROM restaurant WHERE name LIKE '%$restName%' AND category LIKE '%$username%'");
		$restArr = $stmt -> fetchAll(PDO::FETCH_OBJ);
		// 返回页面
	}
	catch (PDOException $e)
	{
		// 用JSON返回错误信息 $e->getMessage()
	}
}*/
?>