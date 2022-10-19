
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('ui.jqgrid.css'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('ui.jqgrid-bootstrap.css'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.theme.css'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.structure.min.css'); ?>" />

<link rel="stylesheet" href="<?php echo css_url('jquery-ui.min.css') ?>">

<script src="<?php echo js_url('jquery-ui.min.js'); ?>"></script>
<link href="<?php echo css_url('select2.min.css'); ?>" rel="stylesheet" />
<script src="<?php echo js_url('select2.min.js') ?>"></script>

<script src="<?php echo js_url('i18n/grid.locale-en.js'); ?>" type="text/javascript"></script>
<script src="<?php echo js_url('jquery.jqGrid.min.js'); ?>" type="text/javascript"></script>

<link href="<?php echo assets_url('magicsuggest/magicsuggest-min.css'); ?>" rel="stylesheet">
<script src="<?php echo assets_url('magicsuggest/magicsuggest-min.js'); ?>"></script>
<div class="right-sidebar"> 
    <div style="clear: both">

    </div>
    <div class="col-md-12">
        <div class="from_to row">
            <h3>Petty Cash</h3>
            <form name="search_form">
                <div class="col-md-3">
                    <input type="text" name="from_date_field" id="from_date_field" class="form-control" placeholder="From Date"/>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control"   id="to_date_field"  name="to_date_field" placeholder="To Date"/>
                </div>

                <div class="col-md-1">
                    <input type="button" class="form-control btn-primary btn btn-sm " id="search" value="Go"/>

                </div>
                <div class="col-md-1">
                    <input type="reset" class="form-control btn-warning btn btn-sm " id="reset" value="Clear"/>
                </div>

            </form>
        </div>
          </div>
        <div style="clear: both; height: 10px;"></div>
       
        <div class="row">
             <h4 class="ajax-status"></h4>
            <div class="col-md-9 col-lg-9 col-xs-9 col-xs-offset-0">
        <table id="pettygrid"></table>
        <div id="pagerDb"></div>
        </div>
        </div>
        <div style="clear: both; height: 20px;"></div>

        <?php
        // echo $this->action('credit',
        //                'petty',
        //          'accounts');
        ?>

  
    <div id='prt-container' class='hide'>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function () {
            $("#from_date_field,#to_date_field").datepicker({dateFormat: 'dd-M-yy'});
            var $table = $("#pettygrid");
            from_date = $("#from_date_field").val();
            to_date = $("#to_date_field").val();
            $("#search").click(function () {
                from_date = $("#from_date_field").val();
                to_date = $("#to_date_field").val();
                $table.trigger('reloadGrid');

            });
            $("#reset").on('click', function () {


                $table.trigger('reloadGrid');


            });
            $("#dept").on("change", function () {

                $table.trigger('reloadGrid');

            });

            jQuery("#pettygrid").jqGrid({
                url: '<?php echo base_url('index.php/billing-users/petty/fetch'); ?>',
                datatype: "json",
                recreateFilter: true,
                mtype: "GET",
                postData: {
                    from_date: function () {
                        return jQuery("#from_date_field").val();
                    },
                    to_date: function () {
                        return jQuery("#to_date_field").val();
                    }

                },
                colNames: ['ID', 'Date', 'Paid Rs', 'Category', 'Paid to', 'Remarks', 'Created By'],
                colModel: [
                    {name: 'id', index: 'id', width: 55, key: true, hidden: true},

                    {name: 'date', index: 'date', width: 80, align: "left", editable: true, editoptions: {
                            "dataInit": function (elem) {

                                $(elem).datepicker({dateFormat: 'dd-mm-yy'});




                            }}, editrules: {required: true}},

                    {name: 'amt', index: 'amt', width: 70, align: "left", editable: true},

                    {name: 'head_id', index: 'head_id', width: 70, align: "left", editable: true},
                    {name: 'contact_id', index: 'contact_id', width: 70, align: "left", editable: true},

                    {name: 'notes', index: 'notes', width: 100, editable: true, edittype: "textarea"},
                    {name: 'first_name', index: 'first_name', width: 100, editable: false}
                ],
                rowNum: 500,
                rowList: [500, 1000, 1500],
                pager: '#pagerDb',
                sortname: 'date',
                gridview: true,
                rownumbers: true,
                toppager: true,
                viewrecords: true,
                footerrow: false,
                loadComplete: function () {

                },
                autowidth: true,
                height: 500,
                multiselect: false,

                rowheight: 300,
                sortorder: "desc",
                caption: "Office Expense",
                editurl: "<?php echo base_url('index.php/billing-users/petty/operations'); ?>",
            });
            jQuery("#pettygrid").jqGrid('navGrid', '#pagerDb', {cloneToTop: true}, {height: 650, width: 500, recreateForm: true, onclickSubmit: function (params, posdata) {
                    $(".topinfo").html("ReAssigning, Please Wait..");
                    var tinfoel = $(".tinfo").show();

                   
                }, beforeShowForm: function (frm) {
                  


                },
                afterSubmit: function (response, postdata) {


                    if (response.status == 200) {

                        $(".topinfo").html(response.responseText);
                        var tinfoel = $(".tinfo").show();
                        tinfoel.delay(3000).fadeOut();

                        $table.trigger('reloadGrid');


                        return [true, ''];
                    } else {
                        $(".topinfo").html("Error Occured. Try Later.");
                        return [false, 'error message'];
                    }

                }}, {height: 650, width: 400, recreateForm: true, onclickSubmit: function (params, posdata) {
                    $(".topinfo").html("Adding, Please Wait..");
                    var tinfoel = $(".tinfo").show();

                  
                
                }, beforeShowForm: function (frm) {

                  
                },
                afterSubmit: function (response, postdata) {

                    if (response.status == 200) {

                        $(".topinfo").html(response.responseText);
                        var tinfoel = $(".tinfo").show();
                        tinfoel.delay(3000).fadeOut();

                        $table.trigger('reloadGrid');


                        return [true, ''];
                    } else {
                        $(".topinfo").html("Error Occured. Try Later.");
                        return [false, 'error message'];
                    }


                }}, {height: 200, width: 600}, {height: 200, width: 800}).navButtonAdd("#pettygrid_toppager_left", {caption: "Checked", buttonicon: "ui-icon-approve", id: "approve_advance", onClickButton: function (params, posdata) {
                    var myGrid = $('#pettygrid')
                    var selRowIds = myGrid.jqGrid('getGridParam', 'selarrrow');
                    var approve = 1;

                    sendApprovalRequest(selRowIds, approve);



                }, position: "last", title: "", cursor: "pointer"})

            //jQuery("#progress_date").datepicker();

            var mygrid = $("#pettygrid");
            jQuery("#pettygrid").jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});
            jQuery("#pettygrid").jqGrid('navButtonAdd', "#pettygrid_toppager_left", {caption: "Clear Search", title: "Clear Search", onClickButton: function () {
                    mygrid[0].clearToolbar()
                }});

            $("#reset").on("click", function () {
                setTimeout(function () {
                    $("#pettygrid").trigger('reloadGrid');
                }, 100)

            });
        });



        function sendApprovalRequest(selRowIds, approve) {
            jQuery("body").css("opacity", ".7");

            jQuery(".ajax-status").html("Processing");

            jQuery(".ajax-status").fadeIn(1);

            if (approve == 1) {
                var request_url = "petty/adjust/adjusted/1";
            } else {

                var request_url = "petty/adjust/adjusted/0";

            }
            jQuery.ajax(
                    {
                        type: "POST",
                        url: request_url,
                        data: "ids=" + selRowIds,
                        success: function (text) {

                            jQuery("body").css("opacity", "1");
                            jQuery(".ajax-status").html(text);
                            jQuery(".ajax-status").delay(3000).slideUp(1000);
                            jQuery("#pettygrid").trigger("reloadGrid");

                            // Insert new element before the Add button

                        }
                    }
            );



        }
    
    </script>
    <div style="clear: both" media="print">

    </div>

</div>
<style type="text/css">
    .ui-icon-search{
        display: none !important;
    }
    #gbox_pettygrid
    {

    }
    .ui-datepicker{
        z-index: 9999999 !important;
    }
    
    @media print{

        .nav_holder,.from_to,.ui-pager-table,.ui-search-toolbar,#alerthd_pettygrid,#alertmod_pettygrid{
            display: none;
        }
        table{
            width: 80%;
        }
        table td, table th{
            padding: 5px;
            border: 1px solid #aaaaaa;
        
        }
        body{
            
        }
        table th{
            text-align: center;
        }
        .ui-jqgrid-sdiv{
              position:absolute;
   bottom:0;
   width:100%;
   height:60px;
        }
    }
</style>




