<?php

$bill_no="";
$due_date=date('d-m-Y');
$transaction_date=$due_date;
$payment_notes="";


 $business_contact_id="";
if (isset($details)):
    $bill_no = $details['bill_no'];
    $due_date = date("d-m-Y", strtotime($details['due_date']));
    $transaction_date=date("d-m-Y", strtotime($details['transaction_date']));
    $payment_notes=$details['payment_notes'];
    $type=$details['type'];
    $business_contact_id=$details['business_contact_id'];
     $business_contact_name=$details['business_contact_name'];
endif;
?>
 <link rel="stylesheet" href="<?php echo css_url('jquery-ui.min.css')?>">

<script src="<?php echo js_url('jquery-ui.min.js'); ?>"></script>
<link href="<?php echo css_url('select2.min.css'); ?>" rel="stylesheet" />
<script src="<?php echo js_url('select2.min.js')?>"></script>
<div class="row">
 
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <h3>Payments</h3>

            <table class="table table-bordered table-responsive transaction_table">
                <thead>
                    <tr>
                       
                        <th  width="10%">Date</th>
                          <th width="7%">Sale #</th>
                        <th width="10%">User</th>
                      
                        <th  width="7%">Bill #</th>
                        <th  width="15%">Buyer Detail </th>
                        <th  width="10%">Product details  </th>
                        <th  width="15%">Bill Value  </th>
                        <th  width="15%">Due date  </th>
                       
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        
                        <td><?php echo $transaction_date; ?> <input type="hidden" value="<?php echo $type; ?>" name="type"  id="type" /></td>
                        
                        <td><?php echo $details['transaction_id']; ?><input type="hidden" name="transaction_id" id="transaction_id" value="<?php echo  $details['transaction_id']; ?>"/></td>
                        <td><?php echo get_session_data('first_name') . " " . get_session_data('middle_name'); ?></td>
                        <td><?php echo $bill_no; ?></td>
                        <td><?php echo $business_contact_name; ?></td>
                        <td><?php echo $product_details['product_names']; ?></td>
                        <td><?php echo $product_details['total']; ?></td>
                        <td><?php echo $due_date; ?></td>
                        

                    </tr>


                </tbody>
            </table>
          
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">


        </div>

    </div>
    <div id="paymentsGrid" >


    </div>
</div>
<script type="text/javascript">
   
   

    
</script>

<script type="text/javascript">
    $("document").ready(function () {



        $("#paymentsGrid").jsGrid({
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
                {name: "payment_id", title: " #", type: "number", width: 30, readOnly: true, visible: false, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
               
                
                {name: "issued_date",css:"datepicker", title: "Date of rcpt", type: "text", width: 80, validate: "required", align: "left"},
                {name: "mode_id",
                    type: "select",
                    title: "Mode ",
                    width: 110,
                    autosearch: true,
                    validate: ["required", function (value, item) {
                            item_value = item.mode_id;

                            if (item_value === 0) {
                                return false;
                            }
                            return true;
                        }],
                    align: "left",
                    valueType: "number",
                    items: <?php echo $mode_options; ?>,
                    valueField: "mode_id", // name of property of item to be used as value
                    textField: "mode_name"
                },
                        {name: "pdc_dated",css:"datepicker", title: "PDC dated", type: "text", width: 80, validate: "required", align: "left"},
                         {name: "amount", title: "Paid Amount", type: "number", width: 80, validate: "required", align: "left"},
                          {name: "ref_no", title: "CQ No/Ref no", type: "text", width: 80, validate: "required", align: "left"},
                {name: "bank_name", title: "Bank", type: "text", width: 80, validate: "required", align: "left"},
                {name: "balance", title: "Balance", type: "text", width: 50,filtering:false,inserting:false,editing:false, align: "left"},
                {name: "stmt_checked", title: "Stmt Checked", type: "checkbox", width: 50,filtering:false, align: "left"},
                 {name: "mgmt_checked", title: "Mgmt Checked", type: "checkbox", width: 80,filtering:false, align: "left"},
                {type: "control", modeSwitchButton: false, editButton: true}
            ],
            controller: {
                loadData: function (filter) {
                 filter.transaction_id=$("#transaction_id").val();
                    return $.ajax({
                        type: "GET",
                        url: "<?php echo site_url('billing-users/payments/purchase_fetch'); ?>",
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

                // cancel loading data if 'name' is empty

                $("#grand_total").val(args.data.total_value);
            }
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



       
   $(".datepicker input").datepicker({ dateFormat: 'dd-mm-yy' });

       



  


    });

    function save_data(items, type) {
        $("#return_msg").html("<i class='fa fa-spinner'></i> Loading...");
        items.transaction_id = $("#transaction_id").val();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('billing-users/payments/purchase_save'); ?>",
            data: {data: items, type: type},
            dataType: "json",
            success: function (response) {


                $("#paymentsGrid").jsGrid('loadData');



            }

        });

    }


</script>
<style type="text/css">
    #return_msg{
        display: none;
    }    

</style>