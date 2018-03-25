<?php
require './checkLogin.php'; 
session_start();
$result = checkLogin();
if ($result['status'])
{
	header("Location: Foodie_home.php");
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="Foodie_style.css">
</head>
<body>


<!-- the head bar of the page including a logo and a register button -->
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
			<a href="/slimRequestRedirector.php/register" id="setR" onclick="setRegister();" class="loginPart" style="text-decoration: none; font-size: 0.7em; color: rgb(235,196,147); border: 1.5px solid rgb(235,196,147); border-radius: 5px; padding: 0.22em 0.6em;">Register</a>
		</p>
	</div>

</div>


<!-- get input from user -->
<div class="checkInTable">
	<p>Please enter your username: <br></p>
	<input type="text" id="username_login">
	<p>Please enter your password: <br></p>
	<input type="password" id="password_login">
	<input type="checkbox" name="remember" id="rememberBox" value="true">Remember Me<br>
	<p>Don't have an account? <a href="register_page.php" id="setRAgain" >Click to register</a></p><br>
	<button type="button" onclick="checkLogin()">Login</button><br>
	<p id="aMessage"></p><br>
</div>

<!-- <p>the value of continue is </p><p id="test1"></p><br> -->
<p id="test2"></p><br>
<p id="test3"></p>

<script>
	var url = window.location.href;

	// get the url if want to continue to the previous page after logged in
	function parseContinue(aUrl) {
		var original = aUrl.split("continue=")[1];
		if(original!=null){
			return original;
		}else{
			return url;
		}
		
	}


	// if want to register from this login page
	function setRegister() {
		var r = document.getElementById("setR");
		r.setAttribute("href","register_page.php?continue="+parseContinue(url));
		return false;
	}
	function setRegisterAgain() {
		var r = document.getElementById("setRAgain");
		r.setAttribute("href","register_page.php?continue="+parseContinue(url));
		return false;
	}


	//to check if login information entered is correct
	function checkLogin() {
		var xhr = new XMLHttpRequest();
		var username = document.getElementById("username_login").value;
		var password = document.getElementById("password_login").value;
		var remember = document.getElementById("rememberBox").checked;

	  	xhr.onreadystatechange = function() {
    		if (this.readyState == 4 && this.status == 200) {
				console.log(this.responseText);
				console.log(remember);
				var resp = JSON.parse(this.responseText);
				success(resp);
			}
  		};

		xhr.open("post","slimRequestRedirector.php/login",true);
		xhr.setRequestHeader("Content-type","application/json");
		xhr.send(JSON.stringify(
			{ username_login: username, password_login: password, remember: remember}));
	}

	function success(response){
		//if succeed
		if (response['code'] == 1) {
			window.location.href = parseContinue(url);		
		}
		else {
			document.getElementById("aMessage").innerHTML = response['err'];
		}
	}



</script>


</body>
</html>