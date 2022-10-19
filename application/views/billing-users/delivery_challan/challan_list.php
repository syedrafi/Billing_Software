<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.theme.css'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.structure.min.css'); ?>" />

<script src="<?php echo js_url('jquery-ui.min.js') ?>"></script>
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

        </div>
    </div>
    <div style="height: 10px"></div>
    <div class="row">
        <div class="col-md-12 nopadding"
             <div id="challanGrid" >


            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#search_form").on('submit', function (e) {
            e.preventDefault();
            $("#challanGrid").jsGrid('loadData');
        });
        $("#reset").on('click', function () {
            setTimeout(function () {
                $("#challanGrid").jsGrid("clearFilter");
            }, 100);

        });
        $("#from_date_field,#to_date_field").datepicker({dateFormat: "dd-mm-yy"});
        var $grid = $("#challanGrid").jsGrid({
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
                {name: "sno", title: "S.No", type: "text", width: 25, readOnly: true, filtering: false, visible: true, inserting: false, editing: false, sorting: true, align: "left",
                    sorter: "number"},
                {name: "challan_id", title: " #", type: "text", align: "left", width: 45, readOnly: true, visible: true, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
                {name: "challan_date", title: "Date", align: "left", type: "text", width: 70},
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
                {name: "dc_no", title: "Bill no", type: "text", width: 60, align: "left"},
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
                {name: "challan_value", title: "Challan Value", type: "text", width: 70, filtering: false, align: "left"},

                {name: "edit_btn", title: "", type: "text", width: 115, filtering: false, css: "hidden-print"},
<?php if ($can_edit == 1): ?>
                    {type: "control", modeSwitchButton: true, editButton: false, width: 30, css: "hidden-print"}
<?php endif; ?>
            ],
            controller: {
                loadData: function (filter) {
                    filter.type = $("#type").val();
                    filter.from_date_field = $("#from_date_field").val();
                    filter.to_date_field = $("#to_date_field").val();
                    return $.ajax({
                        type: "GET",
                        url: "<?php echo site_url('billing-users/delivery_challan/fetch_challans'); ?>",
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
                footer_content = "<td>" + args.data.total_dc_value + "</td>";
                $(".jsgrid-grid-body .jsgrid-table").append("<tr class='footer_row'><td colspan='7'class='text-right'>Total</td>" + footer_content + "</tr>");




            },
            deleteItem: function (item) {
                save_data(item, "delete");
                $("#challanGrid").jsGrid('loadData');


            }



        });


        $("#challanGrid").on('click', ".adjust_advance", function () {
            $("#adjustModal").modal('show');
            contact_id = $(this).attr('data-contact-id');
            challan_id = $(this).attr('data-challan-id');
            $("#business_contact_id").val(contact_id);
            $("#challan_id").val(challan_id);
            $("#paymentsGrid").jsGrid('loadData');
        });

    });
    $(".jsgrid-table").addClass("table");
    $(".jsgrid-table").addClass("table-bordered");
    function save_data(items, type) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('billing-users/delivery_challan/delete'); ?>",
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



