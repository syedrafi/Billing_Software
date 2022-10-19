<?php
$bill_no = "";
$due_date = date('d-m-Y');
$transaction_date = $due_date;
$payment_notes = "";

$tax_type_id = "";
$tax_percent = "";
$business_contact_id = "";
$user_id = get_session_data('user_id');
$transport_charges = "";
$freight_charges = "";
if (isset($transaction_details)):
    $bill_no = $transaction_details['bill_no'];
    $user_id = $transaction_details['user_id'];

    if ($transaction_details['due_date'] != "" && $transaction_details['due_date'] != '0000-00-00'):
        $due_date = date("d-m-Y", strtotime($transaction_details['due_date']));
    else:
        $due_date = "";

    endif;
    $transaction_date = date("d-m-Y", strtotime($transaction_details['transaction_date']));
    $payment_notes = $transaction_details['payment_notes'];
    $type = $transaction_details['type'];
    $transport_charges = $transaction_details['transport_charges'];
    $freight_charges = $transaction_details['freight_charges'];
    $business_contact_id = $transaction_details['business_contact_id'];
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

<div class="row">
    <?php echo form_open(current_url(), array('method' => 'post', 'id' => 'transaction_form')); ?>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class='col-md-12'> <h3 style="float: left;"><?php echo $title; ?></h3><div class='' style="float: right;margin:5px;" ><div class="col-md-12 text-center">
                        <span id="return_msg"></span>
                        <button id="transaction_save" type="submit" class="btn-success text-center "><i class="fa fa-save fa-2x"></i> </button>

                        <button  id="transaction_cancel" type="button" class="btn-danger text-center"> <i class="fa fa-times fa-2x"></i> </button>
                    </div></div></div>

            <table class="table table-bordered table-responsive transaction_table">
                <thead>
                    <tr>

                        <th  width="7%">Bill No.</th>
                        <th  width="15%"><?php echo $business_contact_heading; ?>  </th>
                        <th  width="10%">Date</th>
                        <th class="hidden" width="10%">User</th>
                        <th class="hidden" width="10%">Purchased by</th>

                        <th  width="10%" class="hidden">Payment Due Date  </th>
                        <th  width="10%">Payment Notes  </th>
                        <th  width="17%">Grand Total  </th>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" value="<?php echo $bill_no; ?>" name="bill_no" class="form-control" /></td>

                        <td><?php echo form_dropdown('business_contact_id', $contact_options, $business_contact_id, "class='form-control', required='required' id='business_contact_id'"); ?></td>
                        <td><input type="text" value="<?php echo $transaction_date; ?>" name="transaction_date" class="form-control" id="transaction_date" /> <input type="hidden" value="<?php echo $type; ?>" name="type"  id="type" /></td>
                        <td class="hidden"><?php echo get_session_data('first_name') . " " . get_session_data('middle_name'); ?></td>
                        <td class="hidden"><?php echo form_dropdown('user_id', $user_options, $user_id, "class='form-control'"); ?><input type="hidden" value="<?php echo $transaction_id; ?>" name="transaction_id"  required="required" id="transaction_id" class="form-control" readonly="readonly" /></td>


                        <td class="hidden"><input type="text" value="<?php echo $due_date; ?>" name="due_date" id="due_date" class="form-control"  /></td>
                        <td><textarea name="payment_notes"><?php echo $payment_notes; ?></textarea></td>
                        <td>
                            <div class="row">
                                <label class="col-md-3">Freight</label><div class="col-md-9"><input type="text" name="freight_charges" value="<?php echo $freight_charges; ?> " id="freight_charges"/>
                                    <input type="hidden" name="transport_charges" value="<?php echo $transport_charges; ?> " id="transport_charges"/>
                                </div>

                            </div>
                            <div style="height: 10px;"></div>
                            <div class="col-md-3 nopadding hidden">
                                <?php echo form_dropdown('tax_type_id', $tax_options, $tax_type_id, "class='tax_type_id'  id='tax_type_id'"); ?>
                            </div>
                            <div class="col-md-3 hidden">
                                <?php echo form_dropdown('tax_percent', array(), "", "id='tax_percent'"); ?>
                            </div>
                            <div class="col-md-6 total_tax text-right  hidden">
                                0
                            </div>
                            <div style="height: 5px; clear: both; border-bottom: 1px solid #ddd"></div>
                            <div class="col-md-6 without_tax hidden"></div>
                            <div class="col-md-6 with_tax text-right"></div>
                            <input type="hidden" value="" name="grand_total" id="grand_total" class="form-control" readonly="readonly" /></td>


                    </tr>


                </tbody>
            </table>
            <?php echo form_close(); ?>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">


        </div>

    </div>
    <div id="productsGrid" >


    </div>
