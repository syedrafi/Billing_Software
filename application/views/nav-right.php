<?php if(is_user_logged_in()): ?>
<ul class="nav navbar-nav navbar-right">
    <li class="dropdown"><a  data-toggle="dropdown" href="#" class="dropdown-toggle"><span class="glyphicon glyphicon-user"></span>   <?php echo get_session_data('first_name')." ".  get_session_data('last_name'); ?> <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="<?php echo site_url('home/reset');?>">Change Password</a></li>
                <li><a href="<?php echo site_url('login/logout');?>">Logout</a></li>
        
        </ul>
      
      </li>
      
     
    </ul>
<?php endif; ?>