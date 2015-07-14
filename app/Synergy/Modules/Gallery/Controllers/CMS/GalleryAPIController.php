<?php

namespace Synergy\Modules\Gallery\Controllers\CMS;

class GalleryAPIController extends \Controllers\CMSAPIController
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
                'services' => \Synergy\Modules\Services\Models\Service::orderBy('service_order', 'asc')->get()
            )
        );

        $data['html'] = $view->render();

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    /* */
    
    protected function actionAnyAlbums()
    {
        $data = array();

        // 

        $view = $this->loadAjaxView('albums');
        
        $view->with(
            array(
                'albums' => \Synergy\Modules\Gallery\Models\Album::orderBy('album_order', 'asc')->get()
            )
        );

        $data['html'] = $view->render();

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionAnyPictures()
    {
        $data = array();

        // 

        $album = \Synergy\Modules\Gallery\Models\Album::where('album_id', '=', $this->input->get('album'))->first();
            
        if (!$album) {
            $data['l'] = $this->input->get('album');
            $data['error'] = 1;
        } else {
            $view = $this->loadAjaxView('pictures');

            $view->with(
                array(
                    'album' => $album,
                    'upload_script' => \Config::get('synergy.uploads.gallery.upload'),
                )
            );

            $data['html'] = $view->render();
        }

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

    protected function actionPostDeletePicture()
    {
        $data = array();

        // Attempt to find service.

        $picture = \Synergy\Modules\Gallery\Models\Picture::where('picture_id', '=', $this->post->get('id'))->first();

        if (!$picture) {
            $data['error'] = 1;
        } else {
            //$album = \Synergy\Modules\Gallery\Models\Album::where('service_id', '=', $album_id)->first();
            
            $picture->delete();

            $data['error'] = 0;
            $data['success'] = 1;
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    //

    protected function actionPostPictureReorder()
    {
        $data = array();

        $data['error'] = 0;
        $data['success'] = 1;

        // Attempt to find service.
        
        if (is_array(($orders = $this->post->get('posi')))) {
            $position = 1;
            
            $album_mgr = new \Synergy\Modules\Gallery\Models\AlbumManager;
            
            foreach ($orders as $order) {
                $album_mgr->setPictureOrder($order, $position);
                ++$position;
            }
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostAlbumReorder()
    {
        $data = array();


        // Attempt to find service.
        
        if (is_array(($orders = $this->post->get('data')))) {
            $album_mgr = new \Synergy\Modules\Gallery\Models\AlbumManager;
            
            $position = 1;            
            foreach ($orders as $order) {
                $album_mgr->setAlbumOrder($order, $position);
                ++$position;
            }

            $data['error'] = 0;
            $data['success'] = 1;
        } else {
            $data['success'] = 0;
            $data['error'] = 1;
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostMovePicture()
    {
        $data = array();

        $new_album = \Synergy\Modules\Gallery\Models\Album::where('album_id', '=', $this->post->get('newalbumid'))->first();
        
        $picture = \Synergy\Modules\Gallery\Models\Picture::where('picture_id', '=', $this->post->get('picid'))->first();

        // Attempt to find service.
        
        if (!$new_album || !$picture) {
            $data['error'] = 1;
            $data['success'] = 0;
        } else {
            $picture->picture_album_id = $new_album->album_id;
            $picture->save();
            
            $data['error'] = 0;
            $data['success'] = 1;
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }


    //
    
    protected function actionGetEditAlbum()
    {
        // Attempt to find service registration in database.
        
        if (($album_id = $this->input->get('id'))) {
            $album = \Synergy\Modules\Gallery\Models\Album::where('album_id', '=', $album_id)->first();
        } else {
            $album = null;
        }
        
        if ($album_id && !$album) {
            $data = array('error' => 1);
        } else {
            // Load form view.
 
            $data = $this->loadAjaxView('editalbum');

            // Get a blank service object.

            if (!$album) {
                // Shift the order of existing albums; new album is placed at the top.
                
                $album_mgr = new \Synergy\Modules\Gallery\Models\AlbumManager;
                $album_mgr->incrementAlbumOrder();
                
                // Create new instance
                
                $album = new \Synergy\Modules\Gallery\Models\Album;
                
                $new_album = true;
            } else {
                $new_album = false;
            }

            // Pass view data.

            $data->with(
                array(
                    'album' => $album,
                    'new_album' => $new_album,
                    'upload_script' => \Config::get('synergy.uploads.gallery.upload'),
                )
            );
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostEditAlbum()
    {
        $data = array();
        
        // Attempt to find album in database.

        if (($album_id = $this->post->get('id_edit'))) {
            $album = \Synergy\Modules\Gallery\Models\Album::where('album_id', '=', $album_id)->first();
        } else {
            $album = null;
        }
        
        //
        
        $album_name = $this->post->get('name');
        
        // 
        
        if ($album_id && !$album) {
            $data['success'] = 0;
            $data['error'] = 1;
        } elseif (\Synergy\Modules\Gallery\Models\Album::conflicts($album_name, 'album_name') && (!$album || $album->album_name != $album_name)) {
            $data['success'] = 0;
            $data['error'] = 2;
        } else {
            if (!$album) {
                $album = new \Synergy\Modules\Gallery\Models\Album;
            }
            
            $album->album_name = $album_name;
            $album->album_url = \Str::slug($album_name);
            
            if (($thumbnail_id = $this->post->get('thumbPreview'))) {
                $album->album_thumbnail_id = $thumbnail_id;
            }

            $album->save();

            //
            
            $data['success'] = 1;
            $data['error'] = 0;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    // 
    
    protected function actionPostEditAlbumTitle()
    {
        $data = array();
        
        // Attempt to find album in database.

        $album = \Synergy\Modules\Gallery\Models\Album::where('album_id', '=', $this->post->get('id'))->first();

        // 

        $album_name = $this->post->get('title');
        
        // 
        
        if (!$album) {
            $data['success'] = 0;
            $data['error'] = 1;
        } elseif (\Synergy\Modules\Gallery\Models\Album::conflicts($album_name, 'album_name') && $album->album_name != $album_name) {
            $data['success'] = 0;
            $data['error'] = 2;
        } else {
            $album->album_name = $album_name;

            $album->save();

            //
            
            $data['success'] = 1;
            $data['error'] = 0;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    // 
    
    protected function actionPostEditPictureTitle()
    {
        $data = array();
        
        // Attempt to find album in database.

        $picture = \Synergy\Modules\Gallery\Models\Picture::where('picture_id', '=', $this->post->get('id'))->first();

        // 
        
        if (!$picture) {
            $data['success'] = 0;
            $data['error'] = 1;
        } else {            
            $picture->picture_title = $this->post->get('title');

            $picture->save();

            //
            
            $data['success'] = 1;
            $data['error'] = 0;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    /* */
    
    protected function actionGetUpload()
    {
        // Attempt to find album in database.

        $album = \Synergy\Modules\Gallery\Models\Album::where('album_id', '=', $this->input->get('album'))->first();
        
        if (!$album) {
            $data = array('error' => 1);
        } else {
            // Load form view.
 
            $data = $this->loadAjaxView('upload');

            // Pass view data.

            $data->with(
                array(
                    'album' => $album,
                    'upload_script' => \Config::get('synergy.uploads.gallery.upload'),
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
            $category->category_keywords = $this->post->get('tags_input');
            
            // 

            $category->save();

            $data['success'] = 1;
            $data['error'] = 0;
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
    
    protected function actionPostDeleteAlbum()
    {
        $data = array();

        // Attempt to find album.
        
        $album = \Synergy\Modules\Gallery\Models\Album::where('album_id', '=', $this->post->get('id'))->first();
        
        if (!$album) {
            $data['error'] = 1;
            $data['success'] = 0;
        } else {
            $album_mgr = new \Synergy\Modules\Gallery\Models\AlbumManager;
            
            $album_mgr->deleteAlbum($album);
            
            $data['error'] = 0;
            $data['success'] = 1;
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