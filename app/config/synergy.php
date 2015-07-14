<?php

/* */

return [
    'auth' => [
        'mode' => 'lax',

        'rules' => [
            [
                'url' => 'cms',
                'permissions' => 'access_cms',
                'redirect' => 'cms/login',
            ],
            
            [
                'url' => 'cms/login',
                'permissions' => 'void',
                'redirect' => 'cms/login',
            ],
            
            [
                'url' => 'cms/loginapi',
                'permissions' => 'void',
                'redirect' => 'cms/login',
            ],
            
            [
                'url' => 'cms/logout',
                'permissions' => 'void',
                'redirect' => 'cms/login',
            ],
            
            [
                'url' => 'cms/forgot_password',
                'permissions' => 'void',
                'redirect' => 'cms/login',
            ],
            
            [
                'url' => 'cms/password_reset',
                'permissions' => 'void',
                'redirect' => 'cms/login',
            ],
            
            [
                'url' => 'cms/modules',
                'permissions' => 'view_modules',
                'redirect' => 'cms',
            ],
            
            [
                'url' => 'cms/modulesapi',
                'permissions' => 'view_modules',
                'redirect' => 'cms',
            ],
            
            [
                'url' => 'cms/settings',
                'permissions' => 'view_settings',
                'redirect' => 'cms',
            ],
            
            [
                'url' => 'cms/settingsapi',
                'permissions' => 'view_settings',
                'redirect' => 'cms',
            ],
        ],

        'redirect' => 'account/login',
    ],

    'pagination' => [
        'news' => [
            'per_page' => 10,
        ],
        'search' => [
            'per_page' => 15,
        ],
        'portfolio' => [
            'per_page' => 10,
        ]
    ],
    
    'modules' => [
        'directory' => app_path().'/modules',
    ],
    
    'icons' => [
        'directory' => 'cms/images/icon/gray_18',
    ],
    
    'uploads' => [
        'images' => [
            'upload' => 'uploadapi/upload',

            'directory' => public_path().'/uploads/images/', // Ensure you include the trailing slash
            
            'url' => \Request::root().'/uploads/images/',
        ],
        
        'logos' => [
            'upload' => 'uploadapi/logo',

            'url' => \Request::root().'/uploads/images/',
        ],
        
        'documents' => [
            'upload' => 'uploadapi/document',

            'directory' => public_path().'/uploads/files/', // Ensure you include the trailing slash

            'url' => \Request::root().'/uploads/files/',
        ],
        
        'gallery' => [
            'upload' => 'uploadapi/gallery',

            'directory' => public_path().'/gallery/', // Ensure you include the trailing slash
            
            'sizes' => [
                'bigImageX' => 1680,
                'bigImageY' => 1260,

                'midImageX' => 640,
                'midImageY' => 480,

                'smallImageX' => 160,
                'smallImageY' => 120,
            ],

            'url' => \Request::root().'/gallery/',
        ],
    ],
    
    'footer' => [
        'credits' => 'Synergy',
    ]
];