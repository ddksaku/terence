<?php

namespace Controllers\CMS;

class RootController extends \Controllers\CMSController
{
    protected function actionAnyIndex()
    {
        
    }
    
    protected function actionAnyFixAlbums()
    {
        $albums = \Synergy\Modules\Gallery\Models\Album::all();
        
        $fixed = 0;
        
        foreach ($albums as $album) {
            if (empty($album->album_url)) {
                $album->album_url = \Str::slug($album->album_name);
                $album->save();
                ++$fixed;
            }
        }
        
        echo "Done ({$fixed})";
        
        exit;
    }
    
    protected function actionAnyRegen()
    {
        $pages = \Synergy\Modules\Pages\Models\Page::all();
        
        foreach ($pages as $page) {
            $page->page_url = \Str::slug($page->page_title);
            $page->save();
        }
        
        $pages = \Synergy\Modules\News\Models\NewsItem::all();
        
        foreach ($pages as $page) {
            $page->news_url = \Str::slug($page->news_title);
            $page->save();
        }
        
        $pages = \Synergy\Modules\Services\Models\Service::all();
        
        foreach ($pages as $page) {
            $page->service_url = \Str::slug($page->service_title);
            $page->save();
        }
        
        $pages = \Synergy\Modules\Services\Models\ServiceCategory::all();
        
        foreach ($pages as $page) {
            $page->category_url = \Str::slug($page->category_title);
            $page->save();
        }
        
        $pages = \Synergy\Modules\News\Models\NewsCategory::all();
        
        foreach ($pages as $page) {
            $page->category_url = \Str::slug($page->category_title);
            $page->save();
        }
    }

    protected function actionAnyLogin()
    {
        $user = $this->getData('user');
        
        if ($user->isLoggedIn() && $user->hasPermission('access_cms')) {
            return \Redirect::to('cms');
        }

        $this->addPageScript('login_php.js');

        $this->setPageLayout('login');
        
        $this->pageTitle('Login');
    }
    
    protected function actionAnyPasswordReset(&$view, $reset_code = null)
    {
        $user = $this->getData('user');
        
        if ($user->isLoggedIn()) {
            if ($user->hasPermission('access_cms')) {
                return \Redirect::to('cms');
            } else {
                return \Redirect::to(\Request::root());
            }
        }
        
        //
        
        $view->with(
            [
                'reset_code' => htmlspecialchars($reset_code),
            ]
        );
        
        // 
        
        $this->addPageScript('forgot_php.js');
        
        $this->setPageLayout('login');
        
        $this->pageTitle('Reset Password');
    }
    
    protected function actionAnyForgotPassword()
    {
        $user = $this->getData('user');
        
        if ($user->isLoggedIn()) {
            if ($user->hasPermission('access_cms')) {
                return \Redirect::to('cms');
            } else {
                return \Redirect::to(\Request::root());
            }
        }
        
        $this->addPageScript('forgot_password.js');
        
        $this->setPageLayout('login');
        
        $this->pageTitle('Forgot Password');
    }
    
    protected function actionAnyLogout()
    {
        \Models\Zenith::logout();
        
        return \Redirect::to(\Request::segment(1).'/login');
    }
    
    /* */
    
    protected function file_download($type, $file_id)
    {
        switch ($type) {
            case 'news':
                $file = \Synergy\Modules\News\Models\NewsFile::find($file_id);
                break;
            case 'pages':
                $file = \Synergy\Modules\Pages\Models\PageFile::find($file_id);
                break;
            case 'services':
                $file = \Synergy\Modules\Services\Models\ServiceFile::find($file_id);
                break;
            case 'portfolio':
                $file = \Synergy\Modules\Portfolio\Models\PortfolioFile::find($file_id);
                break;
            default:
                die('Invalid file type.');
        }
        
        if (!$file) {
            die('Invalid file ID.');
        } elseif (!is_file(($fileLocation = \Config::get('synergy.uploads.documents.directory').$file->file_name))) {
            die("File not found at '{$fileLocation}'");
        } else {
            return \Response::download(
                $fileLocation,
                $file->file_original_name
            );
        }
    }
    
}