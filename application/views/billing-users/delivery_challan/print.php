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


            <div class="col-xs-6 text-left">GSTIN: <?php echo $company_details['gstin']; ?> </div>  
            <div class="col-xs-6  pull-right text-right">DL No: <?php echo $company_details['company_dl_no']; ?> </div>
        </div>
    </div>
    <div class="meta_info">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <table class="table table-bordered">
                    <tr>
                        <td>   <div class="col-xs-12 pull-left">



                                <div class="col-xs-12 customer_address">
                                    <h5>To:</h5>
                                    <h4 class="customer_heading"><?php echo $challan_details['business_contact_name']; ?></h4>
                                    <?php foreach (explode("@", $challan_details['address']) as $key => $value) {
                                        ?>

                                        <?php echo $value; ?><br>
                                    <?php } ?>
                                    <?php if (strtotime($challan_details['challan_date']) >= strtotime('2017-07-01 00:00:00')): ?>
                                        <div class="col-xs-3 nopadding">GSTIN</div><div class="col-xs-9"> : <?php echo $challan_details['gstin']; ?></div>
                                        <div class="col-xs-3 nopadding">DL No</div><div class="col-xs-9"> : <?php echo $challan_details['dl_no']; ?></div>

                                    <?php else: ?>
                                        <div class="col-xs-3 nopadding">TIN</div><div class="col-xs-9"> : <?php echo $challan_details['tin_no']; ?></div>
                                        <div class="col-xs-3 nopadding">CST</div><div class="col-xs-9"> : <?php echo $challan_details['cst_no']; ?></div>
                                        <div class="col-xs-3 nopadding">DL No</div><div class="col-xs-9"> : <?php echo $challan_details['dl_no']; ?></div>
                                    <?php endif; ?>

                                </div>
                            </div></td>
                        <td><div class="col-xs-12">

                                <table class="table table-borderless no-border">
                                    <tr>
                                        <td>DC No.</td>
                                        <td><?php echo $challan_details['dc_no']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>DC Date.</td>
                                        <td><?php echo date("d-m-Y", strtotime($challan_details['challan_date'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Order No.</td>
                                        <td><?php echo $challan_details['order_no']; ?></td>
                                    </tr>



                                    <tr>
                                        <td>Dispatched to</td>
                                        <td><?php echo $challan_details['dispatched_to']; ?> </td>
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

                <div class=" col-xs-12 nopadding">
                    <table class="table table-bordered ">
                        <thead>
                            <tr>
                                <td width="2%"  class="text-center"><strong>S.No</strong></td>
                                <td width="40%"  class="text-center"><strong>Item</strong></td>
                                <td width="3%"  class="text-center"><strong>HSN</strong></td>

                                <td width="5%" class="text-center"><strong>Unit Price</strong></td>
                                <td width="2%" class="text-center"><strong>Qty</strong></td>


                                <td width="7%" class="text-right"><strong>Total(INR)</strong></td>

                            </tr>
                        </thead>
                        <tbody height="500">
                            <!-- foreach ($order->lineItems as $line) or some such thing here -->

                            <?php
                            $grand_total = 0;
                            $i = 0;

                            $no_of_products = count($transaction_products);
                            $min_height = 300;
                            $height_needed = $no_of_products * 50;
                            $last_tr_height = $min_height - $height_needed;
                            if ($last_tr_height < 0):
                                $last_tr_height = 0;
                            endif;
                            $mrp_visible = false;


                            foreach ($transaction_products as $key => $value) {
                                $i++;
                                $totals = $value['price_per_unit'] * $value['qty'];

                                $grand_total = $grand_total + $totals;
                                ?>

                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $value['brand_name'] . "-" . $value['product_name'] . " " . $value['size'];
                                ?>

                                        <span class="product_meta"><?php
                            if ($value['batch_no'] != ""):
                                echo "<br>" . $value['batch_no'];
                            endif;
                            if ($value['expiry_date'] != ""):
                                echo " Expiry Date: " . date("d-m-Y", strtotime($value['expiry_date']));
                            endif;
                                ?>
                                        </span>

                                    </td><td><?php echo $value['hsn_no']; ?></td>


                                    <td class="text-center">Rs.<?php echo $value['price_per_unit']; ?></td>
                                    <td class="text-center"><?php echo $value['qty']; ?></td>


                                    <td class="text-right"> <?php echo $totals; ?></td>



                                </tr>

<?php } ?>
                            <tr style="height: <?php echo $last_tr_height; ?>px;">
                                <td  colspan=""></td>  
                                <td  colspan=""></td>  

                                <td  colspan="" class="mrp_td"></td>  
                                <td  colspan=""></td>  
                                <td  colspan="" ></td>  
                                <td  colspan="" ></td>  
  <td  colspan="" ></td>  

                            </tr>
                        </tbody>
<?php
$colspan_minus = 0;


?>
                        <tfoot>


                            <tr>
                                <td  colspan="<?php echo 4 - $colspan_minus; ?>" class="text-center"><strong> Rupees <?php
                        $gtotal = $grand_total;
                        echo convert_number($gtotal); 
                        ?> Only.</strong></td>

                                <td  class="thick-line text-center"><strong class="pull-right"> Grand Total</strong></td>
                                <td class="thick-line text-right"><?php echo $gtotal; ?></td>
                            </tr>

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
                    <li>Extra 12% Tax Applicable to Taxable Products.</li>
                      <li>Received Goods in Good Condition.</li>
                </ul>
            </div>
            <div class="col-xs-5">
                <p class='text-right'>For Evita Venture</p>
                <p class='extra-margin-top text-right'>Authorized Signatory</p>
            </div>
        </div>
    </div>
</section>
<style type="text/css">
<?php if (!$discount_visible): ?>
        .discount_td{
            display: none !important;
        }
<?php endif; ?>
<?php if (!$mrp_visible): ?>
        .mrp_td{
            display: none !important;
        }
<?php endif; ?>
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
</style>