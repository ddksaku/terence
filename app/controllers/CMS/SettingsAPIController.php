<?php

namespace Controllers\CMS;

class SettingsAPIController extends \Controllers\CMSAPIController
{
    protected function actionAnyIndex()
    {
        $data = array();

        // 

        $view = $this->loadAjaxView('index');
        
        $view->with(
            array(
                'settings' => \Models\Settings::all()
            )
        );

        $data['html'] = $view->render();

        // Return response.

        return $this->buildAjaxResponse($data);
    }

    // 
    
    protected function actionGetEdit()
    {
        // Attempt to find module registration in database.
        
        $setting = \Models\Settings::first();
        
        if (!$setting) {
            $data = array('error' => 1);
        } else {
            $data = $this->loadAjaxView('edit');

            $data->with(
                array(
                    'setting' => $setting,
                    'upload_script' => \Config::get('synergy.uploads.logos.upload'),
                    'upload_image_script' => \Config::get('synergy.uploads.images.upload')
                )
            );
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostEdit()
    {
        $data = array();
        
        // Attempt to find module registration in database.
        
        $settings = \Models\Settings::first();
        
        if (!$settings) {
            $data['success'] = 0;
        } else {
            $user = $this->getData('user');
            
            $settings->setting_name = $this->post->get('name');

            $settings->setting_image_name = $this->post->get('imagename');

            if($user->hasPermission('edit_advanced_settings')) {
                $settings->setting_thumb_width = $this->post->get('thumb_width');
                $settings->setting_resize_width = $this->post->get('resize_width');
                $settings->setting_square_width = $this->post->get('square_width');

                $settings->setting_default_image_name = $this->post->get('default_imagename');
                $settings->setting_logo_resize_width = $this->post->get('logo_resize_width');
                $settings->setting_logo_thumb_width = $this->post->get('logo_thumb_width');
            }

            if ($user->hasPermission('edit_ga_code')) {
                $settings->setting_google_analytics = $this->post->get('google_analytics');
            }
            
            $settings->setting_facebook = $this->post->get('setting_facebook');
            $settings->setting_twitter = $this->post->get('setting_twitter');
            
            $newImage = $this->post->get('imagefile');
            
            if ($settings->setting_image && $newImage != $settings->setting_image) {
                \File::delete(\Config::get('synergy.uploads.images.directory').$settings->setting_image);
                \File::delete(\Config::get('synergy.uploads.images.directory')."resize/{$settings->setting_image}");
                \File::delete(\Config::get('synergy.uploads.images.directory')."square/{$settings->setting_image}");
                \File::delete(\Config::get('synergy.uploads.images.directory')."thumb/{$settings->setting_image}");
            }
            
            $settings->setting_image = $newImage;
            
            $newDefaultImage = $this->post->get('defaultimagefile');
            
            if ($settings->setting_default_image && $newDefaultImage != $settings->setting_default_image) {
                \File::delete(\Config::get('synergy.uploads.images.directory').$settings->setting_default_image);
                \File::delete(\Config::get('synergy.uploads.images.directory')."resize/{$settings->setting_default_image}");
                \File::delete(\Config::get('synergy.uploads.images.directory')."square/{$settings->setting_default_image}");
                \File::delete(\Config::get('synergy.uploads.images.directory')."thumb/{$settings->setting_default_image}");
            }
            
            $settings->setting_default_image = $newDefaultImage;
            
            $settings->save();

            /* Output data */
            
            $data['success'] = 1;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
} 