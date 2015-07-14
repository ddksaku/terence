<?php if($user->hasPermission('edit_pages_settings')): ?>

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
		    $('#pagessubmit-lightbox').validationEngine({
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
                    'pages',
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
                                alertMessage("success","Page settings changed.");

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
            PAGE SETTINGS
        </span>
        <div class="close_me pull-right"><a href="javascript:void(0)" id="close_windows" class="butAcc"><img src="images/icon/closeme.png" /></a></div>
    </div>
    <div class="content">
        <form name="pagessubmit" id="pagessubmit-lightbox">
            
            <div class="section">
                <label>Image Sizes</label>
                <div>
                    <span class="f_help">Resize Width</span><input type="text"
                            name="setting_pages_resize_width" id="resize_width"
                            class="validate[minSize[2],maxSize[3] ] small"
                            value="<?php echo $settings->setting_pages_resize_width ?: ''; ?>" />
                    <span class="f_help">Thumb
                            Width</span><input type="text" name="setting_pages_thumb_width" id="thumb_width"
                            class="validate[minSize[2],maxSize[3] ] small"
                            value="<?php echo $settings->setting_pages_thumb_width ?: ''; ?>" />
                    <span class="f_help">Square
                            Width</span><input type="text" name="setting_pages_square_width"
                            id="square_width"
                            class="validate[minSize[2],maxSize[3] ] small"
                            value="<?php echo $settings->setting_pages_square_width ?: ''; ?>" />
                </div>
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