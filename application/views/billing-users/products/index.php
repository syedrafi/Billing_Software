
<div class="row">
    <h3>Products</h3>
    <div class="row">
        <!--<div class="col-md-12 btn_holder">
        <button class="insert_btn btn btn-sm btn-success"> <i class="fa fa-plus"></i> Add</button>
         <button class="close_btn btn btn-sm btn-danger"> <i class="fa fa-plus"></i> Close</button>
        </div>-->
    </div>
    <div id="productsGrid" >


    </div>
</div>
<script type="text/javascript">
    $(function () {

    var $grid = $("#productsGrid").jsGrid({
    height: "500px",
            width: "98%",
            filtering: true,
            editing: true,
            inserting: true,
            sorting: true,
            paging: true,
            autoload: true,
            pageSize: 20,
            pageButtonCount: 5,
            pageLoading: true,
            deleteConfirm: "Do you really want to delete the User?",
            fields: [
            {name: "sno", title: "S.No.", type: "text", width: 30, readOnly: true, visible: true, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
            {name: "product_id", title: "#", type: "text", width: 30, readOnly: true, visible: false, inserting: false, editing: false, sorting: true,
                    sorter: "number", align: "center"},
            {name: "product_name", title: "Product Name", align: "center", type: "text", width: 60, validate:[ "required", {validator:"maxLength", param:"100"}]},
             {name: "size", title: "Size", align: "center", type: "text", width: 60, visible:false},
           
            {name: "product_code", title: "Product Code", align: "center", type: "text", width: 60},
             {name: "hsn_no", title: "HSN No", align: "center", type: "text", width: 40},
            {name: "brand_id",
                    type: "select",
                    title: "Brand",
                    width: 50,
                    autosearch: true,
                 
                    align: "center",
                    valueType: "number",
                    items: <?php echo $brand_options; ?>,
                    valueField: "brand_id", // name of property of item to be used as value
                    textField: "brand_name",
            },
             {name: "category_id",
                    type: "select",
                    title: "Category",
                    width: 50,
                    visible:false,
                    autosearch: true,
                    align: "center",
                    valueType: "number",
                    items: <?php echo $category_options; ?>,
                    valueField: "category_id", // name of property of item to be used as value
                    textField: "category_name",
            },
           {name: "unit_type_id",
                    type: "select",
                    title: "Unit Type",
                    width: 40,
                    autosearch: true,
                    validate: ["required", function (value, item) {
                    item_value = item.unit_type_id;
                    if (item_value === 0) {
                    return false;
                    }
                    return true;
                    }],
                    align: "center",
                    valueType: "number",
                    items: <?php echo $unit_type_options; ?>,
                    valueField: "unit_type_id", // name of property of item to be used as value
                    textField: "unit_type"
            },
            {name: "stock", title: "Stock", type: "text", width: 30, filtering: false, align: "center", inserting: false, editing: false},
            {name: "is_active",
                    type: "select",
                    title: "Active",
                    width: 50,
                    autosearch: true,
                    validate: "required",
                    align: "center",
                    valueType: "number",
                    items: [{"id": "1", "label": "Active"}, {"id": "0", "label": "InActive"}],
                    valueField: "id", // name of property of item to be used as value
                    textField: "label",
            },
<?php if ($can_edit == 1): ?>
                {type: "control", modeSwitchButton: true, editButton: true}
<?php endif; ?>
            ],
            controller: {
            loadData: function (filter) {

            return $.ajax({
            type: "GET",
                    url: "<?php echo site_url('billing-users/products/fetch'); ?>",
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
    });
    function save_data(items, type) {
    $.ajax({
    type: "POST",
            url: "<?php echo site_url('billing-users/products/save'); ?>",
            data: {data: items, type: type},
            dataType: "json",
            success: function (response) {
            $("#productsGrid").jsGrid('loadData');
            //   $grid.jsGrid("refresh");



            }

    });
    }


</script>

