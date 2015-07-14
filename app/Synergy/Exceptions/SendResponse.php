<?php

namespace Synergy\Exceptions;

class SendResponse extends \Exception
{
    protected $response;
    
    public function __construct($response)
    {
        $this->response = $response;
    }
    
    public function getResponse()
    {
        return $this->response;
    }
}