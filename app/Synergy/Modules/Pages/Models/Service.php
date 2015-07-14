<?php

namespace Synergy\Modules\Service\Models;

class Service extends \Models\BaseModel
{
    protected $table = 'synergy_services';
    
    protected $primaryKey = 'service_id';
    
    const CREATED_AT = 'service_created';
    const UPDATED_AT = 'service_updated';
    
    /* */
    
    public function parent()
    {
        return $this->belongsTo('\\Synergy\\Modules\\Pages\\Models\\Page', 'page_parent_id');
    }
    
    /* */
    
    public static function conflictingURL($url)
    {
        return (static::where('page_url', '=', $url)->count() != 0);
    }
}