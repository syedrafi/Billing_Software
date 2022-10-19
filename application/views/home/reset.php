<div class="container top_margin">
    <div class="row">
        <h3 class="text-center">Reset Password</h3>
        
        <form class="form-horizontal" method="post" id="user_form_reset" action="#">
<fieldset>

<!-- Form Name -->


<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="password">Password</label>  
  <div class="col-md-4">
      <input id="password" required="required" name="password" placeholder="Password"  value=""class="form-control input-md" type="password">
 
  </div>
</div>
<div class="form-group">
  <label class="col-md-4 control-label" for="password">Confirm Password</label>  
  <div class="col-md-4">
      <input id="password" required="required" name="password_conf" placeholder="Password"  value=""class="form-control input-md" type="password">
      <input id="forgot_password_key"  name="forgot_password_key"  value="<?php echo $forgot_password_key; ?>"class="form-control input-md" type="hidden">
 
  </div>
</div>
<!-- Button -->
<div class="form-group">
 
  <div class="col-md-12 text-center">
      <button  type="submit"id="user_submit" name="user_submit" class="btn btn-primary btn-lg" ><i class="fa fa-save"></i> Save</button>
      <div id="return_msg"></div>
  </div>
</div>

</fieldset>
</form>

        
    </div>
</div>
<script type="text/javascript">
 // Reset Password
      $("#user_form_reset").on('submit',function(e){
        e.preventDefault();
        save_user_reset();
    });   
    function save_user_reset() {
        $("#return_msg").html("<i class='fa fa-spinner'></i> Loading...");

        $.ajax({
            type: "POST",
            url: "<?php echo base_url('index.php/home/reset_password'); ?>",
            data: $("#user_form_reset").serializeArray(),
            dataType: "json",
            success: function (response) {
                if(response.msg){
                    $('#user_form_reset')[0].reset();
                $("#return_msg").html(response.msg);
               
    }
    else{
          $("#return_msg").html(response.errors);
          
    }




            }

        });

    }s

</script>