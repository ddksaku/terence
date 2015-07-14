<?php

namespace Controllers\Site;

class PortfolioController extends \Controllers\SiteController
{
    protected $prefix = 'portfolio';

    protected $module;
    
    /* */
    
    protected function startUp()
    {
        $this->module = \Models\CMS\Module\Registration::where('module_identifier', '=', 'portfolio_module')->first();
        
        $this->routePrefix = $this->module->module_url;
        
        \View::share('module', $this->module);
        
        return parent::startUp();
    }

    protected function beforeProcessing()
    {
        \View::share(
            array(
                'sidebar_portfolio_categories' => \Synergy\Modules\Portfolio\Models\PortfolioCategory::where('category_active', '=', 1)
                    ->orderBy('category_order', 'asc')
                    ->with('portfolioItems')
                    ->get(),
            )
        );
        
        return parent::beforeProcessing();
    }
    
    protected function actionAnyIndex(&$page)
    {
        $this->pageTitle($this->module->module_name)
            ->breadcrumb($this->module->module_name);
        
        if ($this->module->page) {
            if ($this->module->page->page_image) {
                $this->openGraph('image', $this->module->page->page_image);
            } elseif (($defaultImage = $this->getData('settings')->setting_default_image)) {
                $this->openGraph('image', $defaultImage);
            }

            $this->metaDescription($this->module->page->page_introduction);
        }
        
        $portfolio = \Synergy\Modules\Portfolio\Models\Portfolio::where('portfolio_active', '=', 1)
                    ->orderBy('portfolio_order', 'asc');
        
        if (($per_page = \Config::get('synergy.pagination.portfolio.per_page'))) {
            $portfolio = $portfolio->paginate($per_page);
            
            $portfolio->getEnvironment()->setViewName('site/pagination/portfolio');
        } else {
            $portfolio = $portfolio->get();
        }
        
        $page->with(
            array(
                'portfolio' => $portfolio,
            )
        );
    }
    
    protected function actionAnyCategory(&$page)
    {
        $url = trim(\Request::segment(3), '/\\');

        $category = \Synergy\Modules\Portfolio\Models\PortfolioCategory::where('category_url', '=', $url)
            ->where('category_active', '=', 1)
            ->first();
        
        if (!$category) {
            return false;
        }

        if ($category->category_image) {
            $this->openGraph('image', $category->category_image);
        } elseif (($defaultImage = $this->getData('settings')->setting_default_image)) {
            $this->openGraph('image', $defaultImage);
        }
        
        $this->pageTitle($category->category_title)
            ->pageTitle($this->module->module_name)
			->metaDescription($category->category_introduction)
            ->breadcrumb($this->module->module_name, $this->module->module_url)
            ->breadcrumb($category->category_title);
        
        $portfolio = $category->activePortfolioItems()
                ->orderBy('portfolio_order', 'asc');

        if (($per_page = \Config::get('synergy.pagination.portfolio.per_page'))) {
            $portfolio = $portfolio->paginate($per_page);
            
            $portfolio->getEnvironment()->setViewName('site/pagination/portfolio');
        } else {
            $portfolio = $portfolio->get();
        }

        $page->with(
            array(
                'category' => $category,
                'portfolio' => $portfolio,
            )
        );
    }
    
    // 
    
    protected function loadPortfolioPage()
    {
        $url = trim(\Request::segment(2), '/\\');
        
        $portfolioItem = \Synergy\Modules\Portfolio\Models\Portfolio::where('portfolio_url', '=', $url)->where('portfolio_active', '=', 1)->first();

        if (!$portfolioItem) {
            return false;
        }
        
        if ($portfolioItem->portfolio_image) {
            $this->openGraph('image', $portfolioItem->portfolio_image);
        } elseif (($defaultImage = $this->getData('settings')->setting_default_image)) {
            $this->openGraph('image', $defaultImage);
        }
        
        $this->pageTitle($portfolioItem->portfolio_title)
            ->pageTitle($this->module->module_name)
            ->metaDescription($portfolioItem->portfolio_introduction)
            ->breadcrumb($this->module->module_name, $this->module->module_url)
            ->breadcrumb($portfolioItem->portfolio_title);
        
        $this->setPageView('portfolio/item')->with(
            array(
                'portfolio' => $portfolioItem,
                'in_categories' => implode(', ', $portfolioItem->categories->lists('category_title', 'category_id')),
            )
        );
        
        // Set sidebar data.

        \View::share(
            array(
                'sidebar_related_portfolio' => $portfolioItem->getRelatedPortfolioItems(2),
            )
        );
    }
    
    /* Overrides */
    
    protected function handlePageRequest()
    {
        if (!$this->actionCallable) {
            $response = $this->loadPortfolioPage();
        } else {
            $response = $this->callActionHandler();
        }

        if ($response === false) {
            $this->setResponseCode(404);

            $this->pageTitle('Not found');

            $this->setSublayoutEnabled(false);
            $this->setAutoloadPageView(false);

            $this->setPageView('404', false);
        } else {
            return $response;
        }
    }
}