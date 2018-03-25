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
	$url=$_SERVER['REQUEST_URI'];
	$toLogin_href = '/login_page.php?continue=https://yofoodie.com'.$url;
	$toReg_href = '/register_page.php?continue=https://yofoodie.com'.$url;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Search results</title>
	<link rel="stylesheet" type="text/css" href="Foodie_style.css">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<script>
		var url = window.location.href;
		var u = url.split("start")[0];
		function goPage(){
			var inputPage=document.getElementById("page").value;
			window.location.href=u+"start="+inputPage;
		}
		function loginPage(){
			window.location.href= '/login_page.php?continue='+url;
		}
		function registerPage(){
			window.location.href= '/register_page.php?continue='+url;
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
				<option class="types" value="Chinese">Chinese</option>
				<option class="types" value="Japanese">Japanese</option>
				<option class="types" value="Italian">Italian</option>
				<option class="types" value="Maxican">Mexican</option>
				<option class="types" value="Indian">Indian</option>
			</select>
			<input type="text" name="keyword" placeholder="What would you like today..." style="font-style: italic;">
			<input type="hidden" name="start" value="1">
			<input type="submit" value="Search" id="searchicon">
		</form>
		
	</div>

	<div id="register2">
		<p>
			<a href="<?php echo $toLogin_href?>" class="loginPart" style="text-decoration: none; font-size: 0.7em; color: rgb(235,196,147); border: 1.5px solid rgb(235,196,147); border-radius: 5px; padding: 0.22em 0.6em;"><?php echo $toLogin_text ?></a>&emsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="<?php echo $toReg_href?>" class="loginPart" style="text-decoration: none; font-size: 0.7em; color: rgb(235,196,147); border: 1.5px solid rgb(235,196,147); border-radius: 5px; padding: 0.22em 0.6em;"><?php echo $toReg_text ?></a>
		</p>
	</div>

</div>

<div id="resultList"></div>

<?php
require "db.php";
$cat = $_GET['category'];
$key = $_GET['keyword'];
$st = $_GET['start'];
$url = $_SERVER['REQUEST_URI'];

$arr = explode("start", $url);
$newU = $arr[0];

search($cat, $key, $st);

function search($category,$keyword,$start){
    try{
        $db=getAccess();
        $sql=" from `restaurant`";
        if($keyword!=null){
            $sql=$sql." where `name` like '%$keyword%'";
            if($category!=null){
            	$sql=$sql." and";
            }
        }else if($category!=null){
			$sql=$sql." where";
		}
        if($category!=null){
            $sql=$sql." find_in_set('$category',`category`)>0";
        }
        $sql1="select count(*)".$sql;
        $stmt=$db->query($sql1);
        $count=$stmt->fetch(PDO::FETCH_ASSOC);
		$total=floor($count['count(*)']/10)+1;
		$num=($start-1)*10;
        $sql="select *".$sql." limit $num,10";
        $stmt=$db->query($sql);
		$result=array();
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$obj=array(
				'id'=>$row['id'],
                'name'=>$row['name'],
                'cat'=>$row['category'],
                'address1'=>$row['address1'],
                'address2'=>$row['address2'],
                'city'=>$row['city'],
				'province'=>$row['province'],
				'country'=>$row['country'],
				'tel'=>$row['tel']
                );
            $result[]=$obj;
        }

        echo '<div id="resultList"><ul class="theList">';

        for( $i = 0; $i<count($result); $i++ ) {
			$addr2=$result[$i]['address2'];
			if($addr2!=null){
				$addr2=', '.$addr2;
			}
            echo '<li><a href="restaurant_page.php?restId=' . $result[$i]['id'] .'">' . $result[$i]['name'] . '</a>
        		<br>Category: ' . $result[$i]['cat'] . '<br>' . $result[$i]['address1'] . $addr2 . '<br>' . $result[$i]['city'] . ", " . $result[$i]['province'] . "<br></li>";
         }

        echo "</ul></div>";

		if($start>1){
			$start1 =$start-1;
		}else{
			$start1=$start;
		}
		
		$url = $_SERVER['REQUEST_URI'];
		$arr = explode("start", $url);
		$newU = $arr[0];

		echo '<div id="pagination">
				<a href="'. $newU .'start='. $start1 .'" id="prev" style="float: left;">&laquo; Previous</a>';

		echo '<input type="text" id="page" value="'.$start.'" style="font-size: 20px; margin: 0 10px;"><span id="total">/'.$total.'</span>
				<button type="button" onclick="goPage()">Go</button>';

		if($start==$total){
			$start2=$start;
		}else{
			$start2=$start+1;
		}
		echo '<a href="'. $newU .'start='. $start2 .'" id="next" style="float: right;">Next &raquo;</a>
			</div>';

	}catch(Exception $e){
		echo $e->getMessage();
	}
}
?>


</body>
</html>