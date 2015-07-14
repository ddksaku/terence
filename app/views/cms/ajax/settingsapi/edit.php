<script>
    
    var settingsContainer = {

	//Delete the image
	delete_image: function ($btn, force, container) {
            if (typeof force == 'undefined') {
                var force = false;
            }

            container.find('.settings_progress_perc').hide();

            container.find('.settings_files').html('');

            container.find('.settings_progress').hide();

            container.find('.settings_progress .bar').css(
                'width',
                '0%'
            );

            container.find('.dummy-file-name').html('Choose Image');

            //Show file seletor
            container.find('.choose-file').fadeIn('fast');

            container.find('.imagefile').val('');

            container.find('.preview-holder').hide();

            container.find('.delete-images-second').hide();

            //Check deleteing file is allowed
            var deletable = container.find('.settings_delete_image_file').val();

            if (deletable == 'true' || force) {
                //Delete files
                var file = encodeURIComponent($btn.attr('data-filename'));

                ajaxCallAPI(
                    'upload',
                    'post',
                    'delete_image',
                    null,
                    { filename: file },
                    function(data) {
                    }
                );

                //Delete file
                /*$.ajax({
                url: upload_url + "?file=" + file,
                type: 'DELETE',
                success: function(result) {  }
                });*/
            } else {
                container.find('input.imagename').val('');
                container.find('.settings_delete_image_file').val('true');
            }
	},
        
        //////////////////////////////////////////////////////

	Add_database_light: function(form, options) {
		 loading('Loading',0);

                    ajaxCallAPI(
                        'settings',
                        'post',
                        'edit',
                        null,
                        form.serialize(),
                        function(data)
                        {
                              if(data.success==0){
                                      $('#preloader').fadeOut(400,function(){ $(this).remove(); });		

                                      alertMessage("error","Sorry please try again");
 
                                        return false;
                              }
                              if(data.success==1){ // complete return 1
                                       // show error messages
                                       alertMessage("success","Settings edited successfully");
                                       
                                       //synergyRefreshSettingsList();

                                       // fancybox close
                                       $.fancybox.close();
                                       setTimeout('unloading()',500);
                              }
                        }
                    );		
	}
        
    };
	
	
    // Change this to the location of your server-side upload handler:
    settingsContainer.upload_url = '<?php echo $upload_script; ?>';
    settingsContainer.upload_image_url = '<?php echo $upload_image_script; ?>';
    
