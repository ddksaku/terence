<?php

namespace Synergy\Modules\Contact\Models;

class Contact extends \Models\BaseModel
{
    protected $table = 'synergy_contact';
    
    protected $primaryKey = 'contact_id';
    
    public $timestamps = false;
}