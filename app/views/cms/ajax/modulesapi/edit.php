<script>
$(document).ready(function(){	
    
    $('.chzn-module-pages select').change(function()
    {
        if ($(this).val()) {
            $('#name').attr('disabled', 'disabled');
        } else {
            $('#name').removeAttr('disabled');
        }
    });

    //////////////////////////////////////////////////////
	// User Level Radios
	//////////////////////////////////////////////////////
	
	//Mark higher values on radio load
	var index = $('.user-level:checked').index('.user-level');
	
	//Check only lower values
	$('.user-level').each(function(key,val){
	
		//Get inner index
		var i = $(this).index('.user-level');
                
		//Check lower values
		if(i <= index){
		
			$(this).attr('checked', true);
		
		}
	
	
	});
	
	
	
	$('.user-level').die();
	$('.user-level').live('click', function(){
	
	    $('.user-level').removeAttr('checked');
		
		//Get current index
		var index = $(this).index('.user-level');
		
		
		//Check only lower values
		$('.user-level').each(function(key,val){
		
		    //Get inner index
			var i = $(this).index('.user-level');
			
			//Check lower values
			if(i <= index){
			
			    $(this).attr('checked', true);
			
			}
		
		
		});
	
	});
	//////////////////////////////////////////////////////
	// User Level Radios END
	//////////////////////////////////////////////////////
	
	
    //Select
	$(".chzn-select").chosen();
	
	
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
	

	
	//Icon Dropdown list
	$("#menuicon").msDropDown();
	

	
	

 });	





	function Add_database_light(form, options)
        {
            loading('Loading',0);

            ajaxCallAPI(
                'modules',
                'post',
                'edit',
                null,
                form.serialize(),
                function(data)
                {
                    if (data.success == 0) {
                          // loading remove
                          $('#preloader').fadeOut(400,function(){ $(this).remove(); });		

                          //MySQL Query error
                          alertMessage("error", "Sorry please try again");

                          return false;
                    } else if(data.success == 1) {
                        // show error messages
                        alertMessage("success","Module edited successfully");

                        // reload data
                        synergyRefreshModulesList();
                        
                        // reload menu
                        synergyUpdateMenu(data.modules);

                        $.fancybox.close();
                        setTimeout('unloading()', 500);
                    }
                }
            );
	}
</script>
<div class="modal_dialog" style="min-height:50px">
  <div class="header">
      <span>EDIT MODULE (<?php echo $module->getName(); ?>)</span>
      <div class="close_me pull-right"><a href="javascript:void(0)" id="close_windows" class="butAcc"><img src="images/icon/closeme.png" /></a></div>
  </div>
  <div class="content">
			<form name="usersubmit" id="usersubmit-lightbox">
				
                 <div class="section">
                          <label>User Level</label>
                          <div>
                              <?php

                              foreach ($groups as $group) {
                                  if (!$user->hasPermission('manage_module_level_'.$group->group_level) && $group->group_level != $module->module_view_level) {
                                      continue;
                                  }
                                  
                                  ?>
                            <div class="radiorounded">
                                <input
                                    type="radio"
                                    id="radiorounded<?php echo $group->group_id; ?>"
                                    name="userlevel_<?php echo $group->group_id; ?>"
                                    value="<?php echo $group->group_id; ?>"
                                    class="user-level"
                                    <?php if ($module->module_view_level == $group->group_level): ?>
                                        checked
                                    <?php endif; ?>
                                    >
                                <label for="radiorounded<?php echo $group->group_id; ?>" title="<?php echo $group->group_name; ?>">
                                </label>
                            </div>
                                  <?php
                              }
                              
                              ?>
                          </div>
                  </div>
                            
                    <div class="section">
                        <label>Users<small>Select users to exclude</small></label>
                        <div>
                            <select class="chzn-select" name="chzn-select[]" multiple tabindex="4">
                               <option value=""></option>
                                <?php

                                $blocked_users = $module->blockedUsers->lists('user_username', 'user_id');

                                foreach ($users as $site_user) {
                                    if (!$user->hasPermission('block_level_'.$site_user->getHighestLevel())) {
                                        continue;
                                    }
                                        
                                    ?>
                                <option
                                    value="<?php echo $site_user->user_id; ?>"
                                    <?php if(isset($blocked_users[$site_user->user_id])): ?>
                                        selected
                                    <?php endif; ?>
                                    >
                                    <?php echo $site_user->getFullName(); ?> (<?php echo $site_user->user_username; ?>)
                                </option>
                                   <?php
                                }

                                ?>
                            </select>
                        </div>
                    </div>
                            
                    <div class="section">
                        <label>Page</label>
                        <div class="chzn-module-pages">
                            <select class="chzn-select" name="module_page" tabindex="4">
                               <option value="">-</option>
                                <?php

                                foreach ($pages as $page) {
                                    ?>
                                <option
                                    value="<?php echo $page->page_id; ?>"
                                    <?php if($page->page_id == $module->module_page_id): ?>
                                        selected
                                    <?php endif; ?>
                                    >
                                    <?php echo $page->page_title; ?>
                                </option>
                                   <?php
                                }

                                ?>
                            </select>
                        </div>
                    </div>
				
                  <?php if($user->hasPermission('activate_modules')): ?>
                    <div class="section">
                        <label>Active</label>
                        <div>
                            <div class="checkslide" >
                                <input type="checkbox" id="status" name="status" value="1" <?php if($module->isInstalled()): ?>checked="checked"<?php endif; ?>>
                                <label for="checkslide"></label>
                            </div>
                        </div>
                    </div>
                  <?php endif; ?>

                  <div class="section">
                    <label>Icon</label>   
                        <div>
                            <select name="menuicon" id="menuicon">

                            <?php

                            //Loop through images
                            foreach ($icons as $icon) {
                                ?>
                            <option
                                value="<?php echo $icon->getName(); ?>"
                                data-image="<?php echo $icon->getURL(); ?>"
                                <?php if($icon->getName() == $module->module_icon): ?>
                                    selected
                                <?php endif; ?>
                                >
                            </option>
                                <?php
                            }
                            
                            ?>

                            </select>
			</div>
                    </div>

				 
                            <div class="section">
                                <label>Module</label>   
                                <div>
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    class="validate[required,minSize[2],maxSize[40] ] medium"
                                    value="<?php echo $module->getName();?>"
                                    <?php if(!$user->hasPermission('rename_modules') || $module->page): ?>
                                        disabled
                                    <?php endif; ?>
                                >
                                </div>
                            </div>

			
				 <div class="section last">
					  <div><a class="btn btn-success sendform-lightbox">Update</a>
                      
                      <a id="close_windows" class="btn btn-danger butAcc">Cancel</a></div>
				 </div>
				 <input type="hidden" id="id" name="id" value="<?php echo $module->module_id; ?>" />
			</form>
  </div>
</div>