<?php
include('session.php');
require_once('function.php');
// Include Composer autoloader if not already done.
$user_id = $userRow['user_id'];

	

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>File Uploading With PHP and MySql</title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body>
<?php
include('header.php');
?>
<div id="body">
	

	<table id="files" class="table table-striped">
    <tr>
    <th>File Name</th>
    <th>Date Modified</th>
    <th>File Size(KB)</th>
    <th>Code</th>
    <th>Action</th>
	
    </tr>
	<form method="post">
    <?php
	if(isset($_GET['delete'])) {
	$result = mysql_query('DELETE FROM exam WHERE id = '.(int)$_GET['delete']);
	}
	
	$sql="SELECT * FROM exam WHERE user_id=".$user_id." ORDER BY id DESC";
	$result_set=mysql_query($sql);
	while($row=mysql_fetch_array($result_set))
	{
		
		?>
		
		
        <tr>
        <!--td><input type="checkbox" class='form' value="<?php //echo $row['id'] ?>" name="checkbox[]" /></td-->
        <td><?php echo $row['name'].'.pdf' ?></td>
        <td><?php echo $row['date_time'] ?></td>
        <td><?php echo $row['size'] ?></td>
        <td><?php echo $row['code'] ?></td>
		<td class="e-action">
		<a href="pdf/<?php echo $row['file'] ?>" target="_blank"  class='btn btn-primary btn-xs' >View</a>
		<button type="button" name="btn-review" class="btn btn-success btn-xs btn-review" value="<?php echo $row['code'] ?>"  data-toggle="modal" data-target="#reviewModal">Edit</button>
		<a href="?delete=<?php echo $row['id'] ?>" class='btn btn-danger btn-xs' target="" onClick="deleteView(this)">Delete</a>
		<a href="pdf.php?download=<?php echo $row['id'] ?>" target=""  class='btn btn-success btn-xs'><span class="glyphicon glyphicon-save"></span>.PDF</a>
		<a href="doc.php?download=<?php echo $row['id'] ?>" target=""  class='btn btn-info btn-xs'><span class="glyphicon glyphicon-save"></span>.DOC</a>
		<button type="button" name="btn-result" class="btn btn-warning btn-xs btn-result" value="<?php echo $row['id'] ?>"  data-toggle="modal" data-target="#scoreModal">Scores</button>
		
        </tr>
        <?php
	}
	?>
    </table>
    </form>
</div>

<?php
include('select-question.php');
?>

<script>
    $(document).ready(function(){
		
        $(".btn-result").click(function(){
			
            var value = $(this).val();
			//showLoading();
            $.post("score.php", //Required URL of the page on server
               { // Data Sending With Request To Server
                  id:value,
               },
         function(response,status){ // Required Callback Function
             $("#score").html(response);//"response" receives - whatever written in echo of above PHP script.
             //$("#form")[0].reset();
          });
        
		});
		
		 $(".btn-review").click(function(){
			
            var value = $(this).val();
			//showLoading();
             $.post("review.php", //Required URL of the page on server
               { // Data Sending With Request To Server
                  code:value,
               
               },
         function(response,status){ // Required Callback Function
             $("#review-body").html(response);//"response" receives - whatever written in echo of above PHP script.
             //$("#form")[0].reset();
          });
        
		});
		
		
   });
   

</script>
<div class="modal fade" id="scoreModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Scores</h4>
      </div>
	  <form method="post">
      <div class="modal-body">
		<div id="score"></div>

      </div>
	   <div class="modal-footer">
       <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
	  </form>
    </div>
   </div>
</div>

<?php
include('review-modal.php');
include('footer.php'); ?>
</body>
</html>