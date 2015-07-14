<!-- Breadcrumbs -->
<div class="fullblock blockgrey padding20">
    <div class="container">	    

        <div class="row breadcrumbs">
                <div class="span6">
                <h1>
                    Search results
                    @if($tagged)
                        tagged "{{ $tagged->tag_name }}"
                    @elseif($searchQuery)
                        "{{ $searchQuery }}"
                    @endif
                </h1>
            </div>
            <div class="span6">
                @include('site/snippets/breadcrumbs')
            </div>

        </div>

    </div>
</div>
<!-- /Breadcrumbs -->

<!-- Search Masonry Posts -->
<div class="fullblock blockwhite padding50">
    <div class="container">
        
        @if($search->tagLookupFailed())
            Tag doesn't exist.
        @else
            @if(!empty($filters))
                <div class="tag-list">
                    <ul>
                        <li>
                            <a
                                href="{{ \Request::path() }}"
                                @if(!$currentFilter)
                                    class="current"
                                @endif
                                >
                                All
                            </a>
                        </li>
                        @foreach($filters as $filterUrl => $filter)
                            <li>
                                <a
                                    href="{{ \Request::path() }}?filter={{ $filterUrl }}"
                                    @if($filterUrl == $currentFilter)
                                        class="current"
                                    @endif
                                    >
                                    {{ $filter }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!empty($results))
            
                <div class="row">
                    <div class="masonry-wrap-4columns">

                        @foreach($results as $result)
                            <?php

                            if ($result instanceof \Synergy\Modules\Pages\Models\Page) {
                                $resultImage = $result->page_image
                                                ? \Config::get('synergy.uploads.images.url').'resize/'.$result->page_image
                                                : null;
                                $resultImageAlt = $result->page_image_alt;
                                $resultTitle = $result->page_title;
                                $resultDate = $result->page_created->format('F j, Y');
                                $resultAuthor = null;
                                $resultIntroduction = $result->page_introduction;
                                if ($result->page_homepage) {
                                    $resultLink = \URL::to('/');
                                } else {
                                    $resultLink = \URL::to($result->page_url);
                                }
                            } elseif ($result instanceof \Synergy\Modules\Services\Models\Service) {
                                $resultImage = $result->service_image
                                                ? \Config::get('synergy.uploads.images.url').'resize/'.$result->service_image
                                                : null;
                                $resultImageAlt = $result->service_image_alt;
                                $resultTitle = $result->service_title;
                                $resultDate = $result->service_created->format('F j, Y');
                                $resultAuthor = null;
                                $resultIntroduction = $result->service_introduction;
                                $resultLink = \URL::to($services_module_url.'/'.$result->service_url);
                            } elseif ($result instanceof \Synergy\Modules\News\Models\NewsItem) {
                                $resultImage = $result->news_image
                                                ? \Config::get('synergy.uploads.images.url').'resize/'.$result->news_image
                                                : null;
                                $resultImageAlt = $result->news_image_alt;
                                $resultTitle = $result->news_title;
                                $resultDate = date('F j, Y', $result->news_publish_date);
                                $resultAuthor = ($result->author)
                                                    ? $result->author->getFullName()
                                                    : null;
                                $resultIntroduction = $result->news_introduction;
                                $resultLink = \URL::to($news_module_url.'/'.$result->news_url);
                            } elseif ($result instanceof \Synergy\Modules\Services\Models\ServiceCategory) {
                                $resultImage = $result->category_image
                                                ? \Config::get('synergy.uploads.images.url').'resize/'.$result->category_image
                                                : null;
                                $resultImageAlt = $result->category_image_alt;
                                $resultTitle = $result->category_title;
                                $resultDate = null;
                                $resultAuthor = null;
                                $resultIntroduction = $result->category_introduction;
                                $resultLink = \URL::to($services_module_url.'/category/'.$result->category_url);
                            } elseif ($result instanceof \Synergy\Modules\News\Models\NewsCategory) {
                                $resultImage = $result->category_image
                                                ? \Config::get('synergy.uploads.images.url').'resize/'.$result->category_image
                                                : null;
                                $resultImageAlt = $result->category_image_alt;
                                $resultTitle = $result->category_title;
                                $resultDate = null;
                                $resultAuthor = null;
                                $resultIntroduction = $result->category_introduction;
                                $resultLink = \URL::to($news_module_url.'/category/'.$result->category_url);
                            } elseif ($result instanceof \Synergy\Modules\Portfolio\Models\Portfolio) {
                                $resultImage = $result->portfolio_image
                                                ? \Config::get('synergy.uploads.images.url').'resize/'.$result->portfolio_image
                                                : null;
                                $resultImageAlt = $result->portfolio_image_alt;
                                $resultTitle = $result->portfolio_title;
                                $resultDate = $result->portfolio_created->format('F j, Y');
                                $resultAuthor = null;
                                $resultIntroduction = $result->portfolio_introduction;
                                $resultLink = \URL::to($portfolio_module_url.'/'.$result->portfolio_url);
                            } elseif ($result instanceof \Synergy\Modules\Portfolio\Models\PortfolioCategory) {
                                $resultImage = $result->category_image
                                                ? \Config::get('synergy.uploads.images.url').'resize/'.$result->category_image
                                                : null;
                                $resultImageAlt = $result->category_image_alt;
                                $resultTitle = $result->category_title;
                                $resultDate = null;
                                $resultAuthor = null;
                                $resultIntroduction = $result->category_introduction;
                                $resultLink = \URL::to($portfolio_module_url.'/category/'.$result->category_url);
                            } elseif ($result instanceof \Synergy\Modules\Gallery\Models\Album) {
                                $resultType = 'album';
                                $resultImage = $result->thumbnail
                                                ? \Config::get('synergy.uploads.gallery.url').'m/'.$result->thumbnail->picture_file
                                                : null;
                                $resultImageAlt = $result->thumbnail ? $result->thumbnail->picture_title : '';
                                $resultTitle = $result->album_name;
                                $resultDate = $result->album_created->format('F j, Y');
                                $resultAuthor = null;
                                $resultIntroduction = '';
                                $resultLink = \URL::to($gallery_module_url.'/'.$result->album_url);
                            }
                            
                            if (empty($resultType)) {
                                $resultType = '';
                            }

                            ?>

                            <div class="masonry">
                                <div class="masonry-post-wrap">
                                    <?php if ($resultImage): ?>
                                        <div class="post-type">
                                            <a href="{{ $resultLink }}"><img
                                                src="{{ $resultImage }}"
                                                <?php if ($resultImageAlt): ?>
                                                alt="{{ $resultImageAlt }}"
                                                <?php endif; ?>
                                                ></a>
                                        </div>
                                    <?php endif; ?>

                                    <div class="post-desc">
                                        <h2 class="post-title"><a href="{{ $resultLink }}">{{ $resultTitle }}</a></h2>
                                        <div class="post-meta">
                                            <p>
                                                <span class="post-date-2">{{ $resultDate }}</span>
                                                <?php if ($resultAuthor): ?>
                                                | <span class="post-author-2">by {{ $resultAuthor }}</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        @if ($resultIntroduction)
                                            <p>{{ $resultIntroduction }} [...]</p>
                                        @endif
                                        <div class="masonry-meta">
                                            <a href="{{ $resultLink }}" class="more">
                                                @if($resultType == 'album')
                                                    See
                                                @else
                                                    Read
                                                @endif
                                                More <i class="icon-right-open"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

                {{ $paginator->appends(array('filter' => \Input::get('filter')))->links(); }}
            </div>


        @else
            No results to display.
        @endif

    @endif

</div>
<!-- /Search Masonry Posts -->