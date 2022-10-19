<link rel="stylesheet" href="<?php echo css_url('jquery-ui.min.css')?>">

<script src="<?php echo js_url('jquery-ui.min.js'); ?>"></script>
<link href="<?php echo css_url('select2.min.css'); ?>" rel="stylesheet" />
<script src="<?php echo js_url('select2.min.js')?>"></script>
<div class="row">

  
    <div class="col-md-12 col-xs-12 "> <h4>Warehouse Stock</h4></div>
 
    
    <div id="productsGrid" >


    </div>
</div>
<script type="text/javascript">
            $(function () {
            $("#date_select").datepicker({
            dateFormat: "dd-mm-yy"
                    }).on("input change", function (e) {
 //$("#productsGrid").jsGrid('loadData');
 $("#date_form").submit();
});;
                    var $grid = $("#productsGrid").jsGrid({
            height: "500px",
                    width: "98%",
                    filtering: true,
                    editing: false,
                    inserting: false,
                    sorting: true,
                    paging: true,
                    autoload: true,
                    pageSize: 100,
                    pageButtonCount: 5,
                    pageLoading: true,
                    deleteConfirm: "Do you really want to delete the User?",
                    fields: [
                         {name: "sno", title: "S.No.", type: "text", width: 30, readOnly: true, visible: true, inserting: false, editing: false, sorting: false,filtering:false,
                           },
                    {name: "product_id", title: "#", type: "text", width: 30, readOnly: true, visible: false, inserting: false, editing: false, sorting: false,filtering:false,
                            sorter: "number" ,css:"id_td"},
                          {name: "product_code", title: "Product Code", type: "text", width: 30, readOnly: true, visible: true, inserting: false, editing: false, sorting: false,filtering:false,
                            sorter: "number" ,css:"id_td"},
                    {name: "product_name", title: "Name", type: "text", width: 90, validate: "required",css:"product_td"},
                      {name: "total_purchase", title: "Total Purchase", type: "text",filtering:false,sorting:false, width: 90, validate: "required",css:"product_td"},
                       {name: "total_sales", filtering:false,sorting:false,title: "Total Sales", type: "text", width: 90, validate: "required",css:"product_td"},

                    {name: "stock", title: "Stock",filering:false,sorting:false, type: "text", width: 40, filtering:false,css:"stock_td"},
                             {name: "value", title: "Value", type: "text", width: 40, filtering:false,visible:false}
                    ],
                    controller: {
                    loadData: function (filter) {
filter.date=$("#date_select").val();
                    return $.ajax({
                    type: "GET",
                            url: "<?php echo site_url('billing-users/stock/fetch'); ?>",
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
    $(".jsgrid-table").addClass("table");
                            // cancel loading data if 'name' is empty
                            $(".jsgrid-insert-row").css("display", "none");
                            },
                            onItemInserted:function(args){
                            $("#productsGrid").jsGrid('loadData');
                            },
                            onItemUpdated:function(args){
                            $("#productsGrid").jsGrid('loadData');
                            },
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
                    $tr = "<tr class='date_holder'><td></td><td></td>";
<?php foreach ($months as $value) { ?>
                $tr = $tr + "<td colspan='3'>";
                        $tr = $tr + "<?php echo $value ?>";
                        $tr = $tr + "</td>";
<?php } ?>
           // $('.jsgrid-table tr:first').before($tr);
                $(".jsgrid-table").addClass("table");
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
<style type="text/css">
    .date_holder td{
        text-align: center;
        font-size: 12px;
    }
    .date_select_holder{
        margin: 10px;
    }
    @media print {
     table {
    table-layout: fixed;
    word-wrap: break-word;
    overflow: visible;
}
th,td{
    width: 50px !important;
}
    
}
</style>

