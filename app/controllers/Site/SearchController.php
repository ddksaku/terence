<?php

namespace Controllers\Site;

class SearchController extends \Controllers\SiteController
{
    protected function processSearch(&$page)
    {
        /* Create/resume search session */
        
        if ($this->post->has('query')) {
            $query = trim($this->post->get('query'));

            \Session::set('search_query', $query);

            return \Redirect::to(\Request::path());
        } elseif (\Request::segment(2) == 'tagged') {
            $tagged = trim(\Request::segment(3));
        } else {
            $query = \Session::get('search_query');
        }
        
        /* Create new search instance */

        $perPage = \Config::get('synergy.pagination.search.per_page');

        $search = new \Models\Site\Search;
        
        /* Set query, if applicable */
        
        if (isset($query)) {
            $search->setQuery($query);
        }
        
        /* Set tag condition " */
        
        if (isset($tagged)) {
            $search->setTagged($tagged);
        }
        
        /* Set filter " */
        
        if ($this->input->has('filter')) {
            /*
             * The URL filter is only an alias for a module->filter conversion. 
             * Ergo, we need to lookup the module.
             * 
             */
            
            $filterAlias = $this->input->get('filter');
            
            $isCategories = (preg_match('/\\-categories$/ism', $filterAlias)) ? true : false;
            
            $filterAliasLookup = preg_replace('/\\-categories$/ism', '', $filterAlias);
            
            $module = \Models\CMS\Module\Registration::where('module_url', '=', $filterAliasLookup)->first();
            
            if ($module) {
                switch ($module->module_identifier) {
                    case 'services_module': {
                        if ($isCategories) {
                            $filter = 'service_categories';
                        } else {
                            $filter = 'services';
                        }
                        
                        break;
                    } case 'news_module': {
                        if ($isCategories) {
                            $filter = 'news_categories';
                        } else {
                            $filter = 'news';
                        }
                        
                        break;
                    } case 'portfolio_module': {
                        if ($isCategories) {
                            $filter = 'portfolio_categories';
                        } else {
                            $filter = 'portfolio';
                        }
                        
                        break;
                    } case 'gallery_module': {
                        $filter = 'albums';

                        break;
                    } default: {
                        $filter = $filterAlias;
                    }
                }
            } else {
                $filter = $filterAlias;
            }

            $search->setFilter($filter);
        }
        
        /* Run search */
        
        $search->prepareSearch(
                $this->input->get('page'),
                $perPage
            );

        $paginator = $search->getPaginator();
        
        if ($perPage) {
            $paginator->getEnvironment()->setViewName('site/pagination/search');
        }
        
        /* Set 404 code if requested tag doesn't exist */
        
        if ($search->tagLookupFailed()) {
            $this->setResponseCode(404);
        }
        
        /* Create list of available filters */
        
        $filters = array();
        
        $filterApplicatorsOrdered = array();
        $filterApplicators = array();
        
        if (
            ($module = \Models\CMS\Module\Registration::where('module_identifier', '=', 'pages_module')->first())
            && $module->isInstalled()
        ) {
            $filters[$module->getURL()] = $module->getName();
        }

        if (
            ($module = \Models\CMS\Module\Registration::where('module_identifier', '=', 'services_module')->first())
            && $module->isInstalled()
        ) {
            $applicator = function() use (&$filters, $module) {
                $filters[$module->getURL()] = $module->getName();
                $filters[$module->getURL().'-categories'] = "{$module->getName()} categories";
            };
            
            if ($module->page) {
                $filterApplicatorsOrdered[$module->page->page_order] = $applicator;
            } else {
                $filterApplicators[] = $applicator;
            }
        }
        
        if (
            ($module = \Models\CMS\Module\Registration::where('module_identifier', '=', 'news_module')->first())
            && $module->isInstalled()
        ) {
            $applicator = function() use (&$filters, $module) {
                $filters[$module->getURL()] = $module->getName();
                $filters[$module->getURL().'-categories'] = "{$module->getName()} categories";
            };

            if ($module->page) {
                $filterApplicatorsOrdered[$module->page->page_order] = $applicator;
            } else {
                $filterApplicators[] = $applicator;
            }
        }
        
        if (
            ($module = \Models\CMS\Module\Registration::where('module_identifier', '=', 'portfolio_module')->first())
            && $module->isInstalled()
        ) {
            $applicator = function() use (&$filters, $module) {
                $filters[$module->getURL()] = $module->getName();
                $filters[$module->getURL().'-categories'] = "{$module->getName()} categories";
            };

            if ($module->page) {
                $filterApplicatorsOrdered[$module->page->page_order] = $applicator;
            } else {
                $filterApplicators[] = $applicator;
            }
        }
        
        if (
            ($module = \Models\CMS\Module\Registration::where('module_identifier', '=', 'gallery_module')->first())
            && $module->isInstalled()
        ) {
            $filters[$module->getURL()] = $module->getName();
        }
        
        ksort($filterApplicatorsOrdered);
        
        foreach (array($filterApplicatorsOrdered, $filterApplicators) as $applicators) {
            foreach ($applicators as $applicator) {
                $applicator();
            }
        }

        /* Pass data to view */

        $page->with(
            array(
                'filters' => $filters,
                'currentFilter' => isset($filterAlias) ? $filterAlias : null,
                'search' => $search,
                'results' => $paginator->getItems(),
                'paginator' => $paginator,
                'tagged' => $search->getTagged(),
                'searchQuery' => isset($query) ? $query : null,
            )
        );
    }
    
    /*
     * Custom page load behaviour for generic pages.
     * 
     */
    
    protected function handlePageRequest()
    {
        $this->breadcrumb('Search');
        
        switch (\Request::segment(2)) {
            case '':
            case 'tagged':
            {
                return $this->processSearch($this->pageView);
            }
            default:
            {
                $this->setResponseCode(404);

                $this->pageTitle('Not found');

                $this->setSublayoutEnabled(false);
                $this->setAutoloadPageView(false);

                $this->setPageView('404', false);
            }
        }
    }
    
    /* */
    
    protected function loadPageView()
    {
        $this->setPageView("{$this->prefix}/index");
    }
}