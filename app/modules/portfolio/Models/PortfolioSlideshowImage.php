<?php

namespace Synergy\Modules\Portfolio\Models;

class PortfolioSlideshowImage extends \Models\BaseModel
{
    protected $table = 'synergy_portfolio_slideshow_images';
    
    protected $primaryKey = 'image_id';
    
    const CREATED_AT = 'image_created';
    const UPDATED_AT = 'image_updated';
    
    /* */
    
    public function portfolio()
    {
        return $this->belongsTo('\\Synergy\\Modules\\Portfolio\\Models\\Portfolio', 'portfolio_id');
    }
    
    public function delete()
    {
        $directory = \Config::get('synergy.uploads.images.directory');

        \File::delete("{$directory}{$this->image_filename}");
        \File::delete("{$directory}resize/{$this->image_filename}");
        \File::delete("{$directory}square/{$this->image_filename}");
        \File::delete("{$directory}thumb/{$this->image_filename}");

        return parent::delete();
    }
}