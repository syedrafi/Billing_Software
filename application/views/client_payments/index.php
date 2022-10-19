<link rel="stylesheet" href="<?php echo assets_url("jqgrid/css/ui.jqgrid.css"); ?>" /><link rel="stylesheet" href="<?php echo assets_url("jqgrid/css/ui.jqgrid-bootstrap-ui.css"); ?>" />

<script src="<?php echo assets_url("jqueryui/jquery-ui.min.js"); ?>"></script>
<script src="<?php echo assets_url("jqueryui/js/i18n/grid.locale-en.js"); ?>"></script>
<script src="<?php echo assets_url("jqgrid/js/jquery.jqGrid.min.js"); ?>"></script>
<link rel="stylesheet" href="<?php echo assets_url("jqueryui/jquery-ui.css"); ?>" />
<link rel="stylesheet" href="<?php echo assets_url("jqueryui/jquery-ui.theme.css"); ?>" />
<link rel="stylesheet" href="<?php echo assets_url("select2/css/select2.min.css"); ?>" />
<script src="<?php echo assets_url("select2/js/select2.min.js"); ?>"></script>
<div class="page-title">
    <div class="title_left">
        <h3><?php echo $page_title; ?></h3>
    </div>

    <div class="title_right">

    </div>
</div>
<div class="clearfix"></div>
<div class="col-md-12">
    <div class="from_to row">
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
<div style="height: 10px; clear: both;"></div>
<div class="row">
    
    <div class="col-md-12">
        <table id="paymentsGrid"></table>
        <div id="paymentsPager"></div>
    </div> 
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
    var $table = $("#paymentsGrid");
       $("#from_date_field,#to_date_field").datepicker({ dateFormat: 'dd-M-yy' });
      
       from_date =$("#from_date_field").val();
       to_date=$("#to_date_field").val();
    $("#search").click(function(){
       
       from_date =$("#from_date_field").val();
       to_date=$("#to_date_field").val();
       $("#paymentsGrid").trigger('reloadGrid');
   ;
    });
       
        jQuery("#paymentsGrid").jqGrid({
            url: '<?php echo site_url('client_payments/fetch?type='.$business_contact_type); ?>',
            datatype: "json",
            recreateFilter: true,
            mtype: "GET",
             postData: {
        from_date: function() { return jQuery("#from_date_field").val(); },
        to_date: function() { return jQuery("#to_date_field").val(); }
       
    },
            loadOnce: false,
            multiselect: false,
            colNames: ['ID', 'Paid Date','Paid Amt','Paid By',  'Payment Mode', 'Transaction ID/Cheque ID'],
            colModel: [
                {name: 'id', index: 'id', width: 80, align: "left", editable: false, key: true, hidden: true},
                 {name: 'paid_date', index: 'paid_date', width: 80, align: "left", editable: true, editrules: {required: true},editoptions: {
                            "dataInit": function (elem) {

                                $(elem).datepicker({dateFormat: 'dd-mm-yy'});

                            }}, edittype: "text"},
                  {name: 'paid_amt', index: 'paid_amt', width: 80, align: "left", editable: true, editrules: {required: true, number:true}, edittype: "text"},
                    
                     {name: 'business_contact_id', index: 'business_contact_id', width: 80, align: "left", editable: true, editrules: {required: true}, edittype: "select", formatter: 'select',
                    editoptions: {
                        value: "<?php echo $business_contact_options; ?>","dataInit": function (elem) {

            setTimeout(function(){
                 $(elem).select2({
                                    allowClear: true,width:"270"});     
            },00);
                        
                            }

                    },
                    stype: 'select',
                     searchoptions: {
                        value: "<?php echo $business_contact_options; ?>"

                        }
                },
                  {name: 'payment_mode_id', index: 'payment_mode_id', width: 80, align: "left", editable: true, editrules: {required: true}, edittype: "select", formatter: 'select',
                    editoptions: {
                        value: "<?php echo $payment_mode_options; ?>",
                        "dataInit": function (elem) {

            setTimeout(function(){
                 $(elem).select2({
                                    allowClear: true,width:"270"});     
            },00);
                        
                            }

                    },
                    stype: 'select',
                    searchoptions: {
                        value: "<?php echo $payment_mode_options; ?>"

                        }
                },
                    {name: 'transaction_id', index: 'transaction_id', width: 120, align: "left", editable: true, editrules: {required: true}, edittype: "text"}
              



            ],
            rowNum: 500,
            rowList: [500, 1000, 1500],
            pager: '#paymentsPager',
            sortname: 'client_payment_id',
            gridview: true,
            rownumbers: true,
            toppager: true,
            viewrecords: true,
            footerrow: true,
            loadComplete: function () {
                /*
                 jQuery("#gs_contact_id,#gs_head_id,#gs_sub_head_id").select2({
                 allowClear:true,
                 width:"150"
                 });  
                 */



            },
            autowidth: true,
            height: 500,
            width: 500,
            rowheight: 300,
            sortorder: "desc",
            caption: "<?php echo $page_title; ?>",
            editurl: "<?php echo site_url('client_payments/operations'); ?>"


        });
        jQuery("#paymentsGrid").jqGrid('navGrid', '#paymentsPager', {cloneToTop: true}, {height: 800, width: 600, recreateForm: true, onclickSubmit: function (params, posdata) {
                $(".topinfo").html("ReAssigning, Please Wait..");
                var tinfoel = $(".tinfo").show();


            }, beforeShowForm: function (frm) {
                
                // $("#event_type").select2("val", "");
                $("#tr_client_username,#tr_client_password,#tr_conf_password").remove();
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

            }}, {height: 800, width: 600, recreateForm: true, onclickSubmit: function (params, posdata) {
                $(".topinfo").html("Adding, Please Wait..");
                var tinfoel = $(".tinfo").show();

            }, beforeShowForm: function (frm) {
                //  $("select").select2("val", "");

                // ms.clear();
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


            }}, {height: 200, width: 600}, {height: 200, width: 800});
        //jQuery("#progress_date").datepicker();
       
        var mygrid = $("#paymentsGrid");
        jQuery("#paymentsGrid").jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});
        jQuery("#paymentsGrid").jqGrid('navButtonAdd', "#paymentsGrid_toppager_left", {caption: "Clear Search", title: "Clear Search", onClickButton: function () {
                mygrid[0].clearToolbar();
            }});




    });
    function usernameCheckForValue(value, colname) {

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('clients/checkusername'); ?>",
            data: {username: value},
            dataType: "json",
            async: false,
            success: function (response) {
                if (response.is_available) {
                    return_array= [true, ""];
                } else {
                    return_array= [false, "Username Already Taken"];
                }

           



            }

        });

return return_array;
    }
       function passwordCheck(value, colname) {

      if(value===$("#conf_password").val()){
          return [true,""];
      }
      return [false,"Passwords Does not match"];

return return_array;
    }
     function mobilenoCheck(value, colname) {

      if(value.length==10){
        
          return [true,""];
      }
      return [false,"Invalid Mobile No"];

return return_array;
    }

</script>

<style type="text/css">
   
    #tr_business_contact_id  td{
      margin-top: 20px !important;
    }
</style>


