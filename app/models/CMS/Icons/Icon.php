<?php

namespace Models\CMS\Icons;

class Icon
{
    protected $iconName;
    protected $iconURL;
    
    public function __construct($path, $prefix)
    {
        $path_info = pathinfo($path);
        
        $this->iconName = $path_info['filename'];
        $this->iconURL = "{$prefix}/{$path_info['basename']}";
    }
    
    public function getName()
    {
        return $this->iconName;
    }
    
    public function getURL()
    {
        return $this->iconURL;
    }
}