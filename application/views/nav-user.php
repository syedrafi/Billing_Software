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
            <a class="navbar-brand" href="<?php echo site_url('billing-users/home'); ?>"><h1 style="color:#5b6701 !important">MKM Agencies</h1></a>
        </div>
        <?php if (is_user_logged_in()): ?>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="<?php echo site_url('billing-users/products') ?>">Master Entries
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo site_url('billing-users/products') ?>">Products</a></li>        
                            <li><a href="<?php echo site_url('billing-users/business_contacts') ?>">Business Contacts</a></li>        
                            <li><a href="<?php echo site_url('billing-users/brands') ?>">Brands</a></li>        
                            <!--<li><a href="<?php echo site_url('billing-users/categories') ?>">Categories</a></li>        -->


                        </ul>
                    </li>

 <li><a  class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-inr"></i> Payments <span class="caret"></span></a>
        <ul  class="dropdown-menu">
            <li><a href="<?php echo base_url('index.php/client_payments'); ?>">Client Payments</a></li>       
              <li><a href="<?php echo base_url('index.php/supplier_payments'); ?>">Suppier Payments</a></li>       

        </ul>
    </li>

                    <li class="dropdown"><a data-toggle="dropdown"  href="<?php echo site_url('billing-users/transactions/index/purchase') ?>">Purchase <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <?php if (get_session_data('user_role_id') != _VIEWER_ROLE_ID): ?>
                                <li><a href="<?php echo site_url('billing-users/transactions/index/purchase') ?>"> New Purchase</a></li>        
                            <?php endif; ?>
                            <li><a href="<?php echo site_url('billing-users/transactions/transaction_list/purchase') ?>"> Purchase List</a></li> 
                            <li class="hidden"><a href="<?php echo site_url('billing-users/replacement_products') ?>"> Replacement Products</a></li> 




                        </ul>
                    </li>
                    <li class="dropdown "><a data-toggle="dropdown"  href="<?php echo site_url('billing-users/transactions/index/sales') ?>">Sales <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <?php if (get_session_data('user_role_id') != _VIEWER_ROLE_ID): ?>
                                <li><a href="<?php echo site_url('billing-users/transactions/index/sales') ?>">New Sales</a></li>        
                            <?php endif; ?>
                            <li><a href="<?php echo site_url('billing-users/transactions/transaction_list/sales') ?>"> Sales List</a></li>   



                        </ul>
                    </li>
                    <!--  <li class="dropdown "><a data-toggle="dropdown"  href="<?php echo site_url('billing-users/delivery_challan/index') ?>">DC <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <?php if (get_session_data('user_role_id') != _VIEWER_ROLE_ID): ?>
                                <li><a href="<?php echo site_url('billing-users/delivery_challan/index') ?>">New Delivery Challan</a></li>        
                            <?php endif; ?>
                            <li><a href="<?php echo site_url('billing-users/delivery_challan/challan_list') ?>"> Challan  List</a></li>   



                        </ul>
                    </li>
                  <li class="dropdown "><a data-toggle="dropdown"  href="<?php echo site_url('billing-users/advance_payments') ?>">PDC & Advance <span class="caret"></span></a>
                        <ul class="dropdown-menu">

                            <li><a href="<?php echo site_url('billing-users/advance_payments') ?>">Advance Payments</a></li>
                            <li><a href="<?php echo site_url('billing-users/pdc?type=2') ?>">PDC Receivable</a></li>
                            <li><a href="<?php echo site_url('billing-users/pdc?type=1') ?>">PDC Payable</a></li>








                        </ul>
                    </li>-->
                    
                    <li class="dropdown hidden"><a data-toggle="dropdown"  href="<?php echo site_url('billing-users/advance_payments') ?>">Payment Details <span class="caret"></span></a>
                        <ul class="dropdown-menu">

                            <li><a href="<?php echo site_url('billing-users/payment_details/list_view?type=2') ?>">Payment Details</a></li>
                            <li><a href="<?php echo site_url('billing-users/payment_details/list_view?type=1') ?>">Payable Details</a></li>








                        </ul>
                    </li>
 <li class="dropdown "><a data-toggle="dropdown"  href="<?php echo site_url('billing-users/reports/sales') ?>">Reports <span class="caret"></span></a>
                        <ul class="dropdown-menu">

                            <li class="hidden"><a href="<?php echo site_url('billing-users/reports/sales') ?>">Sales Report</a></li>
                            
                             <li><a href="<?php echo site_url('billing-users/reports/customer_report') ?>">Customer Statement</a></li>
                              <li><a href="<?php echo site_url('billing-users/reports/supplier_report') ?>">Supplier Statement</a></li>
                              <li><a href="<?php echo site_url('billing-users/reports/overall_balance?type=1') ?>">Customer Balances</a></li>
                             <li><a href="<?php echo site_url('billing-users/reports/overall_balance?type=2') ?>">Supplier Balances</a></li>
                       <li><a href="<?php echo site_url('billing-users/reports/gst_report?type=2') ?>">Sales Report</a></li>     
    <li><a href="<?php echo site_url('billing-users/reports/gst_report?type=1') ?>">Purchase Report</a></li>   
                        </ul>
                    </li>
                    <li class=""><a href="<?php echo site_url('billing-users/stock') ?>">Inventory</a></li>
                  <!--  <li class=""><a href="<?php echo site_url('billing-users/petty') ?>">Office Expenses</a></li>
                    <li class="hidden"><a href="<?php echo site_url('billing-users/tasks') ?>">Tasks</a></li>-->

                </ul>
                <?php include_once 'nav-right.php'; ?>
            </div>
            <!-- /.navbar-collapse -->
        <?php endif; ?>
        <!-- /.container -->
    </nav>
</div>
