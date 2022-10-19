<?php
$due_date = date('d-m-Y');
$challan_date = $due_date;
$payment_notes = "";
$challan_id = "";
$discount_per = 0;
$transport_charges = 0;
$freight_charges = 0;
$user_id = get_session_data('user_id');
$business_contact_id = "";

$created_date = date('d-m-Y');

$dispatched_to = "";
$order_no = "";
if (isset($challan_details)):
    $challan_id = $challan_details['challan_id'];
    $dispatched_to = $challan_details['dispatched_to'];
    $order_no = $challan_details['order_no'];
    $dc_no = $challan_details['dc_no'];
    $challan_date = date("d-m-Y", strtotime($challan_details['challan_date']));
    $business_contact_id = $challan_details['business_contact_id'];
    $created_date = $challan_details['created_on'];
    $user_id = $challan_details['user_id'];

endif;
?>
<link rel="stylesheet" href="<?php echo css_url('jquery-ui.min.css') ?>">

<script src="<?php echo js_url('jquery-ui.min.js'); ?>"></script>
<link href="<?php echo css_url('select2.min.css'); ?>" rel="stylesheet" />
<script src="<?php echo js_url('select2.min.js') ?>"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.theme.css'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.structure.min.css'); ?>" />
<div class="row tally_background">
    <?php echo form_open(current_url(), array('method' => 'post', 'id' => 'challan_form')); ?>
    <div class="row">
        <div class="col-md-12 col-xs-6">

            <div class='col-md-5 col-xs-12 pull-left'>
                <h3 class="">Challan<span id='challan_id_span ' class="hidden"><?php echo $dc_no; ?></span>
                    <input type="hidden" value="<?php echo $challan_id; ?>" name="challan_id"  required="required" id="challan_id" class="form-control" readonly="readonly" /></h3>
                <div class='col-md-6 nopadding'>Challan No.</div>  <div class='col-md-6'> <span id='challan_id_span'><?php echo $dc_no; ?></span></div>
                <div style="clear: both; height: 10px;"></div>
                <div style="clear: both; height: 10px;"></div>
                <div class='col-md-6 nopadding'>Party  Name</div>  <div class='col-md-6'><?php echo form_dropdown('business_contact_id', $contact_options, $business_contact_id, "class='form-control', id='business_contact_id', required='required'"); ?></div>
                <div style="clear: both; height: 10px;"></div>
                <div class='col-md-6 nopadding hidden'>Bill No.</div>  <div class='col-md-6 hidden'><input type="text" value="<?php echo $dc_no; ?>" name="bill_no" class="form-control"  readonly="readonly"/></div>



   <!--<div class='col-md-6 nopadding'>Due Date </div>  <div class='col-md-6'><input type="text" value="<?php echo $due_date; ?>" name="due_date"id='due_date' class="form-control"  /></div>
   <div style="clear: both; height: 10px;"></div>-->
                <div style="clear: both; height: 10px;"></div>
                <div class='col-md-6 nopadding'>Dispatched to </div>  <div class='col-md-6'><input type="text" name="dispatched_to" id="dispatched_to" class="form-control" value="<?php echo $dispatched_to; ?>"/></div>
                <div style="clear: both; height: 10px;"></div>

                <div class='col-md-6 nopadding'>Order No </div>  <div class='col-md-6'><input type="text" name="order_no" id="order_no" class="form-control" value="<?php echo $order_no; ?>"/></div>
                <div style="clear: both; height: 10px;"></div>



                <div class='col-md-6 nopadding'>Done By </div>  <div class='col-md-6'><?php echo form_dropdown('user_id', $user_options, $user_id, "class='form-control'"); ?></div>
                <div style="clear: both; height: 10px;"></div>
            </div>

            <div class='col-md-7 col-xs-6  '>
                <div class='col-md-12'><div class='' style="float: right;margin:5px;" ><div class="col-md-12 text-center">
                            <span id="return_msg"></span>
                            <button id="challan_save" type="submit" class="btn-success text-center "><i class="fa fa-save fa-2x"></i> </button>

                            <button  id="challan_cancel" type="button" class="btn-danger text-center"> <i class="fa fa-times fa-2x"></i> </button>
                        </div></div></div>
                <input type="text"class='col-md-5 pull-right margin-top'   value="<?php echo $challan_date; ?>" name="challan_date"  id='challan_date'class="form-control" /> 
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
                            <input type="hidden" value="<?php echo $dc_no; ?>" name="dc_no"  required="required" id="dc_no" class="form-control" readonly="readonly" />
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
                <input type="hidden" value="" name="grand_total" id="grand_total" class="form-control" readonly="readonly" />  
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">


        </div>

    </div>
    <div id="productsGrid" >


    </div>
    <div style="clear: both"></div>

    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
    $(document).ready(function () {
    $("#due_date,#challan_date").datepicker({dateFormat: "dd-mm-yy"});
    $("#business_contact_id").select2();
    if ($("#challan_id").val() == "") {
    generate_challan_id();
    }

    $("#challan_form").on('submit', function (e) {
    e.preventDefault();
    save_challan();
    });
    $("#challan_cancel").on("click", function () {
    if (confirm('Are your Sure to cancel Sales?')) {
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/delivery_challan/delete'); ?>",
            dataType: "json",
            data: {challan_id: $("#challan_id").val()},
            success: function (response) {
            $("#return_msg").fadeIn();
            $("#return_msg").html(response.msg);
            window.location = "<?php echo base_url('index.php/billing-users/delivery_challan/list'); ?>";
            }

    });
    }
    else {
    return;
    }

    });
    });
    function generate_challan_id() {
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/delivery_challan/add'); ?>",
            dataType: "json",
            success: function (response) {
            if (response.id) {
            $("#challan_id").val(response.id);
            }
            else {
            alert(response.msg);
            }
            }

    });
    }

    function save_challan() {
    $("#return_msg").fadeIn();
    $("#return_msg").html("<i class='fa fa-spinner'></i> Loading...");
    data = $("#challan_form").serializeArray();
  
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/delivery_challan/save_challan'); ?>",
            data:data,
            dataType: "json",
            success: function (response) {
            $("#return_msg").fadeIn();
            $("#return_msg").html(response.msg);
            if (response.id) {

            window.location = "<?php echo base_url('index.php/billing-users/delivery_challan/print_receipt/'); ?>/" + response.id;
           
            }
            else {
            alert(response.msg);
            }
            }
    });
    }

