<?php

namespace Controllers\CMS;

class ModulesAPIController extends \Controllers\CMSAPIController
{
    protected function actionAnyIndex()
    {
        $data = array();
        
        $module_mgr = new \Models\CMS\Module\Manager;
        
        $module_mgr->registerNewModules();
        
        $module_mgr->loadModules();

        // 

        $view = $this->loadAjaxView('index');
        
        $view->module_mgr = $module_mgr;

        $data['html'] = $view->render();

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    // 
    
    protected function actionAnyStatus()
    {
        $data = array();

        $user = $this->getData('user');
            
        $module_mgr = new \Models\CMS\Module\Manager;
        
        // Attempt to find module registration in database.
        
        $module = \Models\CMS\Module\Registration::where('module_id', '=', $this->post->get('id'))->first();

        if (!$module) {
            $data['error'] = 1;
            $data['message'] = 'Module not found.';
        } elseif (
            ($blockedModules = $user->blockedModules->lists('module_name', 'module_id'))
            && isset($blockedModules[$module->module_id])
        ) {
            $data['error'] = 2;
            $data['message'] = 'You have been blacklisted from this module.';
        } elseif (!$user->hasPermission('activate_modules')) {
            $data['error'] = 3;
            $data['message'] = "You don't have permission to activate modules.";
        } else {
            if ($this->post->get('status') == 1) {
                $module_mgr->installModule($module);
            } else {
                $module_mgr->uninstallModule($module);
            }
            
            $module_mgr->rebuildModuleRoutes();

            $data['error'] = 0;
            $data['success'] = 1;
            
            $blockedModules = $user->blockedModules->lists('module_id');

            $query = \Models\CMS\Module\Registration::where('module_installed', '=', 1)
                ->where('module_view_level', '<=', $user->getHighestLevel())
                ->orderBy('module_order', 'asc');
            
            if (!empty($blockedModules)) {
                $query->whereNotIn('module_id', $blockedModules);
            }
            
             $data['modules'] = $query->get()
                ->toArray();
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    // 
    
    protected function actionPostOrder()
    {
        $data = array();
        
        $target = $this->post->get('id');
        $relative = $this->post->get('relative');

        if ($target && $relative) {
            $module_record = array();
            
            $modules = \Models\CMS\Module\Registration::orderBy('module_order', 'asc')->get();
            
            $position = 1;
            
            foreach ($modules as $module) {
                $module->module_order = $position;
                
                $module_record[$module->module_id] = $module;
                
                ++$position;
            }
            
            // Seek out the two modules and swap their orders.
            
            if (isset($module_record[$target]) && isset($module_record[$relative])) {
                $target_position = $module_record[$target]->module_order;

                $module_record[$target]->module_order = $module_record[$relative]->module_order;
                $module_record[$relative]->module_order = $target_position;
            }
            
            // Save changes
            
            foreach ($modules as $module) {
                $module->save();
            }
            
            // 
            
            $data['error'] = 0;
            
            // 

            $user = $this->getData('user');
            $blockedModules = $user->blockedModules->lists('module_id');

            $query = \Models\CMS\Module\Registration::where('module_installed', '=', 1)
                ->where('module_view_level', '<=', $user->getHighestLevel())
                ->orderBy('module_order', 'asc');
            
            if (!empty($blockedModules)) {
                $query->whereNotIn('module_id', $blockedModules);
            }
            
             $data['modules'] = $query->get()
                ->toArray();
        } else {
            $data['error'] = 1;
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    //
    
    protected function actionGetEdit()
    {
        $data = array();
        
        $user = $this->getData('user');
        
        // Attempt to find module registration in database.
        
        $module = \Models\CMS\Module\Registration::where('module_id', '=', $this->input->get('id'))
            ->where('module_view_level', '<=', $this->getData('user')->getHighestLevel())
            ->first();
        
        if (!$module) {
            $data['error'] = 1;
            $data['message'] = "You have don't have permission to edit this module.";
        } elseif (
            ($blockedModules = $user->blockedModules->lists('module_name', 'module_id'))
            && isset($blockedModules[$module->module_id])
        ) {
            $data['error'] = 2;
            $data['message'] = 'You have been blacklisted from this module.';
        } elseif (!$module->isInstalled() && !$user->hasPermission('activate_modules')) {
            $data['error'] = 3;
            $data['message'] = "You don't have permission to edit inactive modules.";
        } else {
            $data = $this->loadAjaxView('edit');
            
            // Get candidates for page attachment.
            
            $query = \Synergy\Modules\Pages\Models\Page::has('module', '<', 1)
				->orderBy('page_title', 'asc');
            
            if ($module->module_page_id) {
                $query->orWhere('page_id', '=', $module->module_page_id);
            }

            // 

            $data->with([
                'module' => $module,
                'groups' => \Models\UserGroupManager::getCMSGroups(),
                'users' => \Models\User::where('user_id', '!=', $this->getData('user')->user_id)
                    ->orderBy('user_surname', 'asc')
                    ->get(),
                'pages' => $query->get(),
            ]);

            $icon_collection = new \Models\CMS\Icons\Collection;
            
            $data->icons = $icon_collection->getIcons();
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }

    protected function actionPostEdit()
    {
        $user = $this->getData('user');
        
        $data = array();
        
        // Attempt to find module registration in database.
        
        $module = \Models\CMS\Module\Registration::where('module_id', '=', $this->post->get('id'))
            ->where('module_view_level', '<=', $user->getHighestLevel())
            ->first();
        
        if (!$module) {
            $data['success'] = 0;
            $data['error'] = 1;
            $data['message'] = "You have don't have permission to edit this module.";
        } elseif (
            ($blockedModules = $user->blockedModules->lists('module_name', 'module_id'))
            && isset($blockedModules[$module->module_id])
        ) {
            $data['error'] = 2;
            $data['message'] = 'You have been blacklisted from this module.';
        } elseif (!$module->isInstalled() && !$user->hasPermission('activate_modules')) {
            $data['error'] = 3;
            $data['message'] = "You don't have permission to edit inactive modules.";
        } else {
            $module_mgr = new \Models\CMS\Module\Manager;
            
            /* Save changes. */

            $module->module_icon = $this->post->get('menuicon');
            
            if (
                !$module->module_view_level
                || $user->hasPermission('manage_module_level_'.$module->module_view_level)
            ) {
                $selected_levels = $this->post->all();
                
                $lowestLevel = null;
                
                foreach ($selected_levels as $field => $value) {
                    if (substr($field, 0, 10) == 'userlevel_') {
                        $value = (int)$value;
                        
                        if (is_null($lowestLevel) || $value < $lowestLevel) {
                            $lowestLevel = $value;
                        }
                    }
                }
                
                $module->module_view_level = $lowestLevel;
            }
            
            // Delete old relationships (where applicable).
            
            $blockedIds = $module->blockedUsers->lists('user_id');
            
            if (!empty($blockedIds)) {
                $blockedUsers = \Models\User::whereIn('user_id', $blockedIds)->get();

                foreach ($blockedUsers as $blockedUser) {
                    if ($user->hasPermission('block_level_'.$blockedUser->getHighestLevel())) {
                        $module->blockedUsers()->detach($blockedUser->user_id);
                    }
                }
            }
            
            // Create new relationships.

            $blockedUsers = $this->post->get('chzn-select');

            if (!empty($blockedUsers)) {
                if (!is_array($blockedUsers)) {
                    $blockedUsers = array($blockedUsers);
                }

                $module->blockedUsers()->attach($blockedUsers);
            }
            
            // 
            
            $module->module_page_id = $this->post->get('module_page');
            
            // 
            
            $module->save();
            
            /*
             * If the module is linked to a page, override
             * module settings with page settings.
             * 
             */
            
            if ($module->page) {
                $module->setName($module->page->page_title);
                $module->module_url = $module->page->page_url;
            } else {
                $module->setName($this->post->get('name'));
                $module->module_url = \Str::slug($module->module_name);
            }

            $module->save();

            /* Activate or deactivate module */
             
            if ($user->hasPermission('activate_modules')) {
                if ($module->isInstalled() && $this->post->get('status') == 0) {
                    $module_mgr->uninstallModule($module);
                } elseif(!$module->isInstalled() && $this->post->get('status') == 1) {
                    $module_mgr->installModule($module);
                }
            }
            
            /* Rebuild routes */

            $module_mgr->rebuildModuleRoutes();

            /* Output data */
            
            $data['success'] = 1;

            $user = $this->getData('user');
            $blockedModules = $user->blockedModules->lists('module_id');

            $query = \Models\CMS\Module\Registration::where('module_installed', '=', 1)
                ->where('module_view_level', '<=', $user->getHighestLevel())
                ->orderBy('module_order', 'asc');
            
            if (!empty($blockedModules)) {
                $query->whereNotIn('module_id', $blockedModules);
            }
            
             $data['modules'] = $query->get()
                ->toArray();
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
} 