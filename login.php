<?php
//login
require("Scripts/php/controller.php");
if(isset($_POST["log"]))
{
	$username = $_POST["usr"];
	$password = $_POST["pass"];
	$contro = new controller();
	$state = $contro->Login($username, $password);
	echo($state);
	if($state == 0)
	{
		session_start();
		$_SESSION["login"] = $username;
		$_SESSION["monat"] = date("m", time());
		$_SESSION["jahr"] = date("Y", time());
		$_SESSION["selusr"] = "";
		header('location: index.php');
	}
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Login</title>
		<link rel="stylesheet" href="Styles/layout.css">
	</head>
	<body>
		<div id="back">
		</div>
		<div id="loginfield">
			<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
				<input type="text" name="usr" id="usrfield" placeholder="Benutzername">
				<input type="password" name="pass" id="passfield" placeholder="Passwort">
				<input type="submit" name="log" id="loginbtn" value="Login">
			</form>
		</div>
	</body>
</html>