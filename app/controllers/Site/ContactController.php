<?php

namespace Controllers\Site;

class ContactController extends \Controllers\SiteController
{
    protected $prefix = 'contact';
    
    protected $module;
    
    /* */
    
    protected function startUp()
    {
        $this->module = \Models\CMS\Module\Registration::where('module_identifier', '=', 'contact_module')->first();
        
        $this->routePrefix = $this->module->module_url;
        
        \View::share('module', $this->module);
        
        return parent::startUp();
    }
    
    protected function beforeProcessing()
    {
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
        
        $this->setPageView('contact/index')->with(
            array(
                //'page' => $this->module->page,
            )
        );
    }
}