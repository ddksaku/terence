<?php

namespace Synergy\Modules\Users\Controllers\CMS;

class UsersAPIController extends \Controllers\CMSAPIController
{
    protected $ajaxPrefix = 'ajax/modules';
    
    /* */
    
    protected function actionAnyIndex()
    {
        $data = array();

        // 

        $view = $this->loadAjaxView('index');
        
        $view->with(
            'users',
            \Models\User::orderBy('user_id', '!= '.$this->getData('user')->user_id)
                ->orderBy('user_surname', 'asc')
                ->get()
        );

        $data['html'] = $view->render();

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    // 
    
    protected function actionAnyStatus()
    {
        $data = array();

        // Attempt to find user.
        
        $user = \Models\User::where('user_id', '=', $this->post->get('id'))->first();
        
        if (!$user) {
            $data['error'] = 1;
        } else {
            $user->user_active = ($this->post->get('status') == 1)
                                    ? 1
                                    : 0;
            
            $user->save();

            $data['error'] = 0;
            $data['success'] = 1;
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    //
    
    protected function actionGetSettings()
    {
        $data = $this->loadAjaxView('settings');

        $data->with(
            array(
                'groups' => \Models\UserGroup::orderBy('group_level', 'asc')->get(),
                'permissions' => \Models\UserGroup::getPermissionMap(),
            )
        );
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostSettings()
    {
        $data = array();
        
        $user = $this->getData('user');
        
        if($user->hasPermission('edit_users_settings')) {
            // Update permissions.

            $groups = \Models\UserGroup::all();

            $permissions = \Models\UserGroup::getPermissionMap();

            foreach ($groups as $group) {
                foreach ($permissions as $permission => $permissionName) {
                    if (
                        (
                            $user->hasPermission($permission)
                            && $user->getHighestLevel() > $group->group_level
                        )
                        || $user->hasPermission('edit_all_permissions')
                    ) {
                        if ($this->post->get("permission__{$permission}__{$group->group_id}") == 1) {
                            $group->givePermission($permission);
                        } else {
                            $group->takePermission($permission);
                        }
                    }
                }
                
                // Re-apply certain permissions to Admins
                
                if ($group->group_level == 4) {
                    $group->givePermission('edit_users_settings');
                    $group->givePermission('edit_all_permissions');
                }

                $group->save();
            }

            // 

            $data['success'] = 1;
        } else {
            $data['error'] = 1;
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }

    //
    
    protected function actionGetEdit()
    {
        // Attempt to find module registration in database.
        
        if (($user_id = $this->input->get('id'))) {
            $user = \Models\User::where('user_id', '=', $user_id)->first();
        } else {
            $user = null;
        }
        
        if ($user_id && !$user) {
            $data = array('error' => 1);
        } else {

            // Load form view.
 
            $data = $this->loadAjaxView('edit');
            
            // Get a blank user object.
            
            if (!$user) {
                $user = new \Models\User;
                
                $new_user = true;
            } else {
                $new_user = false;
            }
            
            // Pass view data.

            $data->with(
                array(
                    'edit_user' => $user,
                    'new_user'  => $new_user,
                    'groups' => \Models\UserGroup::all(),
                )
            );
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostEdit()
    {
        $data = array();
        
        // Attempt to find user in database.
        
        if (($user_id = $this->post->get('id'))) {
            $user = \Models\User::where('user_id', '=', $user_id)->first();
        } else {
            $user = null;
        }
        
        // 
        
        if ($user_id && !$user) {
            $data['success'] = 0;
        } else {
            if (!$user) {
                $user = new \Models\User;
                
                $new_user = true;
            } else {
                $new_user = false;
            }
            
            // 
            
            $new_username = ($this->post->get('username') != $user->user_username)
                                ? $this->post->get('username')
                                : false;
            
            $new_email = ($this->post->get('email') != $user->user_email)
                                ? $this->post->get('email')
                                : false;

            if ($new_username && \Models\Zenith::conflicts($new_username, 'user_username')) {
                $data['success'] = 0;
                $data['error'] = 2;
            } elseif ($new_email && \Models\Zenith::conflicts($new_email, 'user_email')) {
                $data['success'] = 0;
                $data['error'] = 1;
            } else {
                if ($this->post->has('status')) {
                    $user->user_active = ($this->post->get('status') == 1)
                                            ? 1
                                            : 0;
                }
                
                $user->user_title = $this->post->get('titlename');
                $user->user_forename = $this->post->get('name');
                $user->user_surname = $this->post->get('lastname');

                if ($new_username) {
                    $user->user_username = $new_username;
                }
                
                if (($new_password = $this->post->get('password'))) {
                    $user->setPassword($new_password);
                }

                $new_user_level = $this->post->get('userlevel');

                if (!$new_user_level) {
                    $new_user_level = 1;
                }

                if ($new_email) {
                    $user->user_email = $new_email;
                }

                $user->user_facebook = $this->post->get('fb');
                
                $user->save();

                if (
                    $this->getData('user')->hasPermission('make_level_'.$new_user_level)
                ) {
                    $user->setUserGroup($new_user_level);
                }
                
                $data['success'] = 1;
                $data['new_entry'] = $new_user;
            }
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    
    //
    
    protected function actionGetProfile()
    {
        // Attempt to find module registration in database.
        
        $user = $this->getData('user');
        
        // 
        
        if (!$user) {
            $data = array('error' => 1);
        } else {

            // Load form view.
 
            $data = $this->loadAjaxView('profile');
            
            // Get a blank user object.
            
            if (!$user) {
                $user = new \Models\User;
                
                $new_user = true;
            } else {
                $new_user = false;
            }
            
            // Pass view data.

            $data->with(
                array(
                    'edit_user' => $user,
                    'new_user'  => $new_user,
                    'groups' => \Models\UserGroup::all(),
                )
            );
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostProfile()
    {
        $data = array();
        
        // 
        
        $user = $this->getData('user');
        
        // 
        
        if (!$user) {
            $data['success'] = 0;
        } else {
            if (!$user) {
                $user = new \Models\User;
            }
            
            // 
            
            $new_username = ($this->post->get('username') != $user->user_username)
                                ? $this->post->get('username')
                                : false;
            
            if ($new_username && \Models\Zenith::conflicts($new_username, 'user_username')) {
                $data['success'] = 0;
                $data['error'] = 2;
            } else {
                if ($this->post->has('status')) {
                    $user->user_active = ($this->post->get('status') == 1)
                                            ? 1
                                            : 0;
                }
                
                $user->user_title = $this->post->get('titlename');
                $user->user_forename = $this->post->get('name');
                $user->user_surname = $this->post->get('lastname');

                if ($new_username) {
                    $user->user_username = $new_username;
                }
                
                if (($new_password = $this->post->get('password'))) {
                    $user->setPassword($new_password);
                }
                
                $user->user_email = $this->post->get('email');

                $user->user_facebook = $this->post->get('fb');
                
                $user->save();
                
                $data['success'] = 1;
            }
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
    
    //
    
    protected function actionAnyDelete()
    {
        $data = array();

        // Attempt to find user.
        
        $target_user = $this->post->get('id');

        if (stristr($target_user, ',')) {
            $target_user = explode(',', $target_user);
        }
        
        if (is_array($target_user)) {
            $target = \Models\User::whereIn('user_id', $target_user)->get();
        } else {
            $target = \Models\User::where('user_id', '=', $target_user)->first();
        }
        
        if (!$target) {
            $data['error'] = 1;
        } else {
            if ($target instanceof \Illuminate\Database\Eloquent\Collection) {
                foreach ($target as $user) {
                    $user->delete();
                }
            } else {
                $target->delete();
            }
            
            $data['error'] = 0;
        }

        // Return response.

        return $this->buildAjaxResponse($data);
    }
} 