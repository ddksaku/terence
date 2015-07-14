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

<!-- Service Posts -->
<div class="fullblock blockwhite padding50">
    <div class="container">	    
        <div class="row">
                <div class="span9">

                <!-- Post Wrap -->
                <div class="blog-post-wrap">
                    
                    <div class="post-desc">
                        <h1 class="post-title"><a href="{{ \URL::to($services_module_url.'/'.$service->service_url) }}">{{ $service->service_title }}</a></h1>
                        <div class="post-meta">
                            <p>
                                <span class="post-date-2">Published {{ $service->service_created->format('F d, Y') }}</span>
                                @if($in_categories)
                                    |   <span class="post-cat-2">
                                            in
                                            <?php
                                            $total = count($service->categories);
                                            $shown = 0;
                                            ?>
                                            @foreach($service->categories as $category)
                                                <a href="{{ \URL::to($services_module_url.'/category/'.$category->category_url) }}">{{ $category->category_title }}</a>@if(++$shown < $total), @endif
                                            @endforeach
                                        </span>
                                @endif
                            </p>	
                        </div>
                        
                        @if($service->service_image)
                        <img src="{{ \Config::get('synergy.uploads.images.url') }}resize/{{ $service->service_image }}" class="alignleft fixed" @if($service->service_image_alt) alt="{{ $service->service_image_alt }}" @endif>
                    @endif
                    <h2>{{ $service->service_introduction }}</h2>

                        {{ $service->service_description }}

                    </div>
                    
                    <div class="post-downloads">
                        @if(count($service->files) > 0)
                            <h4>Downloads</h4>
                            @foreach($service->files as $file)
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
<!-- /Service Posts -->