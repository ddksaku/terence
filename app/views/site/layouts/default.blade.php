<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->

<head>
    
    <base href="{{ \Request::root(); }}">
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

@if ($meta_description)
    <meta name="description" content="{{ $meta_description }}">
@endif
    <meta name="author" content="<?php echo $settings->setting_name; ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="{{ $settings->setting_name }}" />
    <meta property="og:title" content="{{ $title }}" />

@if ($meta_description)
    <meta property="og:description" content="{{ $meta_description }}" />
@endif

@if (isset($og_image))
    <meta property="og:image" content="{{ $og_image }}" />
@endif

    <meta property="og:url" content="{{ \Request::url() }}" />

@foreach ($fb_admins as $admin)
    <meta property="fb:admins" content="{{ $admin->user_facebook }}" />
@endforeach

<title>{{ $title }}</title>

<link rel="shortcut icon" href="favicon.ico" />

<!-- STYLES -->
<link rel="stylesheet" type="text/css" href="css/fancybox.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/fontello/fontello.css" />
<link rel="stylesheet" type="text/css" href="css/flexslider.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/superfish.css" />
<link rel="stylesheet" type="text/css" href="css/font/ptsans.css" />

<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css" />
     
<!-- REVOLUTION BANNER CSS SETTINGS -->
<link rel="stylesheet" type="text/css" href="rs-plugin/css/settings.css" media="screen" />

<link rel="stylesheet" type="text/css" href="css/base.css" />
<link rel="stylesheet" type="text/css" href="css/animation.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />

<!--[if lt IE 9]> <script type="text/javascript" src="js/customM.js"></script> <![endif]-->

@if($settings->setting_google_analytics)
@include('site.snippets.analytics')
@endif

</head>

