
<div class="row">
    <h3>Business Contacts </h3>
    <div class="row">
        <!--<div class="col-md-12 btn_holder">
        <button class="insert_btn btn btn-sm btn-success"> <i class="fa fa-plus"></i> Add</button>
         <button class="close_btn btn btn-sm btn-danger"> <i class="fa fa-plus"></i> Close</button>
        </div>-->
    </div>
    <div id="contactsGrid" >


    </div>
</div>
<script type="text/javascript">
    $(function () {

    var $grid = $("#contactsGrid").jsGrid({
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
            deleteConfirm: "Do you really want to delete the Contact?",
            fields: [
            {name: "sno", title: "S.No.", type: "text", width: 30, readOnly: true, visible: true, inserting: false, editing: false, sorting: true, filtering:false,
                    sorter: "number"},
            {name: "business_contact_id", title: "Cust ID", type: "text", width: 50, readOnly: true, visible: true, inserting: false, editing: false, sorting: true, filtering:false,
                    sorter: "number"},
            {name: "business_contact_name", title: "Name", type: "text", width: 80, validate: "required"},
            {name: "ref_by", title: "Ref By", type: "text", width: 80, align:"left"},
            {name: "business_contact_email", title: "Email", type: "text", align:"left", width: 120, validate: "email"},
            {name: "business_contact_mobile", title: "Mobile", type: "text", width: 100, align:"left", validate: "floatwithnull"},
            {name: "landline", title: "Landline", type: "text", width: 80, align:"left"},
            {name: "address", title: "Address", type: "textarea", width: 120, align:"left"},
            {name: "tin_no", title: "TIN", type: "text", width: 80, align:"left", visible:false},
            {name: "gstin", title: "GSTIN", type: "text", width: 80, align:"left"},
              
            {name: "cst_no", title: "CST", type: "text", width: 80, align:"left", visible:false},
            {name: "dl_no", title: "DL No.", type: "text", width: 80, align:"left", visible:false},
            {name: "type_id",
                    type: "select",
                    title:" Type",
                    width: 70,
                    autosearch: true,
                    validate: ["required", function (value, item) {
                    item_value = item.business_contact_type;
                    if (item_value === 0){
                    return false;
                    }
                    return true;
                    }],
                    align: "left",
                    valueType: "number",
                    items: <?php echo $contact_type_options; ?>,
                    valueField: "type_id", // name of property of item to be used as value
                    textField: "type_name",visible:true
            },
            {name: "contact_type_id",
                    type: "select",
                    title:"Contact Type",
                    width: 70,
                    autosearch: true,
                    validate: ["required"],
                    align: "left",
                    valueType: "number",
                    items: <?php echo $business_type_options; ?>,
                    valueField: "contact_type_id", // name of property of item to be used as value
                    textField: "contact_type"
            },
            {name: "is_active",
                    type: "select",
                    title:"Active",
                    width: 50,
                    autosearch: true,
                    validate: "required",
                    align: "left",
                    valueType: "number",
                    items: [{"id":"", "label":"Select"}, {"id":"1", "label":"Active"}, {"id":"0", "label":"InActive"}],
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
                    url: "<?php echo site_url('billing-users/business_contacts/fetch'); ?>",
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
            url: "<?php echo site_url('billing-users/business_contacts/save'); ?>",
            data: {data: items, type: type},
            dataType: "json",
            success: function (response) {


            //   $grid.jsGrid("refresh");



            }

    });
    }


</script>

