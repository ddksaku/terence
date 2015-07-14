<?php

namespace Synergy\Modules\Portfolio\Controllers\CMS;

class PortfolioAPIController extends \Controllers\CMSAPIController
{
    protected $ajaxPrefix = 'ajax/modules';
    
    /* */
    
    protected function actionAnyIndex()
    {
        $data = array();

        // 

        $view = $this->loadAjaxView('index');
        
        $portfolio = \Synergy\Modules\Portfolio\Models\Portfolio::orderBy('portfolio_order', 'asc')->get();
        
        if ($portfolio) {
            $portfolio = new \CachingIterator($portfolio->getIterator());
        }
        
        $view->with(
            array(
                'portfolio' => $portfolio
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
        
        if($this->getData('user')->hasPermission('edit_portfolio_settings')) {
            // Update permissions.

            $settings = \Models\Settings::first();

            $settings->setting_portfolio_thumb_width = $this->post->get('setting_portfolio_thumb_width');
            $settings->setting_portfolio_resize_width = $this->post->get('setting_portfolio_resize_width');
            $settings->setting_portfolio_square_width = $this->post->get('setting_portfolio_square_width');

            $settings->setting_portfolio_slideshow_disabled = ($this->post->get('slideshow_enabled') == 1)
                                                                ? 0 : 1;

            $settings->setting_portfolio_slideshow_thumb_width = $this->post->get('setting_portfolio_slideshow_thumb_width');
            $settings->setting_portfolio_slideshow_resize_width = $this->post->get('setting_portfolio_slideshow_resize_width');
            $settings->setting_portfolio_slideshow_square_width = $this->post->get('setting_portfolio_slideshow_square_width');

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
            $category = \Synergy\Modules\Portfolio\Models\PortfolioCategory::where('category_id', '=', $category_id)->first();
        } else {
            $category = null;
        }
        
        //
        
        $new_title = $this->post->get('categoriestitle');
        
        if (!$category || $category->category_title != $new_title) {
            $data['entry_exists'] = \Synergy\Modules\Portfolio\Models\PortfolioCategory::conflicts(
                $this->post->get('categoriestitle'),
                'category_title'
            ) ? 1 : 0;
        } else {
            $data['entry_exists'] = 0;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostValidatePortfolio()
    {
        $data = array();
        
        // 
        
        if (($portfolio_id = $this->post->get('id'))) {
            $portfolioItem = \Synergy\Modules\Portfolio\Models\Portfolio::where('portfolio_id', '=', $portfolio_id)->first();
        } else {
            $portfolioItem = null;
        }
        
        //
        
        $new_title = $this->post->get('portfoliotitle');
        
        if (!$portfolioItem || $portfolioItem->portfolio_title != $new_title) {
            $data['entry_exists'] = \Synergy\Modules\Portfolio\Models\Portfolio::conflicts(
                $new_title,
                'portfolio_title'
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
            $portfolio_record = array();
            
            $portfolio = \Synergy\Modules\Portfolio\Models\Portfolio::orderBy('portfolio_order', 'asc')->get();
            
            $position = 1;
            
            foreach ($portfolio as $portfolioItem) {
                $portfolioItem->portfolio_order = $position;
                
                $portfolio_record[$portfolioItem->portfolio_id] = $portfolioItem;
                
                ++$position;
            }
            
            // Seek out the two portfolio items and swap their orders.
            
            if (isset($portfolio_record[$target]) && isset($portfolio_record[$relative])) {
                $target_position = $portfolio_record[$target]->portfolio_order;

                $portfolio_record[$target]->portfolio_order = $portfolio_record[$relative]->portfolio_order;
                $portfolio_record[$relative]->portfolio_order = $target_position;
            }
            
            // Save changes
            
            foreach ($portfolio as $portfolioItem) {
                $portfolioItem->save();
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
            
            $categories = \Synergy\Modules\Portfolio\Models\PortfolioCategory::orderBy('category_order', 'asc')->get();
            
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
        
        $categories = \Synergy\Modules\Portfolio\Models\PortfolioCategory::orderBy('category_order', 'asc')->get();
        
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

        // Attempt to find portfolio.

        $portfolioItem = \Synergy\Modules\Portfolio\Models\Portfolio::where('portfolio_id', '=', $this->post->get('id'))->first();
            
        if (!$portfolioItem) {
            $data['error'] = 1;
        } else {
            $portfolioItem->portfolio_active = ($this->post->get('status') == 1)
                                    ? 1
                                    : 0;
            
            $portfolioItem->save();

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

        // Attempt to find category.

        $category = \Synergy\Modules\Portfolio\Models\PortfolioCategory::where('category_id', '=', $this->post->get('id'))->first();
            
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
        // Attempt to find portfolio registration in database.
        
        if (($portfolio_id = $this->input->get('id'))) {
            $portfolio = \Synergy\Modules\Portfolio\Models\Portfolio::where('portfolio_id', '=', $portfolio_id)->first();
        } else {
            $portfolio = null;
        }
        
        if ($portfolio_id && !$portfolio) {
            $data = array('error' => 1);
        } else {

            // Load form view.
 
            $data = $this->loadAjaxView('edit');
            
            // Get a blank portfolio object.
            
            if (!$portfolio) {
                $portfolio = new \Synergy\Modules\Portfolio\Models\Portfolio;
                
                $new_portfolio = true;
            } else {
                $new_portfolio = false;
            }
            
            // Get potential parents.
            
            $categories = \Synergy\Modules\Portfolio\Models\PortfolioCategory::orderBy(\DB::raw('LENGTH(category_title), category_title'))->get();
            
            // Pass view data.
            
            $settings = \Models\Settings::first();

            $data->with(
                array(
                    'portfolio' => $portfolio,
                    'new_portfolio' => $new_portfolio,
                    'upload_script' => \Config::get('synergy.uploads.images.upload').'/portfolio',
                    'slideshow_upload_script' => \Config::get('synergy.uploads.images.upload').'/portfolioslideshow',
                    'document_upload' => \Config::get('synergy.uploads.documents.upload'),
                    'categories' => $categories,
                    'slideshow_enabled' => (!$settings->setting_portfolio_slideshow_disabled),
                )
            );
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostEdit()
    {
        $data = array();
        
        // Attempt to find portfolio in database.

        if (($portfolio_id = $this->post->get('id'))) {
            $portfolio = \Synergy\Modules\Portfolio\Models\Portfolio::where('portfolio_id', '=', $portfolio_id)->first();
        } else {
            $portfolio = null;
        }
        
        // 
        
        if ($portfolio_id && !$portfolio) {
            $data['success'] = 0;
            $data['error'] = 1;
        } else {
            if (!$portfolio) {
                $portfolio = new \Synergy\Modules\Portfolio\Models\Portfolio;
                
                $new_portfolio = true;
            } else {
                $new_portfolio = false;
            }
            
            //

            $portfolio->portfolio_title = $this->post->get('portfoliotitle');

            $portfolio->portfolio_active = ($this->post->get('statusactive') == 1)
                                    ? 1
                                    : 0;

            //
            
            $portfolio->portfolio_image = $this->post->get('imagefile');
            $portfolio->portfolio_image_alt = $this->post->get('imagename');
            
            $portfolio->portfolio_introduction = $this->post->get('Textarealimit');
            
            $portfolio->portfolio_description = $this->post->get('description');
            
            //

            if ($new_portfolio) {
                $portfolio->portfolio_order = $portfolio->getNextOrder();
            }

            // 

            $portfolio->portfolio_url = \Str::slug($portfolio->portfolio_title);

            // 

            $portfolio->save();
            
            // 
            
            $settings = \Models\Settings::first();

            if (!$settings->setting_portfolio_slideshow_disabled) {
                // Remove deleted images / update saved images

                foreach ($portfolio->slideshow as $slideshowImage) {
                    if(!$this->input->get("save_imagefile_{$slideshowImage->image_id}")) {
                        $slideshowImage->delete();
                    } else {
                        $dirty = false;

                        if (($imageName = $this->input->get("save_imagename_{$slideshowImage->image_id}")) != $slideshowImage->image_alt) {
                            $slideshowImage->image_alt = $imageName;
                            $dirty = true;
                        }

                        if (($imageTitle = $this->input->get("save_imagetitle_{$slideshowImage->image_id}")) != $slideshowImage->image_title) {
                            $slideshowImage->image_title = $imageTitle;
                            $dirty = true;
                        }
                        
                        if (($imageOrder = $this->input->get("save_imageorder_{$slideshowImage->image_id}")) != $slideshowImage->image_order) {
                            $slideshowImage->image_order = $imageOrder;
                            $dirty = true;
                        }

                        if ($dirty) {
                            $slideshowImage->save();
                        }
                    }
                }

                unset($portfolio->slideshow);

                // Save slideshow images.

                $inputs = $this->post->all();

                $slideshowImages = array();

                foreach ($inputs as $field => $name) {
                    if (strstr($field, 'imagefile_')) {
                        $parts = explode('_', $field);

                        if ($parts[0] == 'imagefile' && !empty($parts[1])) {
                            $slideshowImages[$this->post->get($field)] = array(
                                'alt' => $this->post->get("imagename_{$parts[1]}"),
                                'title' => $this->post->get("imagetitle_{$parts[1]}"),
                                'order' => $this->post->get("imageorder_{$parts[1]}"),
                            );
                        }
                    }
                }

                $currentImages = $portfolio->slideshow->lists('image_id', 'image_filename');

                foreach ($slideshowImages as $slideshowImage => $slideshowImageData) {
                    if (!isset($currentImages[$slideshowImage])) {
                        $image = new \Synergy\Modules\Portfolio\Models\PortfolioSlideshowImage;
                        $image->portfolio_id = $portfolio->portfolio_id;
                        $image->image_filename = $slideshowImage;
                        $image->image_alt = $slideshowImageData['alt'];
                        $image->image_title = $slideshowImageData['title'];
                        $image->image_order = $slideshowImageData['order'];
                        $image->save();
                    }
                }
            }
            
            /* Handle tags */
            
            $tags = explode(',', $this->post->get('tags_input'));
            
            $attachTags = array();
            
            $portfolio->tags()->detach();
            
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
                $portfolio->tags()->attach($attachTags);
            }

            // Undo previous relationships.
            
            foreach ($portfolio->categories as $category) {
                $portfolio->categories()->detach($category->category_id);
            }
            
            // Create new relationships.

            $categories = $this->post->get('chzn-select');
            
            if (!empty($categories)) {
                if (!is_array($categories)) {
                    $categories = array($categories);
                }

                $portfolio->categories()->attach($categories);
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
                    
                    $file = \Synergy\Modules\Portfolio\Models\PortfolioFile::where('file_id', '=', $document)->first();
                    
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
                    
                    $file = new \Synergy\Modules\Portfolio\Models\PortfolioFile;
                    $file->file_portfolio_id = $portfolio->portfolio_id;
                    $file->file_name = $name;
                    $file->file_original_name = $original_names[$index];
                    
                    $file->save();
                }
            }
            
            //

            $data['success'] = 1;
            $data['error'] = 0;
            $data['new_entry'] = $new_portfolio;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    /* */
    
    protected function actionGetEditCategory()
    {
        // Attempt to find category registration in database.
        
        if (($category_id = $this->input->get('id'))) {
            $category = \Synergy\Modules\Portfolio\Models\PortfolioCategory::where('category_id', '=', $category_id)->first();
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
                $category = new \Synergy\Modules\Portfolio\Models\PortfolioCategory;
                
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
            $category = \Synergy\Modules\Portfolio\Models\PortfolioCategory::where('category_id', '=', $category_id)->first();
        } else {
            $category = null;
        }
        
        // 
        
        if ($category_id && !$category) {
            $data['success'] = 0;
            $data['error'] = 1;
        } else {
            if (!$category) {
                $category = new \Synergy\Modules\Portfolio\Models\PortfolioCategory;
                
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
        
        $target_portfolio = $this->post->get('id');
        
        if (stristr($target_portfolio, ',')) {
            $target_portfolio = explode(',', $target_portfolio);
        }
        
        if (is_array($target_portfolio)) {
            $target = \Synergy\Modules\Portfolio\Models\Portfolio::whereIn('portfolio_id', $target_portfolio)->get();
        } else {
            $target = \Synergy\Modules\Portfolio\Models\Portfolio::where('portfolio_id', '=', $target_portfolio)->first();
        }
        
        if (!$target) {
            $data['error'] = 1;
        } else {
            
            if ($target instanceof \Illuminate\Database\Eloquent\Collection) {
                foreach ($target as $portfolio) {
                    $portfolio->delete();
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
            $target = \Synergy\Modules\Portfolio\Models\PortfolioCategory::whereIn('category_id', $target_category)->get();
        } else {
            $target = \Synergy\Modules\Portfolio\Models\PortfolioCategory::where('category_id', '=', $target_category)->first();
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