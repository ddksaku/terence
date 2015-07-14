<script>
$(document).ready(function(){	

	$(".chzn-select").chosen();
	
	
	 
 	//$("input.fileupload").filestyle();  
 	//$("#datetimepicker").datetimepicker({dateFormat: 'yy-mm-dd',timeFormat: 'hh:mm:ss'});
 
    $("#editor").cleditor();
	
    setTimeout(function(){
	$("#editor").cleditor()[0].disable(false).refresh();
	},500);

        synergyTagsAutocomplete('#category_tags_input');
        
 	$('#Textarealimit').limit('200','.limitchars'); 
	

    function ajaxValidationCallback(status, form, json, options){
        if (json.entry_exists) {
                $('#cat-title').validationEngine('showPrompt', 'Title already exists', 'error',"topLeft");
        }else{
                Add_database_light_upcategories(form, options);
        }
    }
    
    var prefix = "chzn_";
    $('#categories_edit').validationEngine({
          prettySelect : true, 
          useSuffix: prefix,          
          ajaxFormValidation: true,
          //onBeforeAjaxFormValidation: Add_database_light_catz
		  ajaxFormValidationMethod: 'post',
		  onAjaxFormComplete: ajaxValidationCallback
    });

	//////////////////////////////////////////////////////////////////////
	//File uploader
	//////////////////////////////////////////////////////////////////////
	
	
	
    // Change this to the location of your server-side upload handler:
    var upload_script_url = '<?php echo $upload_script; ?>';
	
	
	
	
    var uploadButton = $('<button/>')
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
	$('.delete-images').die();
	$('.delete-images').live('click', function(){
			   delete_image($(this));
	});
	
	
	//Delete images on close
	$('#close_windows').live('click', function(){
	
	    //Get image name
		var $del_btn = $($('.delete-images')[0]);
		var file = $del_btn.attr('data-filename');
	
		if(file){
		
		    //console.log(file);
		    delete_image($del_btn);
		
		}
		
		
	
	});
	
	
	
	////////////////////////////////////////////////////////////////////
	// Rotated image
	////////////////////////////////////////////////////////////////////
	function rotate_img(img,angle, callback){
		loading('Rotating',0);

		$.get(upload_script_url, {"img":img, "rotate":angle}, function(response){
			callback(response);
			$('#preloader').fadeOut('fast', function(){ $(this).remove(); });
		}, 'json');
	
	
	
	}
	
	
	function update_image(img){
	
		var image = '/uploads/images/square/' + img;
		
		//Find images and replace
		var $im = $('img').filter(function() {
						
						var reg = new RegExp(image);
						return this.src.match(reg);
				  });
				  
				  
		
		$im.attr('src', image + '?time='+new Date().getTime());
		
		
		var image = '/uploads/images/resize/' + img;
		
		//Find images and replace
		var $im = $('img').filter(function() {
						
						var reg = new RegExp(image);
						return this.src.match(reg);
				  });
				  
				  
		
		$im.attr('src', image + '?time='+new Date().getTime());


	
	}
	
	
	
	//Rotate Images
	$('.rotate-right').die();
	$('.rotate-right').live('click', function(){
		
		
		var img = $(this).parent().attr('data-img');
		
		
		
		//Rotate
		rotate_img(img, -90, function(data){
		
			var file = data.files[0].name;
			update_image(file);
			
		
		});
		
	
	});
	
	
	$('.rotate-left').die();
	$('.rotate-left').live('click', function(){
	
		var img = $(this).parent().attr('data-img');
		
		//Rotate
		rotate_img(img, 90, function(data){
		
			var file = data.files[0].name;
			update_image(file);
		
		});
	
	});
	////////////////////////////////////////////////////////////////////
	// Rotated image End
	////////////////////////////////////////////////////////////////////
	
		
    $('#fileupload').fileupload({
        url: upload_script_url,
        dataType: 'json',
        autoUpload: true,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 10000000, // 10 MB
        loadImageMaxFileSize: 15000000, // 15MB
        disableImageResize: false,
        previewMaxWidth: 200,
        previewMaxHeight: 150,
        previewCrop: true,
		singleFileUploads:true
    }).on('fileuploadadd', function (e, data) {
		
		
		//Setup for browsers that dont support preview
		$('.delete-images-second').hide();
		
		
		//Disable Button
		$('.mock_submit').show();
		$('.submit_form').hide();
		
		
		//Hide error
		$('#file-error').hide();
		
		//make items deletable
		$('#delete_image_file').val('true');
		
		
		$('.progress').show();
		
		//Reset progress
		$('#progress .bar').css('width','0%');
			
		//Remove previous
		$('.img-preview-holder').remove();
		
		
		
		
			
		data.context = $('<div class="img-preview-holder"></div>').appendTo('#files');
		
		
		$.each(data.files, function (index, file) {

			//Set filename
			$('#choose-file').find('.dummy-input').html(file.name);
			
			//Create preview
			var node = $('<p class="img-preview"></p>');
			node.appendTo(data.context);
			
			
		});
		
		
		
    }).on('fileuploadprocessalways', function (e, data) {
		
		
	
		
				var index = data.index,
					file = data.files[index],
					node = $(data.context.children()[index]);
				
				
				if (file.preview) {
					
					$('.preview-holder').show();
					
					//Setup preview
					//Hide file seletor
		            $('#choose-file').hide();
					
					
					node
						.prepend('<br>')
						.prepend(file.preview);
						
					$('#progress_perc').remove();
						
						
				}else{
				
				  
					
				
				}
				
				
				
				
				//Show error
				if (file.error) {
					//Show error
		            $('#file-error').show();
				}else{
				
				    //Show error
		            $('#file-error').hide();
				
				}
				
				
				
				
				
				
				if (index + 1 === data.files.length) {
					data.context.find('button')
						.text('Upload')
						.prop('disabled', !!data.files.error);
				}
		
		
		
		
    }).on('fileuploadprogressall', function (e, data) {
		
		
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress .bar').css(
            'width',
            progress + '%'
        );
		
		//$('#progress_perc').show();
		$('#progress_perc').html(progress + '%');
		
		if(progress > 99){
		
		    $('.progress').hide();
			
		}
		
		
    }).on('fileuploaddone', function (e, data) {
		
		
        $.each(data.result.files, function (index, file) {
			
			
		   
			
		    
			
			var menu = '<ul class="image-menu" data-img="' +file.name+ '">';
			menu += '<li class="delete-images main-delete" data-filename=""><img src="images/icon/gray_18/trash_can.png" width="14" height="14"/></li>';
			menu += '<li class="rotate-right"><img src="images/icon/gray_18/rotate_right.png" width="14" height="14"/></li>';
			menu += '<li class="rotate-left"><img src="images/icon/gray_18/rotate_left.png" width="14" height="14"/></li>';
			menu += '</ul>';
			
			//Create delete button
			var IMGMenu = $(menu);
                                                              
			
			//Delete button functionality
			IMGMenu.appendTo(data.context);
			
			
			//Remove canvas and add image
			$holder = $(data.context).find('.img-preview');
			$holder.html('');
			$img = $('<img width="200" src="/uploads/images/resize/' +file.name+ '">');
			$holder.html($img);
			
			
			$('.delete-images').attr('data-filename', file.name);
			
			
			//Delete Button
			$('.delete-images').die();
			$('.delete-images').on('click', function(){
					
				  //remove the image
				  delete_image($(this)); 
				  
	         })
			
			
		    
		    //Set filename
		    $('#imagefile').val(file.name);
				
				
       });
		
		
    }).on('fileuploadstop', function(e){
	
	
	    //Setup for browsers that dont support preview
		$('.delete-images-second').show();
		
		
	    //Disable Button
		$('.mock_submit').hide();
		$('.submit_form').show();
	
	
	
	
	}).on('fileuploadfail', function (e, data) {

        });
	
	
	
	

 });	



	
	
	//Delete the image
	function delete_image($btn){
	
	
	
	       
	      
		   $('#progress_perc').hide();
		   
		   $('#files').html('');
					   
					   
		   $('.progress').hide();
		   
		   
		   $('#progress .bar').css(
				'width',
				'0%'
			);
			
			$('#dummy-file-name').html('Choose Image');
			
			//Show file seletor
			$('#choose-file').fadeIn('fast');
			
		
			$('#imagefile').val('');
			
			$('.preview-holder').hide();
			
			$('.delete-images-second').hide();
			
			
		   //Check deleteing file is allowed
	       var deletable = $('#delete_image_file').val();
	
		   if(deletable == 'true'){
			   
				   
				//Delete files
				var file = encodeURIComponent($btn.attr('data-filename'));
						
                                ajaxCallAPI(
                                   'upload',
                                   'post',
                                   'delete_image',
                                   null,
                                   { filename: file },
                                   function(data)
                                   {
                                   }
                               );
				
			
	        }else{
			
			    $('input[name="imagename"]').val('');
				$('#delete_image_file').val('true');
				
			
			
			}
			
	

	
	}
	
	//after
	
	
	function Add_database_light_upcategories (form, options){
		loading('Loading',0); 

                 ajaxCallAPI(
                    'portfolio',
                    'post',
                    'editcategory',
                    null,
                    form.serialize(),
                    function(data)
                    {
                          if(data.success==0){   // uncomplete return 0
                                    // loading remove
                                $('#preloader').fadeOut(400,function(){ $(this).remove(); });		
                                // show error messages
                                if(data.error==1){
                                   alertMessage("error","Sorry please try again");
                                }
                                return false;
                          }
                          
                          if(data.success==1) { // complete return 1
                              if (data.new_entry == 1) {
                                alertMessage("success","Added successfully");
                              } else {
                                alertMessage("success","Edited successfully");
                              }

                              $.fancybox.close();                                                    

                              setTimeout('unloading()',900);  

                              synergyModulePortfolioRefresh();
                            }
                    }
                );		
	}
	
	


