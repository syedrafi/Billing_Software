<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.theme.css'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.structure.min.css'); ?>" />

<link rel="stylesheet" href="<?php echo css_url('jquery-ui.min.css')?>">

<script src="<?php echo js_url('jquery-ui.min.js'); ?>"></script>

<div class="row">
    <h3><?php echo $title; ?></h3>
    <div class="row">

        <div class="col-md-12">
            <form name="search_form" id='search_form'>
                <div class="col-md-3">
                    <input type="text" name="from_date_field" id="from_date_field" class="form-control" placeholder="From Date" required="required"/>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control"   id="to_date_field"  name="to_date_field" placeholder="To Date" required="required"/>
                </div>

                <div class="col-md-1">
                    <input type="submit" class="form-control btn-primary btn btn-sm " id="search" value="Go"/>

                </div>
                <div class="col-md-1">
                    <input type="reset" class="form-control btn-warning btn btn-sm " id="reset" value="Clear"/>
                </div>

            </form>

            <!--<div class="col-md-12 btn_holder">
            <button class="insert_btn btn btn-sm btn-success"> <i class="fa fa-plus"></i> Add</button>
             <button class="close_btn btn btn-sm btn-danger"> <i class="fa fa-plus"></i> Close</button>
            </div>-->
            <input type="hidden" name="type" id="type" value="<?php echo $trans_type; ?>"/>
        </div>
    </div>
    <div style="height: 10px"></div>
    <div id="transactionsGrid" >


    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#search_form").on('submit', function (e) {
            e.preventDefault();
            $("#transactionsGrid").jsGrid('loadData');
        });
        $("#reset").on('click', function () {


            setTimeout(function () {
                $("#transactionsGrid").jsGrid("clearFilter");
            }, 100);



        });
        $("#from_date_field,#to_date_field").datepicker({dateFormat: "dd-mm-yy"});
        var $grid = $("#transactionsGrid").jsGrid({
            height: "500px",
            width: "98%",
            filtering: true,
            editing: false,
            deleting: true,
            inserting: false,
            sorting: true,
            paging: true,
            autoload: true,
            pageSize: 200,
            pageButtonCount: 10,
            pageLoading: true,
            deleteConfirm: "Do you really want to delete the User?",
            fields: [
                 {name: "transaction_date", title: "Date", type: "text", width: 70},
                {name: "sno", title: "S.No", type: "text", width: 25, readOnly: true, filtering: false, visible: false, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
                {name: "transaction_id", title: " #", type: "text", width: 45, readOnly: true, visible: true, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
                
               
                {name: "created_by",
                    type: "select",
                    title: "Created by",
                    width: 80,
                    autosearch: true,
                    align: "left",
                    valueType: "number",
                    items: <?php echo $user_options; ?>,
                    valueField: "user_id", // name of property of item to be used as value
                    textField: "uname"
                },
               
                {name: "bill_no", title: "Bill no", type: "text", width: 40},
                {name: "business_contact_id",
                    type: "select",
                    title: "<?php echo $business_contact_heading; ?>",
                    width: 120,
                    autosearch: true,
                    align: "left",
                    valueType: "number",
                    items: <?php echo $business_contact_options; ?>,
                    valueField: "business_contact_id", // name of property of item to be used as value
                    textField: "business_contact_name",
                },
                {name: "bill_value", title: "Bill Value", type: "text", width: 60, filtering: false},
                {name: "receivable", title: "<?php echo $balance_label; ?>", type: "text", width: 60, filtering: false},
                {name: "due_date", title: "Due Date", type: "text", width: 60},
               
                {name: "payment_status", title: "Status", type: "text", width: 40, filtering: false, visible: true, cellRenderer: function (value, item) {
                        color = "white";
                        if (value == 1) {
                            color = "green";
                            label_value = "Paid";
                        }
                        if (value == 0) {
                            color = "red";
                            label_value = "Not Paid";
                        }
                        if (value == 2) {
                            color = "yellow";
                            label_value = "PDC Given";
                        }

                        return "<td  class='" + color + "'><span>" + label_value + "</span></td>";

                    }},
                         {name: "payment_details",  filtering:false,type: "text", width: 380,itemTemplate:function(value,item){
                        return   generate_payment_table(value,item);
                           
                         },headerTemplate:function(){
                           return generate_payment_header();  
                         } }
                
                
            ],
            controller: {
                loadData: function (filter) {
                    filter.type = $("#type").val();
                    filter.from_date_field = $("#from_date_field").val();
                    filter.to_date_field = $("#to_date_field").val();
                    return $.ajax({
                        type: "GET",
                        url: "<?php echo site_url('billing-users/payment_details/fetch_transactions'); ?>",
                        data: filter,
                        dataType: "json"

                    });

                }},
            onDataLoading: function (args) {



            },
            onDataLoaded: function (args) {
                parent = $(".red").parent("tr");
                parent.children("td").css("background", "#f2dddc");
                parent = $(".green").parent("tr");
                parent.children("td").css("background", "#eaf1dd");
                parent = $(".yellow").parent("tr");
                parent.children("td").css("background", "#dbeef3");
                footer_content = "<td>" + args.data.total_bill_value + "</td>";
                $(".jsgrid-grid-body .jsgrid-table").append("<tr class='footer_row'><td colspan='7'class='text-right'>Total</td>" + footer_content + "</tr>");




            },
            deleteItem: function (item) {
                save_data(item, "delete");
                $("#transactionsGrid").jsGrid('loadData');


            }



        });

        /*
         $("#type").on('change',function(){
         $("#transactionsGrid").jsGrid('loadData');
         
         });
         */

        $("#transactionsGrid").on('click', ".adjust_advance", function () {
            $("#adjustModal").modal('show');
            contact_id = $(this).attr('data-contact-id');
            transaction_id = $(this).attr('data-transaction-id');
            $("#business_contact_id").val(contact_id);
            $("#transaction_id").val(transaction_id);
            $("#paymentsGrid").jsGrid('loadData');
        });
         $(".jsgrid-table").addClass("table");
  $(".jsgrid-table").addClass("table-bordered");

    });

