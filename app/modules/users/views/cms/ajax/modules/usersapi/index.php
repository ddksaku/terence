<script>
$(document).ready(function(){	
	
        $('.user-change-status').change(function()
        {
            ajaxCallAPI(
                'users',
                'post',
                'status',
                null,
                {
                    id: $(this).data('user-id'),
                    status: $(this).attr('checked') ? 1 : 0
                },
                function(data)
                {
                    if (data.error == "1") {
                        alertMessage('error','There was an error!');  
                    } else {
                        alertMessage("success", "User status updated");
                        
                        synergyModuleUsersRefresh();
                    }

                    $.fancybox.close();
                }
            );
        });
        
        
	
	// Fancybox 
	$(".pop_box").fancybox({ 
		'showCloseButton': false,
			
		onStart     :   function() {
		
			SCROLL_POS = $('html').scrollTop();
			$("html, body").animate({ scrollTop: 0 }, 0);
		
		},
		
		onClosed    :   function() {
		
			$("html, body").animate({ scrollTop: SCROLL_POS }, "slow");
		
		}
	 });
	
	
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
          { "bSortable": false, "aTargets": [ 0,7,8] }
       ],
	   "aoColumns": [
			null,
			null,
			null,
			null,
			null,
			null,
			{ "sSortDataType": "dom-checkbox" },
			null,
			null
		]
	});
	
	
	// Select boxes
	$("select").not("select.chzn-select,select[multiple],select#box1Storage,select#box2Storage").selectBox();
	
	
	// Delete user  
	   $("#deleteuser").live('click',function(e) {
		   
		         e.preventDefault();
		   
				 var name = $(this).attr("name");
				 Deleteuser($(this).attr("rel"), name);
	  });
	  function Deleteuser(datavalue, name){
			  $.confirm({
			  'title': 'DELETE USER ('+name+')','message': "Do you want to delete this user?",'buttons': {'Yes': {'class': 'btn btn-success',
			  'action': function() {
                                loading('Deleting',1);

                                synergyDeleteUser(datavalue);
                            }},
                            'No' : {'class'	: 'btn btn-danger'}}});
	  }
          
          function synergyDeleteUser(user_id)
          {
              if (user_id instanceof Array) {
                  user_id = user_id.join(',');
              }

              ajaxCallAPI(
                    'users',
                    'post',
                    'delete',
                    null,
                    {
                        id: user_id
                    },
                    function(data)
                    {
                        if (data.error == "1") { 
                            alertMessage("error","You don't have permission to delete this user!"); 

                        }	else {
                              alertMessage('success','Success');

                              synergyModuleUsersRefresh();
                        }

                        $.fancybox.close();

                        setTimeout('unloading()',900);						

                        return false;
                    }
                );
          }
	  
		function Deletejob(){
                    var delete_ids = new Array();
                    loading('Deleting',1);
                    $('input.delete-user:checked').each(function() {
                        var the_id = $(this).val();
                        delete_ids.push(the_id);
                    });
                    
                    synergyDeleteUser(delete_ids);
		}
	  
	  $('.DeleteAll').live('click',function() {
		   
		   var checked = $('input.delete-user:checked').length;
		   
		   if(checked > 0){
			   
			   $.confirm({
				  'title': 'DELETE ALL','message': "Do you want to delete all selected users?",'buttons': {'Yes': {'class': 'btn btn-success',
				  'action': function(){ 
				  Deletejob();
				  }},'No'	: {'class'	: 'btn btn-danger'}}});
			  
		  }else{
		   
		       //Nothing was selected
		      alertMessage('error','Please select a user to delete')
	   
		   }

     });
     
     
});

</script>


                              <div class="btn-group pull-top-right btn-square">
                                <a class="btn btn-large synergy-modal" href="usersapi/edit"><i class="icon-plus"></i> Add User</a>
                                <?php if($user->hasPermission('edit_users_settings')): ?>
                                <a class="btn btn-large synergy-modal" href="usersapi/settings"><i class="icon-cogs"></i> Settings</a>
                                <?php endif; ?>
                                <a class="btn btn-large btn-danger DeleteAll white"><i class="icon-trash"></i> Delete Selected</a>
                              </div>
                              <form class="users-table">
                                <table class="table table-bordered table-striped  data_table3" id="data_table3">
                                <thead>
                                  <tr align="center">
                                    <th width="15">
										<div class="checksquared"><input type="checkbox" class="checkAll" /><label></label></div>
									</th>
                                    <th width="352" align="left">Name</th>
                                    <th width="174">Email Address</th>
                                    <th width="246">Level</th>
                                    <th width="246">Added</th>
									<th width="246">Updated</th>
									<th width="199" class="child_7">Active</th>
                                    <th width="50" class="child_8">Edit</th>
                                    <th width="50" class="child_9">Delete</th>
                                  </tr>
                                </thead>
                                <tbody align="center">
		<?php
                
                $user_level = $user->getHighestLevel();
                
                foreach ($users as $site_user) {
                    if (!$user->hasPermission('manage_level_'.$site_user->getHighestLevel()) && $site_user->user_id != $user->user_id) {
                        continue;
                    }
                    
                    ?>
                                    
                  <tr>
                    <?php if($site_user->user_id == $user->user_id): ?>
                        <td width="15" style="background-color:#c2cd23;">
                        </td>
                    <?php else: ?>
                        <td width="15">
                            <div class="checksquared">
                                <input class="delete-user" type="checkbox" name="id" value="<?php echo $site_user->user_id; ?>">
                                <label></label>
                            </div>
                        </td>
                    <?php endif; ?>

                    <td align="left">
                        <?php echo $site_user->getFormalName(); ?>
                    </td>
                    
                    <td>
                        <?php echo $site_user->user_email; ?>
                    </td>

                    <td>
                        <?php echo $site_user->getUserLevel(); ?>
                    </td>

                    <td>
                        <?php echo $site_user->user_created->format('j M Y'); ?>
                    </td>
                    
                    <td>
                        <?php echo $site_user->user_updated->format('j M Y'); ?>
                    </td>

                    <td>
                        <div class="checkslide <?php if($site_user->user_id == $user->user_id): ?> hidedisabled <?php endif; ?>">
                            <input
                                type="checkbox"
                                id="status"
                                name="status"
                                value="1"
                                <?php if($site_user->user_active): ?>
                                    checked
                                <?php endif; ?>
                                <?php if($site_user->user_id == $user->user_id): ?>
                                    disabled
                                <?php endif; ?>
                                class="user-change-status"
                                data-user-id="<?php echo $site_user->user_id; ?>"
                                >
                            <label for="checkslide"></label>
                        </div>
                    </td>
                    
                    <td>
                        <a
                            href="usersapi/edit"
                            data-id="<?php echo $site_user->user_id; ?>"
                            class="synergy-modal"
                            >
                                <img src="images/icon/gray_18/pencil.png" alt="Edit"/>
                        </a>
                    </td>
                    
                    <td>
                        <div
                            <?php if($site_user->user_id == $user->user_id): ?>
                                class="hidedisabled"
                            <?php else: ?>
                                id="deleteuser"
                                rel="<?php echo $site_user->user_id; ?>"
                                name="<?php echo $site_user->user_username; ?>"
                            <?php endif; ?>
                            >
                            <?php if($site_user->user_id != $user->user_id): ?>
                                <a href="#">
                            <?php endif; ?>
                                    <img src="images/icon/gray_18/trash_can.png" alt="Delete"/>
                            <?php if($site_user->user_id != $user->user_id): ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                  </tr>
                    <?php
                }
                
                ?>
                
                                </tbody>
                              </table>
                              </form>
                              
