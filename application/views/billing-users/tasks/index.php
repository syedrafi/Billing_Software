
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('ui.jqgrid.css'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('ui.jqgrid-bootstrap.css'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.theme.css'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo css_url('jquery-ui.structure.min.css'); ?>" />

<link rel="stylesheet" href="<?php echo css_url('jquery-ui.min.css')?>">

<script src="<?php echo js_url('jquery-ui.min'); ?>"></script>
<link href="<?php echo css_url('select2.min.css'); ?>" rel="stylesheet" />
<script src="<?php echo js_url('select2.min.js')?>"></script>

<script src="<?php echo js_url('i18n/grid.locale-en.js'); ?>" type="text/javascript"></script>
<script src="<?php echo js_url('jquery.jqGrid.min.js'); ?>" type="text/javascript"></script>

<div  class="right-sidebar">
    
    
    <div class="col-md-12" >
        <h3 class="pull-left">Tasks </h3><button id="print_button"type="button" class="btn btn-success print_hide_button btn-lg pull-right">Print</button><div class="clearfix"></div>
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
              <?php // echo $this->form; ?>
        <div style="clear:both; height: 10px;"></div>
                 
                     <table id="list2" style="width: 100%;"></table>
<div id="pager2"></div>
      
             
</div>
</div>
<script type="text/javascript">
jQuery("document").ready(function(){
    
 var $table = $("#list2");
jQuery($table).jqGrid({
   	url:'',
	datatype: "json",
   	colNames:['Ticket#','Task Name','Task Descrition','Task From','Task Deadline','Task Lead','Score','Task Users','Created By','Recurring Type','Recurring Times','Send SMS','Remarks','Status','Actions'],
   	colModel:[
   		{name:'task_id',index:'task_id', width:125,key: true},
   		{name:'task_name',index:'task_name',width:220, align:"left",editable:true,editrules: {required: true},edittype:"textarea"},
                {name:'task_desc',index:'task_desc',width:320, align:"left",editable:true,editrules: {required: true},edittype:"textarea"},
   		
             {name:'task_from',index:'task_created_time', width:120, align:"left",editable:true,editoptions:{
             "dataInit":function(elem){
       setTimeout(function(){
          $(elem).datepicker({dateFormat: 'dd-mm-yy'});
       },100);
            
            
            
        },defaultValue:'<?php echo date('d-m-Y'); ?>'},editrules: {required: true}} ,
        {name:'task_to',index:'task_deadline', width:120, align:"left",editable:true,editoptions:{
             "dataInit":function(elem){
       setTimeout(function(){
          $(elem).datepicker({dateFormat: 'dd-mm-yy'});
       },100);
            
            
            
        },defaultValue:'<?php $datetime = new DateTime('tomorrow');
echo $datetime->format('d-m-Y'); ?>'},editrules: {required: true}} ,
      
        
       {name:'task_lead',index:'task_lead',width:170, align:"left",editable:true,editrules: {},edittype:"select",multiple:true,formatter:'select',
     stype:"select", 
searchoptions: { 
value: "<?php  echo $user_options; ?>"

}, editoptions:{
          value:"<?php  echo $user_options; ?>",  multiple:true,  "dataInit":function(elem){
    
             $(elem).select2({placeholder: "Select Leads",
allowClear: true,width:"100%"});
            
            
        },
         
           
      }},
      {name:'task_score',index:'task_score',width:50, align:"left",editable:true,editrules: {required: true},editoptions:{
          defaultValue:1
         
           
      }},
     
       {name:'task_users',index:'task_users',width:190, align:"left",editable:true,editrules: {},edittype:"select",multiple:true,formatter:'select',    stype:"select", 
searchoptions: { 
value: "<?php   echo $user_options;  ?>"

}, 
      editoptions:{
          value:"<?php  echo $user_options; ?>",
          multiple:true,  "dataInit":function(elem){
   
        $(elem).select2({placeholder: "Select Users",
allowClear: true,width:"100%"});
       
        }
         
           
      }},
         {name:'task_created_user',search:false,index:'task_created_user',width:120, align:"left",editable:false,editrules: {},edittype:"select",multiple:true,formatter:'select',
      editoptions:{
          value:"<?php  echo $user_options; ?>"
    
         
           
      }},
   
    {name:'recurring_type',index:'recurring_type',width:120, align:"left",editable:true,editrules: {required: true},edittype:"select",multiple:true,formatter:'select',
stype:"select", 
searchoptions: { 
value: "<?php  echo $recurring_types;  ?>"

}, 
      editoptions:{
          value:"<?php  echo $recurring_types;  ?>",   "dataInit":function(elem){
    
           
            
            
        }
         
           
      }},
       {name:'recurring_times',index:'recurring_times',search:false,width:80, align:"left",editable:true,editrules: {},editoptions:{defaultValue:1 
         
           
      }},
          {name:'send_sms',index:'send_sms',width:120, align:"left",editable:true,editrules: {required: true},edittype:"select",multiple:true,formatter:'select',
stype:"select", 
searchoptions: { 
value: "1:Yes;2:No"

}, 
      editoptions:{
          value:"1:Yes;2:No",   "dataInit":function(elem){
    
           
            
            
        }
         
           
      }}, 
       {name:'Remarks',index:'Remarks',width:210, align:"left",editable:false,search:false},
        {name:'status',index:'status',width:200, align:"left",editable:false,editrules: {},edittype:"select",formatter:'select',
stype:"select", 
searchoptions: { 
value: ":Select;1:Completed;2:Pending"

}, 
      editoptions:{
          value:":Select;1:Completed;2:Pending",   "dataInit":function(elem){
    
           
            
            
        }
         
           
      }},
      {name:'actions',search:false,classes:'iframe iframeremove',index:'actions',formatter:'showlink', formatoptions:{baseLinkUrl:''}, width:100, align:"left"},
        
        ],
   rowNum:50,
   	rowList:[50,75,100],
   
   	pager: '#pager2',
   	sortname: 'task_created_time',
       gridview: true,
       rownumbers: true,
      
    viewrecords: true,
loadComplete:function(){
  jQuery("#gs_task_lead,#gs_task_users").select2({
      allowClear:true,
      width:"100"
  });  
},
     autowidth: true,
 height: 550,
 toppager:true,
 width:500,
 rowheight: 300,
    sortorder: "asc",
    caption:"Task Details",
   
   editurl: "<?php echo base_url('index.php/billing-users/tasks/operations'); ?>"
 
});

jQuery("#list2").jqGrid('navGrid','#pager2', {cloneToTop:true}, {  recreateForm: true,
    
height:670,width:600,modal: true,closeAfterEdit:true,onclickSubmit : function(params, posdata) {  
 $(".topinfo").html("ReAssigning, Please Wait.."); 
      var tinfoel = $(".tinfo").show();
},
afterSubmit: function (response, postdata) { 
  if(response.status == 200){ 
  
      $(".topinfo").html(response.responseText); 
      var tinfoel = $(".tinfo").show();
  tinfoel.delay(3000).fadeOut();
   
        $table.trigger('reloadGrid');
  
    
      return [true,''];
} else {
     $(".topinfo").html("Error Occured. Try Later."); 
      return [false,'error message'];
}
} },  { recreateForm: true,height:730,modal: true,width:600,closeAfterAdd:true,afterSubmit: function (response, postdata) { 
  if(response.status == 200){ 
    
      $(".topinfo").html(response.responseText); 
      var tinfoel = $(".tinfo").show();
  tinfoel.delay(3000).fadeOut();
     $table.trigger('reloadGrid');
  
      return [true,''];
}else {
     $(".topinfo").html("Error Occured. Try Later."); 
      return [false,'error message'];
}
},onclickSubmit : function(params, posdata) {  
  $(".topinfo").html("Assigning, Please Wait.."); 
      var tinfoel = $(".tinfo").show();
}},  {height:200,width:730,afterSubmit: function (response, postdata) { 
  if(response.status == 200){ 
      
      $(".topinfo").html(response.responseText); 
      var tinfoel = $(".tinfo").show();
     // tinfoel.delay(3000).fadeOut();
      return [true,''];
} else {
     $(".topinfo").html("Error Occured. Try Later."); 
      return [false,'error message'];
}
},onclickSubmit : function(params, posdata) {  
  $(".topinfo").html("Assigning, Please Wait.."); 
      var tinfoel = $(".tinfo").show();
}}, {height:200,width:800,afterSubmit: function (response, postdata) { 
  if(response.status == 200){ 
      
      $(".topinfo").html(response.responseText); 
      var tinfoel = $(".tinfo").show();
      //tinfoel.delay(3000).fadeOut();
      return [true,''];
} else {
     $(".topinfo").html("Error Occured. Try Later."); 
      return [false,'error message'];
}
},onclickSubmit : function(params, posdata) {  
  $(".topinfo").html("Assigning, Please Wait.."); 
      var tinfoel = $(".tinfo").show();
}} ).navButtonAdd('#list2_toppager_left',{
                                caption:"Export", 
                                buttonicon:"ui-icon-save", 
                                onClickButton: function(params, posdata){ 
                                  exportExcel();
                                }, 
                                position:"last"
                            });


jQuery("#list2").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false});
jQuery(".task_user_search").select2( {placeholder: "Select a State",
allowClear: true});


});

function exportExcel()
    {
        
        var mya=new Array();
        var myGrid = $('#list2')

       mya=$("#list2").getDataIDs();  // Get All IDs
   
        var data=$("#list2").getRowData(mya[0]);     // Get First row to get the labels
        var colNames=new Array(); 
        var ii=0;
        for (var i in data){colNames[ii++]=i;}    // capture col names
        var html="";
            for(k=0;k<colNames.length;k++)
            {
            html=html+colNames[k]+"\t";     // output each Column as tab delimited
            }
            html=html+"\n";   
            // Output header with end of line
            
        for(i=0;i<mya.length;i++)
            {
               
            
            data=$("#list2").getRowData(mya[i]); // get each row
            for(j=0;j<colNames.length;j++)
                {
                  
                
        html=html+data[colNames[j].toString()]+"\t"; // output each Row as tab delimited
                }
            html=html+"\n";  // output each row with end of line

            }
        html=html+"\n";  // end of line at the end
            
        document.forms[0].csvBuffer.value=html;
     
        document.forms[0].method='POST';
    
document.forms[0].action='<?php echo base_url(); ?>/staffs/admission/buffer-excel';  // send it to server which will open this contents in excel file
     
    document.forms[0].target='_blank';
   
      
      document.forms[0].submit();
    }

</script>
<style type="text/css">
    form{
        margin-bottom: 0;
    }
</style>
<form method="post" action="<?php echo base_url(); ?>/staffs/admission/buffer-excel">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value=""  />
</form>