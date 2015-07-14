<?php

namespace Controllers\CMS;

class UploadAPIController extends \Controllers\CMSAPIController
{
    protected function actionAnyUpload($view, $action, $type = null)
    {
        $settings = $this->getData('settings');
        
        // Type-specific overrides.
        
        switch ($type) {
            case 'news':
                $thumbWidth = $settings->setting_news_thumb_width;
                $resizeWidth = $settings->setting_news_resize_width;
                $squareWidth = $settings->setting_news_square_width;
                break;
            case 'pages':
                $thumbWidth = $settings->setting_pages_thumb_width;
                $resizeWidth = $settings->setting_pages_resize_width;
                $squareWidth = $settings->setting_pages_square_width;
                break;
            case 'portfolio':
                $thumbWidth = $settings->setting_portfolio_thumb_width;
                $resizeWidth = $settings->setting_portfolio_resize_width;
                $squareWidth = $settings->setting_portfolio_square_width;
                break;
            case 'portfolioslideshow':
                $thumbWidth = $settings->setting_portfolio_slideshow_thumb_width;
                $resizeWidth = $settings->setting_portfolio_slideshow_resize_width;
                $squareWidth = $settings->setting_portfolio_slideshow_square_width;
                
                if (!$thumbWidth) {
                    $thumbWidth = $settings->setting_portfolio_thumb_width;
                }
                
                if (!$resizeWidth) {
                    $resizeWidth = $settings->setting_portfolio_resize_width;
                }
                
                if (!$squareWidth) {
                    $squareWidth = $settings->setting_portfolio_square_width;
                }
                
                break;
            case 'services':
                $thumbWidth = $settings->setting_services_thumb_width;
                $resizeWidth = $settings->setting_services_resize_width;
                $squareWidth = $settings->setting_services_square_width;
                break;
        }
        
        // Set missing values.
        
        if (empty($thumbWidth)) {
            $thumbWidth = $settings->setting_thumb_width;
        }
        
        if (empty($resizeWidth)) {
            $resizeWidth = $settings->setting_resize_width;
        }
        
        if (empty($squareWidth)) {
            $squareWidth = $settings->setting_square_width;
        }

        $handler = new \Models\CMS\File\UploadHandler(
            array(
                'thumb_width' => $thumbWidth,
                'resize_width' => $resizeWidth,
                'square_width' => $squareWidth,
            ),
            array(
                'upload_dir' => \Config::get('synergy.uploads.images.directory'),
                'upload_url' => \Config::get('synergy.uploads.images.url'),
            )
        );
        
        return $this->buildEmptyResponse(200, 'application/json');
    }
    
    //
    
    protected function actionAnyLogo()
    {
        $settings = $this->getData('settings');
        
        $handler = new \Models\CMS\File\UploadHandler(
            array(
                'thumb_width' => $settings->thumb_width,
                'resize_width' => $settings->resize_width,
                'square_width' => $settings->square_width,
                
                'thumb_width' => $settings->setting_logo_thumb_width,
                'resize_width' => $settings->setting_logo_resize_width,
            ),
            array(
                'upload_dir' => \Config::get('synergy.uploads.images.directory'),
                'upload_url' => \Config::get('synergy.uploads.images.url'),

                'image_versions' => array(
                    'thumb' => array(
                        'max_width' => $settings->setting_logo_thumb_width,
                        'max_height' => 10000
                    ),

                    'resize' => array(
                        'max_width' => $settings->setting_logo_resize_width,
                        'max_height' => 10000
                    ),
                )
            )
        );
        
        return $this->buildEmptyResponse(200, 'application/json');
    }

    // 

    protected function actionAnyDocument()
    {
        $handler = new \Models\CMS\File\UploadHandler(
            null,
            array(
                'upload_dir' => \Config::get('synergy.uploads.documents.directory'),
                'upload_url' => \Config::get('synergy.uploads.documents.url'),
            )
        );
        
        return $this->buildEmptyResponse(200, 'application/json');
    }
    
    protected function actionAnyGallery()
    {
        $config = \Config::get('synergy.uploads.gallery.sizes');
        
        $handler = new \Models\CMS\File\UploadHandler(
            null,
            array(
                'input_callback' => function($file, $index)
                {
                    if (($album_id = $this->post->get('albumid'))) {
                        $album = \Synergy\Modules\Gallery\Models\Album::where('album_id', '=', $album_id)->first();

                        $filename = $file->name;
                        $name = $file->original_name;

                        // Ordering

                        $album_mgr = new \Synergy\Modules\Gallery\Models\AlbumManager;
                        $album_mgr->incrementPictureOrder($album->album_id);

                        // 
                        
                        $picture = new \Synergy\Modules\Gallery\Models\Picture;
                        
                        $picture->picture_album_id = $album->album_id;
                        $picture->picture_file = $filename;
                        $picture->picture_title = $name;
                        $picture->picture_order = 1;
                        
                        $picture->save();
                    }
                },
                
                'upload_dir' => \Config::get('synergy.uploads.gallery.directory'),
                'upload_url' => \Config::get('synergy.uploads.gallery.url'),
                
                'image_versions' => array(
                    'm' => array(
                        'max_width' => $config['midImageX'],
                        'max_height' => $config['midImageY'],
                    ),

                    's' => array(
                        'max_width' => $config['smallImageX'],
                        'max_height' => $config['smallImageY'],
                        'crop' => true
                    )
                )
            )
        );
        
        return $this->buildEmptyResponse(200, 'application/json');
    }
    
    /* */
    
    protected function actionPostDeleteImage()
    {
        $image = $this->post->get('filename');
        
        if ($image) {
            \File::delete(\Config::get('synergy.uploads.images.directory').$image);
            \File::delete(\Config::get('synergy.uploads.images.directory')."resize/{$image}");
            \File::delete(\Config::get('synergy.uploads.images.directory')."square/{$image}");
            \File::delete(\Config::get('synergy.uploads.images.directory')."thumb/{$image}");
        }
        
        return $this->buildEmptyResponse(200, 'application/json');
    }
    
    protected function actionPostDeleteFile()
    {
        $file = $this->post->get('filename');
        
        if ($file) {
            \File::delete(\Config::get('synergy.uploads.documents.directory').$file);
        }
        
        return $this->buildEmptyResponse(200, 'application/json');
    }
} 