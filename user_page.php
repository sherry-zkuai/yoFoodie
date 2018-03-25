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
	<title>User page</title>
	<link rel="stylesheet" type="text/css" href="Foodie_style.css">
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


<div id="hisEvents">
<div style="font-size:20px;text-align:center;margin:10px">Select a date: <input type="date" id="userTime"><button type="button" onclick="require()">Go</button></div>
	<ul id="usersEvent" class="theList"></ul>
</div>


<script>
	require();

	// require all the events hosted by this user
	function require() {
		var xhr = new XMLHttpRequest();
		var time=document.getElementById("userTime").value;
		xhr.onreadystatechange = function() {
    		if (this.readyState == 4 && this.status == 200) {
				var resp = JSON.parse(this.responseText);
				console.log(time);
				console.log(this.responseText);
				if (resp['code'] != 0) {
					alert("Error");
				} 
				else {
					var r = resp['data'];
					reqSucceed(r);
				}
    		}
  		};

		xhr.open("post","slimRequestRedirector.php/userEvents",true);
		xhr.setRequestHeader("Content-type","application/json");
		xhr.send(JSON.stringify(
				{ userId: <?php echo "'".$result['userId']."'"?>,time:time}));
	}

	function reqSucceed(response) {
		document.getElementById("usersEvent").innerHTML="";
		var l = document.getElementById("usersEvent");
		var i, j;

		for (i = 0; i<response.length; i++ ) {
			var line = document.createElement('li');
			var h = document.createElement("H2");
			h.appendChild(document.createTextNode("Restaurant: "+ response[i]['res_name']));
			line.appendChild(h);

			var time = document.createElement("H3");
			time.appendChild(document.createTextNode("Start time: "+response[i]['time']));
			line.appendChild(time);

			var participants = document.createElement("H4");
			var p = "";
			for (j = 1; j <= 5; j++) {
				var thename = "name"+j; 
				var name = response[i][thename];
				if (name) {
					p+=name;
				}
				else {
					break;
				}
			}
			participants.appendChild(document.createTextNode("Participants: "+p));
			line.appendChild(participants);

			if (j == 5) {
				var mes = document.createElement("H5");
				mes.createTextNode("This event is full.");
				line.appendChild(mes);
			}
			else {
				var link = document.createElement('a');
				link.setAttribute("href","javascript: joinEvent(" + response[i]['event_id'] + ")");
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
		
	}
</script>



</body>
</html>