</div>
<script type="text/javascript">
    var PriceVal = {};
    $(document).ready(function () {
    generate_tax_options();
    $("#tax_type_id").on("change", function () {

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
    if (confirm('Are your Sure to cancel Purchase?')) {
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/transactions/delete'); ?>",
            dataType: "json",
            data: {transaction_id: $("#transaction_id").val()},
            success: function (response) {
            $("#return_msg").fadeIn();
            $("#return_msg").html(response.msg);
            window.location = "<?php echo base_url('index.php/billing-users/transactions/transaction_list/purchase'); ?>";
            }

    });
    } else {
    return;
    }

    });
    });
    function generate_tax_options() {

    $('#tax_percent').find('option')
            .remove()
            .end()
            .append('')
            .val('');
    tax_type = $("#tax_type_id").val();
    if (tax_type == 1) {

    $('#tax_percent').append($('<option>').text("5%").attr('value', "12"));
    $('#tax_percent').append($('<option>').text("14.5%").attr('value', "14.5"));
    } else if (tax_type == 2) {


    $('#tax_percent').append($('<option>').text("5%").attr('value', "5"));
    $('#tax_percent').append($('<option>').text("2%").attr('value', "2"));
    }
    else if (tax_type == 3) {
    $('#tax_percent').append($('<option>').text("0%").attr('value', "0"));
    $('#tax_percent').append($('<option>').text("12%").attr('value', "12"));
    //  $('#tax_percent').append($('<option>').text("2%").attr('value', "2"));
    }
    }
    function generate_transaction_id() {
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/transactions/add'); ?>",
            dataType: "json",
            data: {type:<?php echo $type; ?>},
            success: function (response) {
            if (response.id) {
            $("#transaction_id").val(response.id);
            } else {
            alert(response.msg);
            }
            }

    });
    }

    function save_transaction() {
    $("#return_msg").fadeIn();
    $("#return_msg").html("<a class='loading-alert'><i class='fa fa-refresh fa-spin fa-2x'></i> </a>");
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/transactions/save_transaction'); ?>",
            data: $("#transaction_form").serializeArray(),
            dataType: "json",
            success: function (response) {
            $("#return_msg").fadeIn();
            if (response.id) {
            div_html = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>" + response.msg + "</strong></div>";
            $("#return_msg").html(div_html);
            setTimeout(function () {
            window.location = "<?php echo site_url('billing-users/transactions/transaction_list/purchase'); ?>";
            }, 1000);
            } else {
            div_html = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>" + response.msg + "</strong></div>";
            $("#return_msg").html(div_html);
            }

            }

    });
    }
</script>

