<?php

require './checkLogin.php'; 
session_start();
$result = checkLogin();
if ($result['status'])
{
	$toLogin_text = $result['username'];
	$toReg_text = 'Logout';
	$userId = $result['userId'];
	$toLogin_href = 'https://yofoodie.com/user_page.php?userId='.$userId;
	$toReg_href = 'https://yofoodie.com/slimRequestRedirector.php/logout';
}
else
{
	$toLogin_text = 'Login';
	$toReg_text = 'Register';
	$toLogin_href = 'login_page.php?continue=https://yofoodie.com/Foodie_home.php';
	$toReg_href = 'register_page.php?continue=https://yofoodie.com/Foodie_home.php';
}

?>


<!DOCTYPE html>
<html>
<head>
	<title>YoFoodie Home</title>
	<link rel="stylesheet" type="text/css" href="Foodie_style.css">


</head>

<body>

	<!-- the red part as the head part of the whold page -->
	<div id="headerPart">
		<!-- logo -->
		<div id="logo">
			<a href="/Foodie_home.php"><img src="logo2.png" alt="Logo here" style="width: 300px; height: 300px;" ></a>
			
		</div>

		<!-- login and register button -->
		<div id="login">
			<div id="toLogin">
				<a href="<?php echo $toLogin_href?>" class="loginPart"><?php echo $toLogin_text?></a>
			</div>
			<div id="toLogout" style="display: none;">
				<a href="Foodie_home.php" onclick="return logout();"></a>
			</div>
			<div id="toRegister">
				<a href="<?php echo $toReg_href?>" class="loginPart" ><?php echo $toReg_text?></a>
			</div>
		</div>


		<!-- js to check 
		if logged in, instead of displaying login button, display logout button -->
		<script>
		var url = window.location.href;

		function checkLoggedin() {
			var arr = url.split("?");

			if (arr.length > 1) {
				//already logged in
				var log = document.getElementById("toLogin");
				log.style.display = none;
				var out = document.getElementById("toLogout");
				out.style.display = inline;
			}
		}

		function logout() {
			var out = document.getElementById("toLogout");
			out.style.display = none;
			var log = document.getElementById("toLogin");
			log.style.display = inline;
		}
	</script>	

	<!-- the search bar -->
	<div id="search">
		<form id="bar" action="search_result.php">
			<select name="category" id="dropdown">
				<option class="types" value="" selected>All Types</option>
				<option class="types" value="Chinese">Chinese</option>
				<option class="types" value="Japanese">Japanese</option>
				<option class="types" value="Italian">Italian</option>
				<option class="types" value="Mexican">Mexican</option>
				<option class="types" value="Indian">Indian</option>
			</select>
			<input type="text" name="keyword" placeholder="What would you like today..." style="font-style: italic;">
			<input type="hidden" name="start" value="1">
			<input type="submit" value="Search" id="searchicon">
		</form>
		
	</div>

	</div>
	<?php
	require_once 'db.php';
	
		function getTopEvents(){
			$db=getAccess();
			$sql="select * from `event` order by `time` asc limit 0,5";
			$stmt=$db->query($sql);
			$result=array();
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
				$obj=array(
					'time'=>$row['time'],
					'res_id'=>$row['restaurant_id'],
					'event_id'=>$row['event_id'],
					'userid1'=>$row['user1'],
					'userid1'=>$row['user1'],
					'userid2'=>$row['user2'],
					'userid3'=>$row['user3'],
					'userid4'=>$row['user4'],
					'userid5'=>$row['user5']
					);
				$res=$obj['res_id'];
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
				$result[]=$obj;
			}
			echo '<div id="comingEvents">
			<div id="aBackground"></div>';

			for ($i = 0; $i < count($result); $i++) {
				echo '<div class="eventSlides">
				<img src="rest_test_'.($i+1).'.jpg" class="eventBackground">
				<div class="eventContent">
					<a href="restaurant_page.php?restId='.$result[$i]['res_id'].'"><h2>Recent event at '.$result[$i]['res_name'].'</h2></a>
					<p>Start time: '.$result[$i]['time'].'</p>
					<p>Participants: ';

				for ($j=1; $j < 6; $j++) {
					$name = "name".$j;
					if($result[$i][$name]!=null){
						$p=$result[$i][$name]." ";
						echo $p;
					}
				}
					
				echo '</p>
				</div>	
			</div>';
			}

			echo '<button class="theButton" id="left" onclick="toLeft()">&laquo;</button>
			<button class="theButton" id="right" onclick="toRight()">&raquo;</button>
	
		</div>';
		}

		getTopEvents();

