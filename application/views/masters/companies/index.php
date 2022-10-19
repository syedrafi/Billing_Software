
<div class="row">
    <h3>Companies</h3>
    <div class="row">
    <!--<div class="col-md-12 btn_holder">
    <button class="insert_btn btn btn-sm btn-success"> <i class="fa fa-plus"></i> Add</button>
     <button class="close_btn btn btn-sm btn-danger"> <i class="fa fa-plus"></i> Close</button>
    </div>-->
    </div>
    <div id="jsGrid" >


    </div>
</div>
<script type="text/javascript">
    $(function () {

        var $grid = $("#jsGrid").jsGrid({
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
            deleteConfirm: "Do you really want to delete the client?",
            fields: [
                {name: "company_id", type: "text", width: 150, readOnly: true, visible: false, inserting: false, editing: false,    sorting: true,
    sorter: "number"},
                {name: "company_name", title: "Company Name", type: "text", width: 150, validate: "required"},
                {name: "company_email", type: "text", width: 150, title: "Company Email", validate: "required"},
                {name: "company_mobile", type: "text", width: 150, validate: "required", title: "Company Mobile", },
                {name: "company_address", type: "textarea", width: 150, validate: "required", title: "Company Address", },
                {name: "company_tin", type: "text", width: 150, title: "Company Tin"},
                {name: "company_cst", type: "text", width: 150, title: "Company CST"},
                {name: "company_dl_no", type: "text", width: 150, title: "Company DL No"},
                {type: "control", modeSwitchButton: true, editButton: true}
            ],
            controller: {
                loadData: function (filter) {

                    return $.ajax({
                        type: "GET",
                        url: "<?php echo site_url('masters/companies/fetch'); ?>",
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
                onDataLoaded: function(args) {
              
        // cancel loading data if 'name' is empty
       $(".jsgrid-insert-row").css("display","none");
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
});

    function save_data(items, type) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('masters/companies/save'); ?>",
            data: {data: items, type: type},
            dataType: "json",
            success: function (response) {


                $grid.jsGrid("refresh");



            }

        });

    }


</script>

