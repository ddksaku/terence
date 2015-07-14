<?php

return array(
    'name' => 'Pages',
    
    'icon' => 'attachment',

    'identifier' => 'pages_module',

    'url' => 'pages',
    'controller' => '\\Synergy\\Modules\\Pages\\Controllers\\CMS\\PagesController@process_request',

    'routes' => array(
        'cms' => array(
            array('pagesapi', '\\Synergy\\Modules\\Pages\\Controllers\\CMS\\PagesAPIController@process_request'),
        ),
    ),
    
    'publish' => array(
        'Controllers'                               => app_path().'/Synergy/Modules/Pages/Controllers',
        'Models'                                    => app_path().'/Synergy/Modules/Pages/Models',
        'views'                                     => app_path().'/views',
        'public'                                    => public_path(),
    ),
);