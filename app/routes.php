<?php

/* Static CMS routing */

Route::group(array('prefix' => 'cms'), function()
{
    Route::any('{loginapi}', '\\Controllers\\CMS\\LoginAPIController@process_request')->where('loginapi', 'loginapi(/.*)?');
    
    Route::any('{cmsapi}', '\\Controllers\\CMS\\CMSAPIController@process_request')->where('cmsapi', 'cmsapi(/.*)?');
    
    Route::any('{modules}', '\\Controllers\\CMS\\ModulesController@process_request')->where('modules', 'modules(/.*)?');
    Route::any('{modulesapi}', '\\Controllers\\CMS\\ModulesAPIController@process_request')->where('modulesapi', 'modulesapi(/.*)?');
    
    Route::any('{settings}', '\\Controllers\\CMS\\SettingsController@process_request')->where('settings', 'settings(/.*)?');
    Route::any('{settingsapi}', '\\Controllers\\CMS\\SettingsAPIController@process_request')->where('settingsapi', 'settingsapi(/.*)?');
    
    Route::any('{uploadapi}', '\\Controllers\\CMS\\UploadAPIController@process_request')->where('uploadapi', 'uploadapi(/.*)?');

    Route::any('file-download/{type}/{file_id}', '\\Controllers\\CMS\\RootController@file_download');

    Route::any('', '\\Controllers\\CMS\\RootController@process_request');
    Route::any('{any}', '\\Controllers\\CMS\\RootController@process_request')->where('any', '.*');
});

/* CMS-generated routing for modules */

include('module_routes.php');

/* Static site routes */

Route::any('{siteapi}', '\\Controllers\\Site\\SiteAPIController@process_request')->where('siteapi', 'siteapi(/.*)?');
Route::any('captcha', '\\Controllers\\CaptchaController@output');

/* Fallback catch-all routes */

Route::group(array('prefix' => 'cms'), function()
{
    Route::any('', '\\Controllers\\CMS\\RootController@process_request');
    Route::any('{any}', '\\Controllers\\CMS\\RootController@process_request')->where('any', '.*');
});

Route::any('{search}', '\\Controllers\\Site\\SearchController@process_request')->where('search', 'search(/.*)?');

Route::any('sitemap.xml', '\\Controllers\\Site\\SitemapController@outputSitemap');

Route::any('', '\\Controllers\\Site\\RootController@process_request')->where('any', '.*');
Route::any('{any}', '\\Controllers\\Site\\RootController@process_request')->where('any', '.*');