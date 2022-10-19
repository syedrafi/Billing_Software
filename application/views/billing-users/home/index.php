
<div class="row">
    <h3>Welcome</h3>

   
        <div class="row">
             
            <div class="col-md-10 col-xs-12 col-md-offset-1">
                 <div class="alert alert-warning">
  <strong>Warning!</strong> Following Products are Low in Stock.
  
  
</div>
        <div id="productsGrid" >



        </div>
                </div>
        </div>
   
    
</div>
<script type="text/javascript">
var $grid = $("#productsGrid").jsGrid({
            height: "500px",
            width: "98%",
            filtering: true,
            editing: false,
            inserting: false,
            sorting: true,
            paging: true,
            autoload: true,
            pageSize: 20,
            pageButtonCount: 5,
            pageLoading: true,
            deleteConfirm: "Do you really want to delete the User?",
            fields: [
                {name: "sno", title: "S.No", type: "text", width: 20, readOnly: true, visible: true, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
                {name: "product_id", title: "#", type: "text", width: 20, readOnly: true, visible: false, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
                {name: "product_code", title: "Product Code", type: "text", width: 20, readOnly: true, visible: true, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
                {name: "product_name", title: "Product Name", type: "text", width: 60, validate:[ "required",{validator:"maxLength",param:"100"}]},
                {name: "brand_id",
                    type: "select",
                    title: "Brand",
                    width: 50,
                    autosearch: true,
                    validate: "required",
                    align: "left",
                    valueType: "number",
                    items: <?php echo $brand_options; ?>,
                    valueField: "brand_id", // name of property of item to be used as value
                    textField: "brand_name",
                },
              
                {name: "total_purchase", title: " Purchase", type: "text", width: 30, filtering: false, inserting: false, editing: false},
          {name: "total_sales", title: " Sales", type: "text", width: 30, filtering: false, inserting: false, editing: false},
                {name: "stock", title: "Stock", type: "text", width: 30, filtering: false, inserting: false, editing: false}
               
               
            ],
            controller: {
                loadData: function (filter) {

                    return $.ajax({
                        type: "GET",
                        url: "<?php echo site_url('billing-users/products/fetch_lowstock'); ?>",
                        data: filter,
                        dataType: "json"

                    });

                },
                insertItem: function (item) {
                    save_data(item, "add");


                },
                updateItem: function (item) {
                    save_data(item, "update");
                    //   $("#productsGrid").jsGrid('loadData');
                },
                deleteItem: function (item) {
                    save_data(item, "delete");
                    $("#productsGrid").jsGrid('loadData');


                },
                onDataLoaded: function (args) {

                    // cancel loading data if 'name' is empty
                    $(".jsgrid-insert-row").css("display", "none");
                },
                onItemInserted: function (args) {
                    $("#productsGrid").jsGrid('loadData');
                },
                onItemUpdated: function (args) {
                    $("#productsGrid").jsGrid('loadData');
                }

            }
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





        });    
</script>