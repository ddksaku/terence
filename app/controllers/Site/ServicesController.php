<?php

namespace Controllers\Site;

class ServicesController extends \Controllers\SiteController
{
    protected $prefix = 'services';

    protected $module;
    
    /* */
    
    protected function startUp()
    {
        $this->module = \Models\CMS\Module\Registration::where('module_identifier', '=', 'services_module')->first();
        
        $this->routePrefix = $this->module->module_url;
        
        \View::share('module', $this->module);
        
        return parent::startUp();
    }

    protected function beforeProcessing()
    {
        \View::share(
            array(
                'sidebar_services_categories' => \Synergy\Modules\Services\Models\ServiceCategory::where('category_active', '=', 1)
                    ->orderBy('category_order', 'asc')
                    ->with('services')
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
        
        $services = \Synergy\Modules\Services\Models\Service::where('service_active', '=', 1)
                    ->orderBy('service_order', 'asc');
        
        if (($per_page = \Config::get('synergy.pagination.services.per_page'))) {
            $services = $services->paginate($per_page);
            
            $services->getEnvironment()->setViewName('site/pagination/services');
        } else {
            $services = $services->get();
        }
        
        $page->with(
            array(
                'services' => $services,
            )
        );
    }
    
    protected function actionAnyCategory(&$page)
    {
        $url = trim(\Request::segment(3), '/\\');

        $category = \Synergy\Modules\Services\Models\ServiceCategory::where('category_url', '=', $url)
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
        
        $services = $category->activeServices();

        if (($per_page = \Config::get('synergy.pagination.services.per_page'))) {
            $services = $services->paginate($per_page);
            
            $services->getEnvironment()->setViewName('site/pagination/services');
        } else {
            $services = $services->get();
        }

        $page->with(
            array(
                'category' => $category,
                'services' => $services,
            )
        );
    }
    
    // 
    
    protected function loadServicePage()
    {
        $url = trim(\Request::segment(2), '/\\');
        
        $service = \Synergy\Modules\Services\Models\Service::where('service_url', '=', $url)->where('service_active', '=', 1)->first();

        if (!$service) {
            return false;
        }
        
        if ($service->service_image) {
            $this->openGraph('image', $service->service_image);
        } elseif (($defaultImage = $this->getData('settings')->setting_default_image)) {
            $this->openGraph('image', $defaultImage);
        }
        
        $this->pageTitle($service->service_title)
            ->pageTitle($this->module->module_name)
            ->metaDescription($service->service_introduction)
            ->breadcrumb($this->module->module_name, $this->module->module_url)
            ->breadcrumb($service->service_title);
        
        $this->setPageView('services/service')->with(
            array(
                'service' => $service,
                'in_categories' => implode(', ', $service->categories->lists('category_title', 'category_id')),
            )
        );
        
        // Set sidebar data.

        \View::share(
            array(
                'sidebar_related_services' => $service->getRelatedServices(2),
            )
        );
    }
    
    /* Overrides */
    
    protected function handlePageRequest()
    {
        if (!$this->actionCallable) {
            $response = $this->loadServicePage();
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