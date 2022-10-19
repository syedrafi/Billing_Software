
<link rel="stylesheet" href="<?php echo css_url('jquery-ui.min.css')?>">

<script src="<?php echo js_url('jquery-ui.min.js'); ?>"></script>
<link href="<?php echo css_url('select2.min.css'); ?>" rel="stylesheet" />
<script src="<?php echo js_url('select2.min.js')?>"></script>
  <h3>Advance Payments</h3>
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
            filtering: true,
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
                 {name: "sno", title: " Sno", type: "number", width: 30, readOnly: true, visible: true, inserting: false, editing: false, sorting: false,
                    sorter: "number"},
                {name: "payment_id", title: " #", type: "number", width: 30, readOnly: true, visible: true, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
                {name: "business_contact_id",
                    type: "select",
                    title: "Name ",
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
                    css:"business_contact_td",
                    valueType: "number",
                    items: <?php echo $contact_options; ?>,
                    valueField: "business_contact_id", // name of property of item to be used as value
                    textField: "business_contact_name"
                },
                {name: "amount", title: "Paid Amount", type: "number", width: 80, validate: "required", align: "left"},
                
                {name: "issued_date",css:"datepicker", title: "Issued Date", type: "text", width: 80, validate: "required", align: "left"},
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
                          {name: "ref_no", title: "CQ No/Ref no", type: "text", width: 80, validate: "required", align: "left"},
                {name: "bank_name", title: "Bank", type: "text", width: 80, validate: "required", align: "left"},
              
                {name: "stmt_checked", title: "Stmt Checked", type: "checkbox", width: 40,filtering:false, align: "left"},
                 {name: "mgmt_checked", title: "Mgmt Checked", type: "checkbox", width: 40,filtering:false, align: "left"},
                     {name: "transaction_id",
                    type: "select",
                    title: "Adjust Invoice No",
                    editing: false,
                    visible:false,
                    inserting: false,
                    filtering: false,
                    items: "",
                    width: 140,
                    autosearch: true,
                   
                    align: "left",
                    valueType: "number",
                    valueField: "transaction_id", // name of property of item to be used as value
                    textField: "bill_no",
                    css: "adjust_td"

                },
                {type: "control", modeSwitchButton: true, editButton: true}
            ],
            controller: {
                loadData: function (filter) {
                 filter.transaction_id=$("#transaction_id").val();
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
            //    item.transaction_id = $("#transaction_id").val();
                return item;
            },
            onItemUpdating: function (item) {
             
             //   item.transaction_id = $("#transaction_id").val();
                return item;
            },
            onDataLoaded: function (args) {
$(".business_contact_td select").select2({width:"100%"});
                // cancel loading data if 'name' is empty

                $("#grand_total").val(args.data.total_value);
             //    select2Initiate();
            },
             onOptionChanged: function(args) {   }
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

 $(".jsgrid-table").addClass("table");
  $(".jsgrid-table").addClass("table-bordered");

       
   $(".datepicker input").datepicker({ dateFormat: 'dd-mm-yy' });

       

$(".jsgrid").on("click",".adjust_td select",function(){

select2Initiate();
});
  


    });



    function save_data(items, type) {
        $("#return_msg").html("<i class='fa fa-spinner'></i> Loading...");
      //  items.transaction_id = $("#transaction_id").val();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('billing-users/advance_payments/save'); ?>",
            data: {data: items, type: type},
            dataType: "json",
            success: function (response) {


                $("#paymentsGrid").jsGrid('loadData');



            }

        });

    }


    function select2Initiate(value) {

        $(".adjust_td>select").select2({
            ajax: {
                url: "<?php echo site_url('/billing-users/advance_payments/fetch_transactions') ?>",
                dataType: 'json',
                method: "post",
                delay: 0,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        bcid:$(".jsgrid-edit-row .business_contact_td select").val()
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
    }

</script>
<style type="text/css">
    #return_msg{
        display: none;
    }    

</style>



   
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