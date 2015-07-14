<?php

namespace Models\CMS\Module;

class Meta
{
    public $modulePath;
    public $moduleIdentifier;
    public $moduleName;
    public $modulePublishMap;
    public $moduleURL;
    public $modulePrimaryController;
    public $moduleSiteController;

    protected $moduleIsInstalled = false;
    
    protected $moduleIsRegistered = false;
    protected $moduleRegistration;
    
    protected $moduleRoutes = array();

    /* */
    
    public function setName($name)
    {
        $this->moduleName = trim($name);
    }
    
    public function getName()
    {
        return $this->moduleName;
    }
    
    /* */

    public function setRoutes($routes)
    {
        $this->moduleRoutes = $routes;
    }
    
    public function getRoutes()
    {
        return $this->moduleRoutes;
    }

    /* */
    
    public function setIdentifier($identifier)
    {
        $this->moduleIdentifier = strtolower(trim($identifier));
    }
    
    public function getIdentifier()
    {
        return $this->moduleIdentifier;
    }
    
    /* */
    
    public function setRegistration($registration)
    {
        $this->moduleRegistration = $registration;
    }
    
    public function getRegistration()
    {
        return $this->moduleRegistration;
    }
    
    /* */
    
    public function setIsInstalled($value = true)
    {
        return $this->moduleIsInstalled = $value ? true : false;
    }
    
    public function isInstalled()
    {
        return $this->moduleIsInstalled;
    }
    
    /* */
    
    public function setIsRegistered($value = true)
    {
        return $this->moduleIsRegistered = $value ? true : false;
    }
    
    public function isRegistered()
    {
        return $this->moduleIsRegistered;
    }
    
    /* */
    
    public function getPrimaryController()
    {
        return $this->modulePrimaryController;
    }
    
    public function getSiteController()
    {
        return $this->moduleSiteController;
    }
}