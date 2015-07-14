<?php

namespace Synergy\Modules\Gallery\Controllers\CMS;

class GalleryController extends \Controllers\CMSController
{
    protected $pagePrefix = 'modules';
    protected $prefix = 'gallery';
    
    /* */
    
    protected $module;
    
    /* */
    
    protected function startUp()
    {
        $this->module = \Models\CMS\Module\Registration::where('module_identifier', '=', 'gallery_module')->first();
        
        $this->routePrefix = $this->module->module_url;
    }
    
    protected function beforeProcessing()
    {
        if ($this->getData('user')->getHighestLevel() < $this->module->module_view_level) {
            throw new \Synergy\Exceptions\SendResponse(\Redirect::to('cms'));
        }
        
        //
        
        $this->pageTitle($this->module->module_name);
        
        /* Get module scripts for layout */
        
        $this->addPageScript('modules/gallery/script.js');
        
        $this->addPageStylesheet('components/blueimp/css/jquery.fileupload-ui.css');
        
        /* Pass module to view */

        $this->pageView->with('module', $this->module);
        
        return parent::beforeProcessing();
    }
    
    protected function actionAnyIndex()
    {
    }
}