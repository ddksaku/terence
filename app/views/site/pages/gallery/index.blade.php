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

<!-- Portfolio Posts -->
<div class="fullblock blockwhite padding50">
    <div class="container">	    
        <div class="row portfolio-4columns">
            
            <ul id="portfolio-list" class="portfolio-list">
                @foreach($albums as $album)
                    @if (count($album->pictures) > 0)
                        <li class="modern animation">
                            <div class="portfolio-item">
                                <div class="image-wrap">
                                    <div class="maskImage">
                                        <a href="{{ \URL::to($gallery_module_url.'/'.$album->album_url) }}"><span><i class="icon-link-1"></i></span></a>
                                    </div>
                                    @if (($picture = $album->thumbnail))
                                        <a href="{{ \URL::to($gallery_module_url.'/'.$album->album_url) }}">
                                            <img src="{{ \URL::to('/gallery/m/'.$picture->picture_file) }}" alt="{{ $picture->picture_title }}" />
                                        </a>
                                    @endif
                                </div>
                                <div class="title_holder">
                                    <h1><a href="{{ \URL::to($gallery_module_url.'/'.$album->album_url) }}">{{ $album->album_name }}</a></h1>
                                </div>
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>

        </div>
    </div>
</div>
<!-- /Portfolio Posts -->