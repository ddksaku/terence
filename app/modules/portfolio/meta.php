<?php

return array(
    'name' => 'Portfolio',
    
    'icon' => 'paste',

    'identifier' => 'portfolio_module',

    'url' => 'portfolio',
    'controller' => '\\Synergy\\Modules\\Portfolio\\Controllers\\CMS\\PortfolioController@process_request',

    'routes' => array(
        'cms' => array(
            array('portfolioapi', '\\Synergy\\Modules\\Portfolio\\Controllers\\CMS\\PortfolioAPIController@process_request'),
        ),
    ),

    'site_controller' => '\\Controllers\\Site\\PortfolioController@process_request',

    'publish' => array(
        'Controllers'                               => app_path().'/Synergy/Modules/Portfolio/Controllers',
        'Models'                                    => app_path().'/Synergy/Modules/Portfolio/Models',
        'views'                                     => app_path().'/views',
        'public'                                    => public_path(),
    ),
);