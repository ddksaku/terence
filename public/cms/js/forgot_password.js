$(document).ready(function () {
	onfocus();
	$('#password').keypress(function(e) {
		if(e.which == 13) {
			jQuery(this).blur();
			jQuery('#but_forgot').focus().click();
		}
	});
	// Hide All Alert Message Before
	$('.alertMessage').live('click',function(){
		alertHide();
	});
	// Check box iphone Style
	$(".on_off_checkbox").iphoneStyle();
	// Tooltip
	$('.tip a ').tipsy({gravity: 'sw'});
	// Loading Login Animation
	$('#login').show().animate({   opacity: 1 }, 2000);
	$('.logo').show().animate({   opacity: 1,top: '32%'}, 800,function(){			
		 $('.logo').show().delay(1200).animate({   opacity: 1,top: '1%' }, 300,function(){
				  	$('.formLogin').animate({   opacity: 1,left: '0' }, 300);
				  	$('.userbox').animate({ opacity: 0 }, 200).hide();
		  });		
	});
	// Login Click
	$('#but_forgot').click(function(e){				
		  if(document.formLogin.user_email.value == ""){
			  alertMessage("error","Please Input Username / Email");
			  $('.inner').jrumble({ x: 4,y: 0,rotation: 0 });	
			  $('.inner').trigger('startRumble');
			  setTimeout('$(".inner").trigger("stopRumble")',500);
			  return false;
		  }		
		 alertHide();
		$('form:eq(0)').submit();
		e.preventDefault();
	});	
	$('form:eq(0)').submit(function(e){	
		loading('Checking ',1);

                ajaxCallAPI(
                    'login',
                    'post',
                    'forgot_password',
                    null,
                    $('form:eq(0)').serialize(),
                    function(data)
                    {
			unloading();

			if(data.success != 1)
			{
                            alertMessage('error',"Wrong Username / Email! ");
                            alertHide();
			} else {
                            alertHide();
                            Login();
                        }
                    },
                    false
                );

		e.preventDefault();
	});
});			
	  //Login function
	  function Login(){
		alertMessage("success","Password reset instructions sent to your email");
		  $("#login").animate({   opacity: 1,top: '49%' }, 200,function(){
			   $('.userbox').show().animate({ opacity: 1 }, 500);
				$("#login").animate({   opacity: 0,top: '60%' }, 500,function(){
						$(this).fadeOut(200,function(){
								$(".text_success").slideDown();
								$("#successLogin").animate({opacity: 1,height: "200px"},500);   			     
						});							  
				 })	
		   })	
		  setTimeout( "window.location.href='login'", 4000 );
	  }
	 //Hidden All Alert Message Before
	  function alertHide(){
				 $('#alertMessage').each(function(index) {	 
						$(this).attr("id","alertMessage"+index).animate({ opacity: 0,right: '30'}, 500,function(){ $(this).remove(); });	
				});	
	  }
	  // Create Alert Message Box
	  function alertMessage(type,str){
				//Hidden All  Alert Message Before
				alertHide();
				// type is a success ,info, warning ,error
				$('body').append('<div id="alertMessage" class="alertMessage '+type+'">');
				$.alertbox=$('#alertMessage').html(str);
				$.alertbox.show().animate({ opacity: 1,right: '10' },500);
	  }	  
	  function onfocus(){
			if($(window).width()>480) {					  
					$('.tip input').tipsy({ trigger: 'focus', gravity: 'w' ,live: true});
			}else{
				  $('.tip input').tipsy("hide");
			}
	  }
	  // Loading 
	  function loading(name,overlay) { 
			$('body').append('<div id="overlay"></div><div id="preloader">'+name+'..</div>');
					if(overlay==1){
					  		$('#overlay').css('opacity',0.4).fadeIn(400,function(){  $('#preloader').fadeIn(400);	});
					  return  false;
			  		 }
			$('#preloader').fadeIn();	  
	   }
           
	    // Unloading 
            if (typeof unloading != 'function') {            
                function unloading()
                {
                    $('#preloader').fadeOut(400, function()
                    {
                        $('#overlay').fadeOut();

                        if ($.fancybox) {
                            $.fancybox.close();
                        }
                    }).remove();
                }
            }