function generate_payment_header(){
return "<table class='table table-bordered'><tr><td width='15%'>rcvd Amount</td><td width='34%'>Date of rcpt</td><td width='7%'>Mode</td><td width='34%'>PDC Dtd</td><td width='10%'>CQ no/Ref #</td><td width='7%'>Bank</td><td width='10'>Balance</td><td width='5%'>Stmt Check</td><td width='5%'>Mgmt Check</td></tr>";
}

function generate_payment_table(value,item){
$p_table="<table class='table table-bordered'>";
/*
if(item.sno==1){
$p_table="<table><tr><td>rcvd Amount</td><td>Date of rcpt</td><td>Mode</td><td>PDC Dtd</td><td>CQ no/Ref #</td><td>Bank</td><td>Balance</td><td>Stmt Check</td><td>Mgmt Check</td></tr>";
}
*/
$.each(value, function(key,details) {
$p_table=$p_table+"<tr>";
$p_table=$p_table+"<td width='15%'>"+details.amount+"</td>";
$p_table=$p_table+"<td width='15%'>"+details.issued_date+"</td>";
$p_table=$p_table+"<td width='7%'>"+details.mode_name+"</td>";
$p_table=$p_table+"<td width='15%'>"+details.pdc_dated+"</td>";
$p_table=$p_table+"<td width='10%'>"+details.ref_no+"</td>";
$p_table=$p_table+"<td width='7%'>"+details.bank_name+"</td>";
$p_table=$p_table+"<td width='10%'>"+details.balance+"</td>";
$p_table=$p_table+"<td width='5%'>"+details.stmt_checked+"</td>";
$p_table=$p_table+"<td width='5%'>"+details.mgmt_checked+"</td>";
$p_table=$p_table+"</tr>";
}); 
$p_table=$p_table+"</table>";
return $p_table;
}

    function save_data(items, type) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('billing-users/transactions/delete'); ?>",
            data: {data: items, type: type},
            dataType: "json",
            success: function (response) {
                //$("#productsGrid").jsGrid('loadData');

                //   $grid.jsGrid("refresh");



            }

        });

    }

</script>
<?php
//$this->load->view('billing-users/transactions/adjust_grid');

