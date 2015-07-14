        <!-- Breadcrumbs -->
        <div class="fullblock blockgrey padding20">
        	
            <div class="container">	    
            
                <div class="row breadcrumbs">
                	<div class="span6">
                    	<h1>{{ $module->page->page_title }}</h1>
                    </div>
                    <div class="span6">
                        @include('site/snippets/breadcrumbs')
                    </div>
                    
                </div>
                
			</div>
        </div>
        <!-- /Breadcrumbs -->
        
        <!-- Contact -->
        <div class="fullblock blockwhite padding50">
            <div class="container">	    
            	<div class="row">
                	<div class="span12 m-bottom-25">
                        
                        @if($contact->contact_map_status)    
                            <div id="map"></div>
                        @endif

                        <p class="big-highlight">{{ $module->page->page_description }}</p>
                    </div>
                    
                    <div class="span9">
                    	<div class="contact-form">
                        	<h1 class="widget-title">Contact Form</h1>
                            <form action="" method="post" id="contactForm" name="contactForm">
                                <div class="input-holder">
                                    <label>Name*</label>
                                    <div class="input">
                                        <span class="name"></span>
                                        <input type="text" class="name"  name="yourname" id="yourname" />
                                    </div>
                                </div>
                                <div class="input-holder">
                                    <label>Email*</label>
                                    <div class="input">
                                        <span class="email"></span>
                                        <input type="text" class="name"  name="email" id="email" />
                                    </div>
                                </div>
                                <div class="input-holder">
                                    <label>Subject*</label>
                                    <div class="input">
                                        <span class="website"></span>
                                        <input type="text" class="name" name="tele" id="tele"/>
                                    </div>
                                </div>
                                <div class="input-holder">
                                    <label>Security code*</label>
                                    <div class="input">
                                        <span class="captcha"></span>
                                        <img src="captcha?cachekill=<?php echo time(); ?>" class="captcha">
                                        <input type="text" class="captcha input-small" name="captcha" id="captcha"/>
                                    </div>
                                </div>
                                <div class="input-holder">
                                    <label>Message*</label>
                                    <textarea name="message" rows="10" cols="20"></textarea>
                                </div>
                                <div class="form2">
                                    <!--<input type="submit" class="send-message" value="Send Message" />-->
                                    <a href="javascript:submitForm();" class="btn-theme btn-theme-large"><i class="icon-paper-plane left"></i> Send Message</a>
                                </div>
                                
                            </form>
                            
                            <div class="alertMessage"></div>
                            
                            <div id="contactFormThanks">Your enquiry has been sent.</div>
                        </div>
                    </div>
                    
                    <div class="span3">
                    	<div class="contact-widget">
                            <h1 class="widget-title">Contact Details</h1>

                            @if($contact->contact_address)
                                <i class="icon-home"></i>
                                <p>{{ $contact->contact_address }}</p>
                                <div class="clearfix"></div>
                            @endif
                            
                            @if($contact->contact_phone || $contact->contact_mobile || $contact->contact_fax)
                                <i class="icon-phone"></i>
                                <p>
                                    @if($contact->contact_phone)
                                        Phone: {{ $contact->contact_phone }}<br />
                                    @endif
                                    
                                    @if($contact->contact_mobile)
                                        Mobile: {{ $contact->contact_mobile }}<br />
                                    @endif
                                    
                                    @if($contact->contact_fax)
                                        Fax: {{ $contact->contact_fax }}
                                    @endif
                                </p>
                                <div class="clearfix"></div>
                            @endif
                            
                            <i class="icon-mail-3"></i>
                            <p>
                                @if($contact->contact_email_status && $contact->contact_email)
                                    Email: {{ \Models\Site\Util::safe_mailto($contact->contact_email, $contact->contact_email) }}<br />
                                @endif
                                Web: <a href="{{ \Request::root() }}">{{ \Request::getHttpHost() }}</a>
                            </p>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    
                </div>
			</div>
        </div>
        <!-- /Contact -->