<?php

namespace Controllers\CMS;

class ModulesController extends \Controllers\CMSController
{
    protected function beforeProcessing()
    {
        $this->pageTitle('Modules');
        
        return parent::beforeProcessing();
    }
    
    protected function actionAnyIndex()
    {
        
    }
}