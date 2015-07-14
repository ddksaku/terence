<?php

$has_activate_permissions = $user->hasPermission('activate_modules');
$has_delete_permissions = $user->hasPermission('delete_modules');

?>

<script>
$(document).ready(function(){

//////////////////////////////////////////////////
//New modules ordering
//////////////////////////////////////////////////
$('.reorder').die();
$('.reorder').live('click', function()
{
    
    var reorder_row = $(this).parents('.reorder-row');
    
    if (reorder_row.length) {
        var direction = ($(this).attr('name') == 'up')
                            ? 1
                            : 0;
                            
        var relative_row = direction
                            ? reorder_row.prev('.reorder-row')
                            : reorder_row.next('.reorder-row');
                            
        if (relative_row.length) {
            ajaxCallAPI(
                'modules',
                'post',
                'order',
                null,
                {
                    id: reorder_row.data('module-id'),
                    relative: relative_row.data('module-id'),
                    direction: direction
                },
                function(data)
                {
                    if (data.error == "1") {
                        setTimeout("alertMessage('error','There was an error on ReOrder !')", 1000);  
                    } else {
                        setTimeout("alertMessage('success','Module Reordered!')", 1000);  
                    }

                    $.fancybox.close();

                    synergyRefreshModulesList();
                    
                    if (data.modules) {
                        synergyUpdateMenu(data.modules);
                    }
                }
            );
        }
    }

    return false;
});

// Fancybox 
$(".pop_box").fancybox(
{
    'showCloseButton': false,

    onStart: function() {
        SCROLL_POS = $('html').scrollTop();
        $("html, body").animate({ scrollTop: 0 }, 0);
    },

    onClosed: function() {
        $("html, body").animate({ scrollTop: SCROLL_POS }, "slow");
    }
});


var cols = [
    null,
    null,
    null,
    null,
    null,
    null
    <?php if ($has_activate_permissions): ?>
    ,{ "sSortDataType": "dom-checkbox" }
    <?php endif; ?>
    ,null
    <?php if ($has_delete_permissions): ?>
    ,null
    <?php endif; ?>
];

var targets = [
    0,
    1,
    5
    <?php if ($has_activate_permissions): ?>
    ,7
    <?php else: ?>
    ,6
    <?php endif; ?>
];

$('.data_table3').dataTable({
"sDom": "<'row-fluid tb-head'<'span6'f><'span6'<'pull-right'Cl>>r>t<'row-fluid tb-foot'<'span4'i><'span8'p>>",
"bJQueryUI": false,
"iDisplayLength": 10,
"sPaginationType": "bootstrap",
"oLanguage": {
"sLengthMenu": "_MENU_",
"sSearch": "Search"
},
"aoColumnDefs": [
{ "bSortable": false, "aTargets": targets }
],
"aoColumns": cols
});

// Select boxes
$("select").not("select.chzn-select,select[multiple],select#box1Storage,select#box2Storage").selectBox();

// Delete Module 
$("#deletemodule").live('click',function(e) {
    e.preventDefault();

    var name = $(this).attr("name");
    var datavalue ='id='+ $(this).attr("rel");   
    DeleteModule(datavalue, name);
});

function DeleteModule(datavalue, name){

$.confirm({
    'title': 'DELETE MODULE ('+name+')','message': "Do you want to delete this module?",'buttons': {'Yes': {'class': 'btn btn-success',
    'action': function()
    {
        loading('Deleting',1);
        $.ajax({
            url: "module_control/delete.php",
            data: datavalue,
            success: function(data){

                      if (data.check == "0"){ 
                                     $("#tab1 .load_page").fadeOut(500,function(){


                                                              setTimeout("alertMessage('error','You don't have permission to delete this module!')",1000);  


                                             // fancybox close
                                            $.fancybox.close();
                                        setTimeout('unloading()',900);						

                                            }).load('module_control/tableReload.php').fadeIn();			
                      return false;
                      }	   
                      if (data.check == "1"){
                              $("#tab1 .load_page").fadeOut(500,function(){


                                                              setTimeout("alertMessage('success','Success')",1000);  


                                             // fancybox close
                                            $.fancybox.close();
                                        setTimeout('unloading()',900);						

                                            }).load('module_control/tableReload.php').fadeIn();			
                      return false;
                      }
            },
            cache: false,
            type: "POST",
            dataType: 'json'
        });
    }},'No'	: {'class'	: 'btn btn-danger'}}});
}

function Deleteall(datavalue)
{
    $.ajax({
        url: "module_control/delete.php",
        data: datavalue,
        cache: false,
        type: "POST",
        dataType: 'json'
    });
}

function Deletejob()
{
    loading('Deleting',1);
    $('.checksquared').find('input[type=checkbox]:checked').each(function(){
        var the_id = $(this).val();
        var data = "id="+the_id;
        Deleteall(data);
    });

    $("#tab1 .load_page").fadeOut(500,function()
    {

    setTimeout("alertMessage('success','Success')",2000);  


    // fancybox close
    $.fancybox.close();
    setTimeout('unloading()',1500);						

    }).load('module_control/tableReload.php').fadeIn();
}

$('.DeleteAll').live('click',function() {
    var checked = $('.checksquared').find('input[type=checkbox]:checked').length;

    if (checked > 0) {
       $.confirm({
              'title': 'DELETE ALL','message': "Do you want to delete all selected modules?",'buttons': {'Yes': {'class': 'btn btn-success',
              'action': function(){ 
              Deletejob();
              }},'No'	: {'class'	: 'btn btn-danger'}}
       });
    } else {
        //Nothing was selected
        alertMessage('error','Please select a module to delete')
    }
});



});

