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
                    $(this.footer()).html(sum);
                });
            },
            "pageLength": 250,
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
 
    <div style="clear: both"></div>

    <div class="row ">
        <div class="col-xs-12">
            <?php
//   echo anchor(base_url() . 'index.php/league/add/', 'Add League', ' class="btn btn-info"');
            ?>
            <div class="clear">&nbsp;</div>

            
                <table id="sales" class=" table-bordered table-responsive nowrap table" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <td>Sl.No</td>
                            <td>Client</td>
                            <td class="sum">Balance</td>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                      
                       
                        foreach ($balance_list as $key => $result): ?>
<?php 
$classes="green";
if($result['balance']>0):
    $classes="red";
endif;
?>
                            <tr class="<?php echo $classes; ?>">
                                <td><?php echo $i; ?></td>
                                <td><?php echo $result['name']; ?></td>
                                <td><?php echo $result['balance']; ?></td>
                              
                            </tr>
                            <?php
                            $i++;

                         
                            

                        endforeach;
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Sl.No</td>
                            <td>Client</td>
                            <td class="sum">Balance</td>
                            
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
    .green{
        background: #b8deb8 !important;
    }

    .red{
        background: #efefef !important;
    }

    #sales thead{
        background: #83beec;
    }
</style>

