{{ '<?' }}xml version="1.0" encoding="UTF-8" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($pages as $page)
        <url>
            <loc>@if($page->page_homepage){{ \URL::to('/') }}@else{{ \URL::to($page->page_url) }}@endif</loc>
            <lastmod>{{ $page->page_updated->format('c') }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
    
    @if(!empty($news_categories))
        @foreach($news_categories as $category)
            <url>
                <loc>{{ \URL::to($news_module_url.'/category/'.$category->category_url) }}</loc>
                <lastmod>{{ $category->category_updated->format('c') }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.8</priority>
            </url>
        @endforeach
    @endif

    @if(!empty($articles))
        @foreach($articles as $article)
            <url>
                <loc>{{ \URL::to($news_module_url.'/'.$article->news_url) }}</loc>
                <lastmod>{{ $article->news_updated->format('c') }}</lastmod>
                <changefreq>yearly</changefreq>
                <priority>0.8</priority>
            </url>
        @endforeach
    @endif

    @if(!empty($services_categories))
        @foreach($services_categories as $category)
            <url>
                <loc>{{ \URL::to($services_module_url.'/category/'.$category->category_url) }}</loc>
                <lastmod>{{ $category->category_updated->format('c') }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.8</priority>
            </url>
        @endforeach
    @endif

    @if(!empty($services))
        @foreach($services as $service)
            <url>
                <loc>{{ \URL::to($services_module_url.'/'.$service->service_url) }}</loc>
                <lastmod>{{ $service->service_updated->format('c') }}</lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.8</priority>
            </url>
        @endforeach
    @endif

    @if(!empty($albums))
        @foreach($albums as $album)
            @if(count($album->pictures) > 0)
                <url>
                    <loc>{{ \URL::to($gallery_module_url.'/'.$album->album_url) }}</loc>
                    <lastmod>{{ $album->album_updated->format('c') }}</lastmod>
                    <changefreq>weekly</changefreq>
                    <priority>0.8</priority>
                </url>
            @endif
        @endforeach
    @endif
    
    @if(!empty($portfolio_categories))
        @foreach($portfolio_categories as $category)
            <url>
                <loc>{{ \URL::to($portfolio_module_url.'/category/'.$category->category_url) }}</loc>
                <lastmod>{{ $category->category_updated->format('c') }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.8</priority>
            </url>
        @endforeach
    @endif

    @if(!empty($portfolio))
        @foreach($portfolio as $portfolioItem)
            <url>
                <loc>{{ \URL::to($portfolio_module_url.'/'.$portfolioItem->portfolio_url) }}</loc>
                <lastmod>{{ $portfolioItem->portfolio_updated->format('c') }}</lastmod>
                <changefreq>yearly</changefreq>
                <priority>0.8</priority>
            </url>
        @endforeach
    @endif
</urlset>