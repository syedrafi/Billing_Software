<div class="container">

    <div class="row" style="margin-top:20px">
        <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
            <?php echo form_open(current_url(), array('class' => 'form-horizontal form-signin', 'role' => 'form', 'id' => "login_form")); ?>           		<fieldset>
                <h2 class="text-center">MKM Agencies</h2>
                
                <hr class="colorgraph">
                <div class="form-group">
                    <input type="email" name="email" id="email"  required="required"class="form-control input-lg" placeholder="Email Address">
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" required="required"  class="form-control input-lg" placeholder="Password">
                   
                    <span class="button-checkbox forgot_pwd">

                        <a href="<?php echo site_url('login/forgot-password') ?>" class="btn btn-link pull-right">Forgot Password?</a>
                    </span>
                </div>
    <hr class="colorgraph">
             
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">

                        <button type="submit" class="btn  btn-success btn-block"><i class="fa fa-sign-in"> </i> Sign In</button>
                         <div class="return_msg"></div>
                    </div>
                   
                </div>
            </fieldset>
            <?php echo form_close();
            ?>

        </div>
    </div>

</div>
<script type="text/javascript">
    $(document).ready(function () {
     
        $("#login_form").on("submit", function (e) {
            e.preventDefault();
           
            check_login();
        });

    });
    function check_login() {
         $(".return_msg").fadeIn(10);
          $(".return_msg").html("<a class='loading-alert'><i class='fa fa-refresh fa-spin fa-2x'></i> </a>");
          
        form_data = $("#login_form").serializeArray();
        url = "<?php echo base_url('index.php/login/check_login'); ?>";
        $.ajax({
            type: "POST",
            url: url,
            data: form_data, // serializes the form's elements.
            dataType:"json",
           
            success: function (data)
            {

                return_data = data;
              
                if (return_data.success === 1) {
                 div_html = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>" + return_data.msg + "</strong></div>";
                $(".return_msg").html(div_html);
                  window.location = return_data.redirect_url;
                }
                else {
                     div_html = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>" + return_data.msg + "</strong></div>";

                $(".return_msg").html(div_html);
                }

            }
        });
    }
   
</script>
