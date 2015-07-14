<?php

namespace Controllers\CMS;

class PasswordResetController extends \Controllers\CMSController
{
    protected function actionAnyIndex()
    {
        $user = $this->getData('user');
        
        if ($user->isLoggedIn()) {
            if ($user->hasPermission('access_cms')) {
                return \Redirect::to('cms');
            } else {
                return \Redirect::to(\Request::root());
            }
        }
        
        $this->pageTitle('Forgot password');
    }
}