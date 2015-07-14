<?php

namespace Synergy\Classes\Mail;

class Mailer
{
    public static function create($view = null)
    {
        $email = new Email;
        
        if ($view) {
            $email->setView($view);
        }
        
        return $email;
    }
}