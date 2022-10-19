
<link rel="stylesheet" href="<?php echo css_url('jquery-ui.min.css')?>">

<script src="<?php echo js_url('jquery-ui.min.js'); ?>"></script>
<link href="<?php echo css_url('select2.min.css'); ?>" rel="stylesheet" />
<script src="<?php echo js_url('select2.min.js')?>"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.theme.css'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.structure.min.css'); ?>" />

<div class="row">
     <h3>Replacement Products</h3>
      <div class="col-md-12 nopadding">
            <form name="search_form" id='search_form'>
                <div class="col-md-2 col-xs-4"><label>Expiry Date</label></div>
                <div class="col-md-3 col-xs-6 nopadding">
                    <input type="text" name="expiry_date" id="expiry_date" class="form-control" placeholder="Expiry Date" required="required" value="<?php echo date('d-m-Y'); ?>"/>
                </div>
             

                <div class="col-md-1 col-xs-2">
                    <input type="submit" class="form-control btn-primary btn btn-sm " id="search" value="Go"/>

                </div>
                <div class="col-md-1">
                    <input type="reset" class="form-control btn-warning btn btn-sm " id="reset" value="Clear"/>
                </div>

            </form>
      </div>
     <div>
         <div style="height: 10px;clear: both"></div>
         <div class="row">
     <div class="col-md-12">
    <div id="productsGrid" >


    </div>
     </div> 
     </div>
</div>

<script type="text/javascript">
    $("document").ready(function () {
 $("#search_form").on('submit', function (e) {
            e.preventDefault();
            $("#productsGrid").jsGrid('loadData');
        });
$("#expiry_date").datepicker({dateFormat:"dd-mm-yy"});

        $("#productsGrid").jsGrid({
            height: "500px",
            width: "98%",
            filtering: true,
            editing: true,
             deleting: false,
            inserting: false,
            sorting: true,
            paging: true,
            autoload: true,
            pageSize: 20,
            pageButtonCount: 5,
            pageLoading: true,
            deleteConfirm: "Do you really want to delete the User?",
            fields: [
                 {name: "sno", title: "S.No.", type: "text", width: 50, readOnly: true, editing: false, inserting: false},
                {name: "transaction_product_id", title: " #", type: "number", width: 30, readOnly: true, visible: false, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
                {name: "transaction_id", title: "Purchase No.", type: "text", width: 50, readOnly: true, editing: false, inserting: false},
                 
                {name: "product_id",
                    type: "select",
                    title: "Search",
                    editing: false,
                    inserting: true,
                    items: "",
                    width: 80,
                    autosearch: true,
                    visible:false,
                    align: "left",
                    valueType: "number",
                    valueField: "transaction_product_id", // name of property of item to be used as value
                    textField: "product_name",
                    css: "product_td"

                },
                {name: "product_name", title: "Name", type: "text", width: 100, align: "left", css: "name_td", inserting: false, editing: false},
                {name: "batch_no", title: "Batch #", type: "text", width: 50, align: "left"},
                {name: "mfg_date", title: "Mfg Date", type: "text", width: 50, align: "left",css:"mfg_td"},
                {name: "expiry_date", title: "Expiry", type: "text", width: 50, align: "left",css:"exp_td"}, 
                        {name: "qty", title: "Qty", type: "number", width: 40, validate: "required", align: "left"},
                {name: "price_per_unit", title: "Price", type: "text", width: 50, validate: "required"},
                {name: "value", title: "Value", type: "text", width: 50, readOnly: true, editing: false, inserting: false},
               
                {type: "control", modeSwitchButton: false, editButton: true , deleteButton:false}
            ],
            controller: {
                loadData: function (filter) {
                    filter.expiry_date = $("#expiry_date").val();
                    return $.ajax({
                        type: "GET",
                        url: "<?php echo site_url('billing-users/replacement_products/fetch'); ?>",
                        data: filter,
                        dataType: "json"

                    });

                },
                insertItem: function (item) {
                    save_data(item, "add");


                },
                updateItem: function (item) {
                    save_data(item, "update");
                },
                deleteItem: function (item) {
                    save_data(item, "delete");


                }

            },
            onItemInserting: function (item) {
                item.transaction_id = $("#transaction_id").val();
                return item;
            },
            onItemUpdating: function (item) {
                item.transaction_id = $("#transaction_id").val();
                return item;
            },
            onDataLoaded: function (args) {
                $(".product_td select").select2();
                // cancel loading data if 'name' is empty
                products_total = args.data.total_value;
                tax_percent = $("#tax_percent").val();
                //   alert(tax_percent);
                total = (parseFloat(products_total) / 100) * tax_percent;

                $(".total_tax").html(total.toFixed(2));
                $(".without_tax").html(products_total.toFixed(2));
                with_tax = parseFloat(total) + parseFloat(products_total.toFixed(2));
                $(".with_tax").html(with_tax.toFixed(2));
                $("#grand_total").val(with_tax.toFixed(2));
                select2Initiate();
            }
        });
        /*
         // Add row Deleterow
         $(".insert_btn").on("click",function(){
         $(".jsgrid-insert-row").fadeIn(10);
         $(".close_btn").css("display","inline-block");
         $(this).css("display","none");
         });
         $(".close_btn").on("click",function(){
         $(".jsgrid-insert-row").fadeOut(0);
         $(".close_btn").css("display","none");
         $(".insert_btn").css("display","inline-block");
         });
         
         */
        select2Initiate();

        $("#tax_percent,#tax_type_id").on("change", function () {
            $("#productsGrid").jsGrid('loadData');
        })

        $(".product_td select").select2();
    });

    function save_data(items, type) {
 
        $("#return_msg").html("<i class='fa fa-spinner'></i> Loading...");
        items.transaction_id = $("#transaction_id").val();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('billing-users/replacement_products/save'); ?>",
            data: {data: items, type: type},
            dataType: "json",
            success: function (response) {


                $("#productsGrid").jsGrid('loadData');



            }

        });

    }

    function select2Initiate() {


        $(".product_td>select").select2({
            ajax: {
                url: "<?php echo site_url('/billing-users/transactions/fetch_purchase_products') ?>",
                dataType: 'json',
                method: "post",
                delay: 0,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                initSelection: function (element, callback) {

                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: false
            },
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1

        });
        $(".mfg_td input,.exp_td input").datepicker({dateFormat: "dd-mm-yy"});
    }
</script>
<style type="text/css">
    #return_msg{
        display: none;
    }    

</style>