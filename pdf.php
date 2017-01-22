 
 <?php
include('session.php');
	if(isset($_GET['download'])) {
		$file = "";
		$sql="SELECT * FROM exam WHERE id=".(int)$_GET['download'] ;
		$result_set=mysql_query($sql);
		while($row=mysql_fetch_array($result_set))
		{
			$file = $row['file'];
		}
		$path = 'pdf/'.$file;
		header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Type: application/octetstream');
		header("Content-Transfer-Encoding: Binary");
		header("Content-length: ".filesize($path));
		header("Content-disposition: attachment; filename=\"".basename($file)."\"");
		readfile($path);
	}


?>