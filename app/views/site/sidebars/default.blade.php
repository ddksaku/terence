<div class="span3">
    <div class="sidebar">

        @if(isset($sidebar_services_categories) && count($sidebar_services_categories) > 0)
            <div class="widget-block">
                <h1 class="widget-title">{{ $module->module_name }} categories</h1>
                <ul>
                    <li class="category-list">
                        <a href="{{ \URL::to($services_module_url) }}">All @if(($serviceCount = $data->getServicesCount())) ({{ $serviceCount }}) @endif</a>
                    </li>
                    @foreach($sidebar_services_categories as $category)
                        @if(($serviceCount = count($category->activeServices)))
                            <li class="category-list"><a href="{{ \URL::to($services_module_url.'/category/'.$category->category_url) }}">{{ $category->category_title }} ({{ $serviceCount }})</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if(isset($sidebar_news_categories) && count($sidebar_news_categories) > 0)
            <div class="widget-block">
                <h1 class="widget-title">{{ $module->module_name }} categories</h1>
                <ul>
                    <li class="category-list">
                        <a href="{{ \URL::to($news_module_url) }}">All @if(($newsCount = $data->getNewsCount())) ({{ $newsCount }}) @endif</a>
                    </li>
                    @foreach($sidebar_news_categories as $category)
                        @if(($newsCount = count($category->activeNews)))
                            <li class="category-list"><a href="{{ \URL::to($news_module_url.'/category/'.$category->category_url) }}">{{ $category->category_title }} ({{ $newsCount }})</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if(isset($sidebar_portfolio_categories) && count($sidebar_portfolio_categories) > 0)
            <div class="widget-block">
                <h1 class="widget-title">{{ $module->module_name }} categories</h1>
                <ul>
                    <li class="category-list">
                        <a href="{{ \URL::to($portfolio_module_url) }}">All @if(($portfolioCount = $data->getPortfolioCount())) ({{ $portfolioCount }}) @endif</a>
                    </li>
                    @foreach($sidebar_portfolio_categories as $category)
                        @if(($portfolioCount = count($category->activePortfolioItems)))
                            <li class="category-list"><a href="{{ \URL::to($portfolio_module_url.'/category/'.$category->category_url) }}">{{ $category->category_title }} ({{ $portfolioCount }})</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif

        @if(isset($sidebar_related_news) && count($sidebar_related_news) > 0)
            <div class="widget-block">
                <h1 class="widget-title">Similar {{ $module->module_name }}</h1>
                <ul class="widget-recent-work">
                    @foreach($sidebar_related_news as $article)
                        <li>
                            @if($article->news_image)
                                <a href="{{ \URL::to($news_module_url.'/'.$article->news_url) }}">
                                    <img src="{{ \Config::get('synergy.uploads.images.url') }}resize/{{ $article->news_image }}" @if($article->news_image_alt) alt="{{ $article->news_image_alt }}" @endif>
                                </a>
                            @endif
                            <h1 class="small-title">
                                <a href="{{ \URL::to($news_module_url.'/'.$article->news_url) }}">
                                    {{ $article->news_title }}
                                </a>
                            </h1>
                            <div class="small-meta">
                                    <span class="small-tags">
                                    in 
                                    <?php
                                    $total = count($article->categories);
                                    $shown = 0;
                                    ?>
                                    @foreach($article->categories as $category)
                                        <a href="{{ \URL::to($news_module_url.'/category/'.$category->category_url) }}">{{ $category->category_title }}</a>@if(++$shown < $total) | @endif
                                    @endforeach
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
       @endif
       
       @if(isset($sidebar_related_services) && count($sidebar_related_services) > 0)
            <div class="widget-block">
                <h1 class="widget-title">Similar {{ $module->module_name }}</h1>
                <ul class="widget-recent-work">
                    @foreach($sidebar_related_services as $service)
                        <li>
                            @if($service->service_image)
                                <a href="{{ \URL::to($services_module_url.'/'.$service->service_url) }}">
                                    <img src="{{ \Config::get('synergy.uploads.images.url') }}thumb/{{ $service->service_image }}" @if($service->service_image_alt) alt="{{ $service->service_image_alt }}" @endif>
                                </a>
                            @endif
                            <h1 class="small-title">
                                <a href="{{ \URL::to($services_module_url.'/'.$service->service_url) }}">
                                    {{ $service->service_title }}
                                </a>
                            </h1>
                            <div class="small-meta">
                                    <span class="small-tags">
                                    in 
                                    <?php
                                    $total = count($service->categories);
                                    $shown = 0;
                                    ?>
                                    @foreach($service->categories as $category)
                                        <a href="{{ \URL::to($services_module_url.'/category/'.$category->category_url) }}">{{ $category->category_title }}</a>@if(++$shown < $total) | @endif
                                    @endforeach
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
       @endif
       
       @if(isset($sidebar_related_portfolio) && count($sidebar_related_portfolio) > 0)
            <div class="widget-block">
                <h1 class="widget-title">Similar {{ $module->module_name }}</h1>
                <ul class="widget-recent-work">
                    @foreach($sidebar_related_portfolio as $portfolioItem)
                        <li>
                            @if($portfolioItem->portfolio_image)
                                <a href="{{ \URL::to($portfolio_module_url.'/'.$portfolioItem->portfolio_url) }}">
                                    <img src="{{ \Config::get('synergy.uploads.images.url') }}resize/{{ $portfolioItem->portfolio_image }}" @if($portfolioItem->portfolio_image_alt) alt="{{ $portfolioItem->portfolio_image_alt }}" @endif>
                                </a>
                            @endif
                            <h1 class="small-title">
                                <a href="{{ \URL::to($portfolio_module_url.'/'.$portfolioItem->portfolio_url) }}">
                                    {{ $portfolioItem->portfolio_title }}
                                </a>
                            </h1>
                            <div class="small-meta">
                                    <span class="small-tags">
                                    in 
                                    <?php
                                    $total = count($portfolioItem->categories);
                                    $shown = 0;
                                    ?>
                                    @foreach($portfolioItem->categories as $category)
                                        <a href="{{ \URL::to($portfolio_module_url.'/category/'.$category->category_url) }}">{{ $category->category_title }}</a>@if(++$shown < $total) | @endif
                                    @endforeach
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
       @endif
       
       
        @if(isset($sidebar_tags) && count($sidebar_tags) > 0)
            <div class="widget-block">
                <h1 class="widget-title">Popular Tags</h1>
                <div class="tag-list">
                    <ul class="widget-tags">
                        @foreach($sidebar_tags as $tag)
                            <li>
                                <a href="search/tagged/{{ $tag->tag_url }}">{{ $tag->tag_name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        
        <div class="widget-block">
            <h1 class="widget-title">From Twitter</h1>
            <div class="twitterfeed"></div>
        </div>

    </div>
</div>