</script>
  

<div class="modal_dialog" style="min-height:50px">
  <div class="header">
      <span>
          <?php if ($new_category): ?>
            ADD CATEGORY
          <?php else: ?>
            EDIT CATEGORY (<?php echo $category->category_title; ?>)
          <?php endif; ?>
      </span>
      <div class="close_me pull-right"><a href="javascript:void(0)" id="close_windows" class="butAcc"><img src="images/icon/closeme.png" /></a></div>
  </div>
  <div class="content">

<form id="categories_edit" action="portfolioapi/validate_category" name="categories_edit" method="post">
                                      
                                           <div class="section">
                                           <label>Active</label>
                                               <div>
                                                <div class="checkslide">
                                                    <input
                                                        type="checkbox"
                                                        name="statusactive"
                                                        value="1"
                                                        <?php if($new_category || $category->category_active): ?>
                                                            checked
                                                        <?php endif; ?>
                                                        >
                                                    <label for="checkslide"></label>
                                                </div>
                                               </div>
                                           </div>

                                          <!-- Upload Button --> 
                                          <div id="file-upload-holder" class="section">
                                              <label>Image<small>Upload an image</small></label>   
                                              <div> 
                                              
                                                  <div
                                                      id="choose-file"
                                                      class="file-dummy-input"
                                                      <?php if($category->category_image): ?>
                                                        style="display:none;"
                                                      <?php endif; ?>
                                                      >
                                                      <div class="input-holder">
                                                          <div id="dummy-file-name" class="dummy-input">Choose Image</div>
                                                          <div class="file-add-on"><img src="images/addFiles.png" width="36" height="30" ></div>
                                                      </div>
                                                      
                                                      <input id="fileupload" type="file" name="files[]">
                                                      
                                                  </div>
                                                  
                                                  <span class="delete-images delete-images-second" style="display:none;">Remove Image</span> <span id="progress_perc" style="display:none;"></span>
                                                  
                                                  
                                                  <div id="file-error" style="display:none;">File type not supported</div>
                                                  
                                                  
                                                 
                                                   
                                                 
                                                  
                                                  <div
                                                      class="preview-holder"
                                                      <?php if(!$category->category_image): ?>
                                                        style="display:none;"
                                                      <?php endif; ?>
                                                      >
                                                      <!-- The container for the uploaded files -->
                                                      <div id="files">
                                                      
                                                      
							<?php if($category->category_image): ?>
                                                          <div class="img-preview-holder original_image">
                                                              <p class="img-preview">
                                                                  <img src="/uploads/images/resize/<?php echo $category->category_image; ?>?date=<?php echo time(); ?>" width="200">
                                                              </p>
     
                                                              <!-- Image Menu -->
                                                              <ul class="image-menu" data-img="<?php echo $category->category_image; ?>">
                                                              
                                                              
                                                                  <li class="delete-images main-delete" data-filename="">
                                                                      <img src="images/icon/gray_18/trash_can.png" width="14" height="14"/>
                                                                  </li>
                                                                  
                                                                  <li class="rotate-right">
                                                                      <img src="images/icon/gray_18/rotate_right.png" width="14" height="14"/>
                                                                  </li>
                                                                  
                                                                  <li class="rotate-left">
                                                                      <img src="images/icon/gray_18/rotate_left.png" width="14" height="14"/>
                                                                  </li>
                                                              
                                                              </ul>
                                                              <!-- Image Menu End -->
                                                           
                                                          </div>
                                                          <?php endif; ?>
                                                      </div>
                                                      
                                                      
                                                      <!-- The global progress bar -->
                                                      <div id="progress" class="progress progress-success progress-striped active" style="display:none;">
                                                          <div class="bar"></div>
                                                      </div>
                                                  </div>
                                                  
                                                  
                                                  <?php if($category->category_image): ?>
                                                    <input id="delete_image_file" name="delete_image_file"  type="text" value="false" style="display:none">
                                                    <input id="original_file" name="original_file"  type="text" value="<?php echo $category->category_image ?>" style="display:none">
                                                  <?php else:?>
                                                    <input id="delete_image_file" name="delete_image_file"  type="text" value="true" style="display:none">
                                                  <?php  endif; ?>
                                                  
                                                  
                                                  
                                                  
                                                  
                                                  <input id="imagefile" name="imagefile"  type="text" value="<?php echo $category->category_image; ?>" style="display:none">

                                              </div>

                                          <!-- Upload Button End --> 
                                          
                                                <label>Image Name<small>Alt tags</small></label>   
                                                <div><input type="text" class="large" name="imagename" value="<?php echo $category->category_image_alt; ?>"/></div>
                                           </div>
                                        
                                        <div class="section">
                                          <label>Category Title</label>   
                                          <div>
                                              <input
                                                  id="cat-title"
                                                  type="text"
                                                  class="validate[required] large"
                                                  name="categoriestitle"
                                                  value="<?php echo $category->category_title; ?>"
                                                  >
                                          </div>
                                        </div>
                                        
                                        <div class="section">
                                        <label>Introduction<small>One paragraph</small></label>   
                                        <div>
                                        <textarea name="Textarealimit" id="Textarealimit" class="validate[required] large" cols="" rows=""><?php echo $category->category_introduction; ?></textarea>
                                        <span class="f_help">Character limit: <span class="limitchars">200</span></span>
                                        </div>   
                                     	</div>
                                        
                                        <div class="section">
                                        <label>Description</label>
                                        <div> <textarea name="description" id="editor" class="editor" cols="5" rows=""><?php echo $category->category_description; ?></textarea></div>
                                        </div>
                                        
 
                                         <div class="section">
                                                 <label>Tags</label>   
                                               <div><input id="category_tags_input" type="text" class="tags" value="<?php echo implode(',', $category->tags->lists('tag_name')); ?>"  name="tags_input" /></div>   
                                   			</div>
                                            
                                            <div class="section last">
                                            <div id="save_categories">
                                              <a  class="btn btn-success submit_form">
                                                  <?php if ($new_category): ?>
                                                  Submit
                                                  <?php else: ?>
                                                  Update
                                                  <?php endif; ?>
                                              </a>
                                              
                                              <a  class="btn mock_submit disable" style="display:none;">Uploading please wait <img src="images/loader/loader_green.gif" width="16" height="11" /></a>

                                              <?php if ($new_category): ?>
                                              <a class="btn btn-warning special" onClick="ResetForm()">Clear Form</a>
                                              <?php endif; ?>
                                              
                                              <a id="close_windows" class="btn btn-danger butAcc">Cancel</a>
                                           </div>
                                           </div>
       									  <input type="hidden" name="action" value="edit_categories">
                                                                          
                                          <?php if (!$new_category): ?>
                                            <input type="hidden" name="id" value="<?php echo $category->category_id; ?>">
                                          <?php endif; ?>

</form>
                                           

</div>	

</div>