<?php

namespace Models;

class User extends \Models\BaseModel
{
    protected $table = 'synergy_users';
    
    protected $primaryKey = 'user_id';
    
    const CREATED_AT = 'user_created';
    const UPDATED_AT = 'user_updated';
    
    //
    
    public function blockedModules()
    {
        return $this->belongsToMany(
            '\\Models\\CMS\\Module\\Registration',
            'synergy_module_blocked_user_links',
            'user_id',
            'module_id'
        );
    }
    
    /* */

    protected $isLoggedIn = false;
    
    protected $userPermissionsArray = array(
        'void' => 1,
    );
    
    protected $userPermissionsArrayInternal = array(
        'void' => 1,
    );
    
    protected $permissionsLoaded = false;
    
    /* */

    // 

    public function loadPermissions()
    {
        if ($this->permissionsLoaded) {
            return;
        }

        // Get group permissions.
        
        foreach ($this->groups as $group) {
            $this->userPermissionsArray = array_merge(
                $this->userPermissionsArray,
                $group->getPermissions()
            );
        }
        
        // Get user permissions.

        $decoded = json_decode($this->user_permissions, true);

        if (is_array($decoded)) {
            $this->userPermissionsArrayInternal = $decoded;
            
            $this->userPermissionsArray = array_merge(
                $this->userPermissionsArray,
                $decoded
            );
        }

        $this->permissionsLoaded = true;
    }
    
    // 

    public function setLoggedIn($logged_in)
    {
        $this->isLoggedIn = $logged_in;
    }

    /* 
     * Takes a password and hashes.
     * 
     */
    
    public function setPassword($password)
    {
        $this->user_hash = \Models\Leonardo::hash($password);
        
        return $this;
    }
    
    /* 
     * Compares a specified password and hash for a password match.
     * 
     */
    
    public function checkPassword($password)
    {
        return \Models\Leonardo::check($password, $this->user_hash);
    }

    /*
     * 
     */
    
    public function isLoggedIn()
    {
        return $this->isLoggedIn;
    }
    
    /*
     * 
     */
    
    public function getFullName()
    {
        return trim($this->user_forename.' '.$this->user_surname);
    }
    
    public function getFormalName()
    {
        return trim(trim($this->user_title.' '.$this->user_forename).' '.$this->user_surname);
    }
    
    /*
     * 
     */

    public function getPermissions()
    {
        $this->loadPermissions();
        
        return $this->userPermissionsArray;
    }
    
    public function hasPermission($permission)
    {
        $this->loadPermissions();
        
        return (isset($this->userPermissionsArray[$permission]) && $this->userPermissionsArray[$permission] == 1);
    }
    
    public function givePermission($permission)
    {
        $this->loadPermissions();
        
        $this->userPermissionsArray[$permission] = 1;
    }
    
    public function takePermission($permission)
    {
        $this->loadPermissions();
        
        
    }
    
    //
    
    public function isActive()
    {
        return $this->user_active ? true : false;
    }
    
    public function setUserActive($value)
    {
        $this->user_active = $value ? true : false;
        
        return $this;
    }
    
    /* */
    
    public function save(array $options = array())
    {
        $this->loadPermissions();
        
        $this->user_permissions = json_encode($this->userPermissionsArrayInternal);
        
        //
        
        return parent::save($options);
    }
    
    // Relationships
    
    public function groups()
    {
        return $this->belongsToMany('\Models\UserGroup', 'synergy_user_group_links', 'user_id', 'group_id');
    }
    
    // 
    
    public function getHighestLevel(&$groupName = null)
    {
        $level = 0;
        
        foreach ($this->groups as $group) {
            if ($group->group_level > $level) {
                $level = $group->group_level;
                
                $groupName = $group->group_name;
            }
        }
        
        return $level;
    }
    
    //

    public function getUserLevel()
    {
        $this->getHighestLevel($level);
        
        return $level;
    }
    
    public function setUserGroup($group_id)
    {
        $this->groups()->sync(array($group_id));
    }
    
    /* */
    
    public function delete()
    {
        $this->blockedModules()->detach();
        
        $this->groups()->detach();
        
        return parent::delete();
    }
}