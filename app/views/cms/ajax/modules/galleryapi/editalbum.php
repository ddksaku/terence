<script type="text/javascript" >
	 $(document).ready(function(){	
		   $('#Add_album').validationEngine({
					ajaxFormValidation: true,
					onBeforeAjaxFormValidation: Add_album
			});		
			$('.albumsubmit').click(function(){
						var form_id=$(this).parents('form').attr('id');
						$("#"+form_id).submit();
			})		 
	  }); 
	 
	function Add_album(form, options){
		 loading('Loading',0);

                 ajaxCallAPI(
                    'gallery',
                    'post',
                    'editalbum',
                    null,
                    form.serialize(),
                    function(data)
                    {
                        if(data.success==0) {
                                $('#preloader').fadeOut(400,function(){ $(this).remove(); });		
                                
                                if (data.error == 2) {
                                    alertMessage('error', 'That album name is already taken.');
                                } else {
                                    alertMessage('error', 'Error Try Again');
                                }
                                
                                return false;
                        }
                        if(data.success==1){
                            synergyModuleGalleryAlbumRefresh();
                               
                           unloading();
                           setTimeout('$.fancybox.close()',500);
                           alertMessage('success','New Album Created'); return false;
                        }
                    }
                );
	}
</script>   
<div class="modal_dialog" style="min-height:50px">
  <div class="header">
  	  <span>
              <?php if ($new_album): ?>
                NEW ALBUM
              <?php else: ?>
                EDIT ALBUM
              <?php endif; ?>
          </span>
      <div class="close_me pull-right"><a href="javascript:void(0)" id="close_windows" class="butAcc"><img src="images/icon/closeme.png" /></a></div>  
	</div>
  <div class="content">
    <form name="Add_album" id="Add_album">
      <div class="section">
      <label>Album Name</label>   
        <div>
          <input type="text" name="name" id="name" class="validate[required,minSize[3],maxSize[35]] large" value="<?php echo date("Y-m-d"); ?>" />
        </div>
      </div>
      <div class="section last">
        <div>
          <a class="btn btn-success albumsubmit">Submit</a> <a id="close_windows" class="btn btn-danger butAcc">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>