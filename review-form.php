<form>
<input type="text" id="foo" class="hidden" name="foo" value="<?php echo $_SESSION['code'];?>" />
<button type="button" name="btn-test" id="btn-review" class="btn btn-info hidden" data-toggle="modal" data-target="#reviewModal" data-backdrop="static" data-keyboard="false">Button</button>
</form>
<script>
    $(document).ready(function(){
       $("#btn-review").click(function(){
			
            var foo = $("#foo").val();
        
            $.post("review.php", //Required URL of the page on server
               { // Data Sending With Request To Server
                  code:foo,
               
               },
         function(response,status){ // Required Callback Function
             $("#review-body").html(response);//"response" receives - whatever written in echo of above PHP script.
             //$("#form")[0].reset();
          });
        
     });
   });
   

</script>