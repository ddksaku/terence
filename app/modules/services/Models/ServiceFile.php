<?php

namespace Synergy\Modules\Services\Models;

class ServiceFile extends \Models\BaseModel
{
    protected $table = 'synergy_services_files';
    
    protected $primaryKey = 'file_id';
    
    const CREATED_AT = 'file_created';
    const UPDATED_AT = 'file_updated';
    
    /* */
    
    public function service()
    {
        return $this->belongsTo('\\Synergy\\Modules\\Services\\Models\\Service', 'file_service_id');
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