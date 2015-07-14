<?php

namespace Models\CMS\Module;

class Manager
{
    protected $moduleDirectory;

    protected $modulesPresent = array();

    protected $modulesRegistered = array();

    /* */
    
    public function __construct($directory = null)
    {
        if (!$directory) {
            $directory = \Config::get('synergy.modules.directory');
        }
        
        $this->setModuleDirectory($directory);
    }
    
    public function setModuleDirectory($directory)
    {
        $this->moduleDirectory = $directory;
        
        return $this;
    }
    
    public function registerNewModules()
    {
        $this->enumeratePresentModules();
        
        // Register any unknown modules.
        
        foreach ($this->getPresentModules() as $module) {
            if (!$module->isRegistered()) {
                $registration = $module->getRegistration();
                
                $registration->module_name = $module->getName();
                $registration->module_identifier = $module->getIdentifier();
                $registration->module_url = $module->moduleURL;
                $registration->module_path = $module->modulePath;
                $registration->module_icon = $module->moduleIcon;
                
                $registration->save();
                
                $module->setIsRegistered();
            }
        }

        return $this;
    }
    
    public function loadModules()
    {
        $this->modulesRegistered = Registration::orderBy('module_order', 'asc')->get();
        
        return $this;
    }
    
    public function enumeratePresentModules()
    {
        $directories = \File::directories($this->moduleDirectory);

        foreach ($directories as $directory) {
            $module_meta = $this->getModuleMeta($directory);
            
            if ($module_meta) {
                // Record this meta.

                $this->modulesPresent[] = $module_meta;
            }
        }

        return $this;
    }
    
    public function getPresentModules()
    {
        return $this->modulesPresent;
    }
    
    public function countPresentModules()
    {
        return count($this->modulesPresent);
    }
    
    /* */
    
    public function getModules()
    {
        return $this->modulesRegistered;
    }
    
    public function countModules()
    {
        return count($this->modulesRegistered);
    }
    
    /* */
    
    public function installModule($registration)
    {
        if (!$registration->isInstalled()) {
            $meta = $this->getModuleMeta($registration->module_path);
            
            if ($meta) {
                // Publish files.

                $this->publishFiles($meta->modulePath, $meta->modulePublishMap);

                // Update database.

                $registration->module_installed = 1;

                $registration->save();
            }
        }
        
        return $this;
    }
    
    public function uninstallModule($registration)
    {
        if ($registration->isInstalled()) {
            // Get meta data for module.

            $meta = $this->getModuleMeta($registration->module_path);
            
            if ($meta) {
                // Un-publish files.
                
                $this->unpublishFiles($meta->modulePublishMap);
                
                // Update database.
                
                $registration->module_installed = 0;

                $registration->save();
            }

            // 
            
        }

        return $this;
    }
    
    /* */
    
    public function getModuleMeta($module_path)
    {
        if (file_exists(($module_meta_file = $module_path.'/meta.php'))) {
            $meta = include $module_meta_file;

            if (
                is_array($meta)
                && !empty($meta['identifier'])
                && !empty($meta['url'])
            ) {
                $module_meta = new Meta;

                $module_meta->modulePath = $module_path;
                $module_meta->setIdentifier($meta['identifier']);
                $module_meta->moduleURL = $meta['url'];

                if (!empty($meta['name'])) {
                    $module_meta->setName($meta['name']);
                }
                
                if (!empty($meta['icon'])) {
                    $module_meta->moduleIcon = $meta['icon'];
                }
                
                if (!empty($meta['controller'])) {
                    $module_meta->modulePrimaryController = $meta['controller'];
                }
                
                if (!empty($meta['site_controller'])) {
                    $module_meta->moduleSiteController = $meta['site_controller'];
                }
                
                if (!empty($meta['routes'])) {
                    $module_meta->setRoutes($meta['routes']);
                }

                if (!empty($meta['publish'])) {
                    $module_meta->modulePublishMap = $meta['publish'];
                }

                // Lookup an installation for this module.

                $registration = Registration::where('module_identifier', '=', $module_meta->getIdentifier())->first();

                if ($registration) {
                    $module_meta->setIsRegistered();
                } else {
                    $registration = new Registration;
                }

                $module_meta->setIsInstalled($registration->isInstalled());

                $module_meta->setRegistration($registration);
            }
        }

        // 
        
        return isset($module_meta)
                ? $module_meta
                : false;
    }
    
    /* */
    
    public function publishFiles($source, $map)
    {
        $source = rtrim($source, ' \\/');

        foreach ($map as $from => $to) {
            $from = $source.'/'.trim($from, ' \\/');
            $to = rtrim($to, ' \\/');
            
            if (is_dir($from)) {
                \File::copyDirectory($from, $to);
            } else {
                \File::copy($from, $to);
            }
        }
    }
    
    public function unpublishFiles($map)
    {
        // This needs rewriting
        
        return false;
        
        foreach ($map as $destination) {
            $destination = trim($destination, ' \\/');
            
            if (is_dir($destination)) {
                \File::deleteDirectory($destination);
            } else {
                \File::delete($destination);
            }
        }
    }
    
    /* */
    
    public function rebuildModuleRoutes()
    {
        $builder = new \Models\CMS\Routes\Builder;

        $builder->setOutputPath(app_path().'/module_routes.php');
        
        // 
        
        $this->loadModules();
        
        foreach ($this->getModules() as $module) {
            if ($module->isInstalled()) {
                $meta = $this->getModuleMeta($module->module_path);
                
                /* Build primary CMS route. */

                if (($primary_controller = $meta->getPrimaryController())) {
                    $builder->addRule('cms', $module->getURL(), $primary_controller);
                }
                
                /* Build primary site route, if linked to a page. */

                if ($module->page && ($site_controller = $meta->getSiteController())) {
                    $builder->addRule('', $module->getURL(), $site_controller);
                }

                /* Build additional routes specified in the module meta. */

                if ($meta) {
                    foreach ($meta->getRoutes() as $group => $route) {
                        if (is_string($group)) {
                            foreach($route as $grouped_route) {
                                list($url, $controller) = $grouped_route;

                                $regex = isset($grouped_route[2])
                                            ? $grouped_route[2]
                                            : '';

                                $builder->addRule($group, $url, $controller, $regex);
                            }
                        } else {
                            list($url, $controller) = $route;

                            $regex = isset($route[2])
                                        ? $route[2]
                                        : '';

                            $builder->addRule('', $url, $controller, $regex);
                        }
                    }
                }
            }
        }
        
        // 

        $builder->compileRoutes();
    }
}