<?php

namespace Models;

class Zenith
{
    public static function getUser()
    {
        $user_id = \Session::get('user_id');
        
        $remember_code = \Cookie::get('user_remember');
        
        if ($user_id || $remember_code) {
            $user = \Models\User::where('user_active', '=', 1)
                ->where(function ($query) use ($user_id, $remember_code)
                {
                    $query->where('user_id', '=', $user_id);
                    
                    if ($remember_code) {
                        $query->orWhere('user_remember_code', '=', $remember_code);
                    }
                })
                ->first();

            // 
            
            if ($user) {
                $user->setLoggedIn(true);
            }
        }
        
        if (!isset($user) || is_null($user)) {
            $user = new \Models\User;
        }

        // 

        return $user;
    }
    
    public static function login($identifier, $password, $remember = false, $field = 'user_username')
    {
        $user = \Models\User::where($field, '=', $identifier)
            ->first();

        if ($user && $user->checkPassword($password)) {
            if (!$user->isActive()) {
                throw new \Synergy\Exceptions\Zenith\UserInactiveException;
            }
            
            // Log in
            
            \Session::set('user_id', $user->user_id);
            
            $user->setLoggedIn(true);
            
            // Remember login, if applicable.
            
            if ($remember) {
                $user->user_remember_code = $user->user_id.':'.\Str::random(32);
                
                $user->save();
                
                $cookie = \Cookie::forever('user_remember', $user->user_remember_code);
                
                \App::after(function($request, $response) use ($cookie)
                {
                    $response->headers->setCookie($cookie);
                });
            }
        } else {
            $user = new \Models\User;
        }

        return $user;
    }
    
    public static function logout()
    {
        $user = self::getUser();
        
        if ($user) {
            $user->user_remember_code = '';
            
            $user->save();
        }
        
        // 
        
        \Session::flush();
        
        \Cookie::forget('user_remember');
    }
    
    public static function conflicts($value, $field = 'user_email')
    {
        return (\Models\User::where($field, '=', $value)->count() != 0);
    }
    
    public static function create()
    {
        return new \Models\User;
    }
}