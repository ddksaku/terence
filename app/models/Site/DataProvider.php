<?php

namespace Models\Site;

class DataProvider
{
    public function getNews($amount = null)
    {
        $query = \Synergy\Modules\News\Models\NewsItem::where('news_publish_date', '<=', time())
            ->where('news_active', '=', 1)
            ->orderBy('news_publish_date', 'desc');
        
        if ($amount) {
            $query = $query->limit($amount);
        }
        
        return ($amount == 1)
                ? $query->first()
                : $query->get();
    }
    
    public function getNewsCount()
    {
        return \Synergy\Modules\News\Models\NewsItem::where('news_publish_date', '<=', time())
            ->where('news_active', '=', 1)
            ->orderBy('news_publish_date', 'desc')
            ->count();
    }
    
    public function getServices($amount = null)
    {
        $query = \Synergy\Modules\Services\Models\Service::where('service_active', '=', 1)
            ->orderBy('service_order', 'asc');
        
        if ($amount) {
            $query = $query->limit($amount);
        }
        
        return ($amount == 1)
                ? $query->first()
                : $query->get();
    }
    
    public function getServicesCount()
    {
        return \Synergy\Modules\Services\Models\Service::where('service_active', '=', 1)
            ->orderBy('service_order', 'asc')
            ->count();
    }
	
	public function getPortfolio($amount = null)
    {
        $query = \Synergy\Modules\Portfolio\Models\Portfolio::where('portfolio_active', '=', 1)
            ->orderBy('portfolio_order', 'asc');
        
        if ($amount) {
            $query = $query->limit($amount);
        }
        
        return ($amount == 1)
                ? $query->first()
                : $query->get();
    }
    
    public function getPortfolioCount()
    {
        return \Synergy\Modules\Portfolio\Models\Portfolio::where('portfolio_active', '=', 1)
            ->count();
    }
}