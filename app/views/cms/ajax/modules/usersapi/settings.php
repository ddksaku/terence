<?php if($user->hasPermission('edit_users_settings')): ?>

<script>
$(document).ready(function(){	
			// sendform-lightbox  click  
			$('.sendform-lightbox').click(function(){
						// search-form id   
						var form_id=$(this).parents('form').attr('id');
						// submit form
						$("#"+form_id).submit();
			});
			
			// validationEngine  select  
			var prefix = "selectBox_";
		    $('#usersubmit-lightbox').validationEngine({
					prettySelect : true,usePrefix: prefix,
					ajaxFormValidation: true,
					onBeforeAjaxFormValidation: Add_database_light
			});		
			$("select").selectBox();
			$('select').each(function(){ 
					$(this).next('a.selectBox')
					.attr("id", prefix + this.id )
					.removeClass("validate[required]");		
			  })
});	

	function Add_database_light(form, options){
		 loading('Loading',0);

                 ajaxCallAPI(
                    'users',
                    'post',
                    'settings',
                    null,
                    form.serialize(),
                    function(data)
                    {
                        if(data.success==0){   // uncomplete return 0
                                        // loading remove
                                  $('#preloader').fadeOut(400,function(){ $(this).remove(); });		
                                  // show error messages
                                  if(data.error){
                                    //MySQL Query error
                                    alertMessage("error","Sorry please try again");
                                  }

                                setTimeout('unloading()',500); 
                                  
                                   return false;
                          }
                          if(data.success==1){ // complete return 1
                                alertMessage("success","User settings changed.");

                                   // reload data
                                synergyModuleUsersRefresh()
                                
                                $.fancybox.close();
                                setTimeout('unloading()',500); 
                          }
                    }
                );			
	} 
</script>
<div class="modal_dialog" style="min-height:50px">
    <div class="header">
        <span>
            USER SETTINGS
        </span>
        <div class="close_me pull-right"><a href="javascript:void(0)" id="close_windows" class="butAcc"><img src="images/icon/closeme.png" /></a></div>
    </div>
    <div class="content">
        <form name="usersubmit" id="usersubmit-lightbox">
            <div class="section">
                <table width="100%">
                    <tr>
                        <td>
                        </td>
                        <?php foreach ($groups as $group): ?>
                            <th width="14%">
                                <?php echo $group->group_name; ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>

                    <?php foreach ($permissions as $permission => $permissionName): ?>
                        <tr>
                            <td>
                                <?php echo $permissionName; ?>
                            </td>
                            <?php foreach ($groups as $group): ?>
                                <td class="group-permission-checkbox">
                                    <input
                                        type="checkbox"
                                        name="permission__<?php echo $permission; ?>__<?php echo $group->group_id; ?>"
                                        value="1"
                                        <?php if ($group->hasPermission($permission)): ?>
                                            checked
                                        <?php endif; ?>
                                        <?php if ((!$user->hasPermission($permission) || $user->getHighestLevel() <= $group->group_level) && !$user->hasPermission('edit_all_permissions')): ?>
                                            disabled
                                        <?php endif; ?>
                                        >
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
                
            </div>
            
            <div class="section last">
                <div>
                    <a class="btn btn-success sendform-lightbox">Update</a>
                    
                    <a id="close_windows" class="btn btn-danger butAcc">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php endif; ?>