$(document).ready(function() {

    // sendform-lightbox  click  
    $('.sendform-lightbox').click(function() {
                            // search-form id   
                            var form_id=$(this).parents('form').attr('id');
                            // submit form
                            $("#"+form_id).submit();
    });

    // validationEngine  select  
    var prefix = "selectBox_";
    $('#settingssubmit-lightbox').validationEngine({
        prettySelect : true,usePrefix: prefix,
        ajaxFormValidation: true,
        onBeforeAjaxFormValidation: settingsContainer.Add_database_light
    });	
			
				



    //////////////////////////////////////////////////////////////////////
	//File uploader
	//////////////////////////////////////////////////////////////////////
	
	
	
    settingsContainer.uploadButton = $('<button/>')
            .addClass('btn')
            .prop('disabled', true)
            .text('Processing...')
            .on('click', function () {
                var $this = $(this),
                    data = $this.data();
                $this
                    .off('click')
                    .text('Abort')
                    .on('click', function () {
                        $this.remove();
                        data.abort();
                    });
                data.submit().always(function () {
                    $this.remove();
                });
            });
	
	
	
	
	
	//Delete Button
	$('.delete-images').live('click', function(){
            settingsContainer.delete_image($(this), false, $(this).parents('.file-upload-holder')); 
	});
	
	
	//Delete images on close
	$('#close_windows').live('click', function(){
            $('.file-upload-holder').each(function() {
                var container = $(this);
                
                $(this).find('.delete-images').each(function() {
                    if ($(this).attr('data-filename')) {
                        settingsContainer.delete_image($(this), false, container);
                    }
                });
            });
	});
	

    var setUploadHandler = function(handler) {
        handler.on('fileuploadadd', function (e, data) {
            var container = $(this).parents('.file-upload-holder');

            //Setup for browsers that dont support preview
            container.find('.delete-images-second').hide();

            //Disable Button
            $('.mock_submit').show();
            $('.submit_form').hide();


            //Hide error
            container.find('.file-error').hide();

            //make items deletable
            container.find('.settings_delete_image_file').val('true');


            container.find('.settings_progress').show();

            //Reset progress
            container.find('.settings_progress .bar').css('width','0%');

            //Remove previous
            container.find('.settings-img-preview-holder').remove();





            data.context = $('<div class="settings-img-preview-holder"></div>').appendTo(container.find('.settings_files'));


            $.each(data.files, function (index, file) {
                    //Set filename
                    container.find('.choose-file').find('.dummy-input').html(file.name);

                    //Create preview
                    var node = $('<p class="settings-img-preview"></p>');
                    node.appendTo(data.context);
            });

        }).on('fileuploadprocessalways', function (e, data) {
            var container = $(this).parents('.file-upload-holder');

            var index = data.index,
                file = data.files[index],
                node = $(data.context.children()[index]);


            if (file.preview) {
                container.find('.preview-holder').show();

                //Setup preview
                //Hide file seletor
                container.find('.choose-file').hide();

                node.prepend('<br>')
                    .prepend(file.preview);

                container.find('.settings_progress_perc').remove();
            }

            // Show error
            if (file.error) {
                // Show error
                container.find('.file-error').show();
            } else {
                // Hide error
                container.find('.file-error').hide();
            }

            if (index + 1 === data.files.length) {
                data.context.find('button')
                    .text('Upload')
                    .prop('disabled', !!data.files.error);
            }

        }).on('fileuploadprogressall', function (e, data) {
            var container = $(this).parents('.file-upload-holder');

            var progress = parseInt(data.loaded / data.total * 100, 10);

            container.find('.settings_progress .bar').css(
                'width',
                progress + '%'
            );

            //$('#progress_perc').show();
            container.find('.settings_progress_perc').html(progress + '%');

            if(progress > 99){
                container.find('.settings_progress').hide();
            }

        }).on('fileuploaddone', function (e, data) {
            var container = $(this).parents('.file-upload-holder');

            $.each(data.result.files, function (index, file) {
                var menu = '<ul class="image-menu" data-img="' +file.name+ '">';
                menu += '<li class="delete-images main-delete" data-filename=""><img src="images/icon/gray_18/trash_can.png" width="14" height="14"/></li>';
                menu += '</ul>';

                //Create delete button
                var IMGMenu = $(menu);

                //Delete button functionality
                IMGMenu.appendTo(data.context);

                //Remove canvas and add image
                $holder = $(data.context).find('.settings-img-preview');
                $holder.html('');
                $img = $('<img width="200" src="/uploads/images/resize/' +file.name+ '">');
                $holder.html($img);


                container.find('.delete-images').attr('data-filename', file.name);


                //Delete Button
                container.find('.delete-images').die();
                container.find('.delete-images').on('click', function(){
                    //remove the image
                    settingsContainer.delete_image($(this), false, container);
                 })

                //Set filename
                container.find('.imagefile').val(file.name);		
            });

        }).on('fileuploadstop', function(e){
            var container = $(this).parents('.file-upload-holder');

            //Setup for browsers that dont support preview
            container.find('.delete-images-second').show();

            //Disable Button
            $('.mock_submit').hide();
            $('.submit_form').show();

        }).on('fileuploadfail', function (e, data) {
            $.each(data.result.files, function (index, file) {
            });
        });
};

        setUploadHandler($('.fileupload:not(.default)').fileupload({
            url: settingsContainer.upload_url,
            dataType: 'json',
            autoUpload: true,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: 2000000, // 2 MB
            loadImageMaxFileSize: 15000000, // 15MB
            disableImageResize: false,
            previewMaxWidth: 200,
            previewMaxHeight: 150,
            previewCrop: true,
                    singleFileUploads:true
        }));
        
        setUploadHandler($('.fileupload.default').fileupload({
            url: settingsContainer.upload_image_url,
            dataType: 'json',
            autoUpload: true,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: 2000000, // 2 MB
            loadImageMaxFileSize: 15000000, // 15MB
            disableImageResize: false,
            previewMaxWidth: 200,
            previewMaxHeight: 150,
            previewCrop: true,
                    singleFileUploads:true
        }));

});
		
	

