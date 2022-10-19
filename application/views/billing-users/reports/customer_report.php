<script src="<?php echo js_url('jquery-ui.min.js') ?>"></script>
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


        $("#date_from,#date_to").datepicker({dateFormat: "yy-mm-dd"});
        var dtable = $('#sales').dataTable({

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
        $("#search_form").on("submit", function (e) {
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
       <?php echo $page_title; ?>
    </h2>


</section>

<!-- Main content -->
<section class="content">
    <div class="row">

        <div class="col-md-12 ">
            <form name="search_forms" id='search_forms' class="hidden-print" action="#" method="post">
                <div class="col-md-3">
                    <?php echo form_dropdown("business_contact_id", $business_contact_options, $selected_business_contact_id, "id='business_contact_id' class='form-control'"); ?>
                </div>
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
                            <td>Sl.No</td>
                            <td>Txn Date</td>
                           
                            <td>Transaction ID/Bill No</td>
                            <td class="hidden">CR/DR</td>
                            <td>Amt</td>
                            <td>Balance</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $current_balance = $balance;
                       
                        foreach ($result_array as $key => $result):
   if (isset($result['transaction_date']) ):
                                $balance = $balance + $result['bill_value'];
                            endif;
                             if (isset($result['paid_date']) ):
                                $balance = $balance - $result['paid_amt'];
                            endif;
                            if (isset($result['paid_date'])):
                                $cr_dr = "CR";
                                $classes = " paid_row ";

                                $ref_id = $result['payment_transaction_id'];
                                $amt = $result['paid_amt'];
                                $desc = "Payment Added";
                            else:
                             
                            $desc = "Bill No:".$result['bill_no'];
                          
                                $classes = " bill_row ";

                                $ref_id = $result['bill_no'];
                                $amt = $result['bill_value'];
                                $cr_dr = "DR";
                            endif;
                            ?>
                            <tr class="<?php echo $classes; ?>">
                                <td><?php echo $i; ?></td>
                                <td><?php echo date('d-m-Y', $result['date']); ?></td>
                                <td><?php echo $desc ?></td>
                                
                                <td class="hidden"><?php echo $cr_dr; ?></td>
                                <td><?php echo $amt; ?></td>
                                <td><?php echo $balance; ?></td>
                            </tr>
                            <?php
                            $i++;

                         
                            

                        endforeach;
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Sl.No</td>
                            <td>Txn Date</td>
                           
                            <td>Transaction ID/Bill No</td>
                            <td class="hidden">CR/DR</td>
                            <td>Amt</td>
                            <td>Balance</td>
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

