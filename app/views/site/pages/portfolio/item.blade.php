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

<!-- Blog Posts -->
<div class="fullblock blockwhite padding50">
    <div class="container">	    
        <div class="row">
                <div class="span9">
                    
                    @if(count($portfolio->slideshow) > 0)
                        <div class="flexslider">
                            <ul class="slides">
                                @foreach($portfolio->slideshow as $slideshowImage)
                                    <li>
                                        <img
                                            src="{{ \Config::get('synergy.uploads.images.url') }}{{ $slideshowImage->image_filename }}"
                                            @if($slideshowImage->image_alt)
                                                alt="{{ $slideshowImage->image_alt }}"
                                            @endif
                                            />
                                        @if($slideshowImage->image_title)
                                            <p class="flex-caption">
                                                {{ $slideshowImage->image_title }}
                                            </p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                <!-- Post Wrap -->
                <div class="blog-post-wrap">
                        <div class="post-type">
                        @if($portfolio->portfolio_image)
                            <img src="{{ \Config::get('synergy.uploads.images.url') }}{{ $portfolio->portfolio_image }}" @if($portfolio->portfolio_image_alt) alt="{{ $portfolio->portfolio_image_alt }}" @endif>
                        @endif
                        </div>

                    <div class="post-desc">
                        <h1 class="post-title"><a href="{{ \URL::to($portfolio_module_url.'/'.$portfolio->portfolio_url) }}">{{ $portfolio->portfolio_title }}</a></h1>
                        <div class="post-meta">
                            <p>
                                <span class="post-date-2">Published {{ $portfolio->portfolio_created->format('F d, Y') }}</span>

                                @if ($portfolio->author)
                                | <span class="post-author-2">by {{ $portfolio->author->getFullName() }}</span>
                                @endif
                                
                                @if(count($portfolio->categories) > 0)
                                    | <span class="post-cat-2">
                                        in
                                            <?php
                                            $total = count($portfolio->categories);
                                            $shown = 0;
                                            ?>
                                            @foreach($portfolio->categories as $category)
                                                <a href="{{ \URL::to($portfolio_module_url.'/category/'.$category->category_url) }}">{{ $category->category_title }}</a>@if(++$shown < $total), @endif
                                            @endforeach
                                        </span>
                                @endif
                            </p>	
                        </div>

                        {{ $portfolio->portfolio_description }}

                    </div>
                    
                    <div class="post-downloads">
                        @if(count($portfolio->files) > 0)
                            <h4>Downloads</h4>
                            @foreach($portfolio->files as $file)
                                <p>
                                    <a target="_blank" href="{{ \Config::get('synergy.uploads.documents.url').$file->file_name }}">{{ $file->file_original_name }}</a>
                                </p>
                            @endforeach
                        @endif
                    </div>

                    <ul class="sharebox">
                        <li>@include('site.snippets.gplus')</li>
                        <li>@include('site.snippets.tweet')</li>
                        <li>@include('site.snippets.fblike')</li>
                    </ul>

                </div>
                <!-- /Post Wrap -->

            </div>

            <!-- Sidebar -->
            @include('site.sidebars.default')
            <!-- /Sidebar -->

        </div>
                </div>
</div>
<!-- /Blog Posts -->