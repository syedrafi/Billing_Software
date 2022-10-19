<?php
$due_date = date('d-m-Y');
$transaction_date = $due_date;
$payment_notes = "";
$payment_type_options = array(
    1 => "Immediate",
    15 => "15 Days",
    25 => "25 Days",
    30 => "30 Days",
    45 => "45 Days",
    60 => "60 Days"
);
$discount_per = 0;
$transport_charges = 0;
$freight_charges = 0;
$user_id = get_session_data('user_id');
$business_contact_id = "";
$tax_type_id = "";
$tax_percent = "";
$payment_type = "";
$mode_id = "";
$hospital_or_dealer = "";
$created_date = date('d-m-Y');
$dc_no = "";
$dispatched_to = "";
$order_no = "";
if (isset($transaction_details)):
    $dispatched_to = $transaction_details['dispatched_to'];
    $order_no = $transaction_details['order_no'];
    $dc_no = $transaction_details['dc_no'];
    $bill_no = $transaction_details['bill_no'];
    $transport_charges = $transaction_details['transport_charges'];
    $freight_charges = $transaction_details['freight_charges'];
    $discount_per = $transaction_details['discount_per'];
    $due_date = date("d-m-Y", strtotime($transaction_details['due_date']));
    $transaction_date = date("d-m-Y", strtotime($transaction_details['transaction_date']));
    $payment_notes = $transaction_details['payment_notes'];
    $type = $transaction_details['type'];
    $hospital_or_dealer = $transaction_details['type_id'];
    $business_contact_id = $transaction_details['business_contact_id'];
    $mode_id = $transaction_details['likely_payment_mode_id'];
    $payment_type = $transaction_details['payment_type'];
    $created_date = $transaction_details['created_on'];
    $user_id = $transaction_details['user_id'];
    $tax_type_id = $transaction_details['tax_type_id'];
    $tax_percent = $transaction_details['tax_percent'];
endif;
?>
<link rel="stylesheet" href="<?php echo css_url('jquery-ui.min.css') ?>">

