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
                @foreach($album->pictures as $picture)
                    <li class="modern animation">
                            <div class="portfolio-item">
                            <div class="image-wrap">
                                <img src="{{ \URL::to('/gallery/m/'.$picture->picture_file) }}" alt="{{ $picture->picture_title }}" />
                            </div>
                            <div class="title_holder">
                                <h1>{{ $picture->picture_title }}</h1>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

        </div>
    </div>
</div>
<!-- /Portfolio Posts -->