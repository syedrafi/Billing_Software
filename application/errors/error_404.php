<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LeaguePlanIT  </title>
	<!-- BOOTSTRAP STYLES-->
    <link href="<?php echo css_url('bootstrap.css'); ?>" rel="stylesheet" />
     <link href="//fonts.googleapis.com/css?family=Lato:400,700,900" rel='stylesheet' type='text/css'>
     <!-- FONTAWESOME STYLES-->
    <link href="<?php echo css_url('font-awesome.css'); ?>" rel="stylesheet" />
     <!-- MORRIS CHART STYLES-->
    <link href="<?php echo js_url('morris/morris-0.4.3.min.css'); ?>" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="<?php echo css_url('custom.css'); ?>" rel="stylesheet" />
     <link href="<?php echo css_url('select2.css'); ?>" rel="stylesheet" />
      <link href="<?php echo assets_url('datatables/dataTables.bootstrap.css'); ?>" rel="stylesheet" />
       <script src="<?php echo js_url('jquery-1.10.2.js'); ?>"></script>
        <script src="<?php echo js_url('select2.min.js'); ?>"></script>
     <!-- GOOGLE FONTS-->
  <!-- <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />-->
</head>
<body class="bg">
    <div id="wrapper">
    
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper" >
              
      <nav class="navbar navbar-default  " role="navigation" style="">
		<div class="">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div id="mobile_logo"class="col-md-12 text-center">
                             
                            <div class="col-md-12">
                            <a class="nopadding " href="<?php echo base_url("league/index");?>"><img  class="img-responsive"src="<?php echo assets_url('img/logo.png');?>" width="190"  style=""/></a>
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
                            </div>
                              <div class="col-md-3 left-menu pull-left"></div>
                              
			</div>
                        <div style="clear: both"></div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse league_menu" id="bs-example-navbar-collapse-1">
			
                          
                           <div id="desktop_logo">
                            <a class="nopadding " href="<?php echo base_url("league/index");?>"><img  class="img-responsive "src="<?php echo assets_url('img/logo.png');?>" width="190"  style="margin: 0 auto;"/></a>
                          
                            </div>
                           
				
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
                
	</nav>
<style type="text/css">
    #mobile_logo .img-responsive{
      margin: 0 auto !important;
      display: inline-block;
    }
    .panel-heading{
        font-size: 18px;
    }
</style>
    
  
       
           
            
            <div id="page-inner" style="margin-top: 0px;">
                
              <?php //print $content ?>     
                <div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-3">           
			<div class="panel panel-primary">
                        <div class="panel-heading text-center">
                            Unauthorized
                        </div>
                        <div class="panel-body text-center">
                            <p>
                                The page you requested can't be authorized.
                            </p>
                             <p>
                                Perhaps you are here because:
                            </p>
                            <?php echo $heading; ?>
                        </div>
                    </div>
		     </div>
    </div>
            <div style="clear: both"></div>
             <!-- /. PAGE INNER  -->
             <div class="footer">
                 <p class="text-center copyrights"> © LeaguePlanit 2015 | All Rights Reserved | Version 1.0.0</p>
             </div>
            </div>
         <!-- /. PAGE WRAPPER  -->
        </div>
     <!-- /. WRAPPER  -->
    <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!--  SCRIPTS -->
 
      <!-- BOOTSTRAP SCRIPTS -->
    <script src="<?php echo js_url('bootstrap.min.js') ?>"></script>
   
    
    <!-- METISMENU SCRIPTS -->
    <script src="<?php echo js_url('jquery.metisMenu.js') ?>"></script>
     <!-- MORRIS CHART SCRIPTS -->

          <script src="<?php echo js_url('morris/raphael-2.1.0.min.js') ?>"></script>
    <script src="<?php echo js_url('morris/morris.js') ?>"></script>
      <!-- CUSTOM SCRIPTS -->
    <script src="<?php echo js_url('custom.js') ?>"></script>
    

</body>
</html>
