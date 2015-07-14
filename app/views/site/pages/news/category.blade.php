<!-- Breadcrumbs -->
<div class="fullblock blockgrey padding20">

    <div class="container">	    

        <div class="row breadcrumbs">
                <div class="span6">
                <h1>{{ $module->module_name }} - {{ $category->category_title }}</h1>
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
                    @foreach ($articles as $article)
                        <li>
                            <div class="timeline-post clearfix">
                                <div class="timeline-dot"></div>
                                <div class="timeline-date"><span>{{ $article->getPublishDate('F d, Y') }}</span></div>
                                <div class="timeline-post-wrap">
                                    <div class="post-type">
                                        @if($article->news_image)
                                            <a href="{{ \URL::to($news_module_url.'/'.$article->news_url) }}"><img src="{{ \Config::get('synergy.uploads.images.url') }}resize/{{ $article->news_image }}" @if($article->news_image_alt) alt="{{ $article->news_image_alt }}" @endif></a>
                                        @endif
                                    </div>
                                    <h1 class="post-title"><a href="{{ \URL::to($news_module_url.'/'.$article->news_url) }}">{{ $article->news_title }}</a></h1>
                                    <p>{{ $article->news_introduction }} […]</p>
                                    <div class="timeline-meta">
                                        <a href="{{ \URL::to($news_module_url.'/'.$article->news_url) }}" class="more">Read More <i class="icon-right-open"></i></a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{ $articles->links() }}
        </div>


                </div>
</div>
<!-- /Timeline -->
