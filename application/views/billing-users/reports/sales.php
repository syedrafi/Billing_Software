<script src="<?php echo js_url('jquery-ui.min.js')?>"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.theme.css'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.structure.min.css'); ?>" />
<!-- Content Header (Page header) -->
<link rel="stylesheet" type="text/css" href="<?php echo css_url('combined.datatables.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo css_url('dataTables.bootstrap.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo css_url('responsive.bootstrap.min.css'); ?>"/>
<script type="text/javascript" src="<?php echo js_url('datatables.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo js_url('dataTables.responsive.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo js_url('sum().js'); ?>"></script>

<script type="text/javascript" language="javascript" class="init">
    $(document).ready(function () {
        
       
   $("#from_date_field,#to_date_field").datepicker({dateFormat: "dd-mm-yy"});
   var dtable=     $('#sales').dataTable({
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api();

                api.columns('.sum', {
                    page: 'current'
                }).every(function () {
                    var sum = this
                            .data()
                            .reduce(function (a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                    console.log(sum); //alert(sum);
                    $(this.footer()).html(parseInt(sum));
                });
            },
            "pageLength": 50,
             "autoWidth": false,
            dom: 'Bfrtip',
            buttons: [
                {extend: 'copyHtml5', footer: true},
                {extend: 'excelHtml5', footer: true},
                {extend: 'csvHtml5', footer: true},
                {extend: 'pdfHtml5', footer: true}
            ],
            "processing": true,
            "order": [[1, "DESC"]],
            "serverSide": true,
          
          
                "ajax": {
        url: "<?php echo base_url("index.php/billing-users/reports/data_sales_list"); ?>",
        data: {
             "from_date_field": function(){ return $("#from_date_field").val();},
            "to_date_field": function(){ return $("#to_date_field").val();}
        }
    },
            aoColumns: [
                {mData: 'transaction_id'},
                {mData: 'transaction_date', bSortable: true},

                {mData: 'bill_no', bSortable: false},
                {mData: 'business_contact_name', bSortable: false},
                {mData: 'product_name', bSortable: false},
                {mData: 'length', bSortable: false},
                {mData: 'width', bSortable: false},
                {mData: 'qty', bSortable: false},
                {mData: 'price_per_unit', bSortable: false},
                {mData: 'value', bSortable: false}
              



            ],
            "columnDefs": [
                {"name": "transaction_id", "width": "7%", "targets": 0},
                {"name": "transaction_date", "width": "10%", "targets": 1},

                {"name": "bill_no", "targets": 2,"width": "10%"},
                {"name": "business_contact_name","width": "20%", "targets": 3},

                {"name": "product_name", "targets": 4,"width": "20%"},
                {"name": "length", "targets": 5,"width": "7%"},
                {"name": "width", "targets": 6,"width": "7%"},
                {"name": "qty", "targets": 7,"width": "7%"},
                {"name": "price_per_unit", "targets": 8,"width": "12%"},
                {"name": "value", "targets": 9,"width": "14%"}
              
            ]
        });
 $("#search_form").on("submit",function(e){
     e.preventDefault();
     
           $("#sales").DataTable().ajax.reload(); 
        });
        $('#sales thead th').each(function () {
            var title = $('#sales thead th').eq($(this).index()).text();
            if ($(this).attr("column_name")) {
                idtextbox = $(this).attr("column_name");
                elem_width = $(this).attr("element_width");
                text_type = "text";
                if ($(this).attr("hidden_column")) {
                    text_type = "hidden";
                }
                $(this).append('<br><input id=' + idtextbox + ' type="' + text_type + '"  class="form-control" style="width:' + elem_width + '"/>');

            }
        });
        var table = $('#sales').DataTable();
        table.columns().every(function () {
            if ($(this.header()).attr('column_name')) {
                // alert($(this.footer()).attr('column_name'));

                var that = this;
                $('input', this.header()).on('keyup change', function () {

                    that.search($(this).val()).draw();


                });

            }
        });
    });
</script>
<section class="content-header">
    <h2 class="col-md-4 col-xs-12 nopadding">
        Sales Report 
    </h2>


</section>

<!-- Main content -->
<section class="content">
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
    <div style="clear: both"></div>

    <div class="row ">
        <div class="col-xs-12">
            <?php
//   echo anchor(base_url() . 'index.php/league/add/', 'Add League', ' class="btn btn-info"');
            ?>
            <div class="clear">&nbsp;</div>
            <table id="sales" class="table-striped table-bordered table-responsive nowrap table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th column_name="transaction_id" element_width="100%">Sales ID</th>

                        <th column_name="trasaction_date" element_width="100%">Date</th>


                        <th column_name="bill_no" element_width="100%">Bill No</th>
                        <th column_name="business_contact_name" element_width="100%">Customer</th>
                        <th column_name="product_name" element_width="100%" >Product Name</th>
                        <th column_name="height" element_width="100%">Height</th>
                        <th column_name="weight" element_width="100%">Weight</th>
                        <th column_name="qty" element_width="100%">Qty</th>
                        <th column_name="price_per_unit" element_width="100%">Per Per Unit</th>
                        <th class="sum" >Value</th>
                     
                    </tr>
                </thead>

                <tfoot>
                    <tr>
                        <th column_name="transaction_id" element_width="100%">Sales ID</th>

                        <th column_name="trasaction_date" element_width="100%">Date</th>


                        <th column_name="bill_no" element_width="100%">Bill No</th>
                        <th column_name="business_contact_name" element_width="100%">Customer</th>
                        <th column_name="product_name" element_width="100%" >Product Name</th>
                        <th column_name="height" element_width="100%">Height</th>
                        <th column_name="weight" element_width="100%">Weight</th>
                        <th column_name="qty" element_width="100%">Qty</th>
                        <th column_name="price_per_unit" element_width="100%">Per Per Unit</th>
                        <th  class="sum">Value</th>
                       
                    </tr>
                </tfoot>
            </table>

            <script type="text/javascript">
                $(document).ready(function () {

                });
                function trigger_search() {
                    var table = $('#leagues').DataTable();
                    table.columns().every(function () {
                        if ($(this.header()).attr('column_name')) {
                            // alert($(this.footer()).attr('column_name'));

                            var that = this;
                            value = $('input', this.header()).val();
                            if ($('input', this.header()).val() !== "") {

                                that.search(value).draw();
                            }



                        }
                    }
                    );
                }


            </script>
        </div>
    </div>

</section>
<style type="text/css">
    .noti-box .icon-box {
        display: block;
        float: left;
        margin: 0 15px 10px 0;
        width: 35px;
        height: 35px;
        line-height: 40px;
        vertical-align: middle;
        text-align: center;
        font-size: 20px;
    }
    .main-text {
        font-size: 16px;
        font-weight: 600;
        padding-top: 10px;
    }
    .noti-box {
        min-height: 50px;
        padding: 10px;
    }

</style>

