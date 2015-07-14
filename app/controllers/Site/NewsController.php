<?php

namespace Controllers\Site;

class NewsController extends \Controllers\SiteController
{
    protected $prefix = 'news';
    
    protected $module;
    
    /* */
    
    protected function startUp()
    {
        $this->module = \Models\CMS\Module\Registration::where('module_identifier', '=', 'news_module')->first();
        
        $this->routePrefix = $this->module->module_url;
        
        \View::share('module', $this->module);
        
        return parent::startUp();
    }
    
    protected function beforeProcessing()
    {
        \View::share(
            array(
                'sidebar_news_categories' => \Synergy\Modules\News\Models\NewsCategory::where('category_active', '=', 1)
                    ->orderBy('category_order', 'asc')
                    ->with('news')
                    ->get(),
            )
        );

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
        
        $articles = \Synergy\Modules\News\Models\NewsItem::where('news_publish_date', '<=', time())
                    ->where('news_active', '=', 1)
                    ->orderBy('news_publish_date', 'desc');
        
        if (($per_page = \Config::get('synergy.pagination.news.per_page'))) {
            $articles = $articles->paginate($per_page);
            
            $articles->getEnvironment()->setViewName('site/pagination/news');
        } else {
            $articles = $articles->get();
        }

        $page->with(
            array(
                'articles' => $articles,
            )
        );
    }
    
    protected function actionAnyCategory(&$page)
    {
        $url = trim(\Request::segment(3), '/\\');

        $category = \Synergy\Modules\News\Models\NewsCategory::where('category_url', '=', $url)
            ->where('category_active', '=', 1)
            ->first();
        
        if (!$category) {
            return false;
        }
        
        if ($category->category_image) {
            $this->openGraph('image', $category->category_image);
        } elseif (($defaultImage = $this->getData('settings')->setting_default_image)) {
            $this->openGraph('image', $defaultImage);
        }
        
        $this->pageTitle($category->category_title)
            ->pageTitle($this->module->module_name)
			->metaDescription($category->category_introduction)
            ->breadcrumb($this->module->module_name, $this->module->module_url)
            ->breadcrumb($category->category_title);
        
        $articles = $category->activeNews();

        if (($per_page = \Config::get('synergy.pagination.news.per_page'))) {
            $articles = $articles->paginate($per_page);
            
            $articles->getEnvironment()->setViewName('site/pagination/news');
        } else {
            $articles = $articles->get();
        }

        $page->with(
            array(
                'category' => $category,
                'articles' => $articles,
            )
        );
    }
    
    // 
    
    protected function loadNewsArticle(&$page)
    {
        $url = trim(\Request::segment(2), '/\\');
        
        $article = \Synergy\Modules\News\Models\NewsItem::where('news_url', '=', $url)->where('news_active', '=', 1)
		->where('news_publish_date', '<=', time())
		->first();

        if (!$article) {
            return false;
        }
        
        if ($article->news_image) {
            $this->openGraph('image', $article->news_image);
        } elseif (($defaultImage = $this->getData('settings')->setting_default_image)) {
            $this->openGraph('image', $defaultImage);
        }

        $this->pageTitle($article->news_title)
            ->pageTitle($this->module->module_name)
            ->breadcrumb($this->module->module_name, $this->module->module_url)
            ->breadcrumb($article->news_title);
        
        $this->metaDescription($article->news_introduction);

        $this->setPageView('news/article')->with(
            array(
                'article' => $article,
                'in_categories' => implode(', ', $article->categories->lists('category_title', 'category_id')),
            )
        );
        
        // Set sidebar data.

        \View::share(
            array(
                'sidebar_related_news' => $article->getRelatedArticles(2),
            )
        );
    }
    
    /* Overrides */
    
    protected function handlePageRequest()
    {
        if (!$this->actionCallable) {
            $response = $this->loadNewsArticle($this->pageView);
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