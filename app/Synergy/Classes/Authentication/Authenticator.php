<?php

namespace Synergy\Classes\Authentication;

class Authenticator
{
    public static function check(
        $uri_path,
        $first_segment,
        $user,
        $config
    ) {
        if (!is_null($config) && isset($config['mode'])) {
            $mode = $config['mode'];

            $rules = $config['rules'];

            $default_redirect = isset($config['redirect'])
                                    ? $config['redirect']
                                    : '';
            
            $redirect_to = $default_redirect;
            
            if ($default_redirect) {
                array_push(
                    $rules,
                    array(
                        'url' => $default_redirect,
                        'permissions' => 'void'
                    )
                );
            }

            //

            $has_access = ($mode == 'lax') ? true : false;

            // 

            if (is_array($rules) && !empty($rules)) {
                foreach ($rules as $rule) {
                    // Get rule URL. If none set, skip this rule.
                    
                    $url = isset($rule['url'])
                            ? $rule['url']
                            : '';
                    
                    if (!$url) {
                        continue;
                    }

                    // Get required permissions.

                    $required_permissions = isset($rule['permissions'])
                                                ? $rule['permissions']
                                                : 0;

                    // Get redirect URL (or set to default).
                    
                    $redirect = isset($rule['redirect'])
                                    ? $rule['redirect']
                                    : $default_redirect;
                    
                    // Get rule type (regex or default).
                    
                    $type = isset($rule['type'])
                                ? $rule['type']
                                : 'text';
                    
                    // 

                    $matched = false;
                    
                    switch($type) {
                        case 'regex':
                        {
                            $matched = (bool)preg_match('/'.str_replace('/', '\\/', $url).'/ism', $uri_path);

                            break;
                        }
                        default:
                        {
                            if (stristr($url, '/')) {
                                $matched = (strncasecmp($uri_path.'/', $url.'/', strlen($url) + 1) == 0);
                            } else {
                                $matched = ($url == $first_segment);
                            }

                            break;
                        }
                    }

                    if ($matched) {
                        // Check permissions t/f
                        
                        if ($user->hasPermission($required_permissions)) {
                            $has_access = true;
                        } else {
                            $redirect_to = $redirect;
                            
                            $has_access = false;
                        }
                    }
                }
            }
            
            // 

            if (!$has_access) {
                return $redirect_to;
            } else {
                return false;
            }
        }
    }
}