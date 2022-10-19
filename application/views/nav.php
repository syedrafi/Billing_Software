<div class="nav_holder">
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-custom navbar-fixed-top">
       
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo site_url('masters/companies');?>">MKM Agencies</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!--<li>
                        <a href="<?php echo site_url('home'); ?>">Dashboard</a>
                    </li>-->
                 <!-- <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Masters
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
            
           
        
        </ul>
      </li>-->
           <li><a href="<?php echo site_url('masters/companies/index')?>">Companies</a></li>        
            <li><a href="<?php echo site_url('masters/users/index')?>">Users</a></li>
                </ul>
                 <?php include_once 'nav-right.php'; ?>
            </div>
            <!-- /.navbar-collapse -->
       
        <!-- /.container -->
    </nav>
    </div>
  