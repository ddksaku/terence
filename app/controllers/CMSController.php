<?php

namespace Controllers;

class CMSController extends \Controllers\BaseController
{
    protected $section = 'cms';
    
    /*
     * 
     */
    
    protected function beforeProcessing()
    {
        $user = $this->getData('user');
        $blockedModules = $user->blockedModules->lists('module_id');

        $query = \Models\CMS\Module\Registration::where('module_installed', '=', 1)
            ->where('module_view_level', '<=', $user->getHighestLevel())
            ->orderBy('module_order', 'asc');
        
        if (!empty($blockedModules)) {
            $query->whereNotIn('module_id', $blockedModules);
        }

        $this->layoutView->modules = $query->get();
    }

    protected function handlePageRequest()
    {
        if (!$this->actionCallable) {
            $this->setResponseCode(404);

            $this->pageTitle('Not found');
            
            $this->setSublayoutEnabled(false);
            $this->setAutoloadPageView(false);
            
            $this->setPageView('404', false);
        } else {
            return $this->callActionHandler();
        }
    }

    protected function loadPageView()
    {
        try {
            $this->setPageView("{$this->prefix}/{$this->action}");
        } catch (\Exception $exception) {
            
        }
    }

    protected function afterProcessing()
    {
        $site_name = $this->getData('settings')->setting_name;
        
        if (!$site_name) {
            $site_name = 'Synergy';
        }
        
        $this->pageTitle($site_name);
    }
}