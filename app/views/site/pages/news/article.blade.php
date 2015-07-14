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

                <!-- Post Wrap -->
                <div class="blog-post-wrap">
                        <div class="post-type">
                        @if($article->news_image)
                            <img src="{{ \Config::get('synergy.uploads.images.url') }}{{ $article->news_image }}" @if($article->news_image_alt) alt="{{ $article->news_image_alt }}" @endif>
                        @endif
                        </div>

                    <div class="post-desc">
                        <h1 class="post-title"><a href="{{ \URL::to($news_module_url.'/'.$article->news_url) }}">{{ $article->news_title }}</a></h1>
                        <div class="post-meta">
                            <p>
                                <span class="post-date-2">Published {{ $article->getPublishDate('F d, Y') }}</span>

                                @if ($article->author)
                                | <span class="post-author-2">by {{ $article->author->getFullName() }}</span>
                                @endif
                                
                                @if(count($article->categories) > 0)
                                    | <span class="post-cat-2">
                                        in
                                            <?php
                                            $total = count($article->categories);
                                            $shown = 0;
                                            ?>
                                            @foreach($article->categories as $category)
                                                <a href="{{ \URL::to($news_module_url.'/category/'.$category->category_url) }}">{{ $category->category_title }}</a>@if(++$shown < $total), @endif
                                            @endforeach
                                        </span>
                                @endif
                            </p>	
                        </div>

                        {{ $article->news_description }}

                    </div>
                    
                    <div class="post-downloads">
                        @if(count($article->files) > 0)
                            <h4>Downloads</h4>
                            @foreach($article->files as $file)
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