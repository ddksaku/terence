<?php

namespace Controllers;

class SiteController extends \Controllers\BaseController
{
    /*
     * 
     */
    
    protected function startUp()
    {
        \View::share(
            'news_module_url',
            \Models\CMS\Module\Registration::where('module_identifier', '=', 'news_module')->first()->module_url
        );
        
        \View::share(
            'services_module_url',
            \Models\CMS\Module\Registration::where('module_identifier', '=', 'services_module')->first()->module_url
        );
        
        \View::share(
            'portfolio_module_url',
            \Models\CMS\Module\Registration::where('module_identifier', '=', 'portfolio_module')->first()->module_url
        );
        
        \View::share(
            'gallery_module_url',
            \Models\CMS\Module\Registration::where('module_identifier', '=', 'gallery_module')->first()->module_url
        );

        \View::share('settings', $this->getData('settings'));
        
        \View::share('data', new \Models\Site\DataProvider);
        
        \View::share(
            'nav_links',
             \Synergy\Modules\Pages\Models\Page::where('page_active', '=', 1)
                 ->with(
                     array(
                         'children' => function($query) { 
                             $query->where('page_active', '=', 1);
                         }
                     )
                 )
                 ->where('page_nav', '=', 1)
                 ->where('page_parent_id', '=', 0)
                 ->orderBy('page_order', 'asc')
                 ->get()
        );
                     
        \View::share(
            'footer_links',
             \Synergy\Modules\Pages\Models\Page::where('page_active', '=', 1)
                 ->with(
                     array(
                         'children' => function($query) { 
                             $query->where('page_active', '=', 1);
                         }
                     )
                 )
                 ->where('page_footer', '=', 1)
                 ->where('page_parent_id', '=', 0)
                 ->orderBy('page_order', 'asc')
                 ->get()
        );

        \View::share('contact', $this->getData('contact'));
        
        \View::share(
            'sidebar_tags',
             \Models\Site\Tag::orderBy(\DB::raw('tag_name, LENGTH(tag_name)'))->get()
        );
    }
    
    protected function beforeProcessing()
    {
        $this->layoutView->with(
            array(
                'fb_admins' => \Models\User::where('user_facebook', '!=', '')
                    ->where('user_active', '=', 1)
                    ->where(\DB::raw('1'), '<', function ($query)
                    {
                        $query->select(\DB::raw('MAX(synergy_user_groups.group_level)'))
                                ->from('synergy_user_group_links')
                                ->join('synergy_user_groups', function ($join) {
                                    $join->on('synergy_user_groups.group_id', '=', 'synergy_user_group_links.group_id');
                                })
                                ->where('synergy_user_group_links.user_id', '=', \DB::raw('`synergy_users`.`user_id`'));
                    })
                    ->get(),
            )
        );
    }

    protected function handlePageRequest()
    {
        if (!$this->actionCallable) {
            $this->setResponseCode(404);

            $this->pageTitle('Not found');
            
            $this->setSublayoutEnabled(false);
            $this->setAutoloadPageView(false);
            
            $this->setPageView('404', false);
        } else {
            return $this->callActionHandler();
        }
    }

    protected function loadPageView()
    {
        try {
            $this->setPageView("{$this->prefix}/{$this->action}");
        } catch (\Exception $exception) {
            
        }
    }

    protected function afterProcessing()
    {
        $site_name = $this->getData('settings')->setting_name;

        $this->pageTitle($site_name);
    }
}