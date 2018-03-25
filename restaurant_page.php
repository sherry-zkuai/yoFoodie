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
	
	$fn_create = "var xhr = new XMLHttpRequest();
		var time = document.getElementById(\"startT\").value;
		var id = parseRestId(url);
		var user = '".$result['username']."'

		xhr.onreadystatechange = function() {
    		if (this.readyState == 4 && this.status == 200) {
				var resp = JSON.parse(this.responseText);
				console.log(this.responseText);
      			success(resp);
    		}
  		};

		xhr.open(\"post\",\"slimRequestRedirector.php/newEvent\",true);
		xhr.setRequestHeader(\"Content-type\",\"application/json\");
		xhr.send(JSON.stringify(
			{ restId: id, startTime: time, userId: '".$result['userId']."'}));";
	$fn_comment = "var xhr = new XMLHttpRequest();
		var content = document.getElementById(\"newReview\").value;
		var id = parseRestId(url);
		var user = '".$result['username']."';
		var s;

		// if (document.getElementById('s1').checked = true) { s = 1;}
		// if (document.getElementById('s2').checked = true) { s = 2;}
		// if (document.getElementById('s3').checked = true) { s = 3;}
		// if (document.getElementById('s4').checked = true) { s = 4;}
		// if (document.getElementById('s5').checked = true) { s = 5;}

		xhr.onreadystatechange = function() {
    		if (this.readyState == 4 && this.status == 200) {
				console.log(this.responseText);
    			var resp = JSON.parse(this.responseText);
      			sucComment(resp);
    		}
  		};

		xhr.open(\"post\",\"slimRequestRedirector.php/newReview\",true);
		xhr.setRequestHeader(\"Content-type\",\"application/json\");
		xhr.send(JSON.stringify(
			{ restId: id, review: content, userId: '".$result['userId']."',time:(new Date()),score:0}));";

		$fn_join = "var xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function() {
				
				if (this.readyState == 4 && this.status == 200) {
					console.log(this.responseText);
					var resp = JSON.parse(this.responseText);
					  sucJoin(resp);
				}
			  };
	
			xhr.open(\"post\",\"slimRequestRedirector.php/joinEvent\",true);
			xhr.setRequestHeader(\"Content-type\",\"application/json\");
			xhr.send(JSON.stringify(
				{ eventId: eventid, userId: '".$result['userId']."'}));";
}
else
{
	$toLogin_text = 'Login';
	$toReg_text = 'Register';
	$url=$_SERVER['REQUEST_URI'];
	$toLogin_href = '/login_page.php?continue=https://yofoodie.com'.$url;
	$toReg_href = '/register_page.php?continue=https://yofoodie.com'.$url;
	
	$fn_create = "window.location.href = \"https://yofoodie.com/login_page.php?continue=\" + \"https://yofoodie.com/restaurant_page.php?restId=\"+ parseRestId(url);";
	$fn_comment = $fn_create;
	$fn_join=$fn_create;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Restaurant Details</title>
	<link rel="stylesheet" type="text/css" href="Foodie_style.css">
	<script>
		var url = window.location.href;

		var u = url.split("page")[0];
		function goPage() {
			var inputPage = document.getElementById("page").value;
			window.location.href = u + "page=" + inputPage;
		}

		// display the page according to the label selected
		function disPage(index) {
			var b = document.getElementsByClassName("switch-page");
			var listItem = document.getElementsByClassName("bar-element");
			var i, j;
			for (i = 0; i < b.length; i++) {
				b[i].className = b[i].className.replace("page-selected"," ");
				b[i].style.display = "none";
			}
			for (j = 0; j < listItem.length; j++) {
				listItem[j].className = listItem[j].className.replace("bar-selected"," ");
			}


			listItem[index].className += " bar-selected";
			b[index].style.display = "block";
			b[index].className += " page-selected";
		}


		
	</script>
</head>
<body>

<div id="headpart">
	<a href="/Foodie_home.php"><img src="logo3.png" style="width: 146px; height: 80px;" id="anIcon"></a>
	<div id="topSearchBar">
		<form id="bar2" action="search_result.php">
			<select name="category">
				<option class="types" value="" selected>All Types</option>
				<option class="types" value="chinese">Chinese</option>
				<option class="types" value="japanese">Japanese</option>
				<option class="types" value="italian">Italian</option>
				<option class="types" value="maxican">Mexican</option>
				<option class="types" value="indian">Indian</option>
			</select>
			<input type="text" name="keyword" placeholder="What would you like today..." style="font-style: italic;">
			<input type="hidden" name="start" value="1">
			<input type="submit" value="Search" id="searchicon">
		</form>
		
	</div>

	<div id="register2">
		<p>
			<a href="<?php echo $toLogin_href ?>" class="loginPart" style="text-decoration: none; font-size: 0.7em; color: rgb(235,196,147); border: 1.5px solid rgb(235,196,147); border-radius: 5px; padding: 0.22em 0.6em;"><?php echo $toLogin_text ?></a>&emsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="<?php echo $toReg_href ?>" class="loginPart" style="text-decoration: none; font-size: 0.7em; color: rgb(235,196,147); border: 1.5px solid rgb(235,196,147); border-radius: 5px; padding: 0.22em 0.6em;"><?php echo $toReg_text ?></a>
		</p>
	</div>

</div>


<div id="theBody">

<?php
	require_once "db.php";
	// parse restaurant id from url
	$url = $_SERVER['REQUEST_URI'];
	$arr = explode("restId=", $url);
	$id = $arr[1];
	getRes($id);

    

    function getRes($res_id){
    	try{
        	$db=getAccess();
    	    $sql="select * from `restaurant` where `id` = '$res_id'";
	        $stmt=$db->query($sql);
        	$result=$stmt->fetch(PDO::FETCH_ASSOC);

    	    echo '<div id="basicInformation">';
        	echo '<div id="contact">';
    	    echo '<h2>'. $result['name'] .'</h2>';
	        echo '<p>Tel: '. $result['tel'] .'<br>';

        	$addr2=$result['address2'];
			if($addr2!=null){
				$addr2=', '.$addr2;
			}
			$city = $result['city'];
			$prov = $result['province'];
			$country = $result['country'];
			$pc = $result['postal_code'];
			echo 'Address: '. $result['address1'] . $addr2 . '<br></div>';
			$convertedAddr = convertAddr($result['address1'],$addr2,$city,$prov,$country,$pc);
			echo '<div id="right-map"><img id="map" src="https://maps.googleapis.com/maps/api/staticmap?center='.$convertedAddr.'&size=300x300&zoom=13&markers=color:red%7Clabel:I%7C'.$convertedAddr.'&key=AIzaSyBtCg5Bht-S99WfdJAEGEeoKCu5-LRLUbc"></img></div></div>';

        }catch(Exception $e){
			echo $e->getMessage();
		}
	}
	
	function convertAddr($addr1,$addr2,$city,$prov,$country,$pc)
	{
		$fulladdr = $addr1.','.$addr2.','.$city.','.$prov.','.$country.','.$pc;
		return urlencode($fulladdr);
	}
?>

<!-- <div id="basicInformation">
	<img src="rest_test_4.jpg" style="width: 16%; height: 160px; margin: 0 3em 0 4em;" id="anIcon">
	<div id="contact">
		<h2>Restaurant name here..</h2>
		<p>Address...<br>Phone...<br></p>
	</div>	
</div> -->


<div id="created" class="theList">
</div>

<div id="createHis">
	<p>You are welcomed to create your own event! Please enter the start time for your event: </p>
	<input type="datetime-local" id="startT">
	<button type="button" onclick="create()">Create the event!</button><br>
</div>



<!-- js for creating a new event -->
<script>
	var url = window.location.href;

	function parseRestId(aUrl) {
		var rest = aUrl.split("?")[1].split("&");
		var i, item, name, value;

		for (i in rest) {
			item = rest[i];
			name = item.split("=")[0];
			if (name == "restId") {
				value = item.split("=")[1];
				break;
			}
		}

		return value;
	}


	function create() {
		<?php echo $fn_create ?>
	}


	function success(response) {
		if (response['code'] == 1) {
			document.getElementById("created").innerHTML = "Event successfully created! Refresh page in 5 seconds";
			setTimeout(function() { location.reload(); }, 5000);
		}
		else {
			alert(response['err']);
		}
	}


	// for adding a review
	function comment() {
		<?php echo $fn_comment ?>
	}


	function sucComment(response) {
		if (response['code'] == 1) {
			document.getElementById("created").innerHTML = "Comment successfully added! Refresh page in 5 seconds";
			setTimeout(function() { location.reload(); }, 5000);
		}
		else {
			alert(response['err']);
		}
	}

</script>

<div id="addReview">
	<p>You are welcomed to leave your review!</p>
	<textarea id="newReview" rows="3"></textarea><br>
	<button type="button" onclick="comment()">Comment</button>
</div>





<div id="nav-bar">
	<ul id="aBar">
		<li class="bar-selected bar-element"><a href="javascript: disPage(0)">Events</a></li>
		<li class="bar-element"><a href="javascript: disPage(1)">Reviews</a></li>
	</ul>
</div>

<div class="switch-page page-selected" id="eventList">
	<div  style="float:left;position:relative;">Select a specific date: <input type="date" id="searchDate"><button type="button" onclick="require()">Go</button></div><br>
	<ul id="relatedEvents" class="theList"></ul>
	<!-- aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa -->
</div>

<script>
	//disPage(0);
</script>



<script>
	require();

	function requireAgain() {
		document.getElementById("relatedEvents").innerHTML = "";
		require();
		disPage(0);
	}

	// require all the information about the events holding in this restaurant
	function require() {
		
		var xhr = new XMLHttpRequest();
		var id=parseRestId(url);
		var time=document.getElementById("searchDate").value;

		xhr.onreadystatechange = function() {
    		if (this.readyState == 4 && this.status == 200) {
				console.log(this.responseText);
				console.log(time);
				//alert(this.repsonseText);
				var resp = JSON.parse(this.responseText);
				console.log(this.responseText);
				if (resp['code'] != 0) {
					alert(resp['err']);
      			// reqSucceed(resp);
				} 
				else {
					var r = resp['data'];
					// alert(r[0]['event_id']);
					reqSucceed(r);
				}
			 }
  		};

		xhr.open("post","slimRequestRedirector.php/event",true);
		xhr.setRequestHeader("Content-type","application/json");
		xhr.send(JSON.stringify(
			{ rest: id, time: time}));
	}

	function reqSucceed(response) {
		document.getElementById("relatedEvents").innerHTML = "";
		var l = document.getElementById("relatedEvents");
		var i, j;

		// document.getElementById("test1").innerHTML = "Event id is ";
	

		for (i = 0; i<response.length; i++ ) {
			var line = document.createElement("li");
			var h = document.createElement("H2");
			h.appendChild(document.createTextNode("Event id is "+response[i]['event_id']));
			line.appendChild(h);

			var time = document.createElement("h3");
			time.appendChild(document.createTextNode("Start time: "+response[i]['time']));
			line.appendChild(time);

			var participants = document.createElement("H4");
			var p = "";
			for (j = 1; j <= 5; j++) {
				var thename = "name"+j; 
				var name = response[i][thename];
				if (name) {
					p+=" "+name;
				}
				else {
					break;
				}
			}
			participants.appendChild(document.createTextNode("Participants: "+p));
			line.appendChild(participants);

			if (j == 5) {
				var mes = document.createElement("H5");
				mes.setTextNode("This event is full.");
				line.appendChild(mes);
			}
			else {
				// still able to join this event
				var link = document.createElement('a');
				link.setAttribute("href","javascript: joinEvent('" + response[i]['event_id'] + "')");
				link.setAttribute("style","font-size:20px");
				link.innerHTML="Click to Join Event &emsp; &emsp; &emsp; &emsp;";
				line.appendChild(link);
			}

			var chat = document.createElement('a');
			chat.setAttribute("href","http://yofoodie.com:9000/chat/" + response[i]['event_id']);
			chat.setAttribute("style","font-size:20px");
			chat.innerHTML="Click to Chat";
			line.appendChild(chat);
			

			l.appendChild(line);
		}	
		disPage(0);
	}

	function joinEvent(eventid) {
		<?php echo $fn_join ?>
	}

	function sucJoin(response) {
		if (response['code'] == 1) {
			document.getElementById("created").innerHTML = "Successfully joined! Refresh page in 3 seconds";
			setTimeout(function() { location.reload(); }, 3000);
		}
		else {
			alert(response['err']);
		}
	}

</script>


<?php
//require "db.php";

$url = $_SERVER['REQUEST_URI'];
$arr = explode("restId=", $url);
$id = $arr[1];
getReview($id,1);


function getReview($res_id,$page){
    try{
        $db=getAccess();
        $start=($page-1)*10;
        $sql="select * from `review` where `restaurant_id` = '$res_id' limit $start,10";
        $stmt=$db->query($sql);
        $sql1="select count(*) from `review` where `restaurant_id` = '$res_id'";
        $total=floor((($db->query($sql1))->fetch(PDO::FETCH_NUM))[0]/10)+1;
        $result=array();
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $obj=array(
                'review_id'=>$row['id'],
                'res_id'=>$row['restaurant_id'],
                'user_id'=>$row['user_id'],
                'time'=>$row['time'],
                'content'=>$row['content']
                //'score'=>$row['score']
            );
            $res_id=$obj['res_id'];
            $res_name=(($db->query("select `name` from `restaurant` where `id` = '$res_id'"))->fetch(PDO::FETCH_NUM))[0];
            $obj['res_name']=$res_name;
            $usr_id=$obj['user_id'];
            $user_name=(($db->query("select `user_name` from `user` where `user_id` = '$usr_id'"))->fetch(PDO::FETCH_NUM))[0];
            $obj['user_name']=$user_name;
            $result[]=$obj;
        }

        echo '<div class="switch-page" id="review">
	<ul class="theList" id="reviewList">';

        for( $i = 0; $i<count($result); $i++ ) {
            echo '<li><span style="color: #aa2f3b;">User: ' . $result[$i]['user_name'] . '&emsp;&emsp;' . $result[$i]['time'].  "</span><br>" . $result[$i]['content'] . "<br></li>";
         }

        echo "</ul>";

        
        $url = $_SERVER['REQUEST_URI'];
        $array = explode("start", $url);
		$newU = $array[0];
		if($page>1){
			$page1=$page-1;
		}else{
			$page1=$page;
		}
		
        echo '<div id="pagination" >
				<a href="'. $newU .'page='. $page1 .'" id="prev" style="float: left;">&laquo; Previous</a>';

		echo '<input type="text" id="page" value="'.$page.'" style="font-size: 20px; margin: 0 10px;"><span id="total">/'.$total.'</span>
				<button type="button" onclick="goPage()">Go</button>';

		if($page<$total){
			$page2=$page+1;
		}else{
			$page2=$page;
		}
		echo '<a href="'. $newU .'page='. $page2 .'" id="next" style="float: right;">Next &raquo;</a>
			</div></div>';

    }catch(Exception $e){
        echo $e->getMessage();
    }
}

?>

</body>
</html>