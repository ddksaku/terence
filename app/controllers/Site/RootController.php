<?php

namespace Controllers\Site;

class RootController extends \Controllers\SiteController
{   
    /*
     * Custom page load behaviour for generic pages.
     * 
     */
    
    protected function handlePageRequest()
    {
        if ($this->actionCallable) {
            return $this->callActionHandler();
        } else {
            $url = trim(\Request::path(), '/\\');

            // Attempt to load page from database.
            
            if ($url) {
                $page = \Synergy\Modules\Pages\Models\Page::where('page_url', '=', $url)->where('page_active', '=', 1)->first();
            } else {
                $page = \Synergy\Modules\Pages\Models\Page::where('page_homepage', '=', 1)->first();
            }

            if ($page) {
                
                if ($page->page_image) {
                    $this->openGraph('image', $page->page_image);
                } elseif (($defaultImage = $this->getData('settings')->setting_default_image)) {
                    $this->openGraph('image', $defaultImage);
                }
				
				if ($page->page_seo_title) {
                    $this->pageTitle($page->page_seo_title);
				} else {
					$this->pageTitle($page->page_title);
                }
                
                $this->metaDescription($page->page_introduction)
                    ->setPageLayout($page->page_template)
                    ->setPageView(
                        "templates/{$page->page_template}",
                        false
                    )->with(
                        array(
                            'page' => $page,
                        )
                    );
                        
                /* Breadcrumbs */
                        
                $crumbs = array();

                $parent = $page;
                        
                while ($parent->parent) {
                    $parent = $parent->parent;
                    
                    $crumbs[] = array($parent->page_title, $parent->page_url);
                }
                
                $crumbs = array_reverse($crumbs);
                
                foreach ($crumbs as $crumb) {
                    $this->breadcrumb($crumb[0], $crumb[1]);
                }
                
                $this->breadcrumb($page->page_title);
            } else {
                $this->setResponseCode(404);

                $this->pageTitle('Not found');

                $this->setSublayoutEnabled(false);
                $this->setAutoloadPageView(false);

                $this->setPageView('404', false);
            }
        }
    }
}