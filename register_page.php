<?php
/**
 * 这段php用来检查用户登录状态。如果已经登录，则自动跳转首页。
 */
 
require './checkLogin.php'; // 假设在同一个文件夹

$result = checkLogin();
if ($result['status'])
{
	header("Location: Foodie_home.php");
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
	<link rel="stylesheet" type="text/css" href="Foodie_style.css">
</head>
<body>


<!-- the head part of the page including a logo and a login button -->
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
			<a href="login_page.php" id="setL" class="loginPart" style="text-decoration: none; font-size: 0.7em; color: rgb(235,196,147); border: 1.5px solid rgb(235,196,147); border-radius: 5px; padding: 0.22em 0.6em;">Login</a>
		</p>
	</div>

</div>


<!-- to input the information needed to register -->
<div class="checkInTable">
	<p>Please enter a new username: <br></p>
	<input type="text" id="username_register" pattern="[A-Za-z0-9]{1,20}">
	<p>Please set your password: <br></p>
	<input type="password" id="password_register" pattern="[A-Za-z0-9]{1,20}">
	<p>Already have an account? <a href="login_page.php" id="setLAgain">Click to login</a></p><br>
	<button type="button" onclick="checkRegister()">Register</button>
	<p id="aMessage"></p>
</div>


<script>
	var url = window.location.href;


	// get the url to jump back to the previous page if successfully registered
	function parseContinue(aUrl) {
		var original = aUrl.split("?")[1].split("&");
		var name,value,item,i;

		for (i in original) {
			item = original[i];
			name = item.split("=")[0];
			if (name == "continue") {
				value = item.split("=")[1];
				break;
			}
		}

		return value;
	}


	// if want to login instead on this register page
	function setLogin() {
		var r = document.getElementById("setL");
		r.setAttribute("href","register_page.php?continue="+parseContinue(url));
		return false;
	}
	function setLoginAgain() {
		var r = document.getElementById("setLAgain");
		r.setAttribute("href","register_page.php?continue="+parseContinue(url));
		return false;
	}


	// to check the information entered to register is valid
	function checkRegister() {
		var xhr = new XMLHttpRequest();
		var username = document.getElementById("username_register").value;
		var password = document.getElementById("password_register").value;

	  	xhr.onreadystatechange = function() {
    		if (this.readyState == 4 && this.status == 200) {
				console.log(username);
				console.log(password);
				console.log(this.responseText);
				var resp = JSON.parse(this.responseText);
				
      			success(resp);
    		}
  		};

		xhr.open("post","slimRequestRedirector.php/register",true);
		xhr.setRequestHeader("Content-type","application/json");
		xhr.send(JSON.stringify(
			{ username_register: username, password_register: password }));
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