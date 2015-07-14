<?php

namespace Controllers\Site;

class SiteAPIController extends \Controllers\SiteAPIController
{
    protected function actionAnySendContact()
    {
        $data = array();

        $errors = array();
        
        // 
        
        $name = $this->post->get('yourname');
        $email = $this->post->get('email');
        $subject = $this->post->get('tele');
        $message = $this->post->get('message');
        
        
        
        if (empty($name)) {
            $errors[] = 'Please tell us your name.';
        }
        
        if (empty($email)) {
            $errors[] = 'Please tell us your email address.';
        } elseif (!stristr($email, '@')) {
            $errors[] = 'Invalid email address format.';
        }
        
        if (empty($subject)) {
            $errors[] = 'Please tell us your subject.';
        }
        
        if (strcasecmp(\Session::get('captcha_phrase'), $this->post->get('captcha')) != 0) {
            $errors[] = 'Incorrect security code.';
        }
        
        if (empty($message)) {
            $errors[] = 'Please tell us your message.';
        }
        
        // Wipe CAPTCHA code.

        \Session::forget('captcha_phrase');
        
        // 
        
        if (empty($errors)) {
            $contact = $this->getData('contact');
            
            try {
                \Synergy\Classes\Mail\Mailer::create('site/emails/contact')
                    ->to($contact->contact_email)
                    ->subject("Website contact: {$subject}")
                    ->from($email, $name)
                    ->send(
                        array(
                            'name' => htmlspecialchars($name),
                            'email' => htmlspecialchars($email),
                            'subject' => htmlspecialchars($subject),
                            'sender_message' => str_replace("\n", '<br>', htmlspecialchars($message)),
                        )
                    );
            } catch (\Exception $exception) {
                $errors[] = 'Mail sending failed with message: '.$exception->getMessage();
            }
        }
        
        if (!empty($errors)) {
            $data['error'] = 1;
            $data['errors'] = $errors;
        } else {
            $data['success'] = 1;
        }

        // Return response.
        
        return $this->buildAjaxResponse($data);
    }
}