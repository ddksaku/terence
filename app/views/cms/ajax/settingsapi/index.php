<?php

$can_delete = $user->hasPermission('delete_settings');

?>

<script>
$(document).ready(function(){	

	// Fancybox 
	$(".pop_box").fancybox({ 
	
		'showCloseButton': false,
			  
		onStart     :   function() {
		  
		  SCROLL_POS = $('html').scrollTop();
		  $("html, body").animate({ scrollTop: 0 }, 0);
		  
		},
		
		onClosed    :   function() {
		  
		  $("html, body").animate({ scrollTop: SCROLL_POS }, "slow");
		  
		}
		
	 });
	 
	 

var cols = [null,
				null,
				null,
				<?php if ($can_delete): ?>, null<?php endif; ?>];
var targets = [0,2<?php if ($can_delete): ?>, 3<?php endif; ?>];

	
	$('.data_table3').dataTable({
	  "sDom": "<'row-fluid tb-head'<'span6'f><'span6'<'pull-right'Cl>>r>t<'row-fluid tb-foot'<'span4'i><'span8'p>>",
	  "bJQueryUI": false,
	  "iDisplayLength": 10,
	  "sPaginationType": "bootstrap",
	  "oLanguage": {
		  "sLengthMenu": "_MENU_",
		  "sSearch": "Search"
	  },
	  "aoColumnDefs": [
          { "bSortable": false, "aTargets": targets }
       ],
	   "aoColumns": cols
	});
	
	
	// Select boxes
	$("select").not("select.chzn-select,select[multiple],select#box1Storage,select#box2Storage").selectBox();
	
	
	  // Delete Settings 
	  $("#deletesettings").live('click',function(e) {
		         
				 e.preventDefault();
				 
				 var name = $(this).attr("name");
				 var datavalue ='id='+ $(this).attr("rel");   
				 DeleteSettings(datavalue, name);
				
	  });
          
	  function DeleteSettings(datavalue, name){
		  
		  
			  $.confirm({
			  'title': 'DELETE SETTINGS ('+name+')','message': "Do you want to delete these settings?",'buttons': {'Yes': {'class': 'btn btn-success',
			  'action': function(){
								loading('Deleting',1);
								$.ajax({
									url: "settings/delete.php",
									data: datavalue,
									success: function(data){
											
										  if (data.check == "0"){ 
												 $("#tab1 .load_page").fadeOut(500,function(){
															   
															 
															  setTimeout("alertMessage('error','You don't have permission to delete these settings!')",1000);  
													
														
													 // fancybox close
													$.fancybox.close();
												    setTimeout('unloading()',900);						
																					
													}).load('settings/tableReload.php').fadeIn();			
										  return false;
										  }	   
										  if (data.check == "1"){
											  $("#tab1 .load_page").fadeOut(500,function(){
															   
															
															  setTimeout("alertMessage('success','Success')",1000);  
													
														
													 // fancybox close
													$.fancybox.close();
												    setTimeout('unloading()',900);						
																					
													}).load('settings/tableReload.php').fadeIn();			
										  return false;
										  }
									},
									cache: false,
									type: "POST",
									dataType: 'json'
								});
				}},'No'	: {'class'	: 'btn btn-danger'}}});
	  }
	  
	  function Deleteall(datavalue){
		  
			$.ajax({
				url: "settings/delete.php",
				data: datavalue,
				cache: false,
				type: "POST",
				dataType: 'json'
			});
				
	  }
	  function Deletejob(){
		  
		  
		  loading('Deleting',1);
		  $('.checksquared').find('input[type=checkbox]:checked').each(function(){
		  var the_id = $(this).val();
		  var data = "id="+the_id;
		  Deleteall(data);
		  });
		  $("#tab1 .load_page").fadeOut(500,function(){
		  
		  
		  setTimeout("alertMessage('success','Success')",2000);  
		  
		  
		  // fancybox close
		  $.fancybox.close();
		  setTimeout('unloading()',1500);						
					
		  }).load('settings/tableReload.php').fadeIn();			
		  
	  
	  }
	  
	  
	  
	  $('.DeleteAll').live('click',function() {
		   
		 
		   var checked = $('.checksquared').find('input[type=checkbox]:checked').length;
		   
		   if(checked > 0){
			   
			  
				   $.confirm({
					  'title': 'DELETE ALL','message': "Do you want to delete all selected settings?",'buttons': {'Yes': {'class': 'btn btn-success',
					  'action': function(){ 
					  Deletejob();
					  }},'No'	: {'class'	: 'btn btn-danger'}}
				   });
			  
		   }else{
		       
			   //Nothing was selected
		       alertMessage('error','Please select a setting to delete')
		   
		   }

       });
	   
	   
	   
});

	
	
	
</script>

                              <form class="settings-holder">
                                <table class="table table-bordered table-striped  data_table3" id="data_table3">
                                <thead>
                                  <tr align="center">

                                    <th class="child_2">Logo</th>
                                    <th class="child_3">Company Name</th>
                                    <th class="child_10">Edit</th>
                                    <?php if ($can_delete): ?>
                                    <th class="child_11">Delete</th>
                                    <?php endif; ?>
                                  </tr>
                                </thead>
                                <tbody align="center">

				<?php
                                
                                foreach ($settings as $setting) {
                                    ?>

                                  <tr>

                                      <td>
                                          <?php if($setting->setting_image): ?>
                                            <a
                                                href="settingsapi/edit"
                                                data-id="<?php echo $setting->setting_id; ?>"
                                                class="synergy-modal"
                                                >
                                                <img
                                                    src="/uploads/images/thumb/<?php echo $setting->setting_image; ?>?date=<?php echo time(); ?>"
                                                    data-src="uploads/thumb/<?php echo $setting->setting_image; ?>"
                                                    alt="<?php echo $setting->setting_image_name; ?>"
                                                    class="table-thumb">
                                            </a>
                                          <?php endif; ?>
                                      </td>
                                      
                                      <td>
                                          <?php echo $setting->setting_name; ?>
                                      </td>

                                      <td>
                                          <a
                                                href="settingsapi/edit"
                                                data-id="<?php echo $setting->setting_id; ?>"
                                                class="synergy-modal"
                                                >
                                              <img src="images/icon/gray_18/pencil.png" alt="Edit">
                                          </a>
                                      </td>
                                      
                                      <?php if($can_delete): ?>
                                        <td>
                                            <div class="hidedisabled"><img src="images/icon/gray_18/trash_can.png" alt="Delete"/></div>
                                        </td>
                                      <?php endif;  ?>
                                  </tr>
                                  
                                <?php

                            }

                            ?>
                            
                            </tbody>
                          </table>
                          </form>
                              
