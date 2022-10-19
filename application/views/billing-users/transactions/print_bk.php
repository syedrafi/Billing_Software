<section class='bill_body'>
    <div class="header">
        <div class="row">

            <div class="invoice-title col-md-12 col-xs-12 text-center">
                <img src="<?php echo assets_url('img/logo.png'); ?>" height="65"/>

            </div>
            <div class="right-top col-xs-12 "><address class="text-center">

                    <?php foreach (explode("@", $company_details['company_address']) as $key => $value) {
                        ?>
                      <?php echo $value; ?><br>
                    <?php } ?></address></div>
            <div class="right-top col-xs-12 "><address class="text-center">
                    <i class="fa fa-phone"></i>  <?php echo $company_details['company_mobile']; ?>&nbsp
                    <i class="fa fa-envelope"></i>  <?php echo $company_details['company_email']; ?>
                

                </address></div>

        </div>

        <div class="row tax_row">
            <?php if ($transaction_details['tax_percent'] && $transaction_details['tax_type'] == 'VAT'): ?>
                    <!--    <div class="col-xs-6 text-left">TIN: <?php echo $company_details['company_tin']; ?> </div>-->
            <?php endif; ?>
            <?php if ($transaction_details['tax_percent'] && $transaction_details['tax_type'] == 'CST'): ?>
                        <!--<div class="col-xs-4 text-left">CST: <?php echo $company_details['company_cst']; ?> </div>-->
            <?php endif; ?>

            <div class="col-xs-6 text-left">GSTIN: <?php echo $company_details['gstin']; ?> </div>  
            <div class="col-xs-6  pull-right text-right"> <?php echo $company_details['company_dl_no']; ?> </div>
        </div>
    </div>
    <div class="meta_info">
        <div class="row">
            <div class="col-md-12 col-xs-12">
            <table class="table table-bordered">
                <tr>
                    <td> <div class="row">  <div class="col-xs-12 pull-left">
                          
               
              <div class="row">
                  <div class="col-xs-12 customer_address" style="padding-left: 25px;">
                    <h5>To:</h5>
                    <h4 class="customer_heading"><?php echo $transaction_details['business_contact_name']; ?></h4>
                    <?php foreach (explode("@", $transaction_details['address']) as $key => $value) {
                        ?>

                        <?php echo $value; ?><br>
                    <?php } ?>
                  <?php if (strtotime($transaction_details['transaction_date']) >= strtotime('2017-07-01 00:00:00')): ?>
                      
                    <?php else: ?>
                        <div class="col-xs-3 nopadding">TIN</div><div class="col-xs-9"> : <?php echo $transaction_details['tin_no']; ?></div>
                        <div class="col-xs-3 nopadding">CST</div><div class="col-xs-9"> : <?php echo $transaction_details['cst_no']; ?></div>
                        <div class="col-xs-3 nopadding">DL No</div><div class="col-xs-9"> : <?php echo $transaction_details['dl_no']; ?></div>
                    <?php endif; ?>

                </div>
              </div>
                            
                        </div></div>
                        <div class="row" style="margin-top: 50px; padding-left: 10px;">
                                  <div class="col-xs-5  pull-left"> <?php if($transaction_details['gstin']!= ""): ?>GSTIN: <?php echo $transaction_details['gstin'];  endif; ?> </div>
                        <div class="col-xs-7  pull-right text-right"><?php if($transaction_details['dl_no']!= ""): ?>DL No: <?php echo $transaction_details['dl_no'];  endif; ?></div>

                            </div>
                    </td>
                    <td><div class="col-xs-12">
                <h4 class="customer_heading">TAX INVOICE</h4>
                <table class="table table-borderless no-border">
                    <tr>
                        <td>Invoice No.</td>
                        <td><?php echo $transaction_details['bill_no']; ?></td>
                    </tr>
                    <tr>
                        <td>Invoice Date.</td>
                        <td><?php echo date("d-m-Y", strtotime($transaction_details['transaction_date'])) ?></td>
                    </tr>
                     <!--    <tr>
                        <td>Order No.</td>
                        <td><?php echo $transaction_details['order_no'];  ?></td>
                    </tr>
               <tr>
                        <td>Payment Type.</td>
                        <td><?php echo $transaction_details['payment_type']; ?> Days/<?php echo $transaction_details['mode_name']; ?></td>
                    </tr>
                    <tr class="hidden">
                        <td>Payment Mode.</td>
                        <td><?php echo $transaction_details['mode_name']; ?> </td>
                    </tr>
                    <tr>
                        <td>DC No/Date</td>
                        <td><?php echo $transaction_details['dc_no']; ?> </td>
                    </tr>-->
                     <tr>
                        <td>Dispatched to</td>
                        <td><?php echo $transaction_details['dispatched_to']; ?> </td>
                    </tr>
                </table>
            </div></td>
                </tr>
            </table>
         
        </div>
        </div>
    </div>



    <div class="products_list">
        <div class="row">
            <div class="col-md-12 col-xs-12">

                <div class=" col-xs-12 nopadding product_table">
                    <table class="table table-bordered ">
                        <thead>
                            <tr>
                                <td width="2%"  class="text-center"><strong>S.No</strong></td>
                                <td width="35%"  class="text-center"><strong>Particulars</strong></td>
                                <td width="3%"  class="text-center"><strong>HSN</strong></td>
                                <td width="4%" class="text-center mrp_td"><strong>MRP</strong></td>
                                
                                 <td width="5%" class="text-center"><strong>Qty</strong></td>
                                 <td width="5%" class="text-center"><strong> Rate </strong></td>
                                
                               
                                <td width="4%" class="text-center discount_td"><strong>Discount %</strong></td>
                               <td width="7%" class="text-right"><strong>Total(INR)</strong></td>
                                <td width="30" class="hidden"><table width="100%"  class="table-condensed table-bordered border_Table"><tr class="border_bottom"><td align="center" colspan="2" width="100%">CGST</td></tr><tr><td>%</td><td>(Rs.)</td></tr></table></td>
                                <td width="30" class="hidden"><table width="100%"  class="table-condensed table-bordered"><tr class="border_bottom"><td align="center" colspan="2" width="100%">SGST</td></tr><tr><td>%</td><td>(Rs.)</td></tr></table></td>
                                <td width="30" class="hidden"><table width="100%"  class="table-condensed table-bordered"><tr class="border_bottom"><td align="center" colspan="2" width="100%">IGST</td></tr><tr><td>%</td><td>(Rs.)</td></tr></table></td>
                                <td width="3%" class="hidden"><strong>Total(Rs)</strong></td>
                            </tr>
                        </thead>
                        <tbody height="500">
                            <!-- foreach ($order->lineItems as $line) or some such thing here -->

                            <?php
                            $grand_total = 0;
                            $i = 0;
                            $cgst_total = 0;
                            $igst_total = 0;
                            $sgst_total = 0;
                            $no_of_products=count($transaction_products);
                            $min_height=300;
                            $height_needed=$no_of_products*50;
                            $last_tr_height=$min_height-$height_needed;
                            if($last_tr_height<0):
                                $last_tr_height=0;
                            endif;
                            $mrp_visible=false;
                            $discount_visible=false;
                          $only_total=0;
                          
                          $cgst_label="";
                          $sgst_percent="";
                          $igst_percent="";
                            foreach ($transaction_products as $key => $value) {
                                $i++;
                                $totals = $value['discounted_price_per_unit'] * $value['qty'];
                                $only_total=$totals+$only_total;

                                $cgst_percent = $value['cgst_percent'];
                                $sgst_percent = $value['sgst_percent'];
                                $igst_percent = $value['igst_percent'];

                                $cgst_amt = round(($totals / 100) * $cgst_percent,2);
                                $cgst_total = $cgst_total + $cgst_amt;
                                $sgst_amt = round(($totals / 100) * $sgst_percent,2);
                                $sgst_total = $sgst_total + $sgst_amt;
                                $igst_amt = round(($totals / 100) * $igst_percent);
                                $igst_total = $igst_total + $igst_amt;

                                $totals_and_tax = $totals + $cgst_amt + $sgst_amt + $igst_amt;
                                $grand_total = $grand_total + $totals_and_tax;
                                if(!isset($value['mrp'])):
                                    $value['mrp']="-";
                                else:
                                    $mrp_visible=true;
                                
                                endif;
                                if($value['discount_percentage']):
                                 $discount_visible=true;
                                else:
                                    
                                
                                endif;
                                ?>

                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $value['brand_name'] . "-" . $value['product_name']." ";
                                ?>
                                    
                                   
                                    
                                    </td><td><?php echo $value['hsn_no']; ?></td>
                                   <td class="text-center mrp_td">Rs.<?php echo $value['mrp']; ?></td>
                                   <td class="text-center"><?php echo $value['qty']; ?></td>
                                    <td class="text-center">Rs.<?php echo $value['price_per_unit']; ?></td>
                                       
                                  
                                     <td class="text-center discount_td"><?php
                                        if ($value['discount_percentage'] == 0): $value['discount_percentage'] = "-";
                                        endif;
                                        echo $value['discount_percentage'];
                                        ?></td>
                                    <td class="text-right"> <?php echo $totals; ?></td>
                                    <td width="30" class="hidden"><table width="100%" class="table-condensed table-bordered" ><tr><td width="50%"><?php echo $cgst_percent; ?></td><td width="50%"><?php echo $cgst_amt; ?></td></tr></table></td>
                                    <td width="30" class="hidden"><table width="100%"  class="table-condensed table-bordered"><tr><td><?php echo $sgst_percent; ?></td><td><?php echo $sgst_amt; ?></td></tr></table></td>
                                    <td width="30" class="hidden"><table width="100%"  class="table-condensed table-bordered"><tr><td><?php echo $igst_percent; ?></td><td><?php echo $igst_amt; ?></td></tr></table></td>

                                    <td class="text-right hidden"> <?php echo $totals_and_tax; ?></td>

                                </tr>

                            <?php } ?>
                                <tr style="height: <?php echo $last_tr_height; ?>px;">
                                    <td  colspan=""></td>  
                                      <td  colspan=""></td>  
                                      
                                          <td  colspan="" class="mrp_td"></td>  
                                            <td  colspan=""></td>  
                                             <td  colspan="" ></td>  
                                             <td  colspan="" class="discount_td"></td>  
                                      <td  colspan=""></td>  
                                        <td  colspan=""></td>  
                                          
                                </tr>
                        </tbody>
                        <?php   $colspan_minus=0;
                        if(!$mrp_visible):
                            $colspan_minus++;
                        endif;
                          if(!$discount_visible):
                            $colspan_minus++;
                        endif;
                        
                        ?>
                        <tfoot>
                            
                             <tr>

                                <td  colspan="<?php echo 7-$colspan_minus; ?>"class=" text-center"><strong  class="pull-right"> Sub-Total</strong></td>
                                <td class=" text-right"><?php
                                    $gtotal = $grand_total - $igst_total;
                                    echo $only_total ;
                                    ?></td>
                            </tr>
                            <tr class="borderless">

                                <td  colspan="<?php echo 7-$colspan_minus; ?>"class=" text-center"><strong  class="pull-right"> CGST <?php if($cgst_percent>0): echo " @ ". $cgst_percent." %"; endif;  ?></strong></td>
                                <td class=" text-right"><?php
                                   
                                    echo $cgst_total;
                                    ?></td>
                            </tr>
                             <tr class="borderless">

                                <td  colspan="<?php echo 7-$colspan_minus; ?>"class=" text-center"><strong  class="pull-right"> SGST  <?php if($sgst_percent>0): echo " @ ". $sgst_percent." %"; endif; ?></strong></td>
                                <td class=" text-right"><?php
                                   
                                    echo $sgst_total;
                                    ?></td>
                            </tr>
                            <tr class="borderless">

                                <td  colspan="<?php echo 7-$colspan_minus; ?>"class=" text-center"><strong  class="pull-right"> IGST <?php if($igst_percent>0): echo " @ ".$igst_percent." %"; endif; ?></strong></td>
                                <td class=" text-right"><?php
                                   
                                    echo $igst_total;
                                    ?></td>
                            </tr>
                           

                            <?php
                            if ($transaction_details['discount_per'] > 0):
                                $discount_amt = round(($grand_total / 100) * $transaction_details['discount_per'], 2);
                                $grand_total = $grand_total - $discount_amt;
                                ?>
                                <tr>
                                    <td  colspan="<?php echo 7-$colspan_minus; ?>"class="text-center"><strong  class="pull-right"> Discount @ <?php echo $transaction_details['discount_per']; ?></strong></td>
                                    <td class=" text-right">Rs.<?php echo $discount_amt; ?></td>   

                                </tr>
                            <?php endif; ?>

                            <?php if ($transaction_details['tax_percent']): ?>
                                <tr>

                                    <td colspan="<?php echo 7-$colspan_minus; ?>"class=" text-center"><strong  class="pull-right"> <?php echo $transaction_details['tax_type']; ?> @ <?php echo $transaction_details['tax_percent']; ?>%</strong></td>
                                    <td class="thick-line text-right">Rs.<?php echo round(($grand_total / 100) * $transaction_details['tax_percent'], 2); ?></td>
                                </tr>


                            <?php endif; ?>
                            <tr>
                                <td  colspan="<?php echo 6-$colspan_minus; ?>" class="text-center"><strong> Rupees <?php
                                        $gtotal = round((($grand_total / 100) * $transaction_details['tax_percent']) + $grand_total);
                                        echo convert_number($gtotal);
                                        ?></strong></td>

                                <td  class="thick-line text-center"><strong class="pull-right"> Grand Total</strong></td>
                                <td class="thick-line text-right"><?php echo $gtotal; ?></td>
                            </tr>
                            <?php if ($transaction_details['transport_charges'] > 0): ?>
                                <tr>
                                    <td  colspan="<?php echo 6-$colspan_minus; ?>" class="text-center"></td>

                                    <td  class="thick-line text-center"><strong class="pull-right">Transport Charges</strong></td>
                                    <td class="thick-line text-right">Rs.<?php echo $transaction_details['transport_charges']; ?></td>
                                </tr>
                            <?php endif; ?>
                        </tfoot>

                    </table>
                </div>

            </div>
        </div>
    </div>
    <div class="footer">
        <div class="row">
            <div class="col-xs-7">
                <p class='underline half-margin'>Declaration:</p>
                <ul class="nopadding bottom_ul">
                    <li>Payment in <?php echo $transaction_details['payment_type']; ?> days from the date of supply.</li>
                   
                  
                    <li>Goods once Sold cannot be taken back.</li>
                    <li>No Claim for Beakage and Shortage during Transit will be Entertained.</li>
                    <li>Subject if Coimbatore Civil Jurisdiction.</li>
                </ul>
            </div>
            <div class="col-xs-5">
                <p class='text-right'>For Zaaraa Creations</p>
                <p class='extra-margin-top text-right'>Authorized Signatory</p>
            </div>
        </div>
    </div>
