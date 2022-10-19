<?php  $from_date_field=date("Y-m-d"); 
$date = $from_date_field;
//increment 2 days
$mod_date = strtotime($date."+ 7 days");
$to_date_field= date("Y-m-d",$mod_date);
?>
<div id="popupModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                       <button type="button" class="btn btn-warning pull-right" data-dismiss="modal" id="cancel_alerts">Do Not Show alerts again.</button>
         
                <h4 class="modal-title"> Alerts</h4>
            </div>
            <div class="modal-body">
                <h3>PDC Receivable</h3>
                <div id="pdc_r"></div>
                  <h3>PDC Payable</h3>
                 <div id="pdc_p"></div>
            </div>
          
        </div>

    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        initiate_jsgrid("pdc_r", 2);
          initiate_jsgrid("pdc_p", 1);
    });
    function initiate_jsgrid(div_id, type) {
        $("#" + div_id).jsGrid({
            height: "250px",
            width: "98%",
            filtering: true,
            editing: false,
            inserting: false,
            sorting: true,
            deleting: false,
            paging: true,
            autoload: false,
            pageSize: 20,
            pageButtonCount: 5,
            pageLoading: true,
            deleteConfirm: "Do you really want to delete the User?",
            fields: [
                {name: "payment_id", title: " #", type: "number", width: 30, readOnly: true, visible: false, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
                {name: "business_contact_name", title: " Name", type: "text", width: 80, align: "left"},
                {name: "amount", title: " Amount", type: "number", width: 80, align: "left"},
                {name: "issued_date", css: "datepicker", title: "Issued Date", type: "text", width: 80, align: "left"},
                {name: "mode_name", title: " Mode", type: "text", width: 80, align: "left"},
                {name: "pdc_dated", css: "datepicker", title: " Dated", type: "text", width: 80, validate: "required", align: "left"},
                {name: "ref_no", title: "CQ No/Ref no", type: "text", width: 80, validate: "required", align: "left"},
                {name: "bank_name", title: "Bank", type: "text", width: 80, validate: "required", align: "left"},
                {name: "stmt_checked", title: "Stmt Checked", type: "checkbox", width: 50, filtering: false, align: "left",visible:false},
                {name: "mgmt_checked", title: "Mgmt Checked", type: "checkbox", width: 80, filtering: false, align: "left",visible:false}

            ],
            
            controller: {
                loadData: function (filter) {



                    filter.t_type = type;
                    filter.from_date_field ="<?php echo $from_date_field; ?>";
                    filter.to_date_field ="<?php echo $to_date_field; ?>";

                    return $.ajax({
                        type: "GET",
                        url: "<?php echo site_url('billing-users/pdc/fetch'); ?>",
                        data: filter,
                        dataType: "json"

                    });

                }


            },
            onDataLoaded: function (args) {

                // cancel loading data if 'name' is empty

                $("#grand_total").val(args.data.total_value);
            },
            onDataLoading: function (args) {

                return args;
            }
        });
    }

</script>