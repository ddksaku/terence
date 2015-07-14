<?php

namespace Synergy\Modules\News\Models;

class NewsFile extends \Models\BaseModel
{
    protected $table = 'synergy_news_files';
    
    protected $primaryKey = 'file_id';
    
    const CREATED_AT = 'file_created';
    const UPDATED_AT = 'file_updated';
    
    /* */
    
    public function newsItem()
    {
        return $this->belongsTo('\\Synergy\\Modules\\News\\Models\\NewsItem', 'file_news_id');
    }
    
    public function delete()
    {
        $filename = $this->file_name;
        
        $return = parent::delete();
        
        // 
        
        $in_use = static::where('file_name', '=', $filename)->count();

        if ($in_use < 1) {
            \File::delete(\Config::get('synergy.uploads.documents.directory').$filename);
        }

        // 

        return $return;
    }
}