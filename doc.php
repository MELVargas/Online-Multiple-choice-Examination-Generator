<?php
include('session.php');
	if(isset($_GET['download'])) {
		
		$file = "";
		$sql="SELECT * FROM exam WHERE id=".(int)$_GET['download'] ;
		$result_set=mysql_query($sql);
		while($row=mysql_fetch_array($result_set))
		{
			$file = $row['code'].".doc";
		}
		
		$path = 'doc/'.$file;
		header('Content-Description: File Transfer');
		header('Content-Type: application/force-download');
		header("Content-Disposition: attachment; filename=\"" . basename($file) . "\";");
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		//header("Content-Length: " . filesize($file));
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		ob_clean();
		flush();
		readfile($path);
		// header("Content-type: application/vnd.ms-word");
		// header("Content-Disposition: attachment;Filename=document_name.doc");

		// echo "<html>";
		// echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">";
		// echo "<body>";
		// echo "<b>My first document</b>";
		// echo "</body>";
		// echo "</html>";
	// Quick check to verify that the file exists
		// if( !file_exists($file) ) die("File not found");
		// // Force the download
		// header("Content-Disposition: attachment; filename=" . basename($file) . "");
		// header("Content-Length: " . filesize($file));
		// header("Content-Type: application/octet-stream;");
		// readfile("test-files".$file);
	// $result = mysql_query('DELETE FROM tbl_uploads WHERE id = '.(int)$_GET['delete']);
	}


?>