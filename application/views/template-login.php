<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo $title; ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo css_url('bootstrap.min.css')?>" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo css_url('style.min.css'); ?>" rel="stylesheet">
        <link href="<?php echo css_url('login.css'); ?>" rel="stylesheet">
 <script src="<?php echo js_url('jquery.js'); ?>"></script>
    <!-- Custom Fonts -->
    <link href="<?php echo css_url('font-awesome.css');?>" rel="stylesheet" type="text/css">
   <!-- <link href='http://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>-->

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Page Header -->
    <!-- Set your background image for this header on the line below. -->
   
<div id="wrapper">
    <!-- Main Content -->
    <div class="container">
          <?php echo getFlashMessages(); ?>    
        <?php print $content; ?>
    </div>

    <hr>
</div>
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    <ul class="list-inline text-center">
                        <li>
                            <a href="#">
                                <span class="fa-stack fa-lg">
                                    <i class="fa fa-circle fa-stack-2x"></i>
                                    <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <span class="fa-stack fa-lg">
                                    <i class="fa fa-circle fa-stack-2x"></i>
                                    <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <span class="fa-stack fa-lg">
                                    <i class="fa fa-circle fa-stack-2x"></i>
                                    <i class="fa fa-github fa-stack-1x fa-inverse"></i>
                                </span>
                            </a>
                        </li>
                    </ul>
                    <p class="copyright text-muted">Copyright &copy; Aviv Business Solutions <?php echo date('Y'); ?></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
   
  

    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo js_url('bootstrap.min.js'); ?>"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?php echo js_url('clean-blog.min.js'); ?>"></script>

</body>

</html>
