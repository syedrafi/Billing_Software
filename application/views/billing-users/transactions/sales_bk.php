<?php
$bill_no = "";
$due_date = date('d-m-Y');
$transaction_date = $due_date;
$payment_notes = "";

$user_id=  get_session_data('user_id');
$business_contact_id = "";
  $tax_type_id="";
    $tax_percent="";
if (isset($transaction_details)):
    $bill_no = $transaction_details['bill_no'];
    $due_date = date("d-m-Y", strtotime($transaction_details['due_date']));
    $transaction_date = date("d-m-Y", strtotime($transaction_details['transaction_date']));
    $payment_notes = $transaction_details['payment_notes'];
    $type = $transaction_details['type'];
    $business_contact_id = $transaction_details['business_contact_id'];

    $user_id = $transaction_details['user_id'];
      $tax_type_id=$transaction_details['tax_type_id'];
    $tax_percent=$transaction_details['tax_percent'];
endif;
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script>
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

                        <th  width="10%">Date</th>
                        <th width="10%">User</th>
                         <th width="10%">Sold by</th>
                        <th width="7%">#</th>
                        <th  width="7%">B#</th>
                        <th  width="18%"><?php echo $business_contact_heading; ?>  </th>
                        <th  width="10%">Due Date  </th>
                        <th  width="10%">Payment Notes  </th>
                        <th  width="23%">Grand Total  </th>
                       
                    </tr>
                </thead>
                <tbody>
                    <tr>

                        <td><input type="text" value="<?php echo $transaction_date; ?>" name="transaction_date" class="form-control" /> <input type="hidden" value="<?php echo $type; ?>" name="type"  id="type" /></td>
                        <td><?php echo get_session_data('first_name') . " " . get_session_data('middle_name'); ?></td>
                        <td><?php echo form_dropdown('user_id', $user_options, $user_id, "class='form-control'"); ?></td>
                        <td><input type="text" value="<?php echo $transaction_id; ?>" name="transaction_id"  required="required" id="transaction_id" class="form-control" readonly="readonly" /></td>
                        <td><input type="text" value="" name="bill_no" class="form-control" /></td>
                        <td><?php echo form_dropdown('business_contact_id', $contact_options, $business_contact_id, "class='form-control', required='required'"); ?></td>
                        <td><input type="text" value="<?php echo $due_date; ?>" name="due_date" class="form-control"  /></td>
                        <td><textarea name="payment_notes"><?php echo $payment_notes; ?></textarea></td>
                       <td>
                           <div class="col-md-3 nopadding">
                                <?php echo form_dropdown('tax_type_id', $tax_options, $tax_type_id, "class='', required='required'"); ?>
                            </div>
                            <div class="col-md-3 ">
                               <?php echo form_dropdown('tax_percent', array("2"=>"2%","5.5"=>"5.5%","5" => "5%", "14.5" => "14.5%"), $tax_percent, "id='tax_percent'"); ?>
                            </div>
                            <div class="col-md-6 total_tax text-right ">
                                0
                            </div>
                            <div style="height: 5px; clear: both; border-bottom: 1px solid #ddd"></div>
                            <div class="col-md-6 without_tax"></div>
                            <div class="col-md-6 with_tax text-right"></div>
                            <input type="hidden" value="" name="grand_total" id="grand_total" class="form-control" readonly="readonly" /> 
                       </td>
                   

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
    $(document).ready(function () {

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
                        window.location = "<?php echo base_url('index.php/billing-users/transactions/transaction_list'); ?>";

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
            success: function (response) {
                if (response.id) {
                    $("#transaction_id").val(response.id);
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

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('billing-users/transactions/save_transaction'); ?>",
            data: $("#transaction_form").serializeArray(),
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
                    editing:false,
                    inserting:true,
                  items:"",
                    width: 80,
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
                {name: "product_name", title: "Name", type: "text", width: 80, align: "left", css: "name_td",inserting:false,editing:false},
                {name: "cost_price", title: "CP", type: "text", width: 40,  align: "left", css: "cp_td"},
                {name: "percentage", title: "%", type: "text", width: 30, validate: "required", align: "left", css: "percent_td"},
                {name: "qty", title: "Qty", type: "number", width: 50, validate: "required", align: "left"},
                {name: "price_per_unit", title: "Price", type: "text", width: 50, validate: "required", css: "price_td"},
                {name: "value", title: "Value", type: "text", width: 40, readOnly: true, editing: false, inserting: false},
                {type: "control", modeSwitchButton: false, editButton: true}
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

if(args.data.product_options[0]){
    
    

   


}
                // cancel loading data if 'name' is empty
   
              products_total=args.data.total_value;
                tax_percent=$("#tax_percent").val();
                
               total= (parseFloat(products_total)/100)*tax_percent;
                 $(".total_tax").html(total.toFixed(2));
                $(".without_tax").html(products_total.toFixed(2));
                with_tax = parseFloat(total) + parseFloat(products_total.toFixed(2));
                $(".with_tax").html(with_tax.toFixed(2));
                $("#grand_total").val(with_tax.toFixed(2));
        

    

         
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


        $("#productsGrid").on('change','.product_td>select', function () {
      //  $(this).attr('readonly',true);
      //  $("#productsGrid .percent_td>input").attr("readonly",true);
            get_cost_price($(this).val());
        });

        $("#productsGrid").on('keyup','.percent_td>input', function () {
            calculate_price($(this).val());
        });

    });

function select2Initiate(){

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
                         initSelection: function(element, callback) {
                           
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
    function calculate_price(percent) {
        cost_price = $(".cp_td>input").val();
      

        selling_price_profit = (cost_price / 100) * percent;

        selling_price = parseFloat(cost_price) + parseFloat(selling_price_profit);

        $(".price_td>input").val(selling_price);
    }
    function get_cost_price(transaction_product_id) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('billing-users/transactions/get_cost_price'); ?>",
            data: {transaction_product_id: transaction_product_id},
            dataType: "json",
            success: function (response) {


                $(".cp_td input").val(response.cp);
$(".cp_td input").attr("readonly",true);


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


                $("#productsGrid").jsGrid('loadData');



            }

        });

    }


</script>
<style type="text/css">
    #return_msg{
        display: none;
    }    

</style>