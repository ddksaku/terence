<?php

return array(
    'name' => 'News',
    
    'icon' => 'notepad',

    'identifier' => 'news_module',

    'url' => 'news',
    'controller' => '\\Synergy\\Modules\\News\\Controllers\\CMS\\NewsController@process_request',

    'routes' => array(
        'cms' => array(
            array('newsapi', '\\Synergy\\Modules\\News\\Controllers\\CMS\\NewsAPIController@process_request'),
        ),
    ),

    'site_controller' => '\\Controllers\\Site\\NewsController@process_request',

    'publish' => array(
        'Controllers'                               => app_path().'/Synergy/Modules/News/Controllers',
        'Models'                                    => app_path().'/Synergy/Modules/News/Models',
        'views'                                     => app_path().'/views',
        'public'                                    => public_path(),
    ),
);