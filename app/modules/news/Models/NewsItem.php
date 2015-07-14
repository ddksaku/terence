<?php

namespace Synergy\Modules\News\Models;

class NewsItem extends \Models\BaseModel
{
    protected $table = 'synergy_news';
    
    protected $primaryKey = 'news_id';
    
    const CREATED_AT = 'news_created';
    const UPDATED_AT = 'news_updated';
    
    /* */
    
    public function tags()
    {
        return $this->belongsToMany(
            '\\Models\\Site\\Tag',
            'synergy_news_tag_links',
            'news_id',
            'tag_id'
        )->orderBy('tag_name', 'ASC');
    }
    
    public function files()
    {
        return $this->hasMany('\\Synergy\\Modules\\News\\Models\\NewsFile', 'file_news_id');
    }
    
    public function categories()
    {
        return $this->belongsToMany(
            '\\Synergy\\Modules\\News\\Models\\NewsCategory',
            'synergy_news_category_links',
            'news_id',
            'category_id'
        );
    }
    
    public function author()
    {
        return $this->belongsTo('\\Models\\User', 'news_author_id');
    }
    
    /* */
    
    public function setPublishDate($date)
    {
        $datetime = \Datetime::createFromFormat('Y-m-d H:i:s', $date);
        
        $this->news_publish_date = $datetime->getTimestamp();
        
        return $this;
    }
    
    public function getPublishDate($format = 'Y-m-d H:i:s')
    {
        return date($format, $this->news_publish_date);
    }
    
    public function hasPublishDate()
    {
        return $this->news_publish_date ? true : false;
    }
    
    protected function deleteImages($file = null)
    {
        if (func_num_args() == 0) {
            $file = $this->news_image;
        }

        if ($file) {
            $in_use = static::where('news_image', '=', $file)->count();

            if ($in_use < 1) {
                $directory = \Config::get('synergy.uploads.images.directory');

                \File::delete("{$directory}{$file}");
                \File::delete("{$directory}resize/{$file}");
                \File::delete("{$directory}square/{$file}");
                \File::delete("{$directory}thumb/{$file}");
            }
        }
    }
    
    /* */
    
    public function delete()
    {
        $this->categories()->detach();
        
        $this->tags()->detach();
        
        \Models\Site\Tag::pruneUnusedTags();

        foreach ($this->files as $file) {
            $file->delete();
        }
        
        // 
        
        $delete_file = $this->news_image;
        
        $return = parent::delete();

        $this->deleteImages($delete_file);
        
        // 
        
        $this->reorderEntriesSequentially();
        
        //

        return $return;
    }
    
    public function save(array $options = array())
    {        
        $original_image = $this->getOriginal('news_image');

        $return = parent::save($options);

        if ($original_image && $original_image != $this->news_image) {
            $this->deleteImages($original_image);
        }
        
        return $return;
    }
    
    /* */
    
    public function getNextOrder()
    {
        $last_order = (int)\DB::table('synergy_news')
            ->orderBy('news_order', 'desc')
            ->limit(1)
            ->pluck('news_order');
        
        return ($last_order + 1);
    }
    
    //
    
    public function reorderEntriesSequentially()
    {
        $news = static::orderBy('news_order', 'asc')->get();
        
        $position = 1;
        
        foreach ($news as $news_item) {
            $news_item->news_order = $position;
            
            $news_item->save();
            
            ++$position;
        }
    }
    
    /* */
    
    public function getRelatedArticles($limit = 0)
    {
        $categories = $this->categories->lists('category_id');
        
        if (!empty($categories)) {
            $query = static::orderBy('news_publish_date', 'desc')
                ->where('news_id', '!=', $this->news_id)
				->where('news_publish_date', '<=', time())
                ->where('news_active', '=', 1)
                ->where(\DB::raw('0'), '<', function($query) use ($categories)
                {
                    $query->select(\DB::raw('COUNT(*)'))
                            ->from('synergy_news_category_links')
                            ->where('news_id', '=', \DB::raw('`synergy_news`.`news_id`'))
                            ->whereIn('category_id', $categories);
                });

            if ($limit) {
                $query->limit($limit);
            }

            return $query->get();
        } else {
            return null;
        }
    }
}