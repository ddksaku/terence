<?php

namespace Synergy\Modules\Portfolio\Models;

class PortfolioFile extends \Models\BaseModel
{
    protected $table = 'synergy_portfolio_files';
    
    protected $primaryKey = 'file_id';
    
    const CREATED_AT = 'file_created';
    const UPDATED_AT = 'file_updated';
    
    /* */
    
    public function portfolio()
    {
        return $this->belongsTo('\\Synergy\\Modules\\Portfolio\\Models\\Portfolio', 'file_portfolio_id');
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