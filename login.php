<?php
session_start();
include_once 'dbconnect.php';
if(isset($_SESSION['user'])!="")
{
	header("Location: index.php");
}

if(isset($_POST['btn-login']))
{
	$email = mysql_real_escape_string($_POST['email']);
	$upass = mysql_real_escape_string($_POST['pass']);
	
	$email = trim($email);
	$upass = trim($upass);
	
	$res=mysql_query("SELECT user_id, user_name, user_pass FROM users WHERE user_email='$email' OR user_name='$email'");
	$row=mysql_fetch_array($res);
	
	$count = mysql_num_rows($res); // if uname/pass correct it returns must be 1 row
	
	if($count == 1 && $row['user_pass']==md5($upass))
	{
		$_SESSION['user'] = $row['user_id'];
		header("Location: index.php");
	}
	else
	{
		?>
        <script>alert('Username / Password Seems Wrong !');</script>
        <?php
	}
	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Examination Generation</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css">
<script src="js/jquery-3.1.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/script.js"></script>
</head>
<body>


<div class="modal show login-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog login-modal-dialog" role="document">
    <div class="modal-content modal-content-login">
      <div class="modal-header">
        <h4 class="modal-title">Sign-in</h4>
      </div>
      <div class="modal-body">
		<center>
			<div id="login-form">
			<form method="post">
			<table align="center" width="100%" border="0">
			<tr>
			<td><input type="text" class="form-control" name="email" placeholder="Your Email/Username" required /></td>
			</tr>
			<tr>
			<td><input type="password" class="form-control" name="pass" placeholder="Your Password" required /></td>
			</tr>
			<tr>
			<td><div class="checkbox">
        <label>
          <input type="checkbox"> Remember me
        </label>
      </div></td>
			</tr>
			<tr>
			<td><button type="submit" class="btn btn-default" name="btn-login">Sign In</button></td>
			</tr>
			
			</table>
			</form>
			</div>
		</center>
      </div>
      <div class="modal-footer">
        <p class="text-info">Not yet registered? <br /><a href="register.php">Sign Up Here</a></p>
		
		
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</body>
</html>