</section>
<style type="text/css">
    <?php if(!$discount_visible):?>
    .discount_td{
        display: none !important;
    }
   <?php  endif; ?>
     <?php if(!$mrp_visible):?>
    .mrp_td{
        display: none !important;
    }
   <?php  endif; ?>
    .borderlesss td{
        border-bottom: none !important;
        border-top:  none !important;
    }
    body{
        margin: 10px;
        font-size: 12px;
    }
    .border_bottom{
        /* border-bottom:1px solid #ddd; */
    }
    .underline{
        text-decoration: underline;
    }
    .extra-margin-top{
        margin-top: 70px;
    }
    .invoice-title h2, .invoice-title h3 {
        display: inline-block;
    }



    footer{
        display: none;
    }
    .bill_body{

    }
    address{
        margin-bottom: 0px;
    }
    p{
        margin: 15px 0;
    }
    @media print{
        .nav{
            display: none;
        }   
        .nav_holder{
            display: none;
        }
    }

    hr{
        margin-top: 4px;
        margin-bottom: 4px;
    }
    .table-borderless tbody tr td, .table-borderless tbody tr th, .table-borderless thead tr th {
        border: none;
    }
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 4px;
    }
    .half-margin{
        margin: 7px 0;
    }
    .table{
        margin-bottom: 5px;
    }

    .header{
        /*   background-color:#C0C0C0;*/

    }
    .meta_info,.header,.products_list{
        /*   border: 1px solid #424242;*/
        padding: 4px;
        margin: 0 2px  0px 2px !important;
    }
    .customer_heading{
        margin-top: 5px;
        margin-bottom: 5px;
        text-transform: uppercase;
    }
    .customer_address{
        text-transform: uppercase;
    }

    .bottom_ul{
        list-style: decimal;
        list-style-position: outside;
        margin-left: 10px !important;

    }
    .meta_info{
        border-top: 1px solid #424242; 
        padding-top: 15px;
    }
    .tax_row{
        margin-top: 8px;
    }
    .border_Table tbody{
        border-top: none !important;
        border-bottom: none;
        border-right: none;
        border-left: none;
    }
    .no-border tr,.no-border td,.no-border{
        border: none !important;
    }
    .product_meta{
        font-size: 10px;
        font-style: italic;
    }
   .product_table .table-bordered>tbody>tr>td{
        border-bottom: none !important;
         border-top: none !important;
    }
    .product_table .table-bordered>tbody>tr:last-of-type>td{
        border-bottom: 1px solid black !important;
         border-top:none !important;
    }
</style>