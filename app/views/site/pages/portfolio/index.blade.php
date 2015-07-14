
    	
        <!-- Breadcrumbs -->
        <div class="fullblock blockgrey padding20">
        	
            <div class="container">	    
            
                <div class="row breadcrumbs">
                	<div class="span6">
                    	<h1>{{ $module->module_name }}</h1>
                    </div>
                    <div class="span6">
                        @include('site/snippets/breadcrumbs')
                    </div>
                    
                </div>
                
			</div>
        </div>
        <!-- /Breadcrumbs -->
        
        <!-- Timeline -->
        <div class="fullblock blockwhite padding50">
        	
            <div class="container">	    
            	<div class="timeline-with">
                	<div class="timeline-head"><i class="icon-chat-1"></i></div>
                	<ul class="timeline">
                            @foreach ($portfolio as $portfolioItem)
                                <li>
                                    <div class="timeline-post clearfix">
                                        <div class="timeline-dot"></div>
                                        <div class="timeline-date"><span>{{ $portfolioItem->portfolio_created->format('F d, Y') }}</span></div>
                                        <div class="timeline-post-wrap">
                                            <div class="post-type">
                                                @if($portfolioItem->portfolio_image)
                                                    <a href="{{ \URL::to($portfolio_module_url.'/'.$portfolioItem->portfolio_url) }}"><img src="{{ \Config::get('synergy.uploads.images.url') }}resize/{{ $portfolioItem->portfolio_image }}" @if($portfolioItem->portfolio_image_alt) alt="{{ $portfolioItem->portfolio_image_alt }}" @endif></a>
                                                @endif
                                            </div>
                                            <h1 class="post-title"><a href="{{ \URL::to($portfolio_module_url.'/'.$portfolioItem->portfolio_url) }}">{{ $portfolioItem->portfolio_title }}</a></h1>
                                            <p>{{ $portfolioItem->portfolio_introduction }} […]</p>
                                            <div class="timeline-meta">
                                                <a href="{{ \URL::to($portfolio_module_url.'/'.$portfolioItem->portfolio_url) }}" class="more">Read More <i class="icon-right-open"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        
                        {{ $portfolio->links() }}
                </div>
                
                
			</div>
        </div>
        <!-- /Timeline -->