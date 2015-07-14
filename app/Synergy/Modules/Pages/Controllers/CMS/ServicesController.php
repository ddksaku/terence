<?php

namespace Synergy\Modules\Services\Controllers\CMS;

class ServicesController extends \Controllers\CMSController
{
    protected $pagePrefix = 'modules';
    
    /* */
    
    protected $module;
    
    /* */
    
    protected function startUp()
    {
        $this->module = \Models\CMS\Module\Registration::where('module_identifier', '=', 'services_module')->first();
    }
    
    protected function beforeProcessing()
    {
        $this->pageTitle('Services');
        
        /* Get module scripts for layout */
        
        $this->addPageScript('modules/services/script.js');
        
        /* Pass module to view */

        $this->pageView->with('module', $this->module);
        
        return parent::beforeProcessing();
    }
    
    protected function actionAnyIndex()
    {
    }
}