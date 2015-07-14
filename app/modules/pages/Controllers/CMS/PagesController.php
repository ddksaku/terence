<?php

namespace Synergy\Modules\Pages\Controllers\CMS;

class PagesController extends \Controllers\CMSController
{
    protected $pagePrefix = 'modules';
    protected $prefix = 'pages';
    
    /* */
    
    protected $module;
    
    /* */
    
    protected function startUp()
    {
        $this->module = \Models\CMS\Module\Registration::where('module_identifier', '=', 'pages_module')->first();
        
        $this->routePrefix = $this->module->module_url;
    }
    
    protected function beforeProcessing()
    {
        $user = $this->getData('user');
        
        if (
            $user->getHighestLevel() < $this->module->module_view_level
            || $this->module->blockedUsers()->where('synergy_module_blocked_user_links.user_id', '=', $user->user_id)->count() > 0
        ) {
            throw new \Synergy\Exceptions\SendResponse(\Redirect::to('cms'));
        }
        
        //
        
        $this->pageTitle($this->module->module_name);
        
        /* Get module scripts for layout */
        
        $this->addPageScript('modules/pages/script.js');
        
        /* Pass module to view */

        $this->pageView->with('module', $this->module);
        
        return parent::beforeProcessing();
    }
    
    protected function actionAnyIndex()
    {
    }
}