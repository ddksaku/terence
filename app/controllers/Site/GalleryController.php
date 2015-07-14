<?php

namespace Controllers\Site;

class GalleryController extends \Controllers\SiteController
{
    protected $prefix = 'gallery';

    protected $module;
    
    /* */
    
    protected function startUp()
    {
        $this->module = \Models\CMS\Module\Registration::where('module_identifier', '=', 'gallery_module')->first();
        
        $this->routePrefix = $this->module->module_url;
        
        \View::share('module', $this->module);
        
        return parent::startUp();
    }
    
    protected function beforeProcessing()
    {
        \View::share('gallery_module_url', $this->module->module_url);

        return parent::beforeProcessing();
    }

    protected function actionAnyIndex(&$page)
    {
        $this->pageTitle($this->module->module_name)
            ->breadcrumb($this->module->module_name);
        
        if ($this->module->page) {
            if ($this->module->page->page_image) {
                $this->openGraph('image', $this->module->page->page_image);
            } elseif (($defaultImage = $this->getData('settings')->setting_default_image)) {
                $this->openGraph('image', $defaultImage);
            }

            $this->metaDescription($this->module->page->page_introduction);
        }
        
        $albums = \Synergy\Modules\Gallery\Models\Album::orderBy('album_order', 'asc')->get();

        $page->with(
            array(
                'albums' => $albums,
            )
        );
    }
    
    protected function loadAlbum()
    {
        $url = trim(\Request::segment(2), '/\\');

        $album = \Synergy\Modules\Gallery\Models\Album::where('album_url', '=', $url)
            ->with('pictures')
            ->first();
        
        if (!$album) {
            return false;
        }

        if (($thumbnail = $album->thumbnail)) {
            $this->openGraph('album_image', $thumbnail->picture_file);
        } elseif (($defaultImage = $this->getData('settings')->setting_default_image)) {
            $this->openGraph('image', $defaultImage);
        }
        
        if ($this->module->page) {
            $this->metaDescription($this->module->page->page_introduction);
        }
        
        $this->pageTitle($album->album_name)
            ->pageTitle($this->module->module_name)
            ->breadcrumb($this->module->module_name, $this->module->module_url)
            ->breadcrumb($album->album_name);
        
        $this->setPageView('gallery/album')->with(
            array(
                'album' => $album,
            )
        );
    }
    
    /* Overrides */
    
    protected function handlePageRequest()
    {
        if (!$this->actionCallable) {
            $response = $this->loadAlbum();
        } else {
            $response = $this->callActionHandler();
        }

        if ($response === false) {
            $this->setResponseCode(404);

            $this->pageTitle('Not found');

            $this->setSublayoutEnabled(false);
            $this->setAutoloadPageView(false);

            $this->setPageView('404', false);
        } else {
            return $response;
        }
    }
}