<script src="<?php echo js_url('jquery-ui.min.js'); ?>"></script>
<link href="<?php echo css_url('select2.min.css'); ?>" rel="stylesheet" />
<script src="<?php echo js_url('select2.min.js') ?>"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.theme.css'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.structure.min.css'); ?>" />
<div class="row tally_background">
    <?php echo form_open(current_url(), array('method' => 'post', 'id' => 'transaction_form')); ?>
    <div class="row">
        <div class="col-md-12 col-xs-6">

            <div class='col-md-5 col-xs-12 pull-left'>
                <h3 class="">Sales  <span id='transaction_id_span ' class="hidden"><?php echo $bill_no; ?></span><input type="hidden" value="<?php echo $transaction_id; ?>" name="transaction_id"  required="required" id="transaction_id" class="form-control" readonly="readonly" /></h3>
                <div class='col-md-6 nopadding'>Sales No.</div>  <div class='col-md-6'> <span id='transaction_id_span'><?php echo $bill_no; ?></span></div>
                <div style="clear: both; height: 10px;"></div>
            
                <div style="clear: both; height: 10px;"></div>
                <div class='col-md-6 nopadding'>Party  Name</div>  <div class='col-md-6'>
                    <?php echo form_dropdown('business_contact_id', $contact_options, $business_contact_id, "class='form-control', id='business_contact_id', required='required'"); ?></div>
                <div style="clear: both; height: 10px;"></div>
                <div class='col-md-6 nopadding'>Bill No.</div>  <div class='col-md-6'><input type="text" value="<?php echo $bill_no; ?>" name="bill_no" class="form-control"  readonly="readonly"/></div>


                <div style="clear: both; height: 10px;"></div>
                  <!--<div class='col-md-6 nopadding'>Due Date </div>  <div class='col-md-6'><input type="text" value="<?php echo $due_date; ?>" name="due_date"id='due_date' class="form-control"  /></div>
                  <div style="clear: both; height: 10px;"></div>
                <div class='col-md-6 nopadding'>DC No/Date </div> 
                <div class='col-md-6'><input type="text" name="dc_no" id="dc_no" class="form-control" value="<?php echo $dc_no; ?>"/></div>-->
                <div style="clear: both; height: 10px;"></div>
                <div class='col-md-6 nopadding'>Dispatched to </div>  <div class='col-md-6'><input type="text" name="dispatched_to" id="dispatched_to" class="form-control" value="<?php echo $dispatched_to; ?>"/></div>
                <div style="clear: both; height: 10px;"></div>

              <!--   <div class='col-md-6 nopadding'>Order No </div>  <div class='col-md-6'><input type="text" name="order_no" id="order_no" class="form-control" value="<?php echo $order_no; ?>"/></div>
                <div style="clear: both; height: 10px;"></div>

               <div class='col-md-6 nopadding'>Payment Type </div>  <div class='col-md-6'><?php echo form_dropdown('payment_type', $payment_type_options, $payment_type, "class='form-control'"); ?></div>
                <div style="clear: both; height: 10px;"></div>-->
                <div class='col-md-6 nopadding'>Payment Mode </div>  <div class='col-md-6'><?php echo form_dropdown('likely_payment_mode_id', $mode_options, $mode_id, "class='form-control' required='required'"); ?></div>
                <div style="clear: both; height: 10px;"></div>
               <!--  <div class='col-md-6 nopadding'>Sold By </div>  <div class='col-md-6'><?php echo form_dropdown('user_id', $user_options, $user_id, "class='form-control'"); ?></div>
                <div style="clear: both; height: 10px;"></div>-->
            </div>

            <div class='col-md-7 col-xs-6  '>
                <div class='col-md-12'><div class='' style="float: right;margin:5px;" ><div class="col-md-12 text-center">
                            <span id="return_msg"></span>
                            <button id="transaction_save" type="submit" class="btn-success text-center "><i class="fa fa-save fa-2x"></i> </button>

                            <button  id="transaction_cancel" type="button" class="btn-danger text-center"> <i class="fa fa-times fa-2x"></i> </button>
                        </div></div></div>
                <input type="text"class='col-md-5 pull-right margin-top'   value="<?php echo $transaction_date; ?>" name="transaction_date"  id='transaction_date'class="form-control" /> <input type="hidden" value="<?php echo $type; ?>" name="type"  id="type" />
            </div>
            <div class='col-md-6 col-xs-12 col-sm-12'>
                <div style="height: 5px; clear: both; border-bottom: 1px solid #ddd"></div>
                <div class="col-md-6  nopadding"><b>Total</b></div> <div class="col-md-6 discount_per pull-right text-right"> <input type="hidden" name="products_total_value" id="products_total_value"/><span class="products_total_value"></span></div>
                <div style="height: 5px; clear: both; border-bottom: 1px solid #ddd"></div>
                <div class="col-md-6  nopadding hidden">Discount % <input type="text" name="discount_percentage" id="discount_percentage" class="form-control input-sm input-xs pull-right" value="<?php echo $discount_per; ?>"/></div> <div class="col-md-6 discount_per pull-right text-right hidden"><input type="text" name="discount_total" id="discount_total" class="form-control input-sm input-xs pull-right" value="0"/></div>
                <div style="height: 5px; clear: both; border-bottom: 1px solid #ddd"></div>
                <div class="row hidden">
                    <div class="col-md-6 nopadding">
                        <?php echo form_dropdown('tax_type_id', $tax_options, $tax_type_id, "  id='tax_type_id'"); ?>
                        <?php echo form_dropdown('tax_percent', array("0" => "0%", "2" => "2%", "5.5" => "5.5%", "5" => "5%", "14.5" => "14.5%"), $tax_percent, "id='tax_percent'"); ?>
                    </div>

                    <div class="col-md-6 total_tax text-right ">
                        0
                    </div>
                    <div style="height: 5px; clear: both; border-bottom: 1px solid #ddd"></div>

                    <div class="col-md-6  nopadding">Without Tax</div> <div class="col-md-6 without_tax pull-right text-right"></div>
                    <div style="height: 5px; clear: both; border-bottom: 1px solid #ddd"></div>
                    <div class="col-md-6 nopadding">With Tax</div>   <div class="col-md-6 with_tax text-right pull-right"></div>  <div style="height: 5px; clear: both; border-bottom: 1px solid #ddd"></div>
                </div>
                <div class="col-md-6 nopadding">Transport Charges</div>   <div class="col-md-6  text-right pull-right"><input type="hidden" name="freight_charges" value="<?php echo $freight_charges; ?> " id="freight_charges"/>
                    <input class="form-control input-sm input-xs pull-right" type="text" name="transport_charges" value="<?php echo $transport_charges; ?> " id="transport_charges"/></div>
                <input type="hidden" value="" name="grand_total" id="grand_total" class="form-control" readonly="readonly" />  
            </div>

            <table style="display: none;" class="table table-bordered table-responsive transaction_table">
                <thead>
                    <tr>



                    </tr>
                </thead>
                <tbody>
                    <tr>


