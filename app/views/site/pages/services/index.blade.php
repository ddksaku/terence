<?php

$servicesArray = $services->all();

$top3 = array_slice($servicesArray, 0, 3);
$remaining = array_slice($servicesArray, 3);

?>

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

<!-- Parallax -->
<div class="fullblock">
    <div class="parallax-home">
        <div class="container">
                <ul class="service-blocks-wrap-2">
                    
                    @if(isset($top3[0]))
                        <li>
                            <h4><a href="{{ \URL::to($services_module_url.'/'.$top3[0]->service_url) }}">{{ $top3[0]->service_title }}</a></h4>
                            <div class="icon-wrap-2 theme-color">
                                @if($top3[0]->service_image)
                                    <a href="{{ \URL::to($services_module_url.'/'.$top3[0]->service_url) }}"><img src="{{ \Config::get('synergy.uploads.images.url') }}square/{{ $top3[0]->service_image }}" @if($top3[0]->service_image_alt) alt="{{ $top3[0]->service_image_alt }}" @endif></a>
                                @else
                                    <a href="{{ \URL::to($services_module_url.'/'.$top3[0]->service_url) }}"><i class="icon-coffee"></i></a>
                                @endif
                            </div>
                            <p>{{ $top3[0]->service_introduction }}</p>
                            <div class="separotor-1"><span></span></div>
                            <div class="button-holder"><a href="{{ \URL::to($services_module_url.'/'.$top3[0]->service_url) }}" class="btn-theme btn-theme-large">Find out more</a></div>
                        </li>
                    @endif
                    
                    
                    @if(isset($top3[1]))
                        <li>
                            <h4><a href="{{ \URL::to($services_module_url.'/'.$top3[1]->service_url) }}">{{ $top3[1]->service_title }}</a></h4>
                            <div class="icon-wrap-2 theme-color">
                                @if($top3[1]->service_image)
                                    <a href="{{ \URL::to($services_module_url.'/'.$top3[1]->service_url) }}"><img src="{{ \Config::get('synergy.uploads.images.url') }}square/{{ $top3[1]->service_image }}" @if($top3[1]->service_image_alt) alt="{{ $top3[1]->service_image_alt }}" @endif></a>
                                @else
                                    <a href="{{ \URL::to($services_module_url.'/'.$top3[1]->service_url) }}"><i class="icon-coffee"></i></a>
                                @endif
                            </div>
                            <p>{{ $top3[1]->service_introduction }}</p>
                            <div class="separotor-1"><span></span></div>
                            <div class="button-holder"><a href="{{ \URL::to($services_module_url.'/'.$top3[1]->service_url) }}" class="btn-theme btn-theme-large">Find out more</a></div>
                        </li>
                    @endif
                    
                    
                    @if(isset($top3[2]))
                        <li>
                            <h4><a href="{{ \URL::to($services_module_url.'/'.$top3[2]->service_url) }}">{{ $top3[2]->service_title }}</a></h4>
                            <div class="icon-wrap-2 theme-color">
                                @if($top3[2]->service_image)
                                    <a href="{{ \URL::to($services_module_url.'/'.$top3[2]->service_url) }}"><img src="{{ \Config::get('synergy.uploads.images.url') }}square/{{ $top3[2]->service_image }}" @if($top3[2]->service_image_alt) alt="{{ $top3[2]->service_image_alt }}" @endif></a>
                                @else
                                    <a href="{{ \URL::to($services_module_url.'/'.$top3[2]->service_url) }}"><i class="icon-coffee"></i></a>
                                @endif
                            </div>
                            <p>{{ $top3[2]->service_introduction }}</p>
                            <div class="separotor-1"><span></span></div>
                            <div class="button-holder"><a href="{{ \URL::to($services_module_url.'/'.$top3[2]->service_url) }}" class="btn-theme btn-theme-large">Find out more</a></div>
                        </li>
                    @endif
            </ul>
        </div>
    </div>
</div>
<!-- /Parallax -->

<!-- Services -->
@if(count($remaining) > 0)
	<div class="fullblock blockwhite padding50">
    	<div class="container">	   

            <div class="row service_blocks_wrap_5">

                @foreach($remaining as $service)
                    <div class="span4">
                        <div class="icon-wrap-4">
                            @if($service->service_image)
                                <a href="{{ \URL::to($services_module_url.'/'.$service->service_url) }}"><img src="{{ \Config::get('synergy.uploads.images.url') }}square/{{ $service->service_image }}" @if($service->service_image_alt) alt="{{ $service->service_image_alt }}" @endif></a>
                            @else
                                <a href="{{ \URL::to($services_module_url.'/'.$service->service_url) }}"><i class="icon-coffee"></i></a>
                            @endif
                        </div>
                        <h4><a href="{{ \URL::to($services_module_url.'/'.$service->service_url) }}">{{ $service->service_title }}</a></h4>
                        <p>{{ $service->service_introduction }}</p>
                        <a href="{{ \URL::to($services_module_url.'/'.$service->service_url) }}" class="btn-theme">Find out more</a>
                    </div>
                @endforeach
            </div>
            
        </div>
    </div>
@endif
<!-- /Services -->