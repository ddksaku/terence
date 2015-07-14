<?php

namespace Models;

class UserGroup extends \Models\BaseModel
{
    protected $table = 'synergy_user_groups';
    
    protected $primaryKey = 'group_id';
    
    public $timestamps = false;

    /* */

    protected $groupPermissionsArray = array(
        'void' => 1,
    );
    
    protected $groupPermissionsLoaded = false;

    //

    protected function loadPermissions()
    {
        if (!$this->groupPermissionsLoaded) {
            $decoded = json_decode($this->group_permissions, true);

            if (is_array($decoded)) {
                $this->groupPermissionsArray = array_merge(
                    $this->groupPermissionsArray,
                    $decoded
                );
            }
            
            $this->groupPermissionsLoaded = true;
        }
    }
    
    // 

    public function getPermissions()
    {
        $this->loadPermissions();
        
        return $this->groupPermissionsArray;
    }
    
    public function hasPermission($permission)
    {
        $this->loadPermissions();
        
        return (isset($this->groupPermissionsArray[$permission]) && $this->groupPermissionsArray[$permission] == 1);
    }
    
    public function givePermission($permission)
    {
        $this->loadPermissions();
        
        $this->groupPermissionsArray[$permission] = 1;
    }
    
    public function takePermission($permission)
    {
        $this->loadPermissions();
        
        if (isset($this->groupPermissionsArray[$permission])) {
            unset($this->groupPermissionsArray[$permission]);
        }
    }
    
    //
    
    /* */
    
    public function save(array $options = array())
    {
        $this->loadPermissions();

        $this->group_permissions = json_encode($this->groupPermissionsArray);
        
        //
        
        return parent::save($options);
    }
    
    // Relationships

    public function groups()
    {
        return $this->belongsToMany('\Models\User', 'synergy_user_group_links', 'group_id', 'user_id');
    }
    
    /* */
    
    public static function getPermissionMap()
    {
        return array(
            'access_cms' => 'Log into the CMS',
            'manage_level_1' => 'Edit guests',
            'manage_level_2' => 'Edit users',
            'manage_level_3' => 'Edit managers',
            'manage_level_4' => 'Edit admins',
            'view_settings' => 'Change settings',
            'edit_advanced_settings' => 'Change image and logo sizes',
            'edit_ga_code' => 'Change GA code',
            'view_modules' => 'Change modules',
            'rename_modules' => 'Rename modules',
            'activate_modules' => 'Activate/deactivate modules',
            'block_level_1' => 'Block guests from modules',
            'block_level_2' => 'Block users from modules',
            'block_level_3' => 'Block managers from modules',
            'manage_module_level_1' => 'Set module access level to "guest"',
            'manage_module_level_2' => 'Set module access level to "user"',
            'manage_module_level_3' => 'Set module access level to "manager"',
            'manage_module_level_4' => 'Set module access level to "admin"',
            'make_level_1' => 'Set a user\'s level to "guest"',
            'make_level_2' => 'Set a user\'s level to "user"',
            'make_level_3' => 'Set a user\'s level to "manager"',
            'make_level_4' => 'Set a user\'s level to "admin"',
            'edit_all_permissions' => 'Edit all permissions',
            'edit_news_settings' => 'Change news settings',
            'edit_pages_settings' => 'Change pages settings',
            'edit_portfolio_settings' => 'Change portfolio settings',
            'edit_services_settings' => 'Change services settings',
            'edit_users_settings' => 'Change users settings',
			'make_pages' => 'Create pages',
        );
    }
}