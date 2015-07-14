<!-- Widget -->
<div class="widget span12 clearfix">

    <div class="widget-header">
        <span><span class="ico gray shadow <?php echo $module->module_icon; ?>"></span> <?php echo $module->getName(); ?></span>
    </div><!-- End widget-header -->	
    
    <div class="widget-content">        

            <form name="contactsubmit" id="contactsubmit-lightbox">
            <!-- Map content-->
            <div class="map-load-script"></div>
            <div class="disabled_map">Map Disabled</div>
            <div id="map_canvas"></div>       
                                                  
            <div class="row-fluid">
                <div class="span6">
                
                      <div class="section">
                            <label>Company Name</label>
                            <div>
                                    <input type="text" name="name" id="name"
                                            class="validate[required,minSize[2],maxSize[100] ] large"
                                            value="<?php echo $contact->contact_name; ?>" />
                            </div>
                      </div>
                      
                      <div class="section">
                            <label>Address</label>
                            <div>
                                    <textarea name="address" id="address"
                                            class="validate[required] full"><?php echo $contact->contact_address; ?></textarea>
                            </div>
                      </div>
                      
                      <div class="section">
                            <label>Phone Numbers</label>

                            <div><span class="f_help">Telephone</span>
                                    <input type="text" class="large" name="phone" value="<?php echo $contact->contact_phone; ?>" />

                                    <span class="f_help">Mobile</span>
                                    <input type="text" class="large" name="mobile" value="<?php echo $contact->contact_mobile; ?>" />

                                    <span class="f_help">Fax</span>
                                    <input type="text" class="large" name="fax" value="<?php echo $contact->contact_fax; ?>" />
                            </div>
                      </div>
                      
                      <div class="section">
                            <label>Email Address</label>
                            <div>
                                    <input type="text" name="email" id="email"
                                            class="validate[required,custom[email]] large"
                                            value="<?php echo $contact->contact_email; ?>" />
                            </div>
                      </div>
                      
                      <div class="section last">
                            <label>Show Email<small>Throughout website?</small></label>
                            <div>
                                    <div class="checkslide">
                                        <input
                                            type="checkbox"
                                            value="1"
                                            <?php if($contact->contact_email_status): ?>
                                                checked
                                            <?php endif; ?>
                                            name="emailstatusactive" />
                                        <label for="checkslide"></label>
                                    </div>
                            </div>
                      </div>
               </div>
            
                <div class="span6">
                  <div class="section">
                        <label>Show Map</label>
                        <div>
                            <div class="checkslide">
                                <input type="checkbox"
                                    <?php if($contact->contact_map_status): ?>
                                       checked
                                    <?php endif; ?>
                                    id="map_status"
                                    name="map_status"
                                    class="map_status"
                                    value="1"
                                    >
                                <label for="map_status"></label>
                            </div>
                            <span class="f_help">Google Map</span>
                        </div>
                  </div>
                  
                  <div class="section last">
                        <label>Map Settings</label>
                        <div>
                                <span class="wtip"><input name="type_value" type="text"
                                        id="type_value" class="large" value="<?php echo $contact->contact_type_value; ?>"
                                        title="Map Type" /></span>
                        </div>
                        <div>
                                <span class="wtip"><input name="lat_value" type="text"
                                        id="lat_value" class="large" value="<?=$contact->contact_lat_value; ?>"
                                        title="Latitude" /></span>
                        </div>
                        <div>
                                <span class="wtip"><input name="lon_value" type="text"
                                        id="lon_value" class="large" value="<?=$contact->contact_lon_value; ?>"
                                        title="Longitude" /></span>
                        </div>
                        <div>
                                <span class="wtip"><input name="zoom_value" type="text"
                                        class="small" id="zoom_value" value="<?=$contact->contact_zoom_value?>"
                                        title="Zoom Level" /></span>
                        </div>
                  </div>
                    
                    
                        <input type="hidden" name="id" value="<?php echo $contact->contact_id; ?>">

                <div class="section last">
                    <div>
                        <a class="btn btn-success submit_form">Update</a>
                    </div>
                </div>
                  
            </div><!--span6-->
            </div><!--row-fluid-->
          </form> 

    </div><!--  end widget-content -->
</div><!-- widget  span12 clearfix-->

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
		   $('#contactsubmit-lightbox').validationEngine({
					prettySelect : true,usePrefix: prefix,
					ajaxFormValidation: true,
					onBeforeAjaxFormValidation: Add_database_light
			});	
			
    //////////////////////////////////////////////////////////////////////
	//File uploader
	//////////////////////////////////////////////////////////////////////
	
	

	//Delete images on close
	$('#close_windows').live('click', function(){
		
	});

	function Add_database_light(form, options){
		 loading('Loading',0);

                    ajaxCallAPI(
                        'contact',
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
                                       alertMessage("success","Contact details edited successfully");
                   
                                       // fancybox close
                                       $.fancybox.close();
                                       setTimeout('unloading()',500);
                              }
                        }
                    );		
	}

	var chek=$(".map_status").attr('checked');
	  if(chek){
		  $(".disabled_map").hide();
	  }else{
		 $(".disabled_map").show();
	  }

        //  Custom Label With onChange function
        $(".map_status").change(function()
        {
            var chek = $(".map_status").attr('checked');
            
            if(chek) {
                $(".disabled_map").fadeOut();
            } else {
                $(".disabled_map").fadeIn();
            }
        });

 });

function initialize() {
	var map,GGM,geocoder; 
	GGM=new Object(google.maps); 
	geocoder = new GGM.Geocoder();  
	var my_Latlng  = new GGM.LatLng(<?php echo $contact->contact_lat_value ?: 0; ?>,<?php echo $contact->contact_lon_value ?: 0; ?>);
	var my_mapTypeId=GGM.MapTypeId.<?php echo $contact->contact_type_value ?: 'ROADMAP'; ?>;
	var my_DivObj=$("#map_canvas")[0]; 
	var myOptions = {
		zoom: <?php echo $contact->contact_zoom_value ?: 5; ?>,
		center: my_Latlng,
		mapTypeControl: true,
		mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
		mapTypeId:my_mapTypeId 
	};

	  map = new GGM.Map(my_DivObj,myOptions); 
	  var my_Marker = new GGM.Marker({ position: my_Latlng, map: map, draggable:true, title:"Drag Me!" });
	  GGM.event.addListener(my_Marker, 'dragend', function() {
		  var my_Point = my_Marker.getPosition();  
		  map.panTo(my_Point);  
		  geocoder.geocode({'latLng': my_Point}, function(results, status) {
				  if (status == GGM.GeocoderStatus.OK) {
						if (results[1]) { $("#address").val(results[1].formatted_address); }
				  } else {
					  alert("Unknown Place on Map");
				  }
		  });	
		  $("#lat_value").val(my_Point.lat()); 
		  $("#lon_value").val(my_Point.lng()); 
		  $("#zoom_value").val(map.getZoom()); 
	  });		
	GGM.event.addListener(map, 'zoom_changed', function() {
		$("#zoom_value").val(map.getZoom());
	});
	GGM.event.addListener(map, "maptypeid_changed", function() {
		  $("#type_value").val(map.getMapTypeId().toUpperCase());
	})
}

function loadScript() {
  var script = document.createElement("script");
  script.type = "text/javascript";
  script.src = "http://maps.google.com/maps/api/js?v=3&sensor=false&language=en&callback=initialize";
  document.body.appendChild(script);
}

$(document).ready(function() {
    loadScript();
});

</script>
