<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.theme.css'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.structure.min.css'); ?>" />

<script src="<?php echo js_url('jquery-ui.min.js')?>"></script>
<div class="row">
    <h3><?php echo $title; ?></h3>
    <div class="row">

        <div class="col-md-12 ">
            <form name="search_form" id='search_form' class="hidden-print">
                <div class="col-md-3">
                    <input type="text" name="from_date_field" id="from_date_field" class="form-control" placeholder="From Date" required="required"/>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control"   id="to_date_field"  name="to_date_field" placeholder="To Date" required="required"/>
                </div>

                <div class="col-md-1 hidden-print">
                    <input type="submit" class="form-control btn-primary btn btn-sm " id="search" value="Go"/>

                </div>
                <div class="col-md-1 hidden-print">
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
    <div class="row">
        <div class="col-md-12 nopadding"
    <div id="transactionsGrid" >


    </div>
    </div>
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
            width: "100%",
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
                {name: "sno", title: "S.No", type: "text", width: 25, readOnly: true, filtering: false, visible: true, inserting: false, editing: false, sorting: true,align:"left",
                    sorter: "number"},
                {name: "transaction_id", title: " #", type: "text",align:"left", width: 45, readOnly: true, visible: true, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
                {name: "transaction_date", title: "Date",align:"left", type: "text", width: 70},
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
                {name: "user_id",
                    type: "select",
                    
                    title: "<?php echo $by_title; ?>",
                    width: 80,
                    autosearch: true,
                    align: "left",
                    valueType: "number",
                    items: <?php echo $user_options; ?>,
                    valueField: "user_id", // name of property of item to be used as value
                    textField: "uname",
                },
                {name: "bill_no", title: "Bill no", type: "text", width: 60,align:"left"},
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
                {name: "bill_value", title: "Bill Value", type: "text", width: 70, filtering: false,align:"left"},
                {name: "freight_charges", title: "Freight Charges", type: "text", width: 70, filtering: false,align:"left",visible:"<?php echo $freight_visible; ?>"},
                  {name: "transport_charges", title: "Transport Charges", type: "text", width: 70, filtering: false,align:"left",visible:"<?php echo $transport_visible; ?>"},
              
                {name: "due_date", title: "Due Date", type: "text",align:"left", width: 80,visible :false},
                {name: "payment_notes", title: " Notes", type: "text", align:"left",width: 80},
                {name: "payment_status", title: "Status", type: "text", width: 40,align:"left",css:"hidden-print", filtering: false, visible: false, cellRenderer: function (value, item) {
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
                {name: "edit_btn", title: "", type: "text", width: 115, filtering: false, css:"hidden-print"},
     <?php if ($can_edit == 1): ?>
              {type: "control", modeSwitchButton: true, editButton: false, width: 30,css:"hidden-print"}
<?php endif; ?>           
    
    
            ],
            controller: {
                loadData: function (filter) {
                    filter.type = $("#type").val();
                    filter.from_date_field = $("#from_date_field").val();
                    filter.to_date_field = $("#to_date_field").val();
                    return $.ajax({
                        type: "GET",
                        url: "<?php echo site_url('billing-users/transactions/fetch_transactions'); ?>",
                        data: filter,
                        dataType: "json"

                    });

                }},
            onDataLoading: function (args) {



            },
            onDataLoaded: function (args) {
                    $(".jsgrid-table").addClass("table");
                     $(".jsgrid-table").addClass("table-bordered");
                     
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

    });
    $(".jsgrid-table").addClass("table");
  $(".jsgrid-table").addClass("table-bordered");
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
<style type="text/css">
 @media print{
     .jsgrid-filter-row{
         display: none;
     }
    }
</style>
    <?php
$this->load->view('billing-users/transactions/adjust_grid');


