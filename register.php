<?php
session_start();
if(isset($_SESSION['user'])!="")
{
	header("Location: index.php");
}
include_once 'dbconnect.php';

if(isset($_POST['btn-signup']))
{
	
	$count = 0;
	$uname = mysql_real_escape_string($_POST['uname']);
	$email = mysql_real_escape_string($_POST['email']);
	$upass = md5(mysql_real_escape_string($_POST['upass']));
	$fname = mysql_real_escape_string($_POST['fname']);
	$mname = mysql_real_escape_string($_POST['mname']);
	$lname = mysql_real_escape_string($_POST['lname']);
	$school = mysql_real_escape_string($_POST['school']);
	$type = mysql_real_escape_string($_POST['type']);
	
	
	$uname = trim($uname);
	$email = trim($email);
	$upass = trim($upass);
	$fname = trim($fname);
	$mname = trim($mname);
	$lname = trim($lname);
	$school = trim($school);
	$type = trim($type);
	
	// email exist or not
	$query = "SELECT user_email FROM users WHERE user_email='$email'";
	$result = mysql_query($query);
	$count = $count + mysql_num_rows($result); // if email not found then register
	
	// email exist or not
	$query = "SELECT user_name FROM users WHERE user_name='$uname'";
	$result = mysql_query($query);
	$count = $count + mysql_num_rows($result); // if email not found then register
	
	
	if($count == 0){
		
		if(mysql_query("INSERT INTO users(user_name,user_email,user_pass, f_name, m_name, l_name, school, type) VALUES('$uname','$email','$upass', '$fname', '$mname', '$lname', '$school', '$type')")or die(mysql_error()))
		{
			?>
			<script>alert('Successfully Registered ');
					window.location.href='login.php';
			</script>
			<?php
			//sleep(10);
			//header("Location: index.php"); /* Redirect browser */
		}
		else
		{
			?>
			<script>alert('error while registering you...');</script>
			<?php
		}		
	}
	else{
			?>
			<script>alert('Sorry Email ID already taken ...');</script>
			<?php
	}
	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Coding Cage - Login & Registration System</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css">
<script src="js/jquery-3.1.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/script.js"></script>
</head>
<body>

<div class="modal show login-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog login-modal-dialog" role="document">
    <div class="modal-content modal-content-register">
      <div class="modal-header">
        <h4 class="modal-title">Register</h4>
      </div>
      <div class="modal-body">
		<center>
			<div id="login-form">
			<form method="post">
			<table align="center" width="100%" border="0">
			<tr>
			<td><input class="form-control" type="text" name="uname" placeholder="Username" required /></td>
			</tr>
			<tr>
			<td><input class="form-control" type="email" name="email" placeholder="Your Email" required /></td>
			</tr>
			<tr>
			<td><input class="form-control" type="password" name="upass" placeholder="Your Password" required /></td>
			</tr>
			<tr>
			<td><br /></td>
			</tr>
			<tr>
			<td><input class="form-control" type="text" name="fname" placeholder="First Name" required /></td>
			</tr>
			<tr>
			<td><input class="form-control" type="text" name="mname" placeholder="Middle Name" required /></td>
			</tr>
			<tr>
			<td><input class="form-control" type="text" name="lname" placeholder="Last Name" required /></td>
			</tr>
			<tr>
			<tr>
			<td><br /></td>
			</tr>
			<td>
			<label class="radio-inline">
			  <input type="radio" name="type" id="inlineRadio1" value="0" checked> Student
			</label>
			<label class="radio-inline">
			  <input type="radio" name="type" id="inlineRadio2" value="1"> Instructor
			 </label>
			</td>
			</tr>
			<tr>
			<td><input class="form-control" type="text" name="school" placeholder="School/University" required /></td>
			</tr>
			<tr>
			<td><br /></td>
			</tr>
			<tr>
			<td><button type="submit" class="btn btn-default"  name="btn-signup">Sign Me Up</button></td>
			</tr>
			
			</table>
			</form>
			</div>
			</center>
      </div>
      <div class="modal-footer">
        <p class="text-info">Already registered? <br /><a href="index.php">Sign In Here</a></p>
		
		
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</body>
</html>