

<div class="container">

<div class="row" style="margin-top:20px">
    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
		<form role="form" id="forgot_form">
                      <h2 class="text-center">MKM Agencies</h2>
                
			<fieldset>
                            <!--<p class="text-center"><a class="" href="<?php echo site_url('login/');?>"><img src="<?php echo assets_url('img/logo.png');?>" height="50"/></a></p>-->
                           
				<h3 class="text-center">Enter your registered Email</h3>
				<hr class="colorgraph">
				<div class="form-group">
                    <input type="email" name="email" id="email" class="form-control input-lg" placeholder="Email Address">
				</div>
				
				
				<hr class="colorgraph">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
              
                        <button type="submit" class="btn  btn-success btn-block"> Request Password Reset Link</button>
                        <div id="return_msg"></div>
					</div>
					
				</div>
			</fieldset>
		</form>
	</div>
</div>

</div>
<script type="text/javascript">
  $("#forgot_form").on('submit', function (e) {
        e.preventDefault();
        request_reset_link();
    });
    // Ajax request for reset password link
function request_reset_link() {
     $("#return_msg").html("<a class='loading-alert'><i class='fa fa-refresh fa-spin fa-2x'></i> </a>");

    $.ajax({
        type: "POST",
        url: "../home/forgot_password",
        data: $("#forgot_form").serializeArray(),
        dataType: "json",
        success: function (response) {
            if (response.msg) {
              //  $("#email,.btn-submit").css("display", "none");
               div_html = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>" + response.msg + "</strong></div>";
                $("#return_msg").html(div_html);

            }
            else {
                $("#return_msg").html(response.errors);

            }




        }

    });

}
    
</script>