?>

	<!-- js to slideshow the recommended events -->
	<script>
		var eventIndex = 2;
		showEvent(eventIndex);

		function showEvent(n) {
			var i, j;
			var a = document.getElementsByClassName("eventSlides");
			if ( n > a.length) {eventIndex = 1;}
			if ( n < 1) {eventIndex = 5;}
			for (i=0; i < a.length; i++) {
				a[i].className = a[i].className.replace("displayEvent","");
				a[i].className = a[i].className.replace("leftEvent"," ");
				a[i].className = a[i].className.replace("rightEvent"," ");
			}
			for (j=0; j < a.length; j++) {
				a[j].style.display = "none";
			}

			a[(eventIndex-1+5)%5].style.display = "block";
			a[(eventIndex-1+5)%5].style.margin = "auto";

			a[(eventIndex-2+5)%5].style.display = "block";
			a[(eventIndex-2+5)%5].className += " leftEvent";

			a[eventIndex].style.display = "block";
			a[eventIndex].className += " rightEvent";
			
		}

		function toLeft() {
			
				eventIndex -= 1;
			showEvent(eventIndex);
		}

		function toRight() {
				eventIndex += 1;
			
			showEvent(eventIndex);
		}

	</script>


	<img src="rec6.png" style="width: 700px; height: 170px; margin: 0;">
	<!-- recommended restaurants -->
	<div id="recPart">

	<div class="recommendation">
		<div class="show">

			<div class="theSlides"><a href="restaurant_page.php?restId=25333645602455554">
				<img src="rest_test_1.jpg" style="width: 50%; height: 330px;" >
				Le Robin Square
			</a></div>
			<div class="theSlides"><a href="restaurant_page.php?restId=25342382421573632">
				<img src="rest_test_2.jpg" style="width: 50%; height: 330px;">
				Le Majestique
			</a></div>
			<div class="theSlides"><a href="restaurant_page.php?restId=25342382421573633">
				<img src="rest_test_3.jpg" style="width: 50%; height: 330px; ">
				Au Pied de Cochon
			</a></div>
			<div class="theSlides"><a href="restaurant_page.php?restId=25342382421573634">
				<img src="rest_test_4.jpg" style="width: 50%; height: 330px;">
				Bouillon Bilk
			</a></div>
			<div class="theSlides"><a href="restantant_page.php?restId=25342382421573635">
				<img src="rest_test_5.jpg" style="width: 50%; height: 330px; ">
				Les Deux Gamins
			</a></div>
		</div>

		<div class="list">
			<div class="container">
				<img class="thePics opacity hoverOff" src="rest_test_1.jpg" alt="not supported1" style="width: 60%; height: 80px;" onclick="currentPic(1)">
			</div>

			<div class="container">
				<img class="thePics opacity hoverOff" src="rest_test_2.jpg" alt="not supported2" style="width: 60%; height: 80px;" onclick="currentPic(2)">
			</div>

			<div class="container">
				<img class="thePics opacity hoverOff" src="rest_test_3.jpg" alt="not supported3" style="width: 60%; height: 80px;" onclick="currentPic(3)">
			</div>

			<div class="container">
				<img class="thePics opacity hoverOff" src="rest_test_4.jpg" alt="not supported4" style="width: 60%; height: 80px;" onclick="currentPic(4)">
			</div>

			<div class="container">
				<img class="thePics opacity hoverOff" src="rest_test_5.jpg" alt="not supported5" style="width: 60%; height: 80px;" onclick="currentPic(5)">
			</div>
		</div>

	</div>

		<!-- js to slideshow recommended restaurants -->
		<script>
			var slideIndex = 0;
			showPic(slideIndex);
			autoShow();

			function autoShow() {
				var j;
				var x = document.getElementsByClassName("theSlides");

				for (i=0; i<x.length; i++) {
					x[i].style.display = "none";
				}

				slideIndex ++;
				if (slideIndex > x.length) { slideIndex = 1; }
				x[slideIndex-1].style.display = "block";
				setTimeout(autoShow,6000);
			}

			function currentPic(n) {
				showPic(slideIndex = n);
			}

			function showPic(n) {
				var i;
				var a = document.getElementsByClassName("theSlides");
				var b = document.getElementsByClassName("thePics");

				if (n > a.length) { slideIndex = 1; }
				if (n < 1) { slideIndex = a.length; }

				for (i = 0; i<a.length; i++) {
					a[i].style.display = "none";
				}
				for (i = 0; i<b.length; i++) {
					b[i].className = b[i].className.replace("opacityOff","");
				}

				a[slideIndex-1].style.display = "block";
				b[slideIndex-1].className += "opacityOff";
			}
		</script>

	</div>



</body>


</html>