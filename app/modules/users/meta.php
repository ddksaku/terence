<?php

return array(
    'name' => 'Users',
    
    'icon' => 'messenger',

    'identifier' => 'users_module',

    'url' => 'users',
    'controller' => '\\Synergy\\Modules\\Users\\Controllers\\CMS\\UsersController@process_request',

    'routes' => array(
        'cms' => array(
            array('usersapi', '\\Synergy\\Modules\\Users\\Controllers\\CMS\\UsersAPIController@process_request'),
        ),
    ),

    'publish' => array(
        'Controllers'                               => app_path().'/Synergy/Modules/Users/Controllers',
        'views'                                     => app_path().'/views',
        'public'                                    => public_path(),
    ),
);