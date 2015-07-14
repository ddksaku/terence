<?php

/*
 * A view skeleton, intended to allow a view name to be
 * changed without having to reload loads of shite.
 * 
 * The setView method contains some code copied from View\Environment
 * because a) there's no public-interfacing way of using that code
 * and b) despite the Laravel View class offering a setPath method, it
 * does not automatically re-detect the engine when the path is changed (fail).
 * 
 */

namespace Synergy\Classes\View;

class Skeleton extends \Illuminate\View\View
{
    protected $data = array();
    
    protected $hasView = false;
    
    private $engines;
    
    /*
     * 
     */

    public function __construct()
    {
        $this->engines = \View::getEngineResolver();
        
        $this->environment = \View::getShared();
        $this->environment = $this->environment['__env'];
    }

    public function setView($view)
    {
        $this->path = $path = $this->environment->getFinder()->find($view);

        $this->view = $view;
        
        // Get engine for view.
        
        $view_extensions = $this->environment->getExtensions();
        
        $extensions = array_keys($view_extensions);
        $extension = array_first($extensions, function ($key, $value) use($path) {
            return ends_with($path, $value);
        });

        $engine = $view_extensions[$extension];
        $this->engine = $this->engines->resolve($engine);

        if (!$this->hasView) {
            $this->hasView = true;
        }
    }
    
    public function hasView()
    {
        return $this->hasView;
    }
}