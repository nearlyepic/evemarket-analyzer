<!doctype HTML>

<html lang="en">

<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>nearlyepic's eve market analyzer</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/bootstrap-theme.css" rel="stylesheet">
<link href="theme.css" rel="stylesheet">
</head>
<body>
<div class="container">
<br/>
	<div class="jumbotron">
		<h2>Sign in</h2>
		<!--<h3><?php printf("%s", $_SERVER['QUERY_STRING']); ?></h3>--!>
		<form id="login">
			<div class="form-group">
				<label for="email_1">Email</label>
				<input type="email" class="form-control" id="email_1" placeholder="email">
			</div>
			<div class="form-group">
				<label for="password_1">Password</label>
				<input type="password" class="form-control" id="password_1" placeholder="password">
			</div>
			<input type="button" class="btn btn-default" onclick="checkPW();"value="log in">
		</form>
	<br/>
	<div id="loginError" class="alert alert-danger" role="alert"></div>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
<script>
function checkPW()
{
	loginForm = document.forms['login'];
	emailField = loginForm.elements['email_1'];
	passField = loginForm.elements['password_1'];

	email = emailField.value;
	pass = passField.value;

	postString = "email="+ email + "&" + "pass=" + pass;
	
	passCheck=new XMLHttpRequest;

	passCheck.open("POST","login.php",true);
	passCheck.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	passCheck.send(postString);
	passCheck.onreadystatechange=function()
	{
		if (passCheck.readyState==4) {
			if (passCheck.responseText === "Validated") {
				window.location.replace("overview.php");
			} else {
				document.getElementById("loginError").style.visibility = 'visible';
				document.getElementById("loginError").innerHTML=passCheck.responseText;
			}
		}
	}

}
</script>
</body>
</html>
