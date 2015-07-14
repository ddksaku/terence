<script type="text/javascript" >
//Stop multiple clicks on gallery
var album_clicked = false;

$(function() {	
	  	
	// move images to new album
	$('.album').droppable({
		hoverClass	: 'over',
		activeClass	: 'dragging',
		tolerance	:  'pointer',
		accept: ".albumImage",
		
		drop		:   function(event,ui){
			
			      if( $( this).hasClass('selected') ) return false;
			
				  loading('Moving',0);
				  var album = $(this).attr('id');		
				  var datavalue='newalbumid='+album+'&lastalbumid='+ ui.draggable.imgdata(2)+'&picid='+ ui.draggable.imgdata(0); 

                                    ajaxCallAPI(
                                        'gallery',
                                        'post',
                                        'move_picture',
                                        null,
                                        datavalue,
                                        function(data)
                                        {
                                              if(data.success==0) { 
                                                      alertMessage('error','Error Try Again');
                                                      return false;
                                               }
                                               
                                              ui.helper.fadeOut(function(){ui.helper.remove();});

                                              synergyModuleGalleryAlbumRefresh(function()
                                              {
                                                    $('#albumsLoad #albumsList').find("#"+ui.draggable.imgdata(2)).addClass('selected');
                                                    unloading();

                                                    alertMessage('success','Image Moved');
                                                    return false;																		
                                              });

                                              return false;	
                                        }
                                    );
				 
				  ui.helper.fadeOut(400);
				  setTimeout("unloading()",1500); 	
                                  
                                  return false;
	      }
	});
	
	
	
	
	// mouseenter Over album with CSS3
	$(".preview").delegate('img', 'mouseenter', function() {
		  if ($(this).hasClass('stackphotos')) {
		  var $parent = $(this).parent();
				$parent.find('img#photo1').addClass('rotate1');
				$parent.find('img#photo2').addClass('rotate2');
				$parent.find('img#photo3').addClass('rotate3');
		  }
	  }).delegate('img', 'mouseleave', function() {
				$('img#photo1').removeClass('rotate1');
				$('img#photo2').removeClass('rotate2');
				$('img#photo3').removeClass('rotate3');
	});
	
	
	
	
	// jScrollPane Overflow
	//$('#albumsList').jScrollPane({ autoReinitialise: true });
	
	
	
	
	//Sort the albums within the scroller
	$('#albumsList').sortable({
		
		placeholder: "ui-state-highlight",
		distance: 15,
		scroll: true,
		//scrollSensitivity: 100,
		containment: ".scroll-helper",
		axis: 'y',
		tolerance: "pointer",
		stop: function(){
		    
			
			var data_arr = [];
			
		    //Build album order
			$('#albumsList .album').each(function(key,val){
				data_arr.push($(this).attr('id'));
			});

                        ajaxCallAPI(
                            'gallery',
                            'post',
                            'album_reorder',
                            null,
                            { data: data_arr },
                            function(data)
                            {
                                if(data.success == 1) {
                                    alertMessage('success', 'Albums Re-Ordered');
                                } else {
                                    alertMessage('error', 'Album Re-Order Failed');
                                }
                            }
                        );		
		}
	
	});
	
	//Fix scroll height
	$('.scroll-helper').height($('.scroll_container').scrollHeight);
	
	
	
	$('.album.load').die('click');
	$('.album.load').live('click',function(e){

			  if (typeof(loadalbum) != "undefined") { 
    
			  $('.album').removeClass('selected');
			  var albumid=$(this).attr('id');
			  $(this).addClass('selected');
			  loadalbum(albumid);
			  
			  }
			
	});
	
	
	
	//Load Album
	function loadalbum(albumid){
		loading('Loading');
 
		$('.screen-msg').hide();
                         
                synergyModuleGalleryPicturesRefresh(albumid);

                        imgRow();

                          $("#uploadAlbum").data('album', albumid);
                          $("#uploadDisableBut").hide();
                          $('#uploadAlbum').removeClass('disable secure ').addClass('special add  ');

                        unloading();
	}

    
	
	/////////////////////////////////////////////////////////////////////////////////////////////
	//Update the gallery album titles
	/////////////////////////////////////////////////////////////////////////////////////////////
	
	var ACTIVEALBUM = false;
	var $selectedAlbum = false;
				
				
	//Make content editable on gallery album text
	$('.title').click(function(e){ 
	    
		e.preventDefault();
		e.stopPropagation();
		
		
		if($(this)[0] != $selectedAlbum[0]){
					
		    ACTIVEALBUM = false;
					
		}
					
		
		if(!ACTIVEALBUM){
						
			$selectedAlbum = $(this);
			ACTIVEALBUM = true;
									
			//Get elements
			var $button        = $(this);
			var $galleryTitle  = $button.find('.gallery-title');
			var $titleText     = $button.find('.title-text');
			
			//Hide text
			$titleText.hide();
			
			//Show box
			$galleryTitle.show();
			
			//Set value
			$galleryTitle.val($(this).find('.title-text').html());
			
			//Set focus
			$galleryTitle.focus();
		
		}
		
		
		
		
		
	});
	
	
	
	
	//On finnished editing
	$('.gallery-title').blur(function(){
		
		if(ACTIVEALBUM){
			
			//Ajax update the title
			updateGalleryTitle($(this));
			ACTIVEALBUM = false;
			
			$('#name').val($(this).val());
			
		}
	
	});
	
	
	//On enter press
	$('.gallery-title').keypress(function(e){
		
            var code = (e.keyCode ? e.keyCode : e.which);
            if (code == 13){
			    
				if(ACTIVEALBUM){
					
					//Ajax update the title
					updateGalleryTitle($(this));
					
					$('#name').val($(this).val());
					
					e.preventDefault();
					ACTIVEALBUM = false;
				
				}
			}
            
			
    });
	

	
	
	
	
	//Update the gallery title
	function updateGalleryTitle($button){
		
		
		var $galleryTitle  = $button.parent().find('.gallery-title');
		var $titleText     = $button.parent().find('.title-text');
		var id             = $button.closest('.album').attr('id');
		var title          = $galleryTitle.val();
		

	    var obj   = {};
		obj.id    = id;
		obj.title = title;
                
                ajaxCallAPI(
                    'gallery',
                    'post',
                    'edit_album_title',
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
				 
				 alertMessage('success','Album Name Saved');
			 
			 }else{
				 
				 $galleryTitle.val($titleText.html());
				 
			     //Hide text
				 $titleText.show();
				  
				 //Show box
				 $galleryTitle.hide();
				 
				 if(data.error==2) {
                                     alertMessage('error','Album name already in use.');
                                 } else {
                                    alertMessage('error','Album Renaming Failed');
                                 }
			 
			 }//if
                    }
                );
	}//func
	
	
		  
 }); 
 
 
 

