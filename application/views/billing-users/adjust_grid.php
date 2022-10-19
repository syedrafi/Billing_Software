
<link rel="stylesheet" href="<?php echo css_url('jquery-ui.min.css')?>">

<script src="<?php echo js_url('jquery-ui.min.js'); ?>"></script>
<link href="<?php echo css_url('select2.min.css'); ?>" rel="stylesheet" />
<script src="<?php echo js_url('select2.min.js')?>"></script>

   
  <div id="adjustModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Advance Payments</h4>
      </div>
      <div class="modal-body">
          <div class="col-md-12">
              <input type="hidden" name="business_contact_id" id="business_contact_id"/>
                <input type="hidden" name="transaction_id" id="transaction_id"/>
       <div id="paymentsGrid" >


    </div>
                <div id="return_msg"></div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
</div>
<script type="text/javascript">
   
   

    
</script>

<script type="text/javascript">
    $("document").ready(function () {



        $("#paymentsGrid").jsGrid({
            height: "500px",
            width: "100%",
            filtering: false,
            editing: false,
            inserting: false,
            sorting: true,
            paging: true,
            autoload: true,
            pageSize: 20,
            pageButtonCount: 5,
            pageLoading: true,
            deleteConfirm: "Do you really want to delete the User?",
            fields: [
                {name: "payment_id", title: " #", type: "number", readOnly: true, visible: false, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
                {name: "business_contact_id",
                    type: "select",
                    title: "Name ",
                  width:"100%",
                    autosearch: true,
                    validate: ["required", function (value, item) {
                            item_value = item.mode_id;

                            if (item_value === 0) {
                                return false;
                            }
                            return true;
                        }],
                    align: "left",
                    css:"business_contact_td",
                    valueType: "number",
                    items: <?php echo $contact_options; ?>,
                    valueField: "business_contact_id", // name of property of item to be used as value
                    textField: "business_contact_name"
                },
                {name: "amount",  width:"100%",title: "Paid Amount", type: "number", validate: "required", align: "left"},
                
                {name: "issued_date",css:"datepicker", title: "Issued Date", type: "text",  validate: "required", align: "left"},
                {name: "mode_id",
                    type: "select",
                    title: "Mode ",
                   width:"100%",
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
                        {name: "pdc_dated",css:"datepicker", title: "PDC dated", type: "text",  validate: "required", align: "left", width:"100%",},
                          {name: "ref_no", width:"100%", title: "CQ No/Ref no", type: "text", validate: "required", align: "left"},
                {name: "bank_name", width:"100%", title: "Bank", type: "text",  validate: "required", align: "left",visible:false},
                  {name: "adjust", width:"100%", title: "", type: "text",   align: "left",visible:true,editing:false,filtering:false,inserting:false},
              
                {name: "stmt_checked", width:"15%", title: "Stmt Checked", type: "checkbox",filtering:false, align: "left",visible:false},
                 {name: "mgmt_checked", width:"15%", title: "Mgmt Checked", type: "checkbox", filtering:false, align: "left",visible:false},
                
            ],
            controller: {
                loadData: function (filter) {
     
                 filter.business_contact_id=$("#business_contact_id").val();
                    return $.ajax({
                        type: "GET",
                        url: "<?php echo site_url('billing-users/advance_payments/fetch'); ?>",
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
$(".business_contact_td select").select2({width:"100%"});
                // cancel loading data if 'name' is empty
  $(".jsgrid-table").css("width", "95%");
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

       



  
$("#paymentsGrid").on("click",".adjust_btn",function(){
payment_id=$(this).attr('data-payment-id');
transaction_id=$("#transaction_id").val();
update_payment(payment_id,transaction_id);

})

    });


function update_payment(payment_id,transaction_id)
{
  $("#return_msg").html("<i class='fa fa-spinner'></i> Loading...");
      
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('billing-users/advance_payments/update'); ?>",
            data: {transaction_id: transaction_id, payment_id: payment_id},
            dataType: "json",
            success: function (response) {
 $("#return_msg").html(response.msg);
  $("#return_msg").fadeIn();
 
  setTimeout(function () {
                    $('#adjustModal').modal('hide');
                }, 2000);
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