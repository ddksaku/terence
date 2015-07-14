<script type="text/javascript">
    var upload_url = '<?php echo $upload_script; ?>';

	 $(document).ready(function(){
				 // fancybox with Double click			
				  $('.albumImage').dblclick(function(){
						$("a[rel=glr]").fancybox({  'showCloseButton': true,'centerOnScroll' : true, 'overlayOpacity' : 0.8,'padding' : 0 });
						$(this).find('a').trigger('click');
				  })
				  // images hover
				  $('.picHolder,.SEdemo').hover(
						function() {
							$(this).find('.picTitle').fadeTo(200, 1);
						},function() {
							$(this).find('.picTitle').fadeTo(200, 0);
						}
					)	
				  // jScrollPane Overflow
				  //$('.albumpics').jScrollPane({ autoReinitialise: true });
                                  

				  // Sortable
				    $( "#sortable" ).sortable({
					    opacity: 0.6,revert: true,cursor: "move", zIndex:9000,
					    update : function () {
						var order = $('#sortable').sortable('serialize');

                                                ajaxCallAPI(
                                                    'gallery',
                                                    'post',
                                                    'picture_reorder',
                                                    null,
                                                    order,
                                                    function(data)
                                                    {
                                                         if(data.success==0){  alertMessage('error','Error Try Again'); }
                                                         if(data.success==1){
                                                             alertMessage('success','Image Order Changed');
                                                         }

                                                         return false;
                                                    }
                                                );

					 }
				    });
					
					// Cover Album Change
				  $('.picPreview').droppable({
					  hoverClass: 'picPreview-hover',
					  activeClass: 'picPreview-hover',
					   drop: function( event, ui ) { 
						   $('#image-albumPreview').attr('src',ui.draggable.find('.gallery_img').attr('src'));
						   	var id=ui.draggable.imgdata(0);
							 document.Save_album.thumbPreview.value=id;
					   }
				});	
				  
				 // Form Save
				$(".save_").click(function() { 	  	
						 loading('Saving',0);
						 var form_id=$(this).parents('form').attr('id');
						 var datavalue=$('#'+form_id).serialize();	

                                                ajaxCallAPI(
                                                    'gallery',
                                                    'post',
                                                    'editalbum',
                                                    null,
                                                    datavalue,
                                                    function(data)
                                                    {
                                                          if(data.success==0){
                                                              if(data.error == 2) {
                                                                alertMessage('error','Album name already in use.');
                                                              } else {
                                                                alertMessage('error','Error Try Again');
                                                                }
                                                                
                                                          
                                                                unloading();
                                                                return false;
                                                          }
                                                          if(data.success==1){
                                                                synergyModuleGalleryAlbumRefresh();
                                                              
                                                                alertMessage('success','Album Updated');
                                                                
                                                          
                                                                unloading();
                                                          }
                                                    }
                                                );
				});
				
	   // Delete album  
           
           $('.albumDelete').die('click');
	   $(".albumDelete").live('click',function() { 
				 var name = $(this).attr("name");
				 var datavalue ='id='+ $(this).attr("rel");   
				 albumDelete(datavalue,name);
	  });
	  function albumDelete(datavalue,name){
			  $.confirm({
			  'title': 'DELETE ALBUM','message': "<strong>Do you want to delete the following?</strong><br /><font color=red>' "+ name +" ' </font> ",'buttons': {'Yes': {'class': 'btn btn-success',
			  'action': function(){
                                    loading('Deleting',1);

                                        ajaxCallAPI(
                                            'gallery',
                                            'post',
                                            'delete_album',
                                            null,
                                            datavalue,
                                            function(data)
                                            {
                                                  if (data.success == "0"){ alertMessage('error','Deletion Error Try Again'); }	   
                                                  if (data.success == "1"){
                                                    synergyModuleGalleryAlbumRefresh(function(){

                                                          $("#uploadAlbum").removeAttr('href',''); 	
                                                          $("#uploadDisableBut").show();
                                                          $('#uploadAlbum').removeClass(' special add  ').addClass(' disable secure ');
                                                          $('#imageLoad').fadeOut(500,function(){ $(this).html('');}).fadeIn();				
                                                          $('.screen-msg').show();
                                                          setTimeout("unloading();",900); 
                                                          alertMessage('success','Success');
                                                          setTimeout("alertMessage('success','Success')",1000);  							

                                                    });		
                                                    
                                                    return false;
                                                  }
                                            }
                                        );
				}},'No'	: {'class'	: 'btn btn-danger'}}});
	  }
				 // Drag & Drop Delete images
				$('.deletezone').droppable({
					hoverClass: 'deletezoneover',
					activeClass: 'deletezonedragging',
					drop:function(event,ui){	
			
                                            var album_id =  ui.draggable.imgdata(2);
                        
					   var datavalue='id='+ ui.draggable.imgdata(0)+'&albumid='+ album_id; 
					   var name =ui.draggable.imgdata(1); 
			
					$.confirm({
					'title': 'DELETE IMAGE','message': "<strong>Do you want to delete the following?</strong><br /><font color=red>' "+ name +" ' </font> ",'buttons': {'Yes': {'class': 'btn btn-success',
					'action': function(data){
								loading('Deleting',1);
                                                      
                                                            ajaxCallAPI(
                                                                'gallery',
                                                                'post',
                                                                'delete_picture',
                                                                null,
                                                                datavalue,
                                                                function(data)
                                                                {
                                                                      if (data.success == "0"){ alertMessage('error','Deletion Error Try Again'); }	   
                                                                      if (data.success == "1"){
                                                                            ui.helper.fadeOut(function(){ ui.helper.remove(); });
     
                                                                            synergyModuleGalleryAlbumRefresh();
                                                                            synergyModuleGalleryPicturesRefresh(album_id);

                                                                              setTimeout("unloading();",900); 
                                                                              setTimeout("alertMessage('success','Success')",1000);  		
                                                                                      			
                                                                        return false;
                                                                      }
                                                                }
                                                            );
						}},'No'	: {'class'	: 'btn btn-danger'}}});
					},
					tolerance:'pointer'
				});
				
				// Link On/Off Edit Album 
                                
                                $('#editAlbum.editOn').die('click');
				$('#editAlbum.editOn').live('click',function(){							   
					$('.album_edit').fadeIn(400);
					$('.boxtitle').css({'margin-left':'207px'});
					$('.boxtitle .texttip').hide();
						$(this).html('close edit').attr('title','Click here to close edit').removeClass('editOn').addClass('editOff');
						imgRow();
				});
                                
                                $('#editAlbum.editOff').die('click');
				$('#editAlbum.editOff').live('click',function(){													   
						$('.album_edit').fadeOut(400,function(){
						$('.boxtitle .texttip').show();
								 $('.boxtitle').css({'margin-left':'0'});
								 imgRow();
						});
						$(this).html('edit album').attr('title','Click here to edit album').removeClass('editOff').addClass('editOn');
				});
				
				
				
				/////////////////////////////////////////////////////////////////////////////////////////////
				//Update the gallery album titles
				/////////////////////////////////////////////////////////////////////////////////////////////
				
				var ACTIVE = false;
				var $selected = false;
				
				//Make content editable on gallery album text
				$('.picTitle').click(function(e){ 
				
				    if($(this)[0] != $selected[0]){
					
					    ACTIVE = false;
					
					
					}
			
					if(!ACTIVE){
						
						$selected = $(this);
						
						ACTIVE = true;
						
						e.preventDefault();
						e.stopPropagation();
						
						//Get elements
						var $button        = $(this);
						var $galleryTitle  = $button.find('.img-title-input');
						var $titleText     = $button.find('.img-title');
						
						//Hide text
						$titleText.hide();
						
						//Show box
						$galleryTitle.show();
						
						
						//Set focus
						$galleryTitle.focus();
						
					
					}//if
					
				});
				
				
				
				
				//On finnished editing
				$('.img-title-input').blur(function(){
					
					if(ACTIVE){
						
						//Ajax update the title
						updateGalleryTitle($(this));
						
						ACTIVE = false;
					
					}
				
				});
				
				
				//On enter press
				$('.img-title-input').keypress(function(e){
					
						var code = (e.keyCode ? e.keyCode : e.which);
						if (code == 13){
							
							
							if(ACTIVE){
								
								//Ajax update the title
								updateGalleryTitle($(this));
								
								e.preventDefault();
								
								ACTIVE = false;
							
							}
						}
						
						
				});
				
			
				
				
				
				
				//Update the gallery title
				function updateGalleryTitle($button){
					
					
					var $galleryTitle  = $button;
					var $titleText     = $button.parent().find('.img-title');
					var id             = $button.closest('.albumImage').attr('id').replace('posi_', '');
					var title          = $button.val();
					

					var obj   = {};
					obj.id    = id;
					obj.title = title;

                                        ajaxCallAPI(
                                            'gallery',
                                            'post',
                                            'edit_picture_title',
                                            null,
                                            obj,
                                            function(data)
                                            {
						 if(data.success > 0){
							 
							 //Set values
							 $titleText.html(title);
							 
							 //Hide text
							 $titleText.show();
							  
							 //Show box
							 $galleryTitle.hide();
							 
							 alertMessage('success','Image Title Saved');
						 
						 }else{
							 
							 $galleryTitle.val($titleText.html());
							 
							 //Hide text
							 $titleText.show();
							  
							 //Show box
							 $galleryTitle.hide();
							 
							 
							 alertMessage('error','Image Renaming Failed');
						 
						 }//if
                                            }
                                        );
				}//func
				
				
				
				
				//rotate the image
				function rotate_img(img,angle, callback){
				
				    loading('Rotating',1);
				    $.get(upload_url, {"img":img, "rotate":angle}, function(response){

					    callback(response);
						
						setTimeout("unloading();",900); 
						setTimeout("alertMessage('success','Image Rotated')",1000);
						
						 
					
					}, 'json');
				
				
				
				}
				
				
				//Update rotated image
				function update_image(img){
				
				    var image = '/gallery/s/' + img;

					//Find images and replace
					var $im = $('img').filter(function() {
                                                var reg = new RegExp(RegExp.escape(image), 'gim');
                                                return this.src.match(reg);
                                          });

					$im.attr('src', image + '?cachekill=' + (new Date).getTime() + (Math.random().toString().replace('.', '')));
				}
				
				
				
				//Rotate Images
				$('.rotate-img-right').die();
				$('.rotate-img-right').live('click', function(){
				    
					
					var img = $(this).attr('data-img');
					
				    //Rotate
					rotate_img(img, -90, function(data){
					
					
					    var file = data.files[0].name;
						update_image(file);
						
						
					
					
					});
				
				});
				
				
				$('.rotate-img-left').die();
				$('.rotate-img-left').live('click', function(){
				
				    var img = $(this).attr('data-img');
					
				    //Rotate
					rotate_img(img, 90, function(data){
					
					
					    var file = data.files[0].name;
						update_image(file);
					
					});
				
				});
				
				
				//Show menu on hover
				$('.picHolder').die();
				$('.picHolder').mouseenter(function(){
					 
				    //Show menu
					$(this).find('.img-btns').fadeIn('fast');
					
				}).mouseleave(function(){ 
				
				    //Hide menu
				    $(this).find('.img-btns').fadeOut('fast');
				
				});
				
				
				
				//Menu delete button
				$('.delete-img').die();
				$('.delete-img').live('click', function(){
                                    
                                    var album_id = $($(this).closest('.picHolder').find('.dataImg').find('li')[2]).html();
				
					var name = $(this).closest('.picHolder').find('.img-title').html();
					var datavalue='id='+ $($(this).closest('.picHolder').find('.dataImg').find('li')[0]).html();
					datavalue += '&albumid='+ album_id;

					var $img = $(this).closest('.albumImage');
					
                                        $.confirm({
						'title': 'DELETE IMAGE','message': "<strong>Do you want to delete the following?</strong><br /><font color=red>' "+ name +" ' </font> ",'buttons': {'Yes': {'class': 'btn btn-success',
						'action': function(data){
									loading('Deleting',1);

                                                    ajaxCallAPI(
                                                        'gallery',
                                                        'post',
                                                        'delete_picture',
                                                        null,
                                                        datavalue,
                                                        function(data)
                                                        {
                                                          if (data.success == "0"){ alertMessage('error','Deletion Error Try Again'); }	   
                                                          if (data.success == "1"){
                                                              $img.hide('fast', function(){ $(this).remove(); });

                                                                synergyModuleGalleryAlbumRefresh();
                                                                synergyModuleGalleryPicturesRefresh(album_id);
                                                                
                                                                		  
//                                                              if(data.thumb == 0){
//                                                                      $('#image-albumPreview').attr('src','images/icon/empty_album.jpg');
//                                                              }
                                                                			
                                                              setTimeout("unloading();",900); 
                                                              setTimeout("alertMessage('success','Success')",1000); 
		
                                                            return false;
                                                          }
                                                        }
                                                    );
					}},'No'	: {'class'	: 'btn btn-danger'}}});
				
				
				});
				
				
				
				//Trigger save
				$('.tip #name').keypress(function(e){
				        
						
						
						if(e.which == 13) {
							e.preventDefault();
							$('.save_').click();
						}
				
				});
				
				  
	  }); 