<script type="text/javascript">
    $("document").ready(function () {



    $("#productsGrid").jsGrid({
    height: "500px",
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
                    width: 80,
                    autosearch: true,
                    align: "left",
                    valueType: "number",
                    valueField: "transaction_product_id", // name of property of item to be used as value
                    textField: "product_name",
                    css: "product_td"

            },
            {name: "product_name", title: "Name", type: "text", width: 50, align: "left", css: "name_td", inserting: false, editing: false},
            {name: "batch_no", title: "Batch #", type: "text", width: 30, align: "left", visible:false},
            {name: "mfg_date", title: "Mfg Date", type: "text", width: 40, align: "left", css: "mfg_td", visible:false},
            {name: "expiry_date", title: "Expiry", type: "text", width: 40, align: "left", css: "exp_td", visible:false},
            {name: "qty", title: "Qty", type: "text", width: 30, validate: "required", align: "left"},
            {name: "price_per_unit", title: "Price", type: "text", width: 30, validate: "required", css: "price_td"},
            {name: "mrp", title: "MRP", type: "text", width: 30, css: "mrp_td", visible:false},
            {name: "cgst_percent",
                    type: "select",
                    title: "CGST %",
                    width: 30,
                    autosearch: true,
                    align: "center",
                    valueType: "number",
                    items: <?php echo $cgst_percent_options; ?>,
                    valueField: "value", // name of property of item to be used as value
                    textField: "percent",
                    css: "cgst_percent"
            },
            {name: "cgst_amt", title: "CGST Amt", type: "text", width: 30, css: "cgst_amt"},
            {name: "sgst_percent",
                    type: "select",
                    title: "SGST %",
                    width: 35,
                    autosearch: true,
                    align: "center",
                    valueType: "number",
                    items: <?php echo $cgst_percent_options; ?>,
                    valueField: "value", // name of property of item to be used as value
                    textField: "percent",
                    css: "sgst_percent"
            },
            {name: "sgst_amt", title: "SGST Amt ", type: "text", width: 30, css: "sgst_amt"},
            {name: "igst_percent",
                    type: "select",
                    title: "IGST %",
                    width: 30,
                    autosearch: true,
                    align: "center",
                    valueType: "number",
                    items: <?php echo $igst_percent_options; ?>,
                    valueField: "value", // name of property of item to be used as value
                    textField: "percent",
                    css: "igst_percent"
            },
            {name: "igst_amt", title: "IGST Amt ", type: "text", width: 30, css: "igst_amt"},
            {name: "value", title: "Value", type: "text", width: 40, readOnly: true, editing: false, inserting: false},
<?php if ($can_edit == 1): ?>
                {type: "control", modeSwitchButton: false, editButton: true}
<?php endif; ?>
            ],
            controller: {
            loadData: function (filter) {
            filter.transaction_id = $("#transaction_id").val();
            return $.ajax({
            type: "GET",
                    url: "<?php echo site_url('billing-users/transactions/fetch'); ?>",
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
            $(".product_td select").select2();
            // cancel loading data if 'name' is empty
            products_total = args.data.total_value;
            tax_percent = $("#tax_percent").val();
            //   alert(tax_percent);
            total = (parseFloat(products_total) / 100) * tax_percent;
            $(".total_tax").html(total.toFixed(2));
            $(".without_tax").html(products_total.toFixed(2));
            with_tax = parseFloat(total) + parseFloat(products_total.toFixed(2));
            $(".with_tax").html(with_tax.toFixed(2));
            $("#grand_total").val(with_tax.toFixed(2));
            select2Initiate();
            }
    });
    // Globally scoped object
    $(".price_td input").on("keyup", function(){
    val = $(this).val();
    $(this).val(val);
    PriceVal.price = val;
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
    select2Initiate();
    $("#tax_percent,#tax_type_id").on("change", function () {
    $("#productsGrid").jsGrid('loadData');
    })

            $(".product_td select").select2();
    });
    function cal_gst(percentage){

    taxable_value = PriceVal.price
            tax = (taxable_value / 100) * percentage;
    return tax;
    }
    function save_data(items, type) {

    $("#return_msg").html("<i class='fa fa-spinner'></i> Loading...");
    items.transaction_id = $("#transaction_id").val();
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/transactions/save'); ?>",
            data: {data: items, type: type},
            dataType: "json",
            success: function (response) {


            $("#productsGrid").jsGrid('loadData');
            }

    });
    }

    function select2Initiate() {


    $(".product_td>select").select2({
    ajax: {
    url: "<?php echo site_url('/billing-users/transactions/fetch_purchase_products') ?>",
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
            cache: false
    },
            escapeMarkup: function (markup) {
            return markup;
            }, // let our custom formatter work
            minimumInputLength: 1

    });
    $(".mfg_td input,.exp_td input").datepicker({dateFormat: "dd-mm-yy"});
    }
</script>
<style type="text/css">
    #return_msg{
        display: none;
    }    

</style>