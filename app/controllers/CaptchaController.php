<?php

namespace Controllers;

class CaptchaController extends \Controller
{
    /* */
    
    protected function output()
    {
        // Build CAPTCHA.
        
        $builder = new \Gregwar\Captcha\CaptchaBuilder;
        $builder->build();
        
        // Save phrase in session.
        
        \Session::set('captcha_phrase', $builder->getPhrase());
        
        // Create & return response.

        $response = new \Illuminate\Http\Response;
        
        $response->header('Content-type', 'image/jpeg')
            ->setContent($builder->get());
        
        return $response;
    }
}