</script>   

                        <div class="albumImagePreview" style="display:block">
                              <div class="album_edit" style="display:none">
                                <?php

                                if (($thumbnail = $album->thumbnail)) {
                                    $thumbnail = '/gallery/s/'.$thumbnail->picture_file;
                                } else {
                                    $thumbnail = 'images/icon/empty_album.jpg';
                                }
                                
                                ?>
                               <form name="Save_album" id="Save_album" action="">
                              <h1>Edit Album</h1>
                              
                              <div class="picPreview"><img id="image-albumPreview" title="Drop Image Here" src="<?php echo $thumbnail; ?>?cachekill=<?php echo uniqid(); ?>" alt="Image Preview" /></div>
                              <div class="clear"></div>
                              <div class="hr"></div>
                              
    				<input type="hidden" name="id_edit" id="id_edit" value="<?php echo $album->album_id; ?>" />
                                  <input type="hidden" name="thumbPreview" id="thumbPreview" />
                                 <div class="tip">
                                <input type="text" name="name" id="name" class="validate[required]" title="Album name" style="width:146px" value="<?php echo htmlspecialchars($album->album_name); ?>" maxlength="35" />
                                </div>
                                  <div class="hr"></div>
                                  <ul class="uibutton-group">
                                    <a class="btn btn-success save_">Update</a>
                                    <a class="btn btn-danger albumDelete special" rel="<?php echo $album->album_id; ?>" name="<?php echo htmlspecialchars($album->album_name); ?>">Delete</a>
                                  </ul>
                                  <div class="hr"></div>
                                  </form>
                                  <div class="deletezone small"> Drop Images To Delete</div>
                                  <div class="hr"></div>    
                         
                                  <div class="clear"></div>
                              </div>
                              
                              <div class="boxtitle" ><span class="texttip">double click to view large images // </span>
                              <a id="editAlbum" class="editOn" title="Click here to edit album">edit album</a>
                              </div>		
                 
			<?php if ($album->pictures->count() < 1): ?>
                                <div class="screen-msg" style="line-height:470px;">
                                    <span class="ico gray upload"></span> 
                                    Upload some images to this album!
                                </div>
                                <div class="clear"></div>
			<?php else: ?>
                              <div class="albumpics">
                                    <ul id="sortable"  >
                                    
					<?php
                                        
                                        foreach ($album->pictures as $picture) {
                                            ?>
                                        <li class="albumImage" id="posi_<?php echo $picture->picture_id; ?>">
                                        
                                            
                                                  
                                                  
                                            <div class="picHolder">
                                                  
                                                <!-- Image Buttons -->
                                                <ol class="img-btns" style="display:none;">
                                              
                                                  <li class="delete-img" data-img="<?php echo $picture->picture_file; ?>">
                                                  <img src="images/icon/gray_18/trash_can.png" width="18" height="18" />
                                                  </li>
                                                  
                                                  <li class="rotate-img-right" data-img="<?php echo $picture->picture_file; ?>">
                                                  <img src="images/icon/gray_18/rotate_right.png" width="18" height="18" />
                                                  </li>
                                                  
                                                  <li class="rotate-img-left" data-img="<?php echo $picture->picture_file; ?>">
                                                  <img src="images/icon/gray_18/rotate_left.png" width="18" height="18" />
                                                  </li>
                                              
                                                </ol>
                                            
                                            
                                                  <span class="image_highlight"></span>
                                                  <a href="/gallery/m/<?php echo $picture->picture_file; ?>?cachekill=<?php echo uniqid(); ?>" rel='glr'></a>	

                                                  <img class="gallery_img" src="/gallery/s/<?php echo $picture->picture_file; ?>?cachekill=<?php echo uniqid(); ?>" title="Drag This Image" />

                                            <div class="picTitle">
                                            
                                            
                                                <span class="img-title"><?php echo $picture->picture_title; ?></span>
                                                <input class="img-title-input" type="text" value="<?php echo $picture->picture_title; ?>" style="display:none;" maxlength="40">

                                                
                                            
                                            </div>
                                            <ul class="dataImg"><!--// This data images with your call to php or SQL -->
                                                    <li><?php echo $picture->picture_id; ?></li><!--// This id images -->
                                                    <li><?php echo $picture->picture_title; ?></li><!--// This name images -->
                                                    <li><?php echo $album->album_id; ?></li><!--// This album id images -->
                                            </ul>   
                                            </div>
                                        </li>
                                      <?php
                                      
                                      }
                                      
                                      ?>
                                      
                                    </ul>
                              </div> 
                              <?php endif; ?>
                             <br class="clear" />
                        </div>
                        <br class="clear" />