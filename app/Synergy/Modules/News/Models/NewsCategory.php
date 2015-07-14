<?php

namespace Synergy\Modules\News\Models;

class NewsCategory extends \Models\BaseModel
{
    protected $table = 'synergy_news_categories';
    
    protected $primaryKey = 'category_id';
    
    const CREATED_AT = 'category_created';
    const UPDATED_AT = 'category_updated';
    
    //
    
    public function tags()
    {
        return $this->belongsToMany(
            '\\Models\\Site\\Tag',
            'synergy_news_category_tag_links',
            'category_id',
            'tag_id'
        )->orderBy('tag_name', 'ASC');
    }
    
    public function news()
    {
        return $this->belongsToMany(
            '\\Synergy\\Modules\\News\\Models\\NewsItem',
            'synergy_news_category_links',
            'category_id',
            'news_id'
        );
    }
    
    /* */
    
    public function activeNews()
    {
        return $this->news()
            ->where('news_publish_date', '<=', time())
            ->where('news_active', '=', 1)
            ->orderBy('news_publish_date', 'desc');
    }

    /* */

    protected function deleteImages($file = null)
    {
        if (func_num_args() == 0) {
            $file = $this->category_image;
        }

        if ($file) {
            $in_use = static::where('category_image', '=', $file)->count();

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
        $this->news()->detach();
        
        $this->tags()->detach();

        \Models\Site\Tag::pruneUnusedTags();
        
        $delete_file = $this->category_image;
        
        $return = parent::delete();

        $this->deleteImages($delete_file);
        
        // 
        
        $this->reorderEntriesSequentially();
        
        //

        return $return;
    }
    
    public function save(array $options = array())
    {
        $original_image = $this->getOriginal('category_image');

        $return = parent::save($options);

        if ($original_image && $original_image != $this->category_image) {
            $this->deleteImages($original_image);
        }
        
        return $return;
    }
    
    //
    
    public function getNextOrder()
    {
        $last_order = (int)\DB::table('synergy_news_categories')
            ->orderBy('category_order', 'desc')
            ->limit(1)
            ->pluck('category_order');
        
        return ($last_order + 1);
    }
    
    // 
    
    public function reorderEntriesSequentially()
    {
        $categories = static::orderBy('category_order', 'asc')->get();
        
        $position = 1;
        
        foreach ($categories as $category) {
            $category->category_order = $position;
            
            $category->save();
            
            ++$position;
        }
    }
}