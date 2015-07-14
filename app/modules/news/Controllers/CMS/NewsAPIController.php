<?php

namespace Synergy\Modules\News\Controllers\CMS;

class NewsAPIController extends \Controllers\CMSAPIController
{
    protected $ajaxPrefix = 'ajax/modules';
    
    /* */
    
    protected function actionAnyIndex()
    {
        $data = array();

        // 

        $view = $this->loadAjaxView('index');
        
        $view->with(
            array(
                'news' => \Synergy\Modules\News\Models\NewsItem::orderBy('news_publish_date', 'desc')
                    ->orderBy('news_order', 'desc')
                    ->get()
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
        
        if ($this->getData('user')->hasPermission('edit_news_settings')) {
            // Update permissions.

            $settings = \Models\Settings::first();

            $settings->setting_news_thumb_width = $this->post->get('setting_news_thumb_width');
            $settings->setting_news_resize_width = $this->post->get('setting_news_resize_width');
            $settings->setting_news_square_width = $this->post->get('setting_news_square_width');

            $settings->save();

            // 

            $data['success'] = 1;
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
        
        $categories = \Synergy\Modules\News\Models\NewsCategory::orderBy('category_order', 'asc')->get();
        
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

        // Attempt to find news.

        $news = \Synergy\Modules\News\Models\NewsItem::where('news_id', '=', $this->post->get('id'))->first();
            
        if (!$news) {
            $data['error'] = 1;
        } else {
            $news->news_active = ($this->post->get('status') == 1)
                                    ? 1
                                    : 0;

            $news->save();

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

        // Attempt to find news.

        $category = \Synergy\Modules\News\Models\NewsCategory::where('category_id', '=', $this->post->get('id'))->first();
            
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
        // Attempt to find news registration in database.
        
        if (($news_id = $this->input->get('id'))) {
            $news = \Synergy\Modules\News\Models\NewsItem::where('news_id', '=', $news_id)->first();
        } else {
            $news = null;
        }
        
        if ($news_id && !$news) {
            $data = array('error' => 1);
        } else {

            // Load form view.
 
            $data = $this->loadAjaxView('edit');
            
            // Get a blank news object.
            
            if (!$news) {
                $news = new \Synergy\Modules\News\Models\NewsItem;
                
                $new_news = true;
            } else {
                $new_news = false;
            }
            
            // Get potential parent news.
            
            $categories = \Synergy\Modules\News\Models\NewsCategory::orderBy(\DB::raw('LENGTH(category_title), category_title'))->get();
            
            // Pass view data.

            $data->with(
                array(
                    'news' => $news,
                    'new_news' => $new_news,
                    'upload_script' => \Config::get('synergy.uploads.images.upload').'/news',
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
        
        // Attempt to find news in database.

        if (($news_id = $this->post->get('id'))) {
            $news = \Synergy\Modules\News\Models\NewsItem::where('news_id', '=', $news_id)->first();
        } else {
            $news = null;
        }
        
        // 
        
        if ($news_id && !$news) {
            $data['success'] = 0;
            $data['error'] = 1;
        } else {
            if (!$news) {
                $news = new \Synergy\Modules\News\Models\NewsItem;
                
                $new_news = true;
            } else {
                $new_news = false;
            }
            
            $news->news_title = $this->post->get('newstitle');

            $news->news_active = ($this->post->get('statusactive') == 1)
                                    ? 1
                                    : 0;
            if ($new_news) {
                $news->news_order = $news->getNextOrder();
            }

            $news->news_image = $this->post->get('imagefile');
            $news->news_image_alt = $this->post->get('imagename');
            
            //
            
            $news->setPublishDate($this->post->get('datetimepicker'));
            
            //
            
            $news->news_introduction = $this->post->get('Textarealimit');
            
            $news->news_description = $this->post->get('description');

            // Generate URL from title.

            $news->news_url = \Str::slug($news->news_title);
            
            // Set post author.
            
            if ($new_news) {
                $news->news_author_id = $this->getData('user')->user_id;
            }

            //

            $news->save();
            
            /* Handle tags */
            
            $tags = explode(',', $this->post->get('tags_input'));
            
            $attachTags = array();
            
            $news->tags()->detach();
            
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
                $news->tags()->attach($attachTags);
            }

            // Undo previous relationships.
            
            foreach ($news->categories as $category) {
                $news->categories()->detach($category->category_id);
            }
            
            // Create new relationships.

            $categories = $this->post->get('chzn-select');

            if (!empty($categories)) {
                if (!is_array($categories)) {
                    $categories = array($categories);
                }

                $news->categories()->attach($categories);
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
                    
                    $file = \Synergy\Modules\News\Models\NewsFile::where('file_id', '=', $document)->first();
                    
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
                    
                    $file = new \Synergy\Modules\News\Models\NewsFile;
                    $file->file_news_id = $news->news_id;
                    $file->file_name = $name;
                    $file->file_original_name = $original_names[$index];
                    
                    $file->save();
                }
            }
            
            // 

            $data['success'] = 1;
            $data['error'] = 0;
            $data['new_entry'] = $new_news;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    /* */
    
    protected function actionPostCopy()
    {
        $data = array();
        
        // Attempt to find news in database.

        $news = \Synergy\Modules\News\Models\NewsItem::where('news_id', '=', $this->post->get('id'))->first();

        // 

        if (!$news) {
            $data['success'] = 0;
            $data['error'] = 1;
        } else {
            $copy = new \Synergy\Modules\News\Models\NewsItem;

            $copy->news_title           = $news->news_title;
            $copy->news_introduction    = $news->news_introduction;
            $copy->news_description     = $news->news_description;
            $copy->news_publish_date    = $news->news_publish_date;
            $copy->news_created         = $news->news_created;
            $copy->news_updated         = $news->news_updated;
            $copy->news_order           = $news->news_order + 1;
            $copy->news_views           = $news->news_views;
            $copy->news_active          = $news->news_active;
            $copy->news_social          = $news->news_social;
            $copy->news_image           = $news->news_image;
            $copy->news_image_alt       = $news->news_image_alt;
            $copy->news_author_id       = $news->news_author_id;

            if (!preg_match('/ \\(copy( [0-9]*)?\\)$/ism', $copy->news_title)) {
                $copy->news_title .= ' (copy)';
            }
            
            $rawTitle = preg_replace('/ \\(copy( [0-9]*)?\\)$/ism', '', $copy->news_title);
            
            $iteration = 2;
            while (\Synergy\Modules\News\Models\NewsItem::conflicts($copy->news_title, 'news_title')) {
                $copy->news_title = "{$rawTitle} (copy {$iteration})";
                ++$iteration;
            }

            $copy->news_url = \Str::slug($copy->news_title);

            //

            $copy->save();
            
            // Copy relationships.
            
            $categoryIds = $news->categories->lists('category_id');
            $copy->categories()->attach($categoryIds);
            
            // Copy files.
            
            foreach ($news->files as $file) {
                $file_copy = new \Synergy\Modules\News\Models\NewsFile;
                
                $file_copy->file_news_id        = $copy->news_id;
                $file_copy->file_name           = $file->file_name;
                $file_copy->file_original_name  = $file->file_original_name;
                $file_copy->file_created        = $file->file_created;
                
                $file_copy->save();
            }
            
            // Copy tags.
            
            $copy->tags()->attach($news->tags->lists('tag_id'));

            // 

            $data['success'] = 1;
            $data['error'] = 0;
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
            
            $categories = \Synergy\Modules\News\Models\NewsCategory::orderBy('category_order', 'asc')->get();
            
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
    
    protected function actionGetEditCategory()
    {
        // Attempt to find category registration in database.
        
        if (($category_id = $this->input->get('id'))) {
            $category = \Synergy\Modules\News\Models\NewsCategory::where('category_id', '=', $category_id)->first();
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
                $category = new \Synergy\Modules\News\Models\NewsCategory;
                
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
    
    //
    
    
    
    //
    
    protected function actionPostValidateNews()
    {
        $data = array();
        
        // 
        
        if (($news_id = $this->post->get('id'))) {
            $news = \Synergy\Modules\News\Models\NewsItem::where('news_id', '=', $news_id)->first();
        } else {
            $news = null;
        }
        
        //
        
        $new_title = $this->post->get('newstitle');
        
        if (!$news || $news->news_title != $new_title) {
            $data['entry_exists'] = \Synergy\Modules\News\Models\NewsItem::conflicts(
                $new_title,
                'news_title'
            ) ? 1 : 0;
        } else {
            $data['entry_exists'] = 0;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostValidateCategory()
    {
        $data = array();
        
        // 
        
        if (($category_id = $this->post->get('id'))) {
            $category = \Synergy\Modules\News\Models\NewsCategory::where('category_id', '=', $category_id)->first();
        } else {
            $category = null;
        }
        
        //
        
        $new_title = $this->post->get('categoriestitle');
        
        if (!$category || $category->category_title != $new_title) {
            $data['entry_exists'] = \Synergy\Modules\News\Models\NewsCategory::conflicts(
                $this->post->get('categoriestitle'),
                'category_title'
            ) ? 1 : 0;
        } else {
            $data['entry_exists'] = 0;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    //
    
    protected function actionPostEditCategory()
    {
        $data = array();
        
        // Attempt to find category in database.

        if (($category_id = $this->post->get('id'))) {
            $category = \Synergy\Modules\News\Models\NewsCategory::where('category_id', '=', $category_id)->first();
        } else {
            $category = null;
        }
        
        // 
        
        if ($category_id && !$category) {
            $data['success'] = 0;
            $data['error'] = 1;
        } else {
            if (!$category) {
                $category = new \Synergy\Modules\News\Models\NewsCategory;
                
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
        
        $target_news = $this->post->get('id');
        
        if (stristr($target_news, ',')) {
            $target_news = explode(',', $target_news);
        }
        
        if (is_array($target_news)) {
            $target = \Synergy\Modules\News\Models\NewsItem::whereIn('news_id', $target_news)->get();
        } else {
            $target = \Synergy\Modules\News\Models\NewsItem::where('news_id', '=', $target_news)->first();
        }
        
        if (!$target) {
            $data['error'] = 1;
        } else {
            
            if ($target instanceof \Illuminate\Database\Eloquent\Collection) {
                foreach ($target as $news) {
                    $news->delete();
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
            $target = \Synergy\Modules\News\Models\NewsCategory::whereIn('category_id', $target_category)->get();
        } else {
            $target = \Synergy\Modules\News\Models\NewsCategory::where('category_id', '=', $target_category)->first();
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