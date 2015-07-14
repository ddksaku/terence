@extends('site.layouts.default')

@section('header')
    @parent

            <!-- Header Slider -->
            <div class="head-slider">
            	<div class="fullwidthbanner-container">
					<div class="fullwidthbanner">
						<ul>
							<!-- THE FIRST SLIDE -->  
							<li data-transition="3dcurtain-vertical" data-slotamount="10" data-masterspeed="300">
										<img src="images/slides/bg2.jpg" />
										
                                        <div class="caption lfb ltb"
											 data-x="20"
											 data-y="10"
											 data-speed="600"
											 data-start="800"
											 data-easing="easeOutExpo" data-endspeed="600" data-endeasing="easeInSine"><img src="images/slides/slide_1_c.png" alt="Synergy"></div>                          
                                             
                                        <div class="caption mypassion-title lft ltt"
											 data-x="440"
											 data-y="100"
											 data-speed="600"
											 data-start="900"
											 data-easing="easeOutExpo" data-endspeed="600" data-endeasing="easeInSine"><strong>SYNERGY</strong>, <span style="color:#c2cd23;">web</span> design <span style="color:#c2cd23;">print</span> branding</div>

                                        <div class="caption mypassion-text lfr ltr"
											 data-x="440"
											 data-y="175"
											 data-speed="600"
											 data-start="1000"
											 data-easing="easeOutExpo" data-endspeed="600" data-endeasing="easeInSine"><p><strong>Start-up business? Small business? Need to get online?</strong></p>
                                             <p>If you’re new to web development, or simply need to<br />
                                             improve your existing online presence, why not give<br />
                                             us a shout and see how <span style="color:#c2cd23;"><strong>Synergy</strong></span> can help you.</p>
                                        </div> 
                                        
                                        <div class="caption lfb ltb"
											 data-x="440"
											 data-y="320"
											 data-speed="600"
											 data-start="1100"
											 data-easing="easeOutExpo" data-endspeed="600" data-endeasing="easeInSine"><a href="{{ \Request::root() }}/contact" class="btn-theme btn-theme-large">Get in touch</a></div>
                            </li>
                                     

							<!-- THE SECOND SLIDE -->
							<li data-transition="papercut" data-slotamount="15" data-masterspeed="300" data-delay="9400">
										<img src="images/slides/bg1.jpg" >

										<div class="caption randomrotate"
											 data-x="0"
											 data-y="30"
											 data-speed="600"
											 data-start="800"
											 data-easing="easeOutExpo" data-endspeed="600" data-endeasing="easeInSine"><img src="images/slides/responsive2.png" alt="Synergy"></div>                          
                                             
                                        <div class="caption mypassion-themecolor-fixedwidth lft ltt"
											 data-x="620"
											 data-y="100"
											 data-speed="600"
											 data-start="900"
											 data-easing="easeOutExpo" data-endspeed="600" data-endeasing="easeInSine">SYNERGY</div>
                                             
                                        <div class="caption mypassion-themecolor-fixedwidth lft ltt"
											 data-x="620"
											 data-y="143"
											 data-speed="600"
											 data-start="1100"
											 data-easing="easeOutExpo" data-endspeed="600" data-endeasing="easeInSine">Digital Services...</div>
                                             
                                        <div class="caption mypassion-greycolor-fixedwidth lfb ltt"
											 data-x="620"
											 data-y="200"
											 data-speed="600"
											 data-start="1400"
											 data-easing="easeOutExpo" data-endspeed="600" data-endeasing="easeInSine"><i class="icon-right-open"></i> Web Design &amp; Development</div>
                                        
                                        <div class="caption mypassion-greycolor-fixedwidth lfb ltt"
											 data-x="620"
											 data-y="245"
											 data-speed="600"
											 data-start="1600"
											 data-easing="easeOutExpo" data-endspeed="600" data-endeasing="easeInSine"><i class="icon-right-open"></i> Ecommerce</div>
                                        
                                        <div class="caption mypassion-greycolor-fixedwidth lfb ltt"
											 data-x="620"
											 data-y="290"
											 data-speed="600"
											 data-start="1800"
											 data-easing="easeOutExpo" data-endspeed="600" data-endeasing="easeInSine"><i class="icon-right-open"></i> Search Engine Optimisation (SEO)</div>
                                        
                                        <div class="caption mypassion-greycolor-fixedwidth lfb ltt"
											 data-x="620"
											 data-y="335"
											 data-speed="600"
											 data-start="2000"
											 data-easing="easeOutExpo" data-endspeed="600" data-endeasing="easeInSine"><i class="icon-right-open"></i>Email Marketing</div>
                                        
                                        <div class="caption mypassion-greycolor-fixedwidth lfb ltt"
											 data-x="620"
											 data-y="380"
											 data-speed="600"
											 data-start="2200"
											 data-easing="easeOutExpo" data-endspeed="600" data-endeasing="easeInSine"><i class="icon-right-open"></i>Social Media</div>
							</li>
                            
						</ul>
						<div class="tp-bannertimer tp-bottom"></div>
					</div>
				</div>
            </div>
            <!-- /Header Slider -->

@stop