<!--   <td><?php echo get_session_data('first_name') . " " . get_session_data('middle_name'); ?></td>-->




                        <td></td>




                    </tr>


                </tbody>
            </table>

        </div>

    </div>
    <div class="row">
        <div class="col-md-12">


        </div>

    </div>
    <div id="productsGrid" >


    </div>
    <div style="clear: both"></div>
    <div class='row'>
        <div class='col-md-6'>

            <h3>Narration</h3>
            <textarea name="payment_notes"><?php echo $payment_notes; ?></textarea>


        </div>

    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
    $(document).ready(function () {
    generate_tax_options();
    $("#tax_type_id").on("change", function(){
    var business_contact_id_global = "<?php echo $business_contact_id; ?>";
    generate_tax_options();
    });
    $("#due_date,#transaction_date").datepicker({dateFormat: "dd-mm-yy"});
    $("#business_contact_id").select2();
    if ($("#transaction_id").val() == "") {

    generate_transaction_id();
    }

    $("#transaction_form").on('submit', function (e) {
    e.preventDefault();
    save_transaction();
    });
    $("#transaction_cancel").on("click", function () {
    if (confirm('Are your Sure to cancel Sales?')) {
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/transactions/delete'); ?>",
            dataType: "json",
            data: {transaction_id: $("#transaction_id").val()},
            success: function (response) {
            $("#return_msg").fadeIn();
            $("#return_msg").html(response.msg);
            window.location = "<?php echo base_url('index.php/billing-users/transactions/transaction_list/sales'); ?>";
            }

    });
    }
    else {
    return;
    }

    });
    });
    function generate_transaction_id() {
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/transactions/add'); ?>",
            dataType: "json",
            data: {type:<?php echo $type; ?>},
            success: function (response) {
            if (response.id) {
            $("#transaction_id").val(response.id);
            //  $("#transaction_id_span").html(response.id);
            }
            else {
            alert(response.msg);
            }
            }

    });
    }

    function save_transaction() {
    $("#return_msg").fadeIn();
    $("#return_msg").html("<i class='fa fa-spinner'></i> Loading...");
    data = $("#transaction_form").serializeArray();
    data.discount_per = $("#discount_percentage").val();
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/transactions/save_transaction'); ?>",
            data:data,
            dataType: "json",
            success: function (response) {
            $("#return_msg").fadeIn();
            $("#return_msg").html(response.msg);
            if (response.id) {
            if ($("#type").val() == 2) {
            window.location = "<?php echo base_url('index.php/billing-users/transactions/print_receipt/'); ?>/" + response.id;
            }
            }
            else {
            alert(response.msg);
            }
            }

    });
    }
    function generate_tax_options(){
    $('#tax_percent').find('option')
            .remove()
            .end()
            .append('')
            .val('');
    tax_type = $("#tax_type_id").val();
    if (tax_type == 1){

    $('#tax_percent').append($('<option>').text("5%").attr('value', "5"));
    $('#tax_percent').append($('<option>').text("14.5%").attr('value', "14.5"));
    }
    else if (tax_type == 2){


    $('#tax_percent').append($('<option>').text("5%").attr('value', "5"));
    $('#tax_percent').append($('<option>').text("2%").attr('value', "2"));
    }
    }
