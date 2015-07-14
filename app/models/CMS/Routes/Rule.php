<?php

namespace Models\CMS\Routes;

class Rule
{
    protected $url;
    protected $controller;
    protected $regex;
    
    /* */
    
    public function __construct($url, $controller, $regex)
    {
        $this->url = $url;
        $this->controller = $controller;
        $this->regex = $regex;
    }
    
    public function getURL()
    {
        return $this->url;
    }
    
    public function getController()
    {
        return $this->controller;
    }
    
    public function getRegex()
    {
        return $this->regex;
    }
}