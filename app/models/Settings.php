<?php

namespace Models;

class Settings extends \Models\BaseModel
{
    protected $table = 'synergy_settings';
    
    protected $primaryKey = 'setting_id';
    
    public $timestamps = false;
}