<script src="<?php echo js_url('jquery-ui.min.js') ?>"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.theme.css'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.structure.min.css'); ?>" />
<!-- Content Header (Page header) -->

<!-- Content Header (Page header) -->
<link rel="stylesheet" type="text/css" href="<?php echo css_url('combined.datatables.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo css_url('dataTables.bootstrap.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo css_url('responsive.bootstrap.min.css'); ?>"/>
<script type="text/javascript" src="<?php echo js_url('datatables.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo js_url('dataTables.responsive.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo js_url('sum().js'); ?>"></script>

<script type="text/javascript" language="javascript" class="init">
    $(document).ready(function () {


        $("#date_from,#date_to").datepicker({dateFormat: "dd-mm-yy"});
        var dtable = $('#sales').dataTable({
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
                    console.log(sum);
                    $(this.footer()).html(parseInt(sum));
                });
            },
                
            
            "pageLength": 50,
            "autoWidth": true,
            dom: 'Bfrtip',
            buttons: [
                {extend: 'copyHtml5', footer: true},
                {extend: 'excelHtml5', footer: true},
                {extend: 'csvHtml5', footer: true},
                {extend: 'pdfHtml5', footer: true}
            ]








        });
       
       

    });
</script>
<section class="content-header">
    <h2 class="col-md-4 col-xs-12 nopadding">
        <?php echo $page_title; ?>
    </h2>


</section>

<!-- Main content -->
<section class="content">
    <div class="row">

        <div class="col-md-12 ">
            <form name="search_forms" id='search_forms' class="hidden-print" action="#" method="post">

                <div class="col-md-3">
                    <input type="text" name="date_from" id="date_from" class="form-control" placeholder="yyyy-mm-dd" required="required"  value="<?php echo $date_from; ?>" />
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control"   id="date_to"  name="date_to" placeholder="yyyy-mm-dd" required="required" value="<?php echo $date_to; ?>"/>
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

            <?php if (isset($result_array)): ?>
                <table id="sales" class=" table-bordered table-responsive nowrap table" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Inv.No</th>
                            <th>Inv Date</th>

                            <th>GSTIN</th>
                            <th>Party Name</th>
                            <th>Before Tax Value</th>
                            <th class="sum">CGST</th>
                            <th class="sum">SGST</th>
                            <th class="sum">IGST</th>
                            <th class="sum">Total Tax</th>
                            <th class="sum">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($result_array as $key => $result):
                            ?>
                            <tr>
                                <td><?php echo $result['bill_no']; ?></td>
                                <td><?php echo date('d-m-Y', strtotime($result['transaction_date'])); ?></td>
                                <td><?php echo $result['gstin']; ?></td>
                                <td><?php echo $result['business_contact_name']; ?></td>
                                <td><?php echo $result['bill_details']['without_tax']; ?></td>
                                <td><?php echo $result['bill_details']['cgst']; ?></td>
                                <td><?php echo $result['bill_details']['sgst']; ?></td>
                                <td><?php echo $result['bill_details']['igst']; ?></td>
                                 <td><?php echo $result['bill_details']['total_tax']; ?></td>
                                  <td><?php echo $result['bill_details']['bill_value']; ?></td>
                               
                            </tr>
                            <?php
                           




                        endforeach;
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Inv.No</th>
                            <th>Inv Date</th>

                            <th>GSTIN</th>
                            <th>Party Name</th>
                            <th>Before Tax Value</th>
                            <th class="sum">CGST</th>
                            <th class="sum">SGST</th>
                            <th class="sum">IGST</th>
                            <th class="sum">Total Tax</th>
                            <th class="sum">Grand Total</th>
                        </tr>
                    </tfoot>
                </table>
            <?php endif; ?>

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
    .paid_row{
        background: #b8deb8 !important;
    }

    .bill_row{
        background: #efefef !important;
    }

    #sales thead{
        background: #83beec;
    }
</style>

