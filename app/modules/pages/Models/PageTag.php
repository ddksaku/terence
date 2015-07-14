<?php

namespace Synergy\Modules\Pages\Models;

class PageTag extends \Models\BaseModel
{
    protected $table = 'synergy_page_tags';
    
    protected $primaryKey = 'tag_id';
    
    public $timestamps = false;
    
    /* */
    
    public function delete()
    {
       
        
        return parent::delete();
    }
    
}