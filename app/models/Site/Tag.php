<?php

namespace Models\Site;

class Tag extends \Models\BaseModel
{
    protected $table = 'synergy_tags';
    
    protected $primaryKey = 'tag_id';
    
    public $timestamps = false;
    
    /* */
    
    public static function getTagAlways($tagName)
    {
        $tag = static::where('tag_name', '=', $tagName)->first();
                
        if (!$tag) {
            $tag = new static;
            $tag->tag_name = $tagName;
            $tag->tag_url = \Str::slug($tag->tag_name);
            $tag->save();
        }
        
        return $tag;
    }
    
    /* */
    
    public static function pruneUnusedTags()
    {
        $tags = static::where(\DB::raw(0), '=', function($where) {
                $where->select(\DB::raw('COUNT(*)'))
                    ->from('synergy_page_tag_links')
                    ->where('synergy_page_tag_links.tag_id', '=', \DB::raw("`synergy_tags`.`tag_id`"));
            })
            ->where(\DB::raw(0), '=', function($where) {
                $where->select(\DB::raw('COUNT(*)'))
                    ->from('synergy_service_tag_links')
                    ->where('synergy_service_tag_links.tag_id', '=', \DB::raw("`synergy_tags`.`tag_id`"));
            })
            ->where(\DB::raw(0), '=', function($where) {
                $where->select(\DB::raw('COUNT(*)'))
                    ->from('synergy_service_category_tag_links')
                    ->where('synergy_service_category_tag_links.tag_id', '=', \DB::raw("`synergy_tags`.`tag_id`"));
            })
            ->where(\DB::raw(0), '=', function($where) {
                $where->select(\DB::raw('COUNT(*)'))
                    ->from('synergy_news_tag_links')
                    ->where('synergy_news_tag_links.tag_id', '=', \DB::raw("`synergy_tags`.`tag_id`"));
            })
            ->where(\DB::raw(0), '=', function($where) {
                $where->select(\DB::raw('COUNT(*)'))
                    ->from('synergy_news_category_tag_links')
                    ->where('synergy_news_category_tag_links.tag_id', '=', \DB::raw("`synergy_tags`.`tag_id`"));
            })
            ->where(\DB::raw(0), '=', function($where) {
                $where->select(\DB::raw('COUNT(*)'))
                    ->from('synergy_portfolio_tag_links')
                    ->where('synergy_portfolio_tag_links.tag_id', '=', \DB::raw("`synergy_tags`.`tag_id`"));
            })
            ->where(\DB::raw(0), '=', function($where) {
                $where->select(\DB::raw('COUNT(*)'))
                    ->from('synergy_portfolio_category_tag_links')
                    ->where('synergy_portfolio_category_tag_links.tag_id', '=', \DB::raw("`synergy_tags`.`tag_id`"));
            })
            ->get();

        if ($tags) {
            foreach ($tags as $tag) {
                $tag->delete();
            }
        }
    }
    
    /* */
    
    public function delete()
    {
        
        
        return parent::delete();
    }
    
}