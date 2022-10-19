
<link rel="stylesheet" href="<?php echo css_url('jquery-ui.min.css')?>">

<script src="<?php echo js_url('jquery-ui.min.js'); ?>"></script>
<link href="<?php echo css_url('select2.min.css'); ?>" rel="stylesheet" />
<script src="<?php echo js_url('select2.min.js')?>"></script>

 <div class="row">
<div class="col-md-12">
    <h3><?php echo $page_title[$type]; ?></h3>
    <form name="search_form" id="search_form">
                <div class="col-md-3 nopadding">
                    <input type="text" name="from_date_field" id="from_date_field" class="form-control" placeholder="From Date"/>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control"   id="to_date_field"  name="to_date_field" placeholder="To Date"/>
                </div>
               
                <div class="col-md-1 hidden-print">
                    <input type="submit" class="form-control btn-primary btn btn-sm " id="search" value="Go"/>

                </div>
                <div class="col-md-1 hidden-print">
                    <input type="reset" class="form-control btn-warning btn btn-sm " id="reset" value="Clear"/>
                </div>
               
            </form>
  </div>
 </div>
<div style="height: 10px"></div>
<div class="tab-content">
  <div id="gridholder" class="tab-pane fade in active">
  <div id="pdc" >


    </div>
  
  </div>
 
  
</div>
   
</div>


<script type="text/javascript">
    $("document").ready(function () {
 $("#from_date_field,#to_date_field").datepicker({dateFormat: 'dd-M-yy'});
 $("#search_form").on('submit',function(e){
            e.preventDefault();
            $("#pdc").jsGrid('loadData');
        });
        $("#reset").on('click',function(){
            

  setTimeout(function(){
      $("#pdc").jsGrid("clearFilter");
},100);

        });

        $("#pdc").jsGrid({
            height: "500px",
            width: "98%",
            filtering: true,
            editing: false,
            inserting: false,
            sorting: true,
            deleting:false,
            paging: true,
            autoload: true,
            pageSize: 20,
            pageButtonCount: 5,
            pageLoading: true,
            deleteConfirm: "Do you really want to delete the User?",
            fields: [
                {name: "payment_id", title: " #", type: "number", width: 30, readOnly: true, visible: false, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
                {name: "business_contact_id",
                    type: "select",
                    title: "Name ",
                    width: 110,
                    autosearch: true,
                    validate: ["required", function (value, item) {
                            item_value = item.mode_id;

                            if (item_value === 0) {
                                return false;
                            }
                            return true;
                        }],
                    align: "left",
                    css:"business_contact_td",
                    valueType: "number",
                    items: <?php echo $contact_options; ?>,
                    valueField: "business_contact_id", // name of property of item to be used as value
                    textField: "business_contact_name"
                },
                {name: "amount", title: " Amount", type: "number", width: 80, validate: "required", align: "left"},
                
                {name: "issued_date",css:"datepicker", title: "Issued Date", type: "text", width: 80, validate: "required", align: "left"},
                {name: "mode_id",
                    type: "select",
                    title: "Mode ",
                    width: 110,
                    autosearch: true,
                    validate: ["required", function (value, item) {
                            item_value = item.mode_id;

                            if (item_value === 0) {
                                return false;
                            }
                            return true;
                        }],
                    align: "left",
                    valueType: "number",
                    items: <?php echo $mode_options; ?>,
                    valueField: "mode_id", // name of property of item to be used as value
                    textField: "mode_name"
                },
                        {name: "pdc_dated",css:"datepicker", title: " Dated", type: "text", width: 80, validate: "required", align: "left"},
                          {name: "ref_no", title: "CQ No/Ref no", type: "text", width: 80, validate: "required", align: "left"},
                {name: "bank_name", title: "Bank", type: "text", width: 80, validate: "required", align: "left"},
              
                {name: "stmt_checked", title: "Stmt Checked", type: "checkbox", width: 50,filtering:false, align: "left"},
                 {name: "mgmt_checked", title: "Mgmt Checked", type: "checkbox", width: 80,filtering:false, align: "left"}
               
            ],
            controller: {
                loadData: function (filter) {
               
                 t_type=$(".nav-tabs .active a").attr('data-type');
              
filter.t_type=<?php echo $type; ?>;
filter.from_date_field=$("#from_date_field").val();
filter.to_date_field=$("#to_date_field").val();
                
                    return $.ajax({
                        type: "GET",
                        url: "<?php echo site_url('billing-users/pdc/fetch'); ?>",
                        data: filter,
                        dataType: "json"

                    });

                }
                

            },
            
            onDataLoaded: function (args) {
$(".business_contact_td select").select2({width:"100%"});
                // cancel loading data if 'name' is empty
                
                $("#grand_total").val(args.data.total_value);
            },
             onDataLoading: function (args) {

               return args;
            }
        });
       



       
   $(".datepicker input").datepicker({ dateFormat: 'dd-mm-yy' });

        $(".jsgrid-table").addClass("table");
  $(".jsgrid-table").addClass("table-bordered");


  


    });

  


</script>
<style type="text/css">
    #return_msg{
        display: none;
    }    

</style>