</script>

<script type="text/javascript">
    $("document").ready(function () {

    $("#contact_type_id").focus().select();
    // get_contact_options($("#contact_type_id").val());
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
            {name: "challan_product_id", title: " #", type: "number", width: 30, readOnly: true, visible: false, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
            {name: "product_id",
                    type: "select",
                    title: "Search",
                    editing: false,
                    inserting: true,
                    items: "",
                    width: 60,
                    autosearch: true,
                    
                    align: "left",
                    valueType: "number",
                    valueField: "challan_product_id", // name of property of item to be used as value
                    textField: "product_name",
                    css: "product_td"

            },
            {name: "product_name", title: "Name", type: "text", width: 40, align: "left", css: "name_td", inserting: false, editing: false},
            {name: "batch_no", title: "Batch #", type: "text", width: 25, align: "left", css: "batch_td"},
            {name: "mfg_date", title: "Mfg Date", type: "text", width: 25, align: "left", css: "mfg_td"},
            {name: "expiry_date", title: "Expiry", type: "text", width: 25, align: "left", css: "expiry_td"},
            {name: "qty", title: "Qty", type: "text", width: 20, validate: "required", css: "qty_td"},
            {name: "price_per_unit", title: "Price", type: "text", width: 20, validate: "required", css: "price_td"},
            {name: "is_returned",
                    type: "select",
                    title: "Returned",
                    width: 10,
                    autosearch: true,
                      editing: true,
                    inserting: false,
                    align: "left",
                    valueType: "number",
                    items: <?php echo $return_options; ?>,
                    valueField: "return_id", // name of property of item to be used as value
                    textField: "return_name"
                },
            {name: "value", title: "Value", type: "text", width: 20, readOnly: true, editing: false, inserting: false},
<?php if ($can_edit == 1): ?>
                {type: "control", modeSwitchButton: false, editButton: true, width: 30}
<?php endif; ?>


            ],
            controller: {
            loadData: function (filter) {
            filter.challan_id = $("#challan_id").val();
            return $.ajax({
            type: "GET",
                    url: "<?php echo site_url('billing-users/delivery_challan/single_fetch'); ?>",
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
            item.challan_id = $("#challan_id").val();
            return item;
            },
            onItemUpdating: function (item) {
            item.challan_id = $("#challan_id").val();
            return item;
            },
            onDataLoaded: function (args) {

            if (args.data.product_options[0]) {


            }
            // cancel loading data if 'name' is empty
            $("#products_total_value").val(args.data.total_value);
            $(".products_total_value").html(args.data.total_value);
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
    $("#productsGrid").on('keyup', '.percent_td>input', function () {
    calculate_price($(this).val());
    });
    });
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

    function save_data(items, type) {
    $("#return_msg").html("<i class='fa fa-spinner'></i> Loading...");
    items.challan_id = $("#challan_id").val();
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/delivery_challan/save_products'); ?>",
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
    function get_cost_price(transaction_product_id) {
    customer_type = $("#contact_type_id").val();
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/transactions/get_price'); ?>",
            data: {transaction_product_id: transaction_product_id, customer_type: 1},
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
        background-color: #dff0d8 !important;
    }
    .percent select{
        width: 50px;
    }
</style>