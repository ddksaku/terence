<?php

namespace Controllers\Site;

class SitemapController extends \Controller
{
    public function outputSitemap()
    {
        $servicesModule = \Models\CMS\Module\Registration::where('module_identifier', '=', 'services_module')
            ->first();

        $newsModule = \Models\CMS\Module\Registration::where('module_identifier', '=', 'news_module')
            ->first();
        
        $galleryModule = \Models\CMS\Module\Registration::where('module_identifier', '=', 'gallery_module')
            ->first();
        
        $portfolioModule = \Models\CMS\Module\Registration::where('module_identifier', '=', 'portfolio_module')
            ->first();
        
        /* */
        
        $sitemap = \View::make('site.sitemap')
            ->with([
                'pages' => \Synergy\Modules\Pages\Models\Page::where('page_active', '=', 1)
                    ->get(),
            ]);
        
        /* */

        if ($servicesModule && $servicesModule->isInstalled()) {
            $sitemap->with([
                'services_module_url' => $servicesModule->module_url,
                
                'services' => \Synergy\Modules\Services\Models\Service::where('service_active', '=', 1)
                    ->get(),
                
                'services_categories' => \Synergy\Modules\Services\Models\ServiceCategory::where('category_active', '=', 1)
                    ->get(),
                
            ]);
        }
        
        if ($newsModule && $newsModule->isInstalled()) {
            $sitemap->with([
                'news_module_url' => $newsModule->module_url,

                'articles' => \Synergy\Modules\News\Models\NewsItem::where('news_publish_date', '<=', time())
                    ->where('news_active', '=', 1)
                    ->get(),
                
                'news_categories' => \Synergy\Modules\News\Models\NewsCategory::where('category_active', '=', 1)
                    ->get(),
            ]);
        }
        
        if ($galleryModule && $galleryModule->isInstalled()) {
            $sitemap->with([
                'gallery_module_url' => $galleryModule->module_url,

                'albums' => \Synergy\Modules\Gallery\Models\Album::all(),
            ]);
        }
        
        if ($portfolioModule && $portfolioModule->isInstalled()) {
            $sitemap->with([
                'portfolio_module_url' => $portfolioModule->module_url,
        
                'portfolio_categories' => \Synergy\Modules\Portfolio\Models\PortfolioCategory::where('category_active', '=', 1)
                    ->get(),
                
                'portfolio' => \Synergy\Modules\Portfolio\Models\Portfolio::where('portfolio_active', '=', 1)
                    ->get(),
            ]);
        }

        /* */
        
        $response = \Response::make($sitemap, 200);
        
        $response->header('Content-Type', 'application/xml');
        
        return $response;
    }
}

?>
