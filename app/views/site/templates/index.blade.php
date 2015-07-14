
        <!-- Stunning Text -->
        <div class="fullblock blockwhite padding50">
            <div class="container">	    
            
                <div class="stunning">
                    <div class="stunning-text">
                    	<div class="button-wrap">
                            <a href="{{ \Request::root() }}/about-us" class="btn-theme btn-theme-large">Read more...</a>
                        </div>
                        {{ $page->page_description }}
                    </div>
                    <div class="stunning-bottom">
                    	<img src="img/shadow.png" alt="Synergy" />
                    </div>
                </div>
                
            </div>
        </div>
        <!-- /Stunning Text -->
    	
        <!-- Showcase -->
    	<div class="fullblock blockgrey padding50">

        	<div class="container"> 
            	<div class="row showcase">
                	<div class="span6 add-animation">
                    	<img src="img/trash/project.png" alt="Responsive Web Design" />
                    </div>
                    <div class="span6">
                    	<h2>Responsive web design; for the future</h2>
                        <p><strong>We design and develop high-performance, stunning websites that deliver a rich and engaging experience across multiple platforms and devices.</strong></p>
                        <p>Browsing the web on mobile devices is growing at an explosive rate, placing a greater than ever importance on ensuring that your website meets increasing customer demands. There's never been a more important time to future-proof your website design.</p>
                        <p>All of our websites include free SEO and social media tools, helping potential customers to more easily find you online! Interested? Then why not...</p>
                        <p><a href="{{ \Request::root() }}/contact" class="btn-theme btn-theme-large">Get in touch</a></p>
                        <ul class="sharebox">
                    	<li>@include('site.snippets.gplus')</li>
                        <li>@include('site.snippets.tweet')</li>
                        <li>@include('site.snippets.fblike')</li>
                    </ul>
                    </div>
                    
                </div>
            </div>
        </div>
        <!-- /Showcase -->

        <!-- Blog -->
        <div class="fullblock blockgrey padding50">
        	
            <div class="container">	 
            	<div class="blog_block_1">
                	<div class="entrytitle">
                    	<h4>Latest News</h4>
                    </div>
                </div>
            </div>
            
            <div class="blog_blocks_wrap" id="carousel-2">

                @foreach($data->getNews(8) as $article)
                    <div class="item">
                            <div class="image-wrap">
                            <div class="maskImage">
                                <a href="{{ \URL::to($news_module_url.'/'.$article->news_url) }}"><span><i class="icon-link-1"></i></span></a>
                            </div>
                            @if($article->news_image)
                                <a href="{{ \URL::to($news_module_url.'/'.$article->news_url) }}">
                                    <img src="{{ \Config::get('synergy.uploads.images.url') }}resize/{{ $article->news_image }}" @if($article->news_image_alt) alt="{{ $article->news_image_alt }}" @endif />
                                </a>
                            @endif
                        </div>
                        <div class="desc">
                            <h5><a href="{{ \URL::to($news_module_url.'/'.$article->news_url) }}">{{ $article->news_title }}</a></h5>
                            <p>{{ $article->news_introduction }}</p>
                        </div>
                        <div class="title_holder_2">
                            <span class="post-date"><a href="{{ \URL::to($news_module_url.'/'.$article->news_url) }}"><i class="icon-calendar"></i>{{ $article->getPublishDate('F d, Y') }}</a></span>
                            <span class="like-2"><div class="fb-like" data-href="{{ \URL::to($news_module_url.'/'.$article->news_url) }}" data-colorscheme="light" data-layout="button_count" data-action="like" data-show-faces="false" data-send="false"></div></span>
                        </div>
                    </div>
                @endforeach
                
            </div>
            
            <div class="carousel_navigations">
            	<ul>
                	<li><a id="car_prev_2" href="#"></a></li>
                    <li><a id="car_next_2" href="#"></a></li>
                </ul>            	
            </div>
            <div class="clearfix"></div>
            
        </div>
        <!-- /Blog -->
        
        <!-- Associations -->
        <div class="fullblock padding50">
        	<div class="container">
            	
                <div class="entrytitle">
                    <h4>Associations</h4>
                    <p>Just some of the great brands we work with</p>
                </div>
                
            	<ul class="associations">
                	<li rel="tooltip" title="Google"><a href="http://www.google.co.uk"><img src="img/associations/google.png" alt="Google" /></a></li>
                    <li rel="tooltip" title="Apple"><a href="http://www.apple.com"><img src="img/associations/apple.png" alt="Apple" /></a></li>
                    <li rel="tooltip" title="Facebook"><a href="http://www.facebook.com"><img src="img/associations/facebook.png" alt="Facebook" /></a></li>
                    <li rel="tooltip" title="Twitter"><a href="http://www.twitter.com"><img src="img/associations/twitter.png" alt="Twitter" /></a></li>
                    <li rel="tooltip" title="Wordpress"><a href="http://www.wordpress.org"><img src="img/associations/wordpress.png" alt="Wordpress" /></a></li>
                </ul>
            </div>
        </div>
        <!-- /Associations -->