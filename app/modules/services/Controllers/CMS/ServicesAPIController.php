<?php

namespace Synergy\Modules\Services\Controllers\CMS;

class ServicesAPIController extends \Controllers\CMSAPIController
{
    protected $ajaxPrefix = 'ajax/modules';
    
    /* */
    
    protected function actionAnyIndex()
    {
        $data = array();

        // 

        $view = $this->loadAjaxView('index');
        
        $services = \Synergy\Modules\Services\Models\Service::orderBy('service_order', 'asc')->get();
        
        if ($services) {
            $services = new \CachingIterator($services->getIterator());
        }
        
        $view->with(
            array(
                'services' => $services
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
        
        if($this->getData('user')->hasPermission('edit_services_settings')) {
            // Update permissions.

            $settings = \Models\Settings::first();

            $settings->setting_services_thumb_width = $this->post->get('setting_services_thumb_width');
            $settings->setting_services_resize_width = $this->post->get('setting_services_resize_width');
            $settings->setting_services_square_width = $this->post->get('setting_services_square_width');

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
    
    protected function actionPostValidateCategory()
    {
        $data = array();
        
        // 
        
        if (($category_id = $this->post->get('id'))) {
            $category = \Synergy\Modules\Services\Models\ServiceCategory::where('category_id', '=', $category_id)->first();
        } else {
            $category = null;
        }
        
        //
        
        $new_title = $this->post->get('categoriestitle');
        
        if (!$category || $category->category_title != $new_title) {
            $data['entry_exists'] = \Synergy\Modules\Services\Models\ServiceCategory::conflicts(
                $this->post->get('categoriestitle'),
                'category_title'
            ) ? 1 : 0;
        } else {
            $data['entry_exists'] = 0;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostValidateService()
    {
        $data = array();
        
        // 
        
        if (($service_id = $this->post->get('id'))) {
            $service = \Synergy\Modules\Services\Models\Service::where('service_id', '=', $service_id)->first();
        } else {
            $service = null;
        }
        
        //
        
        $new_title = $this->post->get('servicestitle');
        
        if (!$service || $service->service_title != $new_title) {
            $data['entry_exists'] = \Synergy\Modules\Services\Models\Service::conflicts(
                $new_title,
                'service_title'
            ) ? 1 : 0;
        } else {
            $data['entry_exists'] = 0;
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
            $service_record = array();
            
            $services = \Synergy\Modules\Services\Models\Service::orderBy('service_order', 'asc')->get();
            
            $position = 1;
            
            foreach ($services as $service) {
                $service->service_order = $position;
                
                $service_record[$service->service_id] = $service;
                
                ++$position;
            }
            
            // Seek out the two services and swap their orders.
            
            if (isset($service_record[$target]) && isset($service_record[$relative])) {
                $target_position = $service_record[$target]->service_order;

                $service_record[$target]->service_order = $service_record[$relative]->service_order;
                $service_record[$relative]->service_order = $target_position;
            }
            
            // Save changes
            
            foreach ($services as $service) {
                $service->save();
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
 
    protected function actionPostCategoryOrder()
    {
        $data = array();
        
        $target = $this->post->get('id');
        $relative = $this->post->get('relative');

        if ($target && $relative) {
            $category_record = array();
            
            $categories = \Synergy\Modules\Services\Models\ServiceCategory::orderBy('category_order', 'asc')->get();
            
            $position = 1;
            
            foreach ($categories as $category) {
                $category->category_order = $position;
                
                $category_record[$category->category_id] = $category;
                
                ++$position;
            }
            
            // Seek out the two categories and swap their orders.
            
            if (isset($category_record[$target]) && isset($category_record[$relative])) {
                $target_position = $category_record[$target]->category_order;

                $category_record[$target]->category_order = $category_record[$relative]->category_order;
                $category_record[$relative]->category_order = $target_position;
            }
            
            // Save changes
            
            foreach ($categories as $category) {
                $category->save();
            }
            
            // 
            
            $data['error'] = 0;
        } else {
            $data['error'] = 1;
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    /* */
    
    protected function actionAnyCategories()
    {
        $data = array();

        // 

        $view = $this->loadAjaxView('categories');
        
        $categories = \Synergy\Modules\Services\Models\ServiceCategory::orderBy('category_order', 'asc')->get();
        
        if ($categories) {
            $categories = new \CachingIterator($categories->getIterator());
        }
        
        $view->with(
            array(
                'categories' => $categories
            )
        );

        $data['html'] = $view->render();

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    // 
    
    protected function actionAnyStatus()
    {
        $data = array();

        // Attempt to find service.

        $service = \Synergy\Modules\Services\Models\Service::where('service_id', '=', $this->post->get('id'))->first();
            
        if (!$service) {
            $data['error'] = 1;
        } else {
            $service->service_active = ($this->post->get('status') == 1)
                                    ? 1
                                    : 0;
            
            $service->save();

            $data['error'] = 0;
            $data['success'] = 1;
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    // 
    
    protected function actionAnyCategoryStatus()
    {
        $data = array();

        // Attempt to find service.

        $category = \Synergy\Modules\Services\Models\ServiceCategory::where('category_id', '=', $this->post->get('id'))->first();
            
        if (!$category) {
            $data['error'] = 1;
        } else {
            $category->category_active = ($this->post->get('status') == 1)
                                            ? 1
                                            : 0;
                                            
            $category->save();

            $data['error'] = 0;
            $data['success'] = 1;
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }

    //
    
    protected function actionGetEdit()
    {
        // Attempt to find service registration in database.
        
        if (($service_id = $this->input->get('id'))) {
            $service = \Synergy\Modules\Services\Models\Service::where('service_id', '=', $service_id)->first();
        } else {
            $service = null;
        }
        
        if ($service_id && !$service) {
            $data = array('error' => 1);
        } else {

            // Load form view.
 
            $data = $this->loadAjaxView('edit');
            
            // Get a blank service object.
            
            if (!$service) {
                $service = new \Synergy\Modules\Services\Models\Service;
                
                $new_service = true;
            } else {
                $new_service = false;
            }
            
            // Get potential parent services.
            
            $categories = \Synergy\Modules\Services\Models\ServiceCategory::orderBy(\DB::raw('LENGTH(category_title), category_title'))->get();
            
            // Pass view data.

            $data->with(
                array(
                    'service' => $service,
                    'new_service' => $new_service,
                    'upload_script' => \Config::get('synergy.uploads.images.upload').'/services',
                    'document_upload' => \Config::get('synergy.uploads.documents.upload'),
                    'categories' => $categories,
                )
            );
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostEdit()
    {
        $data = array();
        
        // Attempt to find service in database.

        if (($service_id = $this->post->get('id'))) {
            $service = \Synergy\Modules\Services\Models\Service::where('service_id', '=', $service_id)->first();
        } else {
            $service = null;
        }
        
        // 
        
        if ($service_id && !$service) {
            $data['success'] = 0;
            $data['error'] = 1;
        } else {
            if (!$service) {
                $service = new \Synergy\Modules\Services\Models\Service;
                
                $new_service = true;
            } else {
                $new_service = false;
            }
            
            $service->service_title = $this->post->get('servicestitle');

            $service->service_active = ($this->post->get('statusactive') == 1)
                                    ? 1
                                    : 0;

            //
            
            $service->service_image = $this->post->get('imagefile');
            $service->service_image_alt = $this->post->get('imagename');
            
            $service->service_introduction = $this->post->get('Textarealimit');
            
            $service->service_description = $this->post->get('description');
            
            //

            if ($new_service) {
                $service->service_order = $service->getNextOrder();
            }

            // 

            $service->service_url = \Str::slug($service->service_title);

            // 

            $service->save();
            
            /* Handle tags */
            
            $tags = explode(',', $this->post->get('tags_input'));
            
            $attachTags = array();
            
            $service->tags()->detach();
            
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
                $service->tags()->attach($attachTags);
            }

            // Undo previous relationships.
            
            foreach ($service->categories as $category) {
                $service->categories()->detach($category->category_id);
            }
            
            // Create new relationships.

            $categories = $this->post->get('chzn-select');
            
            if (!empty($categories)) {
                if (!is_array($categories)) {
                    $categories = array($categories);
                }

                $service->categories()->attach($categories);
            }
            
            \Models\Site\Tag::pruneUnusedTags();
            
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
                    
                    $file = \Synergy\Modules\Services\Models\ServiceFile::where('file_id', '=', $document)->first();
                    
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
                    
                    $file = new \Synergy\Modules\Services\Models\ServiceFile;
                    $file->file_service_id = $service->service_id;
                    $file->file_name = $name;
                    $file->file_original_name = $original_names[$index];
                    
                    $file->save();
                }
            }

            //

            $data['success'] = 1;
            $data['error'] = 0;
            $data['new_entry'] = $new_service;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    /* */
    
    protected function actionGetEditCategory()
    {
        // Attempt to find category registration in database.
        
        if (($category_id = $this->input->get('id'))) {
            $category = \Synergy\Modules\Services\Models\ServiceCategory::where('category_id', '=', $category_id)->first();
        } else {
            $category = null;
        }
        
        if ($category_id && !$category) {
            $data = array('error' => 1);
        } else {

            // Load form view.
 
            $data = $this->loadAjaxView('edit_category');
            
            // Get a blank category object.
            
            if (!$category) {
                $category = new \Synergy\Modules\Services\Models\ServiceCategory;
                
                $new_category = true;
            } else {
                $new_category = false;
            }

            // Pass view data.

            $data->with(
                array(
                    'category' => $category,
                    'new_category' => $new_category,
                    'upload_script' => \Config::get('synergy.uploads.images.upload'),
                )
            );
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostEditCategory()
    {
        $data = array();
        
        // Attempt to find category in database.

        if (($category_id = $this->post->get('id'))) {
            $category = \Synergy\Modules\Services\Models\ServiceCategory::where('category_id', '=', $category_id)->first();
        } else {
            $category = null;
        }
        
        // 
        
        if ($category_id && !$category) {
            $data['success'] = 0;
            $data['error'] = 1;
        } else {
            if (!$category) {
                $category = new \Synergy\Modules\Services\Models\ServiceCategory;
                
                $new_category = true;
            } else {
                $new_category = false;
            }
            
            $category->category_title = $this->post->get('categoriestitle');

            $category->category_active = ($this->post->get('statusactive') == 1)
                                    ? 1
                                    : 0;

            // 

            
            //
            
            $category->category_image = $this->post->get('imagefile');
            $category->category_image_alt = $this->post->get('imagename');
            
            $category->category_introduction = $this->post->get('Textarealimit');
			
			$category->category_description = $this->post->get('description');
            
            // Generate URL from category title.

            $category->category_url = \Str::slug($category->category_title);
			
			//
            
            if ($new_category) {
                $category->category_order = $category->getNextOrder();
            }
            
            //

            $category->save();
            
            /* Handle tags */
            
            $tags = explode(',', $this->post->get('tags_input'));
            
            $attachTags = array();
            
            $category->tags()->detach();
            
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
                $category->tags()->attach($attachTags);
            }
            
            \Models\Site\Tag::pruneUnusedTags();
            
            // 

            $data['success'] = 1;
            $data['error'] = 0;
            $data['new_entry'] = $new_category;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    /* */
    
    protected function actionAnyDelete()
    {
        $data = array();

        // Attempt to find user.
        
        $target_service = $this->post->get('id');
        
        if (stristr($target_service, ',')) {
            $target_service = explode(',', $target_service);
        }
        
        if (is_array($target_service)) {
            $target = \Synergy\Modules\Services\Models\Service::whereIn('service_id', $target_service)->get();
        } else {
            $target = \Synergy\Modules\Services\Models\Service::where('service_id', '=', $target_service)->first();
        }
        
        if (!$target) {
            $data['error'] = 1;
        } else {
            
            if ($target instanceof \Illuminate\Database\Eloquent\Collection) {
                foreach ($target as $service) {
                    $service->delete();
                }
            } else {
                $target->delete();
            }

            $data['error'] = 0;
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    /* */

    protected function actionAnyDeleteCategory()
    {
        $data = array();

        // Attempt to find user.
        
        $target_category = $this->post->get('id');
        
        if (stristr($target_category, ',')) {
            $target_category = explode(',', $target_category);
        }
        
        if (is_array($target_category)) {
            $target = \Synergy\Modules\Services\Models\ServiceCategory::whereIn('category_id', $target_category)->get();
        } else {
            $target = \Synergy\Modules\Services\Models\ServiceCategory::where('category_id', '=', $target_category)->first();
        }
        
        if (!$target) {
            $data['error'] = 1;
        } else {
            
            if ($target instanceof \Illuminate\Database\Eloquent\Collection) {
                foreach ($target as $category) {
                    $category->delete();
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