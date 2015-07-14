<?php

namespace Synergy\Modules\Pages\Controllers\CMS;

class PagesAPIController extends \Controllers\CMSAPIController
{
    protected $ajaxPrefix = 'ajax/modules';
    
    /* */
    
    protected function actionAnyIndex()
    {
        $data = array();

        // 

        $view = $this->loadAjaxView('index');
        
        $pages = \Synergy\Modules\Pages\Models\Page::orderBy('page_order', 'asc')->get();
        
        if ($pages) {
            $pages = new \CachingIterator($pages->getIterator());
        }

        $view->with(
            array(
                'pages' => $pages
            )
        );

        $data['html'] = $view->render();

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    //

    protected function actionGetSettings()
    {
        $data = $this->loadAjaxView('settings');

        $data->with(
            array(
                'settings' => \Models\Settings::first(),
            )
        );
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostSettings()
    {
        $data = array();
        
        if($this->getData('user')->hasPermission('edit_pages_settings')) {
            // Update permissions.

            $settings = \Models\Settings::first();

            $settings->setting_pages_thumb_width = $this->post->get('setting_pages_thumb_width');
            $settings->setting_pages_resize_width = $this->post->get('setting_pages_resize_width');
            $settings->setting_pages_square_width = $this->post->get('setting_pages_square_width');

            $settings->save();

            // 

            $data['success'] = 1;
        } else {
            $data['error'] = 1;
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
            $page_record = array();
            
            $pages = \Synergy\Modules\Pages\Models\Page::orderBy('page_order', 'asc')->get();
            
            $position = 1;
            
            foreach ($pages as $page) {
                $page->page_order = $position;
                
                $page_record[$page->page_id] = $page;
                
                ++$position;
            }
            
            // Seek out the two pages and swap their orders.
            
            if (isset($page_record[$target]) && isset($page_record[$relative])) {
                $target_position = $page_record[$target]->page_order;

                $page_record[$target]->page_order = $page_record[$relative]->page_order;
                $page_record[$relative]->page_order = $target_position;
            }
            
            // Save changes
            
            foreach ($pages as $page) {
                $page->save();
            }
            
            // 
            
            $data['error'] = 0;
        } else {
            $data['error'] = 1;
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    // 
    
    protected function actionAnyStatus()
    {
        $data = array();

        // Attempt to find page.

        $page = \Synergy\Modules\Pages\Models\Page::where('page_id', '=', $this->post->get('id'))->first();
            
        if (!$page) {
            $data['error'] = 1;
        } else {
            $page->page_active = ($this->post->get('status') == 1)
                                    ? 1
                                    : 0;
            
            $page->save();

            $data['error'] = 0;
            $data['success'] = 1;
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }

    //
    
    protected function actionGetEdit()
    {
        // Attempt to find page registration in database.
        
        if (($page_id = $this->input->get('id'))) {
            $page = \Synergy\Modules\Pages\Models\Page::where('page_id', '=', $page_id)->first();
        } else {
            $page = null;
        }
        
        if ($page_id && !$page) {
            $data = array('error' => 1);
        } else {
            // Load form view.
 
            $data = $this->loadAjaxView('edit');
            
            // Get a blank page object.
            
            if (!$page) {
                $page = new \Synergy\Modules\Pages\Models\Page;
                
                $new_page = true;
            } else {
                $new_page = false;
            }
            
            // Get potential parent pages.
            
            $parentQuery = \Synergy\Modules\Pages\Models\Page::orderBy(\DB::raw('page_title', 'asc'));
            
            if ($page->page_id) {
                $parentQuery->where('page_id', '!=', $page->page_id);
            }
            
            $parent_pages = $parentQuery->get();
            
            // Pass view data.

            $data->with(
                array(
                    'page' => $page,
                    'new_page' => $new_page,
                    'upload_script' => \Config::get('synergy.uploads.images.upload').'/pages',
                    'document_upload' => \Config::get('synergy.uploads.documents.upload'),
                    'parent_pages' => $parent_pages,
                    'show_homepage' => ($page->page_homepage || \Synergy\Modules\Pages\Models\Page::where('page_homepage', '=', 1)->count() == 0),
                )
            );
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostValidatePage()
    {
        $data = array();
        
        // 
        
        if (($page_id = $this->post->get('id'))) {
            $page = \Synergy\Modules\Pages\Models\Page::where('page_id', '=', $page_id)->first();
        } else {
            $page = null;
        }
        
        //
        
        $new_title = $this->post->get('pagestitle');
        
        if (!$page || $page->page_title != $new_title) {
            $data['entry_exists'] = \Synergy\Modules\Pages\Models\Page::conflicts(
                $new_title,
                'page_title'
            ) ? 1 : 0;
        } else {
            $data['entry_exists'] = 0;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostEdit()
    {
        $data = array();
        
        // Attempt to find page in database.

        if (($page_id = $this->post->get('id'))) {
            $page = \Synergy\Modules\Pages\Models\Page::where('page_id', '=', $page_id)->first();
        } else {
            $page = null;
        }
        
        // 
        
        if ($page_id && !$page) {
            $data['success'] = 0;
            $data['error'] = 1;
        } else {
            if (!$page) {
                $page = new \Synergy\Modules\Pages\Models\Page;
                
                $new_page = true;
            } else {
                $new_page = false;
            }
            
            $page->page_title = $this->post->get('pagestitle');
            
            // Generate a URL from the given title.
            
            $page->page_url = \Str::slug($page->page_title);

            // 
            
            if (($this->post->has('homepage') && $this->post->get('homepage') == 1)) {
                $page->page_homepage = 1;

                $page->page_template = 'index';
            } else {
                $page->page_homepage = 0;

                $page->page_template = 'generic';
            }

            $page->page_active = ($this->post->get('statusactive') == 1)
                                    ? 1
                                    : 0;
            
            $page->page_nav = ($this->post->get('navstatusactive') == 1)
                                    ? 1
                                    : 0;
            
            $page->page_footer = ($this->post->get('footerstatusactive') == 1)
                                    ? 1
                                    : 0;
            
            // 
            
            $parent_id = $this->post->get('chzn-select');

            if (is_array($parent_id)) {
                $parent_id = reset($parent_id);
            }
            
            if ($parent_id) {
                $page->page_parent_id = $parent_id;
            } else {
                $page->page_parent_id = 0;
            }
            
            // 
            
            if ($new_page) {
                $page->page_order = $page->getNextOrder();
            }
            
            // 
            
            $page->page_image = $this->post->get('imagefile');
            $page->page_image_alt = $this->post->get('imagename');
            
            $page->page_introduction = $this->post->get('Textarealimit');
            
            $page->page_description = $this->post->get('description');

            // 
			
            $page->page_seo_title = $this->post->get('seopagestitle'); 

            // 
            
            $page->page_url = \Str::slug($page->page_title);

            // 

            $page->save();
            
            /* Handle tags */
            
            $tags = explode(',', $this->post->get('tags_input'));
            
            $attachTags = array();
            
            $page->tags()->detach();
            
            foreach ($tags as $tag) {
                $tag = strtolower(trim($tag));
                
                if (!$tag) {
                    continue;
                }
                
                $dbTag = \Models\Site\Tag::getTagAlways($tag);

                if (!isset($attachTags[$dbTag->tag_id])) {
                    $attachTags[$dbTag->tag_id] = $dbTag->tag_id;
                }
            }
            
            if (!empty($attachTags)) {
                $page->tags()->attach($attachTags);
            }
            
            \Models\Site\Tag::pruneUnusedTags();
            
            // 
            
            if ($page->module) {
                $page->module->setName($page->page_title);
                $page->module->module_url = $page->page_url;
                $page->module->save();
                
                $module_mgr = new \Models\CMS\Module\Manager;
                $module_mgr->rebuildModuleRoutes();
                
                /* Pass data through for menu update */

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
            
            //

            $delete_documents = $this->post->get('file-delete-list');
            
            if ($delete_documents) {
                $delete_documents = explode(',', $delete_documents);
                
                foreach ($delete_documents as $document) {
                    $document = trim($document);
                    
                    if (!$document) {
                        continue;
                    }
                    
                    // 
                    
                    $file = \Synergy\Modules\Pages\Models\PageFile::where('file_id', '=', $document)->first();
                    
                    if ($file) {
                        $file->delete();
                    }
                }
            }
            
            // 
            
            $documents = $this->post->get('file-documents');
            
            if ($documents) {
                $documents = explode('*', $documents);
                $original_names = explode('*', $this->post->get('file-documents1'));
                
                foreach ($documents as $index => $name) {
                    $name = trim($name);
                    
                    if (!$name) {
                        continue;
                    }
                    
                    $file = new \Synergy\Modules\Pages\Models\PageFile;
                    $file->file_page_id = $page->page_id;
                    $file->file_name = $name;
                    $file->file_original_name = $original_names[$index];
                    
                    $file->save();
                }
            }
            
            // 

            $data['success'] = 1;
            $data['error'] = 0;
            $data['new_entry'] = $new_page;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionAnyDelete()
    {
        $data = array();

        // Attempt to find user.
        
        $target_page = $this->post->get('id');
        
        if (stristr($target_page, ',')) {
            $target_page = explode(',', $target_page);
        }
        
        if (is_array($target_page)) {
            $target = \Synergy\Modules\Pages\Models\Page::whereIn('page_id', $target_page)->get();
        } else {
            $target = \Synergy\Modules\Pages\Models\Page::where('page_id', '=', $target_page)->first();
        }
        
        if (!$target) {
            $data['error'] = 1;
        } else {
            
            if ($target instanceof \Illuminate\Database\Eloquent\Collection) {
                foreach ($target as $page) {
                    $page->delete();
                }
            } else {
                $target->delete();
            }

            $data['error'] = 0;
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }
} 