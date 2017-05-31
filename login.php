<?php
session_start();
if(isset($_POST['email']) && isset($_POST['pass'])) {
	
	$email=htmlspecialchars($_POST['email']);
	$password=htmlspecialchars($_POST['pass']);
	
	require 'config.php';
	require 'functions.php';
	
	$userdb=new PDO('mysql:host=localhost;dbname='.$usrdbname.'', $usrdbusr, $usrdbpass);
	$userdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$userdb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

	$userDBpass = doQuery($userdb, "SELECT password FROM users WHERE username=?", array($email));

	$isCorrect = password_verify($password, $userDBpass[0]);
	
	if($isCorrect) {
		$_SESSION['validated']=true;
		$_SESSION['logintime']=date('U');
		echo "Validated";
	} else {
		echo "Login failed. Incorrect email or password.";	
	}
}
?>
