 <?php
include('session.php');
$user_id = $userRow['user_id'];



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome - <?php echo $userRow['user_email']; ?></title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<script src="js/jquery-3.1.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/script.js"></script>
</head>
<body>
<div id="body">

	<table width="80%" border="1" id="files">

    <tr>
	<td>Select</td>
    <td>Word</td>
    <td>Word Type</td>
    <td>Definition</td>
    </tr>
	<form method="post">
    <?php
	//person tags - n.
	//A person who
	//One who
	//One to whom
	//A man who
	//A woman who
	
	$sql="SELECT * FROM entries WHERE word='Manilla'";
	$result_set=mysql_query($sql);
	while($row=mysql_fetch_array($result_set))
	{
		?>
        <tr>
        <td><input type="checkbox" class='form' value="<?php echo $row['id'] ?>" name="checkbox[]" /></td>
        <td><?php echo $row['word'] ?></td>
        <td><?php echo $row['wordtype'] ?></td>
        <td><?php echo $row['definition'] ?></td>
        </tr>
        <?php
	}
	?>
    </table>
	
	<input type="text" name="text" placeholder="Insert Text" /><br>
	<input type="text" name="numberOfItems" placeholder="Please enter the no. of items." /><br>
	<input type="text" name="examName" placeholder="Enter the name of exam" /><br>
	<button type="submit" name="btn-selectfiles">Select</button>
    </form>
</div>

</body>
</html>