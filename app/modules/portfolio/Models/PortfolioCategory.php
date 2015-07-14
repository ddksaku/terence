<?php

namespace Synergy\Modules\Portfolio\Models;

class PortfolioCategory extends \Models\BaseModel
{
    protected $table = 'synergy_portfolio_categories';
    
    protected $primaryKey = 'category_id';
    
    const CREATED_AT = 'category_created';
    const UPDATED_AT = 'category_updated';
    
    //

    public function tags()
    {
        return $this->belongsToMany(
            '\\Models\\Site\\Tag',
            'synergy_portfolio_category_tag_links',
            'category_id',
            'tag_id'
        )->orderBy('tag_name', 'ASC');
    }
    
    public function portfolioItems()
    {
        return $this->belongsToMany(
            '\\Synergy\\Modules\\Portfolio\\Models\\Portfolio',
            'synergy_portfolio_category_links',
            'category_id',
            'portfolio_id'
        );
    }
    
    /* */
    
    public function activePortfolioItems()
    {
        return $this->portfolioItems()
            ->where('portfolio_active', '=', 1);
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
        $this->portfolioItems()->detach();
        
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
        $last_order = (int)\DB::table('synergy_portfolio_categories')
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