</script>  




<div class="scroll_container">
<div class="scroll-helper">
<ul id="albumsList" >

 <?php
		foreach ($albums as $album) {
			  if (($picture = $album->thumbnail)) {
				  $thumbnail = '/gallery/s/'.$picture->picture_file;
			  } else {
				  $thumbnail = 'images/icon/empty_album_icon_small.jpg';
			  }
                    ?>
            
                 <li class="album load" id="<?php echo $album->album_id; ?>">
                      <div class="preview">
			<?php
                        
                        $firstTwo = $album->firstTwo();
                        
                        switch($firstTwo->count()) {
                            case 0:
                            {
                                ?>
                                    <img width="130" id="p1" class="stackphotos" src="<?php echo $thumbnail; ?>?cachekill=<?php echo uniqid(); ?>" alt="Thumbnail">
                                <?php
                                
                                break;
                            }
                            default:
                            {
                                $i = 1;
                                 
                                foreach ($firstTwo as $thumb) {
                                    ?>
                                        <img width="130" id="photo<?php echo $i; ?>" class="stackphotos" src="/gallery/s/<?php echo $thumb->picture_file; ?>?cachekill=<?php echo uniqid(); ?>" alt="Thumbnail" />
                                    <?php
                                    ++$i;
                                }
                                 
                                ?>
                                    <img width="130" id="photo3" class="stackphotos" src="<?php echo $thumbnail; ?>?cachekill=<?php echo uniqid(); ?>" alt="Thumbnail">
                                <?php
                            }
                        }
                        
                        ?>

                      <div style="clear:both"></div>
                      </div>

                            <div class="title">
                            <span class="title-text"><?php echo htmlspecialchars($album->album_name); ?></span>
                            <input class="gallery-title" type="text" size="15" name="gallery_title" value="<?php echo htmlspecialchars($album->album_name)?>" maxlength="30" style="display:none;">
                            </div>
                            <div class="stats">Images: <span class="picCount"><?php echo (int)$album->pictures()->count(); ?></span></div>
                            <div class="clear"></div>
                   </li>
                <?php

                }

                ?>
</ul><!-- End albumsList -->
</div>
</div>