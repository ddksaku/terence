<script>
    
    var documentUrl = '<?php echo $document_upload; ?>';
    var docListArray = new Array();

    // Change this to the location of your server-side upload handler:
    var upload_script_url = '<?php echo $upload_script; ?>';
    var slideshow_upload_script_url = '<?php echo $slideshow_upload_script; ?>';

    // Called once the server replies to the ajax form validation request
    function ajaxValidationCallback(status, form, json, options){
            if (json.entry_exists) {
                    $('input[name="portfoliotitle"]').validationEngine('showPrompt', 'Title already exists', 'error',"topLeft");
            }else{
                    Add_database_light_upportfolio(form, options);
            }
    }
    
    function updateSlideshowOrder()
    {
        $('.slideshow-file-container').each(function(index) {
            $(this).find('.imageorder').val(index + 1);
        });
    }
    
$(document).ready(function(){	
    
    $('.slideshow-images-sortable').sortable({
        create: function() {
            $('.slideshow-images-sortable .slideshow-file-container').css('cursor', 'move');
        },
        update: function() {
            updateSlideshowOrder();
        }
    });

    /* */

	var i = 0;
	var jqXHR = new Array();
	$('#file-upload').fileupload({
		url : documentUrl,
		dataType : 'json',
		autoUpload : true,
		acceptFileTypes : /(\.|\/)(pdf|doc|docx|xls|xlsx)$/i,
		maxFileSize : 5000000, // 5 MB
		// Enable image resizing, except for Android and Opera,
		// which actually support image resizing, but fail to
		// send Blob objects via XHR requests:
		previewMaxWidth : 100,
		previewMaxHeight : 100,
		previewCrop : true
	}).on('fileuploadadd', function(e, data) {
		$('.mock_submit').show();
		$('.submit_form').hide();
		data.context = $('<div/>').appendTo('#files');
		$.each(data.files, function(index, file) {
			jqXHR[i] = data;
			i++;
		});
	}).on('fileuploadprocessalways', function(e, data) {
		var index = data.index, file = data.files[index], node = $(data.context.children()[index]);
		if (file.preview) {
			node.prepend('<br>').prepend(file.preview);
		}
		if (file.error) {
			$('#progress-file').css('display', 'none');
			$('#document-error').show();
			node.append('<br>').append(file.error);
		}
		if (index + 1 === data.files.length) {
			data.context.find('button').text('Upload').prop('disabled', !!data.files.error);
		}
	}).on('fileuploadprogressall', function(e, data) {
		$('#progress-file').css('display', 'block');
		var progress = parseInt(data.loaded / data.total * 100, 10);
		$('#progress-file .bar').css('width', progress + '%');
	}).on('fileuploaddone', function(e, data) {
		data.context = $('<div/>').appendTo('#files-list');
		$.each(data.result.files, function(index, file) {
                        var docItem = {
                            file: file.name,
                            original: file.original_name,
                            active: true
                        };
                        
                        var docItemKey = docListArray.length;
                        
                        docListArray[docItemKey] = docItem;

			var node = $('<p/>').append($('<span/>').text(file.original_name)).append('<br/>').append($('<a href="javascript:void(0)" class="delete-files" data-file-key="' + docItemKey + '">Remove File</a>'));
			node.appendTo(data.context);
			//$('#file-documents').val($('#file-documents').val() + file.name + ',');
			//$('#file-documents1').val($('#file-documents1').val() + file.original_name + ',');
		});
	}).on('fileuploadfail', function(e, data) {
		$.each(data.result.files, function(index, file) {
			var error = $('<span/>').text(file.error);
			$(data.context.children()[index]).append('<br>').append(error);
		});
	}).on('fileuploadstop', function(e) {
		//Disable Button
		$('.mock_submit').hide();
		$('.submit_form').show();
		$('#progress-file').css('display', 'none');
	});
        
        var imageNumber = 1;
        
        // 
    $('.slideshow-file-holder .fileupload').fileupload({
        url: slideshow_upload_script_url,
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
        var thisImage = imageNumber++;
        
        var formContainer = $(this).parents('.slideshow-file-holder');
        
        var container = formContainer.clone();
        
        container.css('cursor', 'move');
        
        container.removeClass('slideshow-file-holder')
            .addClass('slideshow-file-container');
            
        var fileInput = container.find('.imagefile');
        fileInput.attr('name', 'imagefile_' + thisImage);
        
        var fileNameInput = container.find('.imagename');
        fileNameInput.attr('name', 'imagename_' + thisImage);
        
        var fileTitleInput = container.find('.imagetitle');
        fileTitleInput.attr('name', 'imagetitle_' + thisImage);
        
        var fileOrderInput = container.find('.imageorder');
        fileOrderInput.attr('name', 'imageorder_' + thisImage);

        if ($('.slideshow-file-container:first').length > 0) {
            container.insertBefore('.slideshow-file-container:first');
        } else {
            $('.slideshow-images-sortable').append(container);
        }
        
        updateSlideshowOrder();
        
        //Setup for browsers that dont support preview
        container.find('.delete-images-second').hide();

        //Disable Button
        $('.mock_submit').show();
        $('.submit_form').hide();

        //Hide error
        container.find('.file-error').hide();

        //make items deletable
        container.find('.delete_image_file').val('true');

        container.find('.progress').show();

        //Reset progress
        container.find('.progress .bar').css('width','0%');

        //Remove previous
        //container.find('.img-preview-holder').remove();

        data.context = $('<div class="img-preview-holder"></div>').appendTo(container.find('.files'));

        $.each(data.files, function (index, file) {
            //Set filename
            container.find('.choose-file').find('.dummy-input').html(file.name);

            //Create preview
            var node = $('<p class="img-preview"></p>');
            node.appendTo(data.context);
        });

    }).on('fileuploadprocessalways', function (e, data) {
        var container = data.context.parents('.slideshow-file-container');
        
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

            container.find('.progress_perc').remove();
        } else {

        }

        //Show error
        if (file.error) {
            //Show error
            container.find('.file-error').show();
        } else {
            //Show error
            container.find('.file-error').hide();
        }

        if (index + 1 === data.files.length) {
            data.context.find('button')
                .text('Upload')
                .prop('disabled', !!data.files.error);
        }

    }).on('fileuploadprogress', function (e, data) {
        var container = data.context.parents('.slideshow-file-container');

        var progress = parseInt(data.loaded / data.total * 100, 10);
        
        container.find('.progress .bar').css(
            'width',
            progress + '%'
        );

        //$('#progress_perc').show();
        container.find('.progress_perc').html(progress + '%');

        if (progress > 99) {
            container.find('.progress').hide();
        }

    }).on('fileuploaddone', function (e, data) {
        var container = data.context.parents('.slideshow-file-container');
        
        container.find('.progress').hide();
        
        //Setup for browsers that dont support preview
        container.find('.delete-images-second').show();

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
            $img = $('<img width="200" src="/uploads/images/resize/' +file.name+ '">');
            $holder.html($img);

            container.find('.delete-images').attr('data-filename', file.name);

            //Delete Button
            container.find('.delete-images').die();
            container.find('.delete-images').on('click', function(){
                //remove the image
                delete_slideshow_image($(this)); 
            })

            //Set filename
            container.find('.imagefile').val(file.name);
       });

    }).on('fileuploadstop', function(e) {
        //Disable Button
        $('.mock_submit').hide();
        $('.submit_form').show();

    }).on('fileuploadfail', function (e, data) {

    });
        
        // 
	
	var newsUrl = "news/delete_files.php";
	$(document).on('click', '.delete-files', function() {
            var docItemKey = $(this).data('file-key');
            
            if (typeof docItemKey == 'undefined') {
                var fileId = $(this).data('file-id');
                
                $('#file-delete-list').val($('#file-delete-list').val() + fileId + ',');
            } else {
                docListArray[docItemKey].active = false;
                
                ajaxCallAPI(
                   'upload',
                   'post',
                   'delete_file',
                   null,
                   { filename: docListArray[docItemKey].file },
                   function(data)
                   {
                   }
               );
            }
            
            $(this).hide();

            return false;
        
            // old functionality below

		var file = encodeURIComponent($(this).attr('data-filename'));
		var str = $('#file-documents').val();
		var newstr = str.replace(decodeURIComponent(file), '')
		$('#file-documents').val(newstr);
		$.ajax({
			url : newsUrl + "?file="+ file,
			type : 'DELETE',
			success : function(result) {
			}
		});
		deleteFile($(this));
		$(this).hide();
	});
        
        // 
        
	$(".chzn-select").chosen();
	
	
	 
 	//$("input.fileupload").filestyle();  
 	//$("#datetimepicker").datetimepicker({dateFormat: 'yy-mm-dd',timeFormat: 'hh:mm:ss'});
 
    $("#editor").cleditor();
	
    setTimeout(function(){
	$("#editor").cleditor()[0].disable(false).refresh();
	},500);
	
        synergyTagsAutocomplete('#portfolio_tags_input');
        
 	$('#Textarealimit').limit('200','.limitchars'); 
	
	
	
	var prefix = "chzn_";
    $('#portfolio_edit').validationEngine({
          prettySelect : true, 
          useSuffix: prefix,          
          ajaxFormValidation: true,
          ajaxFormValidationMethod: 'post',
          onAjaxFormComplete: ajaxValidationCallback
    }); 
	
	
	//////////////////////////////////////////////////////////////////////
	//File uploader
	//////////////////////////////////////////////////////////////////////
	
	
	
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
	$('#file-upload-holder .delete-images').die();
	$('#file-upload-holder .delete-images').live('click', function(){
			   delete_image($(this));
	});
        
        //Delete Button
	$('.slideshow-file-container .delete-images').die();
	$('.slideshow-file-container .delete-images').live('click', function(){
			   delete_slideshow_image($(this));
	});
	
	
	//Delete images on close
	$('#close_windows').live('click', function(){
            
            $('.delete-images').each(function() {
                if ($(this).attr('data-filename')) {
		    //console.log(file);
		    delete_image($(this));
		}
            });

                //
                
                for (var i in docListArray) {
                    docListArray[i].active = false;

                    ajaxCallAPI(
                       'upload',
                       'post',
                       'delete_file',
                       null,
                       { filename: docListArray[i].file },
                       function(data)
                       {
                       }
                   );
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
						return $(this).attr('src').match(reg);
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
		$('#file-upload-holder .delete-images-second').hide();
		
		
		//Disable Button
		$('.mock_submit').show();
		$('.submit_form').hide();
		
		
		//Hide error
		$('#file-error').hide();
		
		//make items deletable
		$('#delete_image_file').val('true');
		
		
		$('#file-upload-holder .progress').show();
		
		//Reset progress
		$('#progress .bar').css('width','0%');
			
		//Remove previous
		$('#file-upload-holder .img-preview-holder').remove();
		
		
		
		
			
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
					
					$('#file-upload-holder .preview-holder').show();
					
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
		
		    $('#file-upload-holder .progress').hide();
			
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
			
			
			$('#file-upload-holder .delete-images').attr('data-filename', file.name);
			
			
			//Delete Button
			$('#file-upload-holder .delete-images').die();
			$('#file-upload-holder .delete-images').on('click', function(){
					
				  //remove the image
				  delete_image($(this)); 
				  
	         })
			
			
		    
		    //Set filename
		    $('#imagefile').val(file.name);
				
				
       });
		
		
    }).on('fileuploadstop', function(e){
	
	
	    //Setup for browsers that dont support preview
		$('#file-upload-holder .delete-images-second').show();
		
		
	    //Disable Button
		$('.mock_submit').hide();
		$('.submit_form').show();
	
	
	
	
	}).on('fileuploadfail', function (e, data) {

        });
	
	
	
	

 });	


    //Delete the image
    function delete_slideshow_image($btn) {
        var container = $btn.parents('.slideshow-file-container');

        //Check deleteing file is allowed
        var deletable = container.find('.delete_image_file').val();

        if (deletable == 'true') {
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
        }
        
        container.remove();
    }

    // 
	
	
	//Delete the image
	function delete_image($btn){
	
	
	
	       
	      
		   $('#progress_perc').hide();
		   
		   $('#files').html('');
					   
					   
		   $('#file-upload-holder .progress').hide();
		   
		   
		   $('#progress .bar').css(
				'width',
				'0%'
			);
			
			$('#dummy-file-name').html('Choose Image');
			
			//Show file seletor
			$('#choose-file').fadeIn('fast');
			
		
			$('#imagefile').val('');
			
			$('#file-upload-holder .preview-holder').hide();
			
			$('#file-upload-holder .delete-images-second').hide();
			
			
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
			
			    $('#file-upload-holder input[name="imagename"]').val('');
				$('#file-upload-holder #delete_image_file').val('true');
				
			
			
			}
			
	

	
	}
	
	//after
	
	
	function Add_database_light_upportfolio (form, options){
		loading('Loading',0); 

                // Build file input values
                
                var documents = '';
                var documentsOriginal = '';
                
                for (var index in docListArray) {
                    if (docListArray[index].active) {
                        documents += '*' + docListArray[index].file;
                        documentsOriginal += '*' + docListArray[index].original;
                    }
                }

                $('#file-documents').val(documents);
                $('#file-documents1').val(documentsOriginal);
                
                // 
                
                 ajaxCallAPI(
                    'portfolio',
                    'post',
                    'edit',
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
          <?php if ($new_portfolio): ?>
            ADD ITEM
          <?php else: ?>
            EDIT ITEM (<?php echo $portfolio->portfolio_title; ?>)
          <?php endif; ?>
      </span>
      <div class="close_me pull-right"><a href="javascript:void(0)" id="close_windows" class="butAcc"><img src="images/icon/closeme.png" /></a></div>
  </div>
  <div class="content">

<form id="portfolio_edit" action="portfolioapi/validate_portfolio" name="portfolio_edit" method="post">

                                        <div class="section">
                                            <label>Category<small>Select</small></label>   
                                            <div> 
                                              <select class="chzn-select" name="chzn-select[]" multiple tabindex="4">
                                                 <?php 
                                                 
                                                 $in_categories = $portfolio->categories->lists('category_title', 'category_id');

                                                 foreach ($categories as $category) {
                                                     ?>
                                                <option
                                                    value="<?php echo $category->category_id; ?>"
                                                    <?php if(isset($in_categories[$category->category_id])): ?>
                                                        selected
                                                    <?php endif; ?>
                                                    >
                                                    <?php echo $category->category_title; ?>
                                                </option>
                                                     <?php
                                                 }
                                                 
                                                 ?>
                                              </select>
                                              
                                            </div>
                                      </div>
                                      
                                           <div class="section">
                                           <label>Active</label>
                                               <div>
                                                <div class="checkslide">
                                                    <input
                                                        type="checkbox"
                                                        name="statusactive"
                                                        value="1"
                                                        <?php if($new_portfolio || $portfolio->portfolio_active): ?>
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
                                                      <?php if($portfolio->portfolio_image): ?>
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
                                                      <?php if(!$portfolio->portfolio_image): ?>
                                                        style="display:none;"
                                                      <?php endif; ?>
                                                      >
                                                      <!-- The container for the uploaded files -->
                                                      <div id="files">
                                                      
                                                      
							<?php if($portfolio->portfolio_image): ?>
                                                          <div class="img-preview-holder original_image">
                                                              <p class="img-preview">
                                                                  <img src="/uploads/images/resize/<?php echo $portfolio->portfolio_image; ?>?date=<?php echo time(); ?>" width="200">
                                                              </p>
     
                                                              <!-- Image Menu -->
                                                              <ul class="image-menu" data-img="<?php echo $portfolio->portfolio_image; ?>">
                                                              
                                                              
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
                                                  
                                                  
                                                  <?php if($portfolio->portfolio_image): ?>
                                                    <input id="delete_image_file" name="delete_image_file"  type="text" value="false" style="display:none">
                                                    <input id="original_file" name="original_file"  type="text" value="<?php echo $portfolio->portfolio_image ?>" style="display:none">
                                                  <?php else:?>
                                                    <input id="delete_image_file" name="delete_image_file"  type="text" value="true" style="display:none">
                                                  <?php  endif; ?>
                                                  
                                                  
                                                  
                                                  
                                                  
                                                  <input id="imagefile" name="imagefile"  type="text" value="<?php echo $portfolio->portfolio_image; ?>" style="display:none">

                                              </div>

                                          <!-- Upload Button End --> 
                                          
                                                <label>Image Name<small>Alt tags</small></label>   
                                                <div><input type="text" class="large" name="imagename" value="<?php echo $portfolio->portfolio_image_alt; ?>"/></div>
                                           </div>
                
<?php if($slideshow_enabled): ?>
    <div class="slideshow-file-holder section">
        <label>Slideshow Image<small>Upload an image</small></label>
        <div>
            <div class="choose-file file-dummy-input">
                <div class="input-holder">
                    <div class="dummy-file-name dummy-input">Choose Image</div>
                    <div class="file-add-on"><img src="images/addFiles.png" width="36" height="30" ></div>
                </div>

                <input class="fileupload" multiple="multiple" type="file" name="files[]">
            </div>

            <span class="delete-images delete-images-second" style="display:none;">Remove Image</span> <span id="progress_perc" style="display:none;"></span>

            <div class="file-error" style="display:none;">File type not supported</div>

            <div class="preview-holder" style="display:none;">
                <!-- The container for the uploaded files -->
                <div class="files">
                </div>

                <!-- The global progress bar -->
                <div class="progress progress-success progress-striped active" style="display:none;">
                    <div class="bar"></div>
                </div>
            </div>

            <input class="delete_image_file" name="delete_image_file"  type="text" value="true" style="display:none">

            <input class="imagefile" name="imagefileplaceholder" type="text" value="" style="display:none">
        </div>

        <!-- Upload Button End --> 

        <label>Slideshow Image Name<small>Alt tags</small></label>
        <div><input class="imagename" type="text" class="large" name="imagenameplaceholder" value="" /></div>

        <label>Slideshow Image Title</label>
        <div><input class="imagetitle" type="text" class="large" name="imagetitleplaceholder" value="" /></div>
        
        <input class="imageorder" type="hidden" class="large" name="imageorderplaceholder" value="" />
    </div>

    <div class="slideshow-images-sortable">
        
    <?php foreach ($portfolio->slideshow as $slideshowImage): ?>

        <div class="slideshow-file-container section">
            <label>Slideshow Image</label>
            <div>

                <div class="preview-holder">
                    <!-- The container for the uploaded files -->
                    <div class="files">
                        <div class="img-preview-holder original_image">
                            <p class="img-preview">
                                <img src="/uploads/images/resize/<?php echo $slideshowImage->image_filename; ?>?date=<?php echo time(); ?>" width="200">
                            </p>

                            <!-- Image Menu -->
                            <ul class="image-menu" data-img="<?php echo $slideshowImage->image_filename; ?>">


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
                    </div>
                </div>

                <input name="save_imagefile_<?php echo $slideshowImage->image_id; ?>" type="hidden" value="1">
                <input class="delete_image_file" name="delete_image_file"  type="text" value="false" style="display:none">
                <input class="original_file" name="original_file"  type="text" value="<?php echo $slideshowImage->image_filename ?>" style="display:none">
            </div>

            <!-- Upload Button End --> 

            <label>Slideshow Image Name<small>Alt tags</small></label>   
            <div><input class="imagename" type="text" class="large" name="save_imagename_<?php echo $slideshowImage->image_id; ?>" value="<?php echo $slideshowImage->image_alt; ?>"/></div>

            <label>Slideshow Image Title</label>   
            <div><input class="imagetitle" type="text" class="large" name="save_imagetitle_<?php echo $slideshowImage->image_id; ?>" value="<?php echo $slideshowImage->image_title; ?>"/></div>
            
            <input class="imageorder" type="hidden" class="large" name="save_imageorder_<?php echo $slideshowImage->image_id; ?>" value="<?php echo $slideshowImage->image_order; ?>"/>
        </div>
                                          
    <?php endforeach; ?>
        
    </div>

<?php endif; ?>
        

                                        <!-- Upload Document -->
                                      <div id="file-upload-holder" class="section">
                                          <label>File(s)<small>Upload file(s)</small></label>   
                                          <div>
                                                <span class="btn btn-success fileinput-button white">
                                                    <span style="position: relative;">
                                                        <i class="icon-file icon-white"></i>
                                                        Select File(s)...
                                                        <!-- The file input field used as target for the file upload widget -->
                                                        <input id="file-upload" type="file" name="files[]" style="position: absolute; left: -5px; bottom: -5px; z-index: 10; opacity: 0;" multiple />
                                                    </span>
                                                </span>

                                                <!-- The global progress bar -->
                                                <div id="progress-file" class="progress progress-success progress-striped" style="display: none;">
                                                    <div class="bar"></div>
                                                </div>
                                                <!-- The container for the uploaded files -->
                                                <div id="document-error" style="display:none;">File type not supported</div>
                                                <div id="files-list" class="files">
                                                    <?php

                                                    foreach ($portfolio->files as $file) {
                                                        ?>
                                                        <p>
                                                            <span><a href="/cms/file-download/portfolio/<?php echo $file->file_id; ?>"><?php echo $file->file_original_name; ?></a></span>
                                                            <br>
                                                            <a href="javascript:void(0)" class="delete-files" data-file-id="<?php echo $file->file_id; ?>">Remove File</a>
                                                        </p>
                                                        <?php
                                                    }

                                                    ?>
                                                </div>
                                                <input type="hidden" name="file-documents" id="file-documents" />
                                                <input type="hidden" name="file-documents1" id="file-documents1" />
                                                <input type="hidden" name="file-delete-list" id="file-delete-list">
                                            </div>
                                        </div>
                                        <!--Upload Document End -->
                                        
                                        <div class="section">
                                          <label>Title<small>Give your portfolio item a title</small></label>   
                                          <div>
                                              <input
                                                  type="text"
                                                  class="validate[required] large"
                                                  name="portfoliotitle"
                                                  value="<?php echo $portfolio->portfolio_title; ?>"
                                                  >
                                          </div>
                                        </div>
                                        
                                        <div class="section">
                                        <label>Introduction<small>One paragraph</small></label>   
                                        <div>
                                        <textarea name="Textarealimit" id="Textarealimit" class="validate[required] large" cols="" rows=""><?php echo $portfolio->portfolio_introduction; ?></textarea>
                                        <span class="f_help">Character limit: <span class="limitchars">200</span></span>
                                        </div>   
                                     	</div>
                                        
                                        <div class="section">
                                        <label>Description</label>
                                        <div> <textarea name="description" id="editor" class="editor" cols="5" rows=""><?php echo $portfolio->portfolio_description; ?></textarea></div>
                                        </div>
                                        
                                         <div class="section">
                                                 <label>Tags</label>   
                                               <div><input id="portfolio_tags_input" type="text" class="tags" value="<?php echo implode(',', $portfolio->tags->lists('tag_name')); ?>"  name="tags_input" /></div>   
                                   			</div>
                                            
                                            <div class="section last">
                                            <div id="save_portfolio">
                                              <a  class="btn btn-success submit_form">
                                                  <?php if ($new_portfolio): ?>
                                                  Submit
                                                  <?php else: ?>
                                                  Update
                                                  <?php endif; ?>
                                              </a>
                                              
                                              <a  class="btn mock_submit disable" style="display:none;">Uploading please wait <img src="images/loader/loader_green.gif" width="16" height="11" /></a>

                                              <?php if ($new_portfolio): ?>
                                              <a class="btn btn-warning special" onClick="ResetForm()">Clear Form</a>
                                              <?php endif; ?>
                                              
                                              <a id="close_windows" class="btn btn-danger butAcc">Cancel</a>
                                           </div>
                                           </div>
       									  <input type="hidden" name="action" value="edit_portfolio">
                                                                          
                                          <?php if (!$new_portfolio): ?>
                                            <input type="hidden" name="id" value="<?php echo $portfolio->portfolio_id; ?>">
                                          <?php endif; ?>

</form>
                                           

</div>	

</div>