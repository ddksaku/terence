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
                    'profile',
                    null,
                    form.serialize(),
                    function(data)
                    {
                        if(data.success==0){   // uncomplete return 0
                                        // loading remove
                                  $('#preloader').fadeOut(400,function(){ $(this).remove(); });		
                                  // show error messages
                                  if(data.error == 1){
                                    //Error for duplicate email
                                    alertMessage("error","Duplicate email found, try again with other email");
                                  }else if(data.error == 2){
                                    //Error for duplicate username
                                    alertMessage("error","Duplicate username found, try again with other username");
                                  } else {
                                    //MySQL Query error
                                    alertMessage("error","Sorry please try again");
                                  }
                                  
                                  
                                setTimeout('unloading()',500); 
                                  
                                   return false;
                          }
                          if(data.success==1){ // complete return 1
                                  // show error messages
                                  alertMessage("success","User edited successfully");
                                  
                                   // reload data if applicable
                                    if (typeof synergyModuleUsersRefresh == 'function') {
                                        synergyModuleUsersRefresh();
                                    }
                                
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
            <?php if($new_user): ?>
                ADD USER
            <?php else: ?>
                EDIT USER (<?php echo $edit_user->user_username; ?>)
            <?php endif; ?>
      </span>
      <div class="close_me pull-right"><a href="javascript:void(0)" id="close_windows" class="butAcc"><img src="images/icon/closeme.png" /></a></div>
  </div>
  <div class="content">
			<form name="usersubmit" id="usersubmit-lightbox">
				
                            <div class="section">
                                <label>User Level</label>   
                                <div>
                                    <?php
                                    
                                    $user_level = $user->getHighestLevel();

                                    foreach ($groups as $group) {
                                        $in_group = ($edit_user->groups()->where('synergy_user_groups.group_id', '=', $group->group_id)->count() > 0);
                                        
                                        if (!$in_group) {
                                            continue;
                                        }
                                        
                                        ?>
                                    <div class="radiorounded">
                                        <input
                                            type="radio"
                                            id="radiorounded<?php echo $group->group_id; ?>"
                                            name="userlevel"
                                            <?php if($in_group): ?>
                                                checked
                                            <?php endif; ?>
                                            value="<?php echo $group->group_id; ?>"
                                            >
                                        <label
                                            for="radiorounded<?php echo $group->group_id; ?>"
                                            title="<?php echo $group->group_name; ?>"
                                            >
                                        </label>
                                    </div>
                                        <?php
                                    }
                                    
                                    ?>
                                </div>
                            </div>
                            
                  <div class="section">
                  	<label>Active</label>
                    <div>

                        <div
                                class="checkslide <?php if($edit_user->user_id == $user->user_id): ?> hidedisabled <?php endif; ?>"
                                >
                            <input
                                type="checkbox"
                                id="status"
                                name="status"
                                <?php if($new_user || $edit_user->user_active): ?>
                                    checked
                                <?php endif; ?>
                                value="1"
                                <?php if($edit_user->user_id == $user->user_id): ?>
                                    disabled
                                <?php endif; ?>
                                >
                            <label for="checkslide"></label>
                        </div>

                    </div>
                  </div>
				  <div class="section">
					  <label>Title<small>Select Title</small></label>   
				  	<div>
				  	<select name="titlename" id="titlename" class="validate[required]" style="width:100px;">
						
                         <option
                            value="Mr"
                            <?php if($edit_user->user_title == "Mr"): ?>
                                selected
                            <?php endif; ?>
                            >
                                Mr
                         </option>
                         
                         <option
                            value="Mrs"
                            <?php if($edit_user->user_title == "Mrs"): ?>
                                selected
                            <?php endif; ?>
                            >
                                Mrs
                         </option>
                         
                         <option
                            value="Miss"
                            <?php if($edit_user->user_title == "Miss"): ?>
                                selected
                            <?php endif; ?>
                            >
                                Miss
                         </option>
                         
                         <option
                            value="Ms"
                            <?php if($edit_user->user_title == "Ms"): ?>
                                selected
                            <?php endif; ?>
                            >
                                Ms
                         </option>

					</select> 
					</div>
				 </div>
				 <div class="section">
					  <label>First Name</label>   
					  <div>
                                              <input
                                                  type="text"
                                                  name="name"
                                                  id="name"
                                                  class="validate[required,minSize[2],maxSize[20] ] medium"
                                                  value="<?php echo $edit_user->user_forename; ?>"
                                                  >
                                          </div>
				 </div>
                 <div class="section">
					  <label>Last Name</label>   
					  <div><input type="text" name="lastname" id="lastname" class="validate[required,minSize[2],maxSize[40] ] medium" value="<?php echo $edit_user->user_surname ;?>"/></div>
				 </div>
                 <div class="section">
					  <label>Username<small>Between 3 and 20 characters</small></label>   
					  <div><input type="text" name="username" id="username" class="validate[required,minSize[3],maxSize[20] ] medium" value="<?php echo $edit_user->user_username; ?>"/></div>
				 </div>
                 <div class="section">
					  <label>
                                              <?php if ($new_user): ?>
                                                Password
                                              <?php else: ?>
                                                Change Password
                                              <?php endif; ?>
                                              <small>Minimum 6 Characters</small>
                                          </label>   
					  <div><input type="password" name="password" id="password" class="validate[<?php if ($new_user): ?>required,<?php endif; ?>minSize[6]] medium" />
                      <span class="f_help">Confirm Password</span>
                      <input type="password" name="passwordCon" id="passwordCon" class="validate[equals[password]] medium" /></div>
				 </div>
				 <div class="section">
					  <label>Email Address</label>   
					  <div> <input type="text" name="email" id="email" class="validate[required,custom[email]] large" value="<?php echo $edit_user->user_email; ?>"/></div>
				 </div>
                 <div class="section">
					  <label>Facebook ID</label>   
					  <div><input type="text" name="fb" id="fb" value="<?php echo $edit_user->user_facebook; ?>"/></div>
				 </div>
				 <div class="section last">
					  <div>
                                              <a class="btn btn-success sendform-lightbox">
                                                  <?php if ($new_user): ?>
                                                  Submit
                                                  <?php else: ?>
                                                  Update
                                                  <?php endif; ?>
                                              </a>
                                          </div>
				 </div>
                            
                            <?php if (!$new_user): ?>
                                <input type="hidden" id="id" name="id" value="<?php echo $edit_user->user_id; ?>" />
                            <?php endif; ?>
			</form>
  </div>
</div>