</script>
<div class="modal_dialog" style="min-height: 50px">
	<div class="header">
		<span>EDIT SETTINGS</span>
		<div class="close_me pull-right">
			<a href="javascript:void(0)" id="close_windows" class="butAcc"><img
				src="images/icon/closeme.png" /></a>
		</div>
	</div>
	<div class="content">
		<form name="settingssubmit" id="settingssubmit-lightbox">
			<div class="section">
				<label>Company Name</label>
				<div>
					<input type="text" name="name" id="name"
						class="validate[required,minSize[2],maxSize[100] ] large"
						value="<?php echo $setting->setting_name; ?>" />
				</div>
			</div>


			<!-- Upload Button -->
			<div class="file-upload-holder section">
				<label>Logo<small>Upload a logo</small></label>
				<div>

					<div
                                            class="choose-file file-dummy-input"
                                            <?php if($setting->setting_image): ?>
                                                style="display:none;"
                                            <?php endif; ?>
                                            >
						<div class="input-holder">
							<div class="dummy-file-name dummy-input">Choose Image</div>
							<div class="file-add-on">
								<img src="images/addFiles.png" width="36" height="30">
							</div>
						</div>

						<input class="fileupload" type="file" name="files[]">

					</div>

					<span class="delete-images delete-images-second"
						style="display: none;">Remove Image</span> <span
						class="settings_progress_perc" style="display: none;"></span>


					<div class="file-error" style="display: none;">File type not supported</div>

					<div class="preview-holder"
                                            <?php if(!$setting->setting_image): ?>
                                                style="display:none;"
                                            <?php endif; ?>
                                            >
						<!-- The container for the uploaded files -->
						<div class="settings_files">
                                  
                                                    <div
                                                          class="settings-img-preview-holder original_image">
                                                          <p class="settings-img-preview">
                                                                  <img
                                                                          src="/uploads/images/resize/<?php echo $setting->setting_image; ?>?date=<?php echo time(); ?>"
                                                                          width="200">
                                                          </p>




                                                          <!-- Image Menu -->
                                                          <ul class="image-menu" data-img="<?php echo $setting->setting_image; ?>">


                                                                  <li class="delete-images main-delete" data-filename=""><img
                                                                          src="images/icon/gray_18/trash_can.png" width="14" height="14" />
                                                                  </li>


                                                          </ul>
                                                          <!-- Image Menu End -->

                                                    </div>

                                                </div>


						<!-- The global progress bar -->
						<div
							class="settings_progress progress progress-success progress-striped active"
							style="display: none;">
							<div class="bar"></div>
						</div>
					</div>
                              
                              
                              <?php if($setting->setting_image): ?>
                              <input class="settings_delete_image_file"
						name="delete_image_file" type="text" value="false"
						style="display: none"> <input id="original_file"
						name="original_file" type="text"
						value="<?php echo $setting->setting_image; ?>" style="display: none">
                              <?php else: ?>
                              <input class="settings_delete_image_file"
						name="delete_image_file" type="text" value="true"
						style="display: none">
                              <?php  endif; ?>
                              
                              
                              
                              
                              
                              <input class="imagefile" name="imagefile"
						type="text" value="<?php echo $setting->setting_image; ?>"
						style="display: none"> <input class="delete_url" name="delete_url"
						type="text" value="<?php if(0){ echo $delete_url;} ?>"
						style="display: none">


				</div>

				<!-- Upload Button End -->



				<label>Logo Name<small>Alt tags</small></label>
				<div>
					<input type="text" class="imagename large" name="imagename"
						value="<?php echo $setting->setting_image_name; ?>" />
				</div>
			</div>
                        
                        
                        <div class="file-upload-holder section">
                            <label>Default image<small>Upload a default image</small></label>
				<div>

                                    <div
                                        class="choose-file file-dummy-input"
                                        <?php if($setting->setting_default_image): ?>
                                            style="display:none;"
                                        <?php endif; ?>
                                        >
                                            <div class="input-holder">
                                                <div class="dummy-file-name dummy-input">Choose Image</div>
                                                <div class="file-add-on">
                                                    <img src="images/addFiles.png" width="36" height="30">
                                                </div>
                                            </div>

                                            <input class="fileupload default" type="file" name="files[]">
                                    </div>

                                    <span class="delete-images delete-images-second"
                                            style="display: none;">Remove Image</span> <span
                                            class="settings_progress_perc" style="display: none;"></span>

                                    <div class="file-error" style="display: none;">File type not supported</div>

                                    <div class="preview-holder"
                                        <?php if(!$setting->setting_default_image): ?>
                                            style="display:none;"
                                        <?php endif; ?>
                                        >
                                            <!-- The container for the uploaded files -->
                                            <div class="settings_files">
                                                <div
                                                        class="settings-img-preview-holder original_image">
                                                        <p class="settings-img-preview">
                                                            <img
                                                                    src="/uploads/images/resize/<?php echo $setting->setting_default_image; ?>?date=<?php echo time(); ?>"
                                                                    width="200">
                                                        </p>

                                                        <!-- Image Menu -->
                                                        <ul class="image-menu" data-img="<?php echo $setting->setting_default_image; ?>">
                                                            <li class="delete-images main-delete" data-filename=""><img
                                                                    src="images/icon/gray_18/trash_can.png" width="14" height="14" />
                                                            </li>
                                                        </ul>
                                                        <!-- Image Menu End -->

                                                </div>
                                            </div>


						<!-- The global progress bar -->
						<div
							class="settings_progress progress progress-success progress-striped active"
							style="display: none;">
							<div class="bar"></div>
						</div>
					</div>
                              
                              
                              <?php if($setting->setting_default_image): ?>
                              <input class="settings_delete_image_file"
						name="delete_image_file" type="text" value="false"
						style="display: none"> <input class="original_file"
						name="original_file" type="text"
						value="<?php echo $setting->setting_default_image; ?>" style="display: none">
                              <?php else: ?>
                              <input class="settings_delete_image_file"
						name="delete_image_file" type="text" value="true"
						style="display: none">
                              <?php endif; ?>

                              <input class="imagefile" name="defaultimagefile"
						type="text" value="<?php echo $setting->setting_default_image; ?>"
						style="display: none"> <input class="delete_url" name="delete_url"
						type="text" value="<?php if(0){ echo $delete_url;} ?>"
						style="display: none">


				</div>

				<!-- Upload Button End -->

				<label>Image Name<small>Alt tags</small></label>
				<div>
					<input type="text" class="imagename large" name="default_imagename"
						value="<?php echo $setting->setting_default_image_name; ?>" />
				</div>
			</div>

                        
                 <? if($user->hasPermission('edit_advanced_settings')): ?>
                 <div class="section">
				<label>Image Sizes</label>
				<div>
					<span class="f_help">Resize Width</span><input type="text"
						name="resize_width" id="resize_width"
						class="validate[required,minSize[2],maxSize[3] ] small"
						value="<?php echo $setting->setting_resize_width; ?>" /> <span class="f_help">Thumb
						Width</span><input type="text" name="thumb_width" id="thumb_width"
						class="validate[required,minSize[2],maxSize[3] ] small"
						value="<?php echo $setting->setting_thumb_width; ?>" /> <span class="f_help">Square
						Width</span><input type="text" name="square_width"
						id="square_width"
						class="validate[required,minSize[2],maxSize[3] ] small"
						value="<?php echo $setting->setting_square_width; ?>" />
				</div>
			</div>

			<div class="section">
				<label>Logo Sizes</label>
				<div>
					<span class="f_help">Resize Width</span><input type="text"
						name="logo_resize_width" id="logo_resize_width"
						class="validate[required,minSize[2],maxSize[3] ] small"
						value="<?php echo $setting->setting_logo_resize_width;?>" /> <span class="f_help">Thumb
						Width</span><input type="text" name="logo_thumb_width"
						id="logo_thumb_width"
						class="validate[required,minSize[2],maxSize[3] ] small"
						value="<?php echo $setting->setting_logo_thumb_width;?>" />
				</div>
			</div>
            
                 <?php endif; ?>
                        
                <? if($user->hasPermission('edit_ga_code')): ?>
                <div class="section">
                    <label>Google Analytics<small>Unique code: UA-XXXXX-X</small></label>
                    <div>
                        <input type="text" name="google_analytics" id="google_analytics"
                                class="large"
                                value="<?php echo $setting->setting_google_analytics; ?>" />
                    </div>
                </div>
                <?php endif; ?>
                        
                <div class="section">
                    <label>Facebook<small></small></label>
                    <div>
                        <input type="text" name="setting_facebook" id="setting_facebook"
                                class="large"
                                value="<?php echo $setting->setting_facebook; ?>" />
                    </div>
                </div>
                        
                <div class="section">
                    <label>Twitter<small></small></label>
                    <div>
                        <input type="text" name="setting_twitter" id="setting_twitter"
                                class="large"
                                value="<?php echo $setting->setting_twitter; ?>" />
                    </div>
                </div>
                 
                        
				 <div class="section last">
				<div>
					<a class="btn btn-success submit_form">Update</a> <a
						class="btn mock_submit disable" style="display: none;">Uploading please wait <img src="images/loader/loader_green.gif" width="16" height="11" />
					</a>
                    <a id="close_windows" class="btn btn-danger butAcc">Cancel</a>
				</div>
			</div>
                        
                        <input type="hidden" name="id" value="<?php echo $setting->setting_id; ?>">

		</form>
	</div>
</div>