<?php

return array(
    'name' => 'Services',
    
    'icon' => 'paste',

    'identifier' => 'services_module',

    'url' => 'services',
    'controller' => '\\Synergy\\Modules\\Services\\Controllers\\CMS\\ServicesController@process_request',

    'routes' => array(
        'cms' => array(
            array('servicesapi', '\\Synergy\\Modules\\Services\\Controllers\\CMS\\ServicesAPIController@process_request'),
        ),
    ),

    'site_controller' => '\\Controllers\\Site\\ServicesController@process_request',

    'publish' => array(
        'Controllers'                               => app_path().'/Synergy/Modules/Services/Controllers',
        'Models'                                    => app_path().'/Synergy/Modules/Services/Models',
        'views'                                     => app_path().'/views',
        'public'                                    => public_path(),
    ),
);