</script>

<form class="module-holder">
<table class="table table-bordered table-striped  data_table3" id="data_table3">
<thead>
  <tr align="center">
    <th class="child_1">
        <div class="checksquared"><input type="checkbox" class="checkAll" /><label></label></div>
    </th>
    <th class="child_2">Icon</th>
    <th class="child_3">Module</th>
    <th class="child_6">Level</th>
    <th class="child_7">Users</th>
    <th class="child_8">Order</th>

    <?php if ($has_activate_permissions):  ?>
        <th class="child_9">Active</th>
    <?php endif; ?>

        <th class="child_10">Edit</th>

    <?php if ($has_delete_permissions): ?>
        <th class="child_11">Delete</th>
    <?php endif; ?>
  </tr>
</thead>
<tbody align="center">

<?php

$i = 1;

$blockedModules = $user->blockedModules->lists('module_name', 'module_id');

foreach($module_mgr->getModules() as $module)
{
    if (
        $module->module_view_level > $user->getHighestLevel()
        || isset($blockedModules[$module->module_id])
    ) {
        continue;
    }
    
    // Hide if module not active and user has no edit permissions.
    if (!$has_activate_permissions && !$module->isInstalled()) {
        continue;
    }

    //Trigger blacklist
    //if (is_blacklisted($_SESSION['user_id'], $arr['blacklist'])){
    //    continue;
    //}

    ?>

    <tr class="reorder-row" data-module-id="<?php echo $module->module_id; ?>">
        <td>
            <div class="checksquared"><input type="checkbox" name="id" value="<?php echo $module->module_id; ?>" /><label></label></div>
        </td>

        <td>
            <?php if(($icon = $module->getIcon())): ?>
                <a href="modulesapi/edit"
                    data-id="<?php echo $module->module_id; ?>"
                    data-pre-auth="yes"
                    class="synergy-modal"
                    >
                    <img src="/<?php echo $icon; ?>" alt="<?php echo $module->module_icon; ?>" class="table-icon">
                </a>
            <?php endif; ?>
        </td>

        <td>
            <?php echo $module->getName(); ?>
        </td>

        <td>
            <?php echo $module->getGroupName(); ?>
        </td>

        <td>
            <?php 

            if ($module->blockedUsers()->count() > 0) {
                ?>
            Custom
                <?php
            } else {
                ?>
            All
                <?php
            }

            ?>
        </td>

        <td>
            <div class="reorder" data-module-id="<?php echo $module->module_id; ?>" pos="<?php echo $module->module_order; ?>" name="up">
                <a><img src="images/icon/up.png" width="20" height="20">
                </a>
            </div> 

            <div class="reorder" data-module-id="<?php echo $module->module_id; ?>" pos="<?php echo $module->module_order; ?>" name="down">
                <a><img src="images/icon/down.png" width="20" height="20">
                </a>
            </div>
        </td>

        <?php if($has_activate_permissions): ?>
        <td>
            <div class="checkslide module-status">
                <input
                    type="checkbox"
                    id="status"
                    name="status"
                    value="1"
                    <?php if($module->isInstalled()): ?>
                        checked="checked"
                    <?php endif; ?>
                    data-module-id="<?php echo $module->module_id; ?>"
                    >

                <label for="checkslide"></label>
            </div>
        </td>
        <?php endif; ?>
        
        <td>
            <a
                href="modulesapi/edit"
                data-id="<?php echo $module->module_id; ?>"
                data-pre-auth="yes"
                class="synergy-modal"
                >
                <img src="images/icon/gray_18/pencil.png" alt="Edit"/>
            </a>
        </td>


        <?php if($has_delete_permissions): ?>
        <td>
            <div id="deletemodule" rel="<?php echo $module->module_id; ?>" name="<?php echo $module->getName(); ?>">
                <a href="#">
                    <img src="images/icon/gray_18/trash_can.png" alt="Delete"/>
                </a>
            </div>
        </td>
        <?php endif; ?>
    </tr>

    <?php

    $i++; 
}

?>

</tbody>
</table>
</form>

