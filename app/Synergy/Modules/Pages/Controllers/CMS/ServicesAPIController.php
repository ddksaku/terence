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
        
        $view->with(
            array(
                'pages' => \Synergy\Modules\Pages\Models\Page::orderBy('page_order', 'asc')->get()
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
            
            $parent_pages = \Synergy\Modules\Pages\Models\Page::where('page_id', '!=', $page->page_id)->get();
            
            // Pass view data.

            $data->with(
                array(
                    'page' => $page,
                    'new_page' => $new_page,
                    'upload_script' => \Config::get('synergy.uploads.images.upload'),
                    'parent_pages' => $parent_pages,
                )
            );
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
            }
            
            $page->page_title = $this->post->get('pagestitle');
            
            // Generate a URL from the given title.
            
            if (empty($page->page_url)) {
                $page_url_base = $page_url = \Str::slug($page->page_title);

                $appendix = 0;

                while(\Synergy\Modules\Pages\Models\Page::conflictingURL($page_url)) {
                    ++$appendix;

                    $page_url = "{$page_url_base}-{$appendix}";
                }

                $page->page_url = $page_url;
            }
            
            // 

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
            }
            
            //
            
            $page->page_image = $this->post->get('imagefile');
            $page->page_image_alt = $this->post->get('imagename');
            
            $page->page_introduction = $this->post->get('Textarealimit');
            
            $page->page_description = $this->post->get('description');
            $page->page_keywords = $this->post->get('tags_input');
            
            // 

            $page->save();

            $data['success'] = 1;
            $data['error'] = 0;
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