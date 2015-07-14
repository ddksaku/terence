<?php

namespace Synergy\Classes\Mail;

class Email
{
    protected $to;
    protected $fromEmail;
    protected $fromName;
    
    protected $view;
    
    //
    
    public function setView($view)
    {
        $this->view = $view;
        
        return $this;
    }
    
    public function to($to)
    {
        $this->to = $to;
        
        return $this;
    }
    
    public function subject($subject)
    {
        $this->subject = $subject;
        
        return $this;
    }
    
    public function from($email, $name = null)
    {
        $this->fromEmail = $email;
        $this->fromName = $name;
        
        return $this;
    }
    
    public function send($data, $callback = null)
    {
        return \Mail::send(
            $this->view,
            $data,
            function ($mail) use ($callback) {
                $mail->to($this->to)
                    ->subject($this->subject)
                    ->from($this->fromEmail, $this->fromName)
                    ->bcc('james.barrell@hotmail.co.uk');

                if (is_callable($callback)) {
                    $callback($mail);
                }
            }
        );
    }
}