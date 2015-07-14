<?php

namespace Models\CMS\Module;

class Registration extends \Models\BaseModel
{
    protected $table = 'synergy_modules';
    
    protected $primaryKey = 'module_id';
    
    protected $iconCollection;
    
    protected $groupName;
    
    protected $moduleMeta;
    
    public $timestamps = false;
    
    //

    public function blockedUsers()
    {
        return $this->belongsToMany(
            '\\Models\\User',
            'synergy_module_blocked_user_links',
            'module_id',
            'user_id'
        );
    }
    
    //
    
    public function getMeta()
    {
        return null;
    }
    
    /* */
    
    public function isInstalled()
    {
        return $this->module_installed ? true : false;
    }
    
    /* */
    
    public function setName($name)
    {
        $this->module_name = trim($name);
        
        return $this;
    }
    
    public function getName()
    {
        return $this->module_name;
    }
    
    /* */
    
    public function getURL()
    {
        return $this->module_url;
    }
    
    /* */
    
    public function getIcon()
    {
        if (!$this->iconCollection) {
            $this->iconCollection = new \Models\CMS\Icons\Collection;
        }
        
        return $this->iconCollection->getIcon($this->module_icon);
    }
    
    //
    
    public function getGroupName()
    {
        if (!$this->groupName) {
            $group = \Models\UserGroup::where('group_level', '=', $this->module_view_level)->first();

            if ($group) {
                $this->groupName = $group->group_name;
            } else {
                $this->groupName = '';
            }
        }
        
        return $this->groupName;
    }
    
    /* */

    public function page()
    {
        return $this->belongsTo('\\Synergy\\Modules\\Pages\\Models\\Page', 'module_page_id');
    }
    
    //
    
    public function delete()
    {
        $this->blockedUsers()->detach();
        
        return parent::delete();
    }
}