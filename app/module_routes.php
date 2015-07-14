<?php

/*
 *
 * WARNING: This code is generated automatically; changes to this file may be lost.
 *
 */

Route::group(array('prefix' => 'cms'), function () {
    Route::any('{users}', '\\Synergy\\Modules\\Users\\Controllers\\CMS\\UsersController@process_request')->where('users', 'users(/.*)?');
    Route::any('{usersapi}', '\\Synergy\\Modules\\Users\\Controllers\\CMS\\UsersAPIController@process_request')->where('usersapi', 'usersapi(/.*)?');
    Route::any('{pages}', '\\Synergy\\Modules\\Pages\\Controllers\\CMS\\PagesController@process_request')->where('pages', 'pages(/.*)?');
    Route::any('{pagesapi}', '\\Synergy\\Modules\\Pages\\Controllers\\CMS\\PagesAPIController@process_request')->where('pagesapi', 'pagesapi(/.*)?');
    Route::any('{services}', '\\Synergy\\Modules\\Services\\Controllers\\CMS\\ServicesController@process_request')->where('services', 'services(/.*)?');
    Route::any('{servicesapi}', '\\Synergy\\Modules\\Services\\Controllers\\CMS\\ServicesAPIController@process_request')->where('servicesapi', 'servicesapi(/.*)?');
    Route::any('{portfolio}', '\\Synergy\\Modules\\Portfolio\\Controllers\\CMS\\PortfolioController@process_request')->where('portfolio', 'portfolio(/.*)?');
    Route::any('{portfolioapi}', '\\Synergy\\Modules\\Portfolio\\Controllers\\CMS\\PortfolioAPIController@process_request')->where('portfolioapi', 'portfolioapi(/.*)?');
    Route::any('{news}', '\\Synergy\\Modules\\News\\Controllers\\CMS\\NewsController@process_request')->where('news', 'news(/.*)?');
    Route::any('{newsapi}', '\\Synergy\\Modules\\News\\Controllers\\CMS\\NewsAPIController@process_request')->where('newsapi', 'newsapi(/.*)?');
    Route::any('{gallery}', '\\Synergy\\Modules\\Gallery\\Controllers\\CMS\\GalleryController@process_request')->where('gallery', 'gallery(/.*)?');
    Route::any('{galleryapi}', '\\Synergy\\Modules\\Gallery\\Controllers\\CMS\\GalleryAPIController@process_request')->where('galleryapi', 'galleryapi(/.*)?');
    Route::any('{contact}', '\\Synergy\\Modules\\Contact\\Controllers\\CMS\\ContactController@process_request')->where('contact', 'contact(/.*)?');
    Route::any('{contactapi}', '\\Synergy\\Modules\\Contact\\Controllers\\CMS\\ContactAPIController@process_request')->where('contactapi', 'contactapi(/.*)?');
});
Route::any('{services}', '\\Controllers\\Site\\ServicesController@process_request')->where('services', 'services(/.*)?');
Route::any('{portfolio}', '\\Controllers\\Site\\PortfolioController@process_request')->where('portfolio', 'portfolio(/.*)?');
Route::any('{news}', '\\Controllers\\Site\\NewsController@process_request')->where('news', 'news(/.*)?');
Route::any('{gallery}', '\\Controllers\\Site\\GalleryController@process_request')->where('gallery', 'gallery(/.*)?');
Route::any('{contact}', '\\Controllers\\Site\\ContactController@process_request')->where('contact', 'contact(/.*)?');