</script>

<script type="text/javascript">
    $("document").ready(function () {

    $("#contact_type_id").focus().select();
    get_contact_options($("#contact_type_id").val());
    $("#productsGrid").jsGrid({
    height: "300px",
            width: "98%",
            filtering: false,
            editing: true,
            inserting: true,
            sorting: true,
            paging: true,
            autoload: true,
            pageSize: 20,
            pageButtonCount: 5,
            pageLoading: true,
            deleteConfirm: "Do you really want to delete the User?",
            fields: [
            {name: "transaction_product_id", title: " #", type: "number", width: 30, readOnly: true, visible: false, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
            {name: "product_id",
                    type: "select",
                    title: "Search",
                    editing: false,
                    inserting: true,
                    items: "",
                    width: 60,
                    autosearch: true,
                    /*
                     validate: ["required", function (value, item) {
                     item_value = item.product_id;
                     
                     if (item_value === 0) {
                     return false;
                     }
                     return true;
                     }],
                     */
                    align: "left",
                    valueType: "number",
                    valueField: "transaction_product_id", // name of property of item to be used as value
                    textField: "product_name",
                    css: "product_td"

            },
            {name: "product_name", title: "Name", type: "text", width: 40, align: "left", css: "name_td", inserting: false, editing: false},
            {name: "batch_no", title: "Batch #", type: "text", width: 25, align: "left", css: "batch_td",visible:false},
            {name: "mfg_date", title: "Mfg Date", type: "text", width: 25, align: "left", css: "mfg_td",visible:false},
            {name: "expiry_date", title: "Expiry", type: "text", width: 25, align: "left", css: "expiry_td",visible:false},
            {name: "qty", title: "Qty", type: "text", width: 20, validate: "required", css: "qty_td"},
            {name: "price_per_unit", title: "Price", type: "text", width: 20, validate: "required", css: "price_td"},
            {name: "discount_percentage", title: "Discount %", type: "text", width: 20, css: "discount_td"},
            {name: "taxable_value", title: "Taxable Value", type: "text", width: 20, css: "taxable_td"},
            {name: "cgst_percent",
                    type: "select",
                    title: "CGST %",
                    width: 25,
                    autosearch: true,
                    align: "center",
                    valueType: "number",
                    items: <?php echo $cgst_percent_options; ?>,
                    valueField: "value", // name of property of item to be used as value
                    textField: "percent",
                    css: "cgst_percent"
            },
            {name: "cgst_amt", title: "CGST Amt", type: "text", width: 20, css: "cgst_amt"},
            {name: "sgst_percent",
                    type: "select",
                    title: "SGST %",
                    width: 25,
                    autosearch: true,
                    align: "center",
                    valueType: "number",
                    items: <?php echo $cgst_percent_options; ?>,
                    valueField: "value", // name of property of item to be used as value
                    textField: "percent",
                    css: "sgst_percent"
            },
            {name: "sgst_amt", title: "SGST Amt ", type: "text", width: 20,  css: "sgst_amt"},
            {name: "igst_percent",
                    type: "select",
                    title: "IGST %",
                    width: 25,
                    autosearch: true,
                    align: "center",
                    valueType: "number",
                    items: <?php echo $igst_percent_options; ?>,
                    valueField: "value", // name of property of item to be used as value
                    textField: "percent",
                    css: "igst_percent"
            },
            {name: "igst_amt", title: "IGST Amt ", type: "text", width: 20,  css: "igst_amt"},
            {name: "value", title: "Value", type: "text", width: 20, readOnly: true, editing: false, inserting: false},
<?php if ($can_edit == 1): ?>
                {type: "control", modeSwitchButton: false, editButton: true, width: 30}
<?php endif; ?>


            ],
            controller: {
            loadData: function (filter) {
            filter.transaction_id = $("#transaction_id").val();
            return $.ajax({
            type: "GET",
                    url: "<?php echo site_url('billing-users/transactions/sales_fetch'); ?>",
                    data: filter,
                    dataType: "json"

            });
            },
                    insertItem: function (item) {
                    save_data(item, "add");
                    },
                    updateItem: function (item) {
                    save_data(item, "update");
                    },
                    deleteItem: function (item) {
                    save_data(item, "delete");
                    }

            },
            onItemInserting: function (item) {
            item.transaction_id = $("#transaction_id").val();
            return item;
            },
            onItemUpdating: function (item) {
            item.transaction_id = $("#transaction_id").val();
            return item;
            },
            onDataLoaded: function (args) {

            if (args.data.product_options[0]) {


            }
            // cancel loading data if 'name' is empty
            $("#products_total_value").val(args.data.total_value);
            calculate_tax();
            select2Initiate();
            },
            onDataLoading: function (args) {

            // cancel loading data if 'name' is empty

            }
    });
    select2Initiate();
    /*
     // Add row Deleterow
     $(".insert_btn").on("click",function(){
     $(".jsgrid-insert-row").fadeIn(10);
     $(".close_btn").css("display","inline-block");
     $(this).css("display","none");
     });
     $(".close_btn").on("click",function(){
     $(".jsgrid-insert-row").fadeOut(0);
     $(".close_btn").css("display","none");
     $(".insert_btn").css("display","inline-block");
     });
     
     */


    $("#productsGrid").on('change', '.product_td>select', function () {
    //  $(this).attr('readonly',true);
    //  $("#productsGrid .percent_td>input").attr("readonly",true);
    get_cost_price($(this).val());
    });
    $("#productsGrid").on('keyup', '.discount_td>input', function () {
  
     get_disc_price($(this).val());
    
    });
     $("#productsGrid").on('keyup', '.price_td>input', function () {
  
     get_disc_price($(this).val());
    
    });
     $("#productsGrid").on('keyup', '.qty_td>input', function () {
  
     get_disc_price($(this).val());
    
    });
    $("#productsGrid").on('change', '.cgst_percent>select', function () {
    val = $(this).val();
    $("#productsGrid .sgst_percent>select").val(val);
    $("#productsGrid .igst_percent>select").val(0);
    tax = cal_gst(val);
    $("#productsGrid .cgst_amt>input").val(tax);
    $("#productsGrid .sgst_amt>input").val(tax);
    $("#productsGrid .igst_amt>input").val(0);
    });
    $("#productsGrid").on('change', '.igst_percent>select', function () {
    val = $(this).val();
    $("#productsGrid .sgst_percent>select").val(0);
     $("#productsGrid .cgst_percent>select").val(0);
    $("#productsGrid .igst_percent>select").val(val);
    tax = cal_gst(val);
    $("#productsGrid .cgst_amt>input").val(0);
    $("#productsGrid .sgst_amt>input").val(0);
    $("#productsGrid .igst_amt>input").val(tax);
    });
    $("#productsGrid").on('keyup', '.percent_td>input', function () {
    calculate_price($(this).val());
    });
    $("#tax_percent,#tax_type_id").on("change", function () {
    calculate_tax();
    });
    $("#discount_percentage").on("keyup", function () {
    calculate_tax();
    });
    });
    $("#contact_type_id").on("change", function () {

    get_contact_options($(this).val());
    });
    function get_disc_price(percentage){
     $('.price_td>input').each(function () {
    
    if ($(this).val() != ""){
    price_value = $(this).val();
    }
    });
    $('.qty_td>input').each(function () {
    qty=1;
    if ($(this).val() != ""){
    qty = $(this).val();
    }
    });

   taxable_value=price_value*qty;
   taxable_value=taxable_value-((taxable_value/100)*percentage);
   $(".taxable_td input").val(taxable_value);
    }
    function cal_gst(percentage){
    $('.taxable_td>input').each(function () {
    
    if ($(this).val() != ""){
    taxable_value = $(this).val();
    }
    });
   
   
  
    tax = ((taxable_value / 100) * percentage);
    return tax;
    }
    function select2Initiate() {

    $(".product_td>select").select2({
    ajax: {
    url: "<?php echo site_url('/billing-users/transactions/fetch_sales_products') ?>",
            dataType: 'json',
            method: "post",
            delay: 0,
            data: function (params) {
            return {
            q: params.term, // search term
                    page: params.page
            };
            },
            initSelection: function (element, callback) {

            },
            processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;
            return {
            results: data.items,
                    pagination: {
                    more: (params.page * 30) < data.total_count
                    }
            };
            },
            cache: true
    },
            escapeMarkup: function (markup) {
            return markup;
            }, // let our custom formatter work
            minimumInputLength: 1

    });
    }

    function calculate_tax() {
    discount_per = 0;
    products_total = $("#products_total_value").val();
    $(".products_total_value").html(products_total);
    tax_percent = $("#tax_percent").val();
    if ($("#discount_percentage").val() !== "") {
    if ($("#discount_percentage").val() > 0) {
    discount_per = $("#discount_percentage").val();
    }
    }


    discount_total = (parseFloat(products_total) / 100) * discount_per;
    $("#discount_total").val(discount_total);
    $("#discount_total").attr("readonly", "readonly");
    products_total_after_discount = products_total - discount_total;
    total = (parseFloat(products_total_after_discount) / 100) * tax_percent;
    $(".total_tax").html(total.toFixed(2));
    $(".without_tax").html(products_total_after_discount.toFixed(2));
    with_tax = parseFloat(total) + parseFloat(products_total_after_discount.toFixed(2));
    $(".with_tax").html(with_tax.toFixed(2));
    $("#grand_total").val(with_tax.toFixed(2));
    }
    function get_contact_options(type) {

    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/transactions/get_contacts'); ?>",
            data: {type_id: type, business_type: 1},
            dataType: "json",
            success: function (response) {
            $('#business_contact_id').find('option')
                    .remove()
                    .end()
                    .append('')
                    .val('');
            generate_contact_select(response);
            }

    });
    }
    function generate_contact_select(json_res) {
    $('#business_contact_id').append($('<option>').text("Select Options").attr('value', ""));
    $.each(json_res, function (i, value) {
    // alert(value.SportName);
    if (value.roll_no == null) {
    value.roll_no = "";
    }
    $('#business_contact_id').append($('<option>').text(value.business_contact_name).attr('value', value.business_contact_id));
    });
    $("#business_contact_id").select2("updateResults");
<?php if ($business_contact_id != ""): ?>
        id = "<?php echo $business_contact_id ?>";
        $("#business_contact_id").val(id).trigger('change');
<?php endif; ?>
    return true;
    }


    function get_cost_price(transaction_product_id) {
    customer_type = $("#contact_type_id").val();
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/transactions/get_price'); ?>",
            data: {transaction_product_id: transaction_product_id, customer_type: customer_type},
            dataType: "json",
            success: function (response) {
            // $(".price_td input").val(response.price);
            // $(".price_td input").attr("original_price", response.price);
            //$(".price_td input").attr("mrp_price", response.mrp);
            $(".mfg_td input").val(response.mfg_date);
            $(".expiry_td input").val(response.expiry_date);
            $(".batch_td input").val(response.batch_no);
            $(".mfg_td input").attr("readonly", true);
            $(".expiry_td input").attr("readonly", true);
            $(".batch_td input").attr("readonly", true);
            if (customer_type == 3) {

            }
            else {
            //  $(".price_td input").attr("readonly", true);
            }

            }

    });
    }
    function save_data(items, type) {
    $("#return_msg").html("<i class='fa fa-spinner'></i> Loading...");
    items.transaction_id = $("#transaction_id").val();
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/transactions/save_sales'); ?>",
            data: {data: items, type: type},
            dataType: "json",
            success: function (response) {
            if (!response.id){
            alert("Quantity Not Available");
            }
            else{

            $("#productsGrid").jsGrid('loadData');
            }


            }

    });
    }


</script>
<style type="text/css">
    body{

    }
    #return_msg{
        display: none;
    }
    .margin-top{
        margin-top: 10px;
    }
    .tally_background,.jsgrid-grid-header{
        background-color: wheat !important;
    }
    .percent select{
        width: 50px;
    }
</style>