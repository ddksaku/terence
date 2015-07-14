<?php

namespace Controllers\CMS;

class SettingsController extends \Controllers\CMSController
{
    protected function beforeProcessing()
    {
        $this->pageTitle('Settings');
        
        return parent::beforeProcessing();
    }
    
    protected function actionAnyIndex()
    {
        
    }
}