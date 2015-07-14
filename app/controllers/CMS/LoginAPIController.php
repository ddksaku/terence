<?php

namespace Controllers\CMS;

class LoginAPIController extends \Controllers\CMSAPIController
{
    protected function actionAnyLogin()
    {
        $data = array();

        // Attempt login and check CMS status.
         
        $remember = $this->post->get('remember')
                        ? true
                        : false;

        try {
            $user = \Models\Zenith::login(
                $this->post->get('username'),
                $this->post->get('password'),
                $remember
            );
                
            if ($user->isLoggedIn()) {
                if ($user->hasPermission('access_cms')) {
                    $data['status'] = '1';
                } else {
                    $data['errcode'] = '1';
                }
            } else {
                $data['status'] = '0';
            }
        } catch (\Synergy\Exceptions\Zenith\UserInactiveException $exception) {
            $data['errcode'] = '1';
            $data['status'] = '0';
        }

        // Return response.
        
        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostForgotPassword()
    {
        $data = array();
        
        $settings = $this->getData('settings');

        // 
        
        $criteria = $this->post->get('user_email');
        
        $user = \Models\User::where('user_email', '=', $criteria)
                ->orWhere('user_username', '=', $criteria)
                ->first();
        
        if (!$user) {
            $data['success'] = 0;
        } else {
            $reset_code = \Str::random(32);

            $user->user_reset_code = $reset_code;
            
            $user->save();

            // 

            \Synergy\Classes\Mail\Mailer::create('cms/emails/password-reset')
                ->to($user->user_email)
                ->subject('Forgot Password')
                ->from($settings->setting_email, $settings->setting_name)
                ->send(
                    [
                        'full_name' => $user->getFullName(),
                        'reset_code' => $reset_code,
                    ]
                );
            
            $data['success'] = 1;
        }
        
        // 
        
        return $this->buildAjaxResponse($data);
    }
    
    protected function actionPostPasswordReset()
    {
        $data = array();

        // 

        if (($code = $this->post->get('code'))) {
            $user = \Models\User::where('user_reset_code', '=', $code)
                    ->first();
        } else {
            $user = null;
        }
        
        if (!$user) {
            $data['success'] = 0;
        } else {
            $user->user_reset_code = '';

            $user->setPassword(($new_password = $this->post->get('password')));

            $user->save();
            
            $user = \Models\Zenith::login(
                    $user->user_username,
                    $new_password
                );

            $data['success'] = 1;
        }
        
        // 
        
        return $this->buildAjaxResponse($data);
    }
    
    protected function actionAnyCheckLoggedIn()
    {
        $data = array(
            'logged_in' => $this->getData('user')->isLoggedIn()
                            ? 1
                            : 0
        );

        return $this->buildAjaxResponse($data);
    }
}