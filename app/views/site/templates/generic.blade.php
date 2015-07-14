<!-- Breadcrumbs -->
<div class="fullblock blockgrey padding20">

    <div class="container">	    

        <div class="row breadcrumbs">
            <div class="span6">
                <h1>{{ $page->page_title }}</h1>
            </div>
            <div class="span6">
                @include('site/snippets/breadcrumbs')
            </div>
        </div>

                </div>
</div>
<!-- /Breadcrumbs -->

<!-- Posts -->
<div class="fullblock blockwhite padding50">
    <div class="container">	    
        <div class="row">
                <div class="span9">

                <!-- Post Wrap -->
                <div class="blog-post-wrap">
                        <!-- <div class="post-type">
                        @if($page->page_image)
                            <img src="{{ \Config::get('synergy.uploads.images.url') }}{{ $page->page_image }}" @if($page->page_image_alt) alt="{{ $page->page_image_alt }}" @endif>
                        @endif
                        </div> -->

                    <div class="post-desc">
                        <h1 class="post-title"><a href="{{ \URL::to($page->page_url) }}">{{ $page->page_title }}</a></h1>
                        <div class="post-meta">
                            <p>
                                <span class="post-date-2">Published {{ $page->page_created->format('F d, Y') }}</span>
                                @if($page->parent)
                                    | <span class="post-cat-2">in <a href="/{{ $page->parent->page_url }}">{{ $page->parent->page_title }}</a></span>
                                @endif
                            </p>	
                        </div>

                        {{ $page->page_description }}

                    </div>
                        
                    <div class="post-downloads">
                        @if(count($page->files) > 0)
                            <h4>Downloads</h4>
                            @foreach($page->files as $file)
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
<!-- /Posts -->