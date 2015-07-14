<?php

return array(
    'name' => 'Contact',
    
    'icon' => 'messenger',

    'identifier' => 'contact_module',

    'url' => 'contact',
    'controller' => '\\Synergy\\Modules\\Contact\\Controllers\\CMS\\ContactController@process_request',

    'routes' => array(
        'cms' => array(
            array('contactapi', '\\Synergy\\Modules\\Contact\\Controllers\\CMS\\ContactAPIController@process_request'),
        ),
    ),

    'site_controller' => '\\Controllers\\Site\\ContactController@process_request',

    'publish' => array(
        'Controllers'                               => app_path().'/Synergy/Modules/Contact/Controllers',
        'Models'                                    => app_path().'/Synergy/Modules/Contact/Models',
        'views'                                     => app_path().'/views',
        'public'                                    => public_path(),
    ),
);