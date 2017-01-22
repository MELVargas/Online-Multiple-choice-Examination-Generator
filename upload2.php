<?php
include_once 'dbconfig.php';
include('session.php');
	
//echo '<pre>';
//if(isset($_POST['img'])){ $img = $_POST['img']; } 
$img = $_FILES['img'];
$user_id = $userRow['user_id'];
//echo $user_id;

if(!empty($img))
{
    $img_desc = reArrayFiles($img);
   // print_r($img_desc);
    
    foreach($img_desc as $val)
    {
        $newname = date('YmdHis',time()).mt_rand().$val['name'];
		$filetype = $val['type'];
		$filesize = $val['size'];
		$filename = $val['name'];
		
		
        if(move_uploaded_file($val['tmp_name'],'./uploads/'.$newname)){
			$sql="INSERT INTO tbl_uploads(file,type,name,size,user_id) VALUES('$newname','$filetype','$filename','$filesize', '$user_id')";
			mysql_query($sql);
			
			//echo "success upload";
		}else{	
		?>
		<script>
			alert('error while uploading file');
			window.location.href='index.php?fail';
        </script>
		<?php	
		}
    }
	
	?>
		<script>
			alert('successfully uploaded');
			window.location.href='view.php';
        </script>
	<?php
}else{
	?><script>
			alert('error while uploading file');
			window.location.href='index.php?fail';
        </script>
	<?php
	
}

function reArrayFiles($file)
{
    $file_ary = array();
    $file_count = count($file['name']);
    $file_key = array_keys($file);
    
    for($i=0;$i<$file_count;$i++)
    {
        foreach($file_key as $val)
        {
            $file_ary[$i][$val] = $file[$val][$i];
        }
    }
    return $file_ary;
}