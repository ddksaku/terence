<?php

namespace Models\CMS\Icons;

class Collection
{
    protected $iconDirectory;
    
    public function __construct()
    {
        $this->iconDirectory = \Config::get('synergy.icons.directory');
    }
    
    public function enumerateIcons()
    {
        $icons = \File::glob(public_path().'/'.$this->iconDirectory.'/*.png');
        
        foreach ($icons as $icon) {
            $this->icons[] = new Icon($icon, \Request::root().'/'.$this->iconDirectory);
        }

        return $this;
    }

    public function getIcon($name)
    {
        if (file_exists(($icon_file = public_path().'/'.($icon_path = $this->iconDirectory.'/'.$name.'.png')))) {
            $icon = $icon_path;
        } else {
            $icon = false;
        }

        return $icon;
    }
    
    public function getIcons()
    {
        if (empty($this->icons)) {
            $this->enumerateIcons();
        }
        
        return $this->icons;
    }
}