<body>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=207097012794861";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!-- Body Wrapper -->
<div class="body-wrapper">
    
    <!-- Header -->
    <header id="header">
    	<div class="fullblock">
            
             @section('header')
            
        	<div class="topbar">
            	<div class="container">
            	
                    <!-- Logo -->
                    <div class="logo">
                        @if($settings->setting_image)
                        <a href="/">
                            <img
                                src="{{ \Config::get('synergy.uploads.logos.url') }}resize/{{ $settings->setting_image }}"
                                @if($settings->setting_image_name)
                                    alt="{{ $settings->setting_image_name }}"
                                @endif
                                >
                        </a>
                        @endif
                    </div>
                    <!-- /Logo -->
                    
                    <!-- Nav -->
                    <nav id="nav">
                    	
                        <!-- Search -->
                        <div class="search-include">
                            <div class="search-button">
                                <a href="#"><i class="icon-search"></i></a>
                            </div>

                            <div class="popup-search">
                                <form action="search" method="post">
                                    <input name="query" type="text" placeholder="Search" class="ft"/>
                                    <input type="submit" value="" class="fs">
                                </form>
                            </div>
                        </div>
                        <!-- /Search -->
                    
                        <!-- Main Menu -->
                        <ul class="sf-menu">
                            <?php

                            $currentPath = \Request::segment(1);
                            
                            /* A recursive lambda for outputting submenus */
                            
                            $menuIterator = function($nodes, $mode = 'nav') use (&$menuIterator) {
								
								$currentPath = \Request::segment(1);
								
                                ?>
                                    <ul>
                                        <?php

                                        foreach ($nodes as $node) {
                                            ?>
                                        <li 
                                        	@if($node->page_url == $currentPath || (!empty($children) && isset($children[$currentPath])))
                                    		class="current"
                                    		@endif
                                    		>
                                            <a href="@if($node->page_homepage)/@else{{ $node->page_url }}@endif">{{ $node->page_title }}</a>
                                            <?php

                                            if ($mode == 'nav') {
                                                if (count($node->navChildren) > 0) {
                                                    $menuIterator($node->navChildren, $mode);
                                                }
                                            } elseif ($mode == 'footer') {
                                                if (count($node->footerChildren) > 0) {
                                                    $menuIterator($node->footerChildren, $mode);
                                                }
                                            }

                                            ?>
                                        </li>
                                            <?php
                                        }

                                        ?>
                                    </ul>
                                <?php
                            };

                            ?>

                            @foreach($nav_links as $page)
                                <li
                                    @if($page->page_url == $currentPath || (!empty($children) && isset($children[$currentPath])))
                                    class="current"
                                    @endif
                                    >
                                    <a href="@if($page->page_homepage)/@else{{ $page->page_url }}@endif">{{ $page->page_title }}</a>
                                    <?php
                                    
                                    if (count($page->navChildren) > 0) {
                                        $menuIterator($page->navChildren);
                                    }
                                    
                                    ?>
                                </li>
                            @endforeach
                        </ul>
                        <!-- /Main Menu -->
                        
                        <!-- Mobile Nav Toggler -->
                        <div class="nav-toggle">
                        	<a href="#"></a>
                        </div>

                    </nav>
                    <!-- /Nav -->
                    
                    
                    
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div class="mobile-menu">
                <div class="mobile-menu-inner">
                    <ul>
                            @foreach($nav_links as $page)
                                <li
                                    @if($page->page_url == $currentPath || (!empty($children) && isset($children[$currentPath])))
                                    class="current"
                                    @endif
                                    >
                                    <a href="@if($page->page_homepage)/@else{{ $page->page_url }}@endif">{{ $page->page_title }}</a>
                                    <?php
                                    
                                    if (count($page->navChildren) > 0) {
                                        $menuIterator($page->navChildren);
                                    }
                                    
                                    ?>
                                </li>
                            @endforeach
                    </ul>
                </div>
            </div>
            <!-- /Mobile Menu -->
            
            @show
            
        </div>
    </header>
    <!-- /Header -->
    
    <!-- Content -->
    <section id="content">
        
        {{ $body }}
    
    </section>
    <!-- / Content -->
    
    <!-- Footer -->
    <footer id="footer">
		<div class="fullblock noborder">
        	<div class="footer padding50">
            	<div class="container">
                	<div class="row">
                    	<div class="span3">
                        	<h1 class="widget-title">Social Media</h1>
                        	<p>Socialise with us...</p>
                            <ul class="social-list">
                            	@if($settings->setting_facebook)
                                    <li>@include('site.snippets.fbfblike')</li>
                                @endif
                            	@if($settings->setting_twitter)
                                    <li>@include('site.snippets.twitter')</li>
                                @endif
                                <li>@include('site.snippets.gplus')</li>
                            </ul>
                        </div>
                        <div class="span3">
                        	<h1 class="widget-title">Latest News</h1>
                                
                                @foreach($data->getNews(3) as $article)
                                    <div class="from-blog">
                                        @if($article->news_image)
                                            <a href="{{ \URL::to($news_module_url.'/'.$article->news_url) }}">
                                                <img src="{{ \Config::get('synergy.uploads.images.url') }}thumb/{{ $article->news_image }}" @if($article->news_image_alt) alt="{{ $article->news_image_alt }}" @endif>
                                            </a>
                                        @endif
                                        <span>{{ $article->getPublishDate('F d, Y') }}</span>
                                        <p><a href="{{ \URL::to($news_module_url.'/'.$article->news_url) }}">{{ $article->news_title }}</a></p>
                                    </div>
                                @endforeach
                        </div>
                        <div class="span3">
                            <h1 class="widget-title">More Information</h1>
                            <ul>
                                @foreach($footer_links as $page)
                                    <li
                                        @if($page->page_url == $currentPath || (!empty($children) && isset($children[$currentPath])))
                                        class="current"
                                        @endif
                                        >
                                        <a href="@if($page->page_homepage)/@else{{ $page->page_url }}@endif">{{ $page->page_title }}</a>
                                        <?php

                                        if (count($page->footerChildren) > 0) {
                                            $menuIterator($page->footerChildren, 'footer');
                                        }

                                        ?>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="span3">
                            <h1 class="widget-title">Contact Us</h1>

                            <div class="contact-widget">
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
            <div class="copyright">
            	<div class="container">
                	<p>Copyright <?php echo date("Y"); ?> <a href="{{ \Request::root() }}">{{ $settings->setting_name }}</a>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- / Footer -->
    
</div>
<!-- / Body Wrapper -->


@section('bottom_scripts')

<!-- SCRIPTS -->
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/1.8.2.min.js"></script>
<script type="text/javascript" src="js/easing.min.js"></script>
<script type="text/javascript" src="js/ui.js"></script>
<script type="text/javascript" src="js/waypoints.js"></script>
<script type="text/javascript" src="js/modernizr.custom.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/nicescroll.min.js"></script>
<script type="text/javascript" src="js/sticky.js"></script>
<script type="text/javascript" src="js/superfish.js"></script>
<script type="text/javascript" src="js/carouFredSel.js"></script>
<script type="text/javascript" src="js/jflickrfeed.min.js"></script>
<script type="text/javascript" src="js/totop.js"></script>
<script type="text/javascript" src="js/grid.js"></script>
<script type="text/javascript" src="js/excanvas.js"></script>
<script type="text/javascript" src="js/easy-pie-chart.js"></script>
<script type="text/javascript" src="js/twitter/jquery.tweet.js"></script>
<script type="text/javascript" src="js/flexslider-min.js"></script>
<script type="text/javascript" src="js/isotope.min.js"></script>

<!-- jQuery REVOLUTION Slider -->
<script type="text/javascript" src="rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
<script type="text/javascript" src="rs-plugin/js/jquery.themepunch.plugins.min.js"></script>



<!--[if lt IE 9]> <script type="text/javascript" src="js/html5.js"></script> <![endif]-->
<script type="text/javascript" src="js/mypassion.js"></script>

<script type="text/javascript">
window.___gcfg = {
  lang: 'en-GB',
  parsetags: 'onload'
};
(function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

@show

</body>
</html>