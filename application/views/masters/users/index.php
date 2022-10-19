
<div class="row">
    <h3>Users</h3>
    <div class="row">
        <!--<div class="col-md-12 btn_holder">
        <button class="insert_btn btn btn-sm btn-success"> <i class="fa fa-plus"></i> Add</button>
         <button class="close_btn btn btn-sm btn-danger"> <i class="fa fa-plus"></i> Close</button>
        </div>-->
    </div>
    <div id="userGrid" >


    </div>
</div>
<script type="text/javascript">
    $(function () {

        var $grid = $("#userGrid").jsGrid({
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
                {name: "user_id", title: "User #", type: "text", width: 50, readOnly: true, visible: true, inserting: false, editing: false, sorting: true,
                    sorter: "number"},
                {name: "first_name", title: "First Name", type: "text", width: 80, validate: "required"},
                {name: "middle_name", title: "Middle Name", type: "text", width: 80},
                {name: "last_name", title: "last Name", type: "text", width: 80},
                {name: "user_email",
                    type: "text",
                    width: 150,
                    title: "User Email",
                    validate: [
                        function (value, item) {
                            item_value = item.user_email;
                            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                            return regex.test(item_value);
                        }
                    ]},
                {name: "password", title: "Password", type: "text", width: 80},
                {name: "user_mobile",
                    type: "number",
                    width: 150,
                    align:"left",
                    validate: "required", 
                    title: "User Mobile"},
                {name: "company_id",
                    type: "select",
                    title: "Company",
                    width: 110,
                    autosearch: true,
                    validate: ["required", function (value, item) {
                            item_value = item.company_id;

                            if (item_value === 0) {
                                return false;
                            }
                            return true;
                        }],
                    align: "left",
                    valueType: "number",
                    items: <?php echo $company_options; ?>,
                    valueField: "company_id", // name of property of item to be used as value
                    textField: "company_name"
                },
                {name: "user_role_id",
                    type: "select",
                    title: "User Role",
                    width: 80,
                    autosearch: true,
                    validate: ["required", function (value, item) {
                            item_value = item.user_role_id;

                            if (item_value === 0) {
                                return false;
                            }
                            return true;
                        }],
                    align: "left",
                    valueType: "number",
                    items: <?php echo $role_options; ?>,
                    valueField: "user_role_id", // name of property of item to be used as value
                    textField: "user_role"
                },
                {name: "is_active",
                    type: "select",
                    title: "Active",
                    width: 50,
                    autosearch: true,
                    validate: "required",
                    align: "left",
                    valueType: "number",
                    items: [{"id": "", "label": "Select"}, {"id": "1", "label": "Active"}, {"id": "0", "label": "InActive"}],
                    valueField: "id", // name of property of item to be used as value
                    textField: "label",
                },
                {type: "control", modeSwitchButton: true, editButton: true}
            ],
            controller: {
                loadData: function (filter) {

                    return $.ajax({
                        type: "GET",
                        url: "<?php echo site_url('masters/users/fetch'); ?>",
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
            onDataLoaded: function (args) {

                // cancel loading data if 'name' is empty
                $(".jsgrid-insert-row").css("display", "none");
            },
            onItemInserted:function(args){
               $grid.jsGrid('loadData');
            },
             onItemUpdated:function(args){
                 $grid.jsGrid('loadData');
            
                  
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
            url: "<?php echo site_url('masters/users/save'); ?>",
            data: {data: items, type: type},
            dataType: "json",
            success: function (response) {

          
              

  

            }

        });

    }


</script>

