<?php

return array(
    'name' => 'Gallery',
    
    'icon' => 'camera',

    'identifier' => 'gallery_module',

    'url' => 'gallery',
    'controller' => '\\Synergy\\Modules\\Gallery\\Controllers\\CMS\\GalleryController@process_request',

    'routes' => array(
        'cms' => array(
            array('galleryapi', '\\Synergy\\Modules\\Gallery\\Controllers\\CMS\\GalleryAPIController@process_request'),
        ),
    ),
    
    'site_controller' => '\\Controllers\\Site\\GalleryController@process_request',

    'publish' => array(
        'Controllers'                               => app_path().'/Synergy/Modules/Gallery/Controllers',
        'Models'                                    => app_path().'/Synergy/Modules/Gallery/Models',
        'views'                                     => app_path().'/views',
        'public'                                    => public_path(),
    ),
);