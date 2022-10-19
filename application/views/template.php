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
    <link href="<?php echo css_url('custom.css')?>" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="<?php echo css_url('font-awesome.min.css'); ?>" rel="stylesheet" type="text/css">
  <!--<link href='https://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>-->
  

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link type="text/css" rel="stylesheet" href="<?php echo css_url('jsgrid.min.css'); ?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo css_url('jsgrid-theme.min.css'); ?>" />
    <script src="<?php echo js_url('jquery.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo js_url('jsgrid.js'); ?>"></script>
</head>

<body>
<?php require_once 'nav.php'; ?>
    <!-- Page Header -->
    <!-- Set your background image for this header on the line below. -->
  
    <!-- Main Content -->
    <div id="page-wrapper">
        <div id="page-inner">
          <?php echo getFlashMessages(); ?>    
        <?php print $content; ?>
    </div>
    </div>

    <hr>

    <!-- Footer -->
    <?php   $this->load->view('footer'); ?>

    <!-- jQuery -->
 

    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo js_url('bootstrap.min.js'); ?>"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?php echo js_url('clean-blog.min.js'); ?>"></script>

</body>

</html>
