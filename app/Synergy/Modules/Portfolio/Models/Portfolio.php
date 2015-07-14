<?php

namespace Synergy\Modules\Portfolio\Models;

class Portfolio extends \Models\BaseModel
{
    protected $table = 'synergy_portfolio';
    
    protected $primaryKey = 'portfolio_id';
    
    const CREATED_AT = 'portfolio_created';
    const UPDATED_AT = 'portfolio_updated';
    
    /* */
    
    public function tags()
    {
        return $this->belongsToMany(
            '\\Models\\Site\\Tag',
            'synergy_portfolio_tag_links',
            'portfolio_id',
            'tag_id'
        )->orderBy('tag_name', 'ASC');
    }
    
    public function files()
    {
        return $this->hasMany('\\Synergy\\Modules\\Portfolio\\Models\\PortfolioFile', 'file_portfolio_id');
    }
    
    public function slideshow()
    {
        return $this->hasMany('\\Synergy\\Modules\\Portfolio\\Models\\PortfolioSlideshowImage', 'portfolio_id')
            ->orderBy('image_order', 'asc');
    }
    
    public function categories()
    {
        return $this->belongsToMany(
            '\\Synergy\\Modules\\Portfolio\\Models\\PortfolioCategory',
            'synergy_portfolio_category_links',
            'portfolio_id',
            'category_id'
        );
    }

    /* */
    
    protected function deleteImages($file = null)
    {
        if (func_num_args() == 0) {
            $file = $this->portfolio_image;
        }

        if ($file) {
            $directory = \Config::get('synergy.uploads.images.directory');

            \File::delete("{$directory}{$file}");
            \File::delete("{$directory}resize/{$file}");
            \File::delete("{$directory}square/{$file}");
            \File::delete("{$directory}thumb/{$file}");
        }
    }
    
    // 
    
    public function delete()
    {
        $this->categories()->detach();
        
        $this->tags()->detach();
            
        \Models\Site\Tag::pruneUnusedTags();
        
        $delete_file = $this->portfolio_image;
        
        $return = parent::delete();

        $this->deleteImages($delete_file);
        
        //
        
        foreach ($this->files as $file) {
            $file->delete();
        }
        
        foreach ($this->slideshow as $slideshowImage) {
            $slideshowImage->delete();
        }
        
        // 
        
        $this->reorderEntriesSequentially();
        
        //

        return $return;
    }
    
    public function save(array $options = array())
    {        
        $original_image = $this->getOriginal('portfolio_image');

        $return = parent::save($options);

        if ($original_image && $original_image != $this->portfolio_image) {
            $this->deleteImages($original_image);
        }
        
        return $return;
    }
    
    // 
    
    public function getNextOrder()
    {
        $last_order = (int)\DB::table('synergy_portfolio')
            ->orderBy('portfolio_order', 'desc')
            ->limit(1)
            ->pluck('portfolio_order');
        
        return ($last_order + 1);
    }

    //
    
    public function reorderEntriesSequentially()
    {
        $portfolio = static::orderBy('portfolio_order', 'asc')->get();
        
        $position = 1;
        
        foreach ($portfolio as $portfolioItem) {
            $portfolioItem->portfolio_order = $position;
            
            $portfolioItem->save();
            
            ++$position;
        }
    }
    
    /* */
    
    public function getRelatedPortfolioItems($limit = 0)
    {
        $categories = $this->categories->lists('category_id');
        
        if (!empty($categories)) {
            $query = static::orderBy('portfolio_order', 'asc')
                ->where('portfolio_id', '!=', $this->portfolio_id)
                ->where('portfolio_active', '=', 1)
                ->where(\DB::raw('0'), '<', function($query) use ($categories)
                {
                    $query->select(\DB::raw('COUNT(*)'))
                            ->from('synergy_portfolio_category_links')
                            ->where('portfolio_id', '=', \DB::raw('`synergy_portfolio`.`portfolio_id`'))
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