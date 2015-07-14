<?php

namespace Controllers;

abstract class BaseController extends \Controller
{
    protected $section = 'site';

    protected $layout = 'default';
    protected $layoutView;

    protected $response;
    protected $responseCode = 200;

    protected $action;
    protected $actionArguments;
    protected $actionCallable;

    protected $prefix = '';
    protected $prefixLayout;
    protected $routePrefix = '';
    
    protected $pageLayout;

    protected $pageTitle = array();
    protected $pageTitleSeparator = ' - ';
    
    protected $pagePrefix = 'pages';
    
    protected $pageScripts = array();
    protected $pageStylesheets = array();

    protected $pageView;

    protected $sublayout;
    protected $useSublayout = true;
    protected $autoloadPageView = true;

    protected $post;
    
    protected $cookies = array();
    
    protected $metaDescription;
    
    protected $openGraphImage;
    
    protected $breadcrumbs = array();

    /*
     * 
     * 
     */

    abstract protected function handlePageRequest();
    abstract protected function loadPageView();

    /*
     * 
     * 
     */
    
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
    }
    
    public function getData($key)
    {
        return isset($this->data[$key])
                ? $this->data[$key]
                : null;
    }
    
    /* */

    public function __construct()
    {
        $this->data = array();
        
        // User logic.

        $user = \Models\Zenith::getUser();
        \View::share('user', $user);        
        $this->setData('user', $user);

        // Settings
        
        $settings = \Models\Settings::first();
        $this->setData('settings', $settings);
        
        // Contact settings

        $contact = \Synergy\Modules\Contact\Models\Contact::first();
        $this->setData('contact', $contact);

        // ... 
        
        if (method_exists($this, 'startUp')) {
            $this->startUp();
        }
    }
    
    // 
    
    public function process_request()
    {
        // Prepare POST input

        $this->post = new \Symfony\Component\HttpFoundation\ParameterBag;
        $this->input = new \Symfony\Component\HttpFoundation\ParameterBag;

        if (\Request::getMethod() == 'POST') {
            $target = $this->post;
        }
        
        foreach (\Input::get() as $field => $value) {
            if (is_string($value)) {
                $value = trim($value);
            }
            
            if (isset($target)) {
                $target->set($field, $value);
            }

            $this->input->set($field, $value);
        }

        /* Route request */

        $request_segments = \Request::segments();

        // 

        if (count($request_segments) > 6) {
            $request_segments = array_slice($request_segments, 0, 6);
        } elseif (count($request_segments) == 0) {
            $request_segments = array('index');
        }

        /*
         * Get current controller name.
         * We'll figure out which section of
         * the site this is from that.
         * 
         */
        
        if (empty($this->prefix)) {
            $controller_name = strtolower(get_class($this));

            if (($namespace_delimeter = strrpos($controller_name, '\\'))) {
                $controller_name = substr($controller_name, $namespace_delimeter + 1);
            }

            if (($controller_occurrence = strrpos($controller_name, 'controller'))) {
                $controller_name = substr($controller_name, 0, $controller_occurrence);
            }

            $this->prefix = $controller_name;
        }

        /*
         * Find action parameter. This will immediately
         * follow the current subsection e.g. users/add
         * 
         */
        
        $action_arguments = array();
        
        $action_name = '';

        $last_section = $this->section == 'site'
                            ? true
                            : false;
        $last_prefix = false;        
        $found_section = $this->section == 'site'
                            ? true
                            : false;
        $found_prefix = false;
        
        if (!empty($this->routePrefix)) {
            $search_prefix = $this->routePrefix;
        } else {
            $search_prefix = $this->prefix;
        }
        
        foreach ($request_segments as $segment) {
            $segment = strtolower($segment);
            
            if ($last_section || $last_prefix) {
                $action_name = $segment;
            }
            
            if (($found_prefix && !$last_prefix) || ($found_section && !$last_section)) {
                $action_arguments[] = $segment;
            }
            
            $last_section = false;
            $last_prefix = false;
            
            if (!$found_section && $segment === $this->section) {
                $found_section = true;
                $last_section = true;
            }

            if (!$found_prefix && $segment === $search_prefix) {
                $found_prefix = true;
                $last_prefix = true;
            }
        }
        
        if ($last_prefix || $last_section || !$action_name) {
            $action_name = 'index';
        }

        $this->action = preg_replace('/[^a-z0-9\-_]/ism', '', $action_name);
        
        $this->actionArguments = $action_arguments;

        /* Look for a callable method for the specified action. */
        
        $action_input_callable = \Str::camel('action_'.strtolower(\Request::getMethod()).'_'.$this->action);

        if (method_exists($this, $action_input_callable)) {
            $this->actionCallable = $action_input_callable;
        } else {
            $action_generic_callable = \Str::camel("action_any_{$this->action}");

            if (method_exists($this, $action_generic_callable)) {
                $this->actionCallable = $action_generic_callable;
            }
        }
        
        try {
            // Create skeletons for the layout and page view.

            $this->layoutView = new \Synergy\Classes\View\Skeleton;

            $this->pageView = new \Synergy\Classes\View\Skeleton;
            
            // Give the parent controller a chance to do some preprocessing.
            
            $response = $this->beforeProcessing();;

            if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
                
            } elseif (is_array($response)) {
                $this->pageView->with($response);
            }

            /*
             * Handle action/s for page request.
             * 
             * If the return value is a response, return it immediately
             * (it's likely to be a redirect).
             * 
             * If the return value is an array, it's likely to be view data.
             * 
             */

            $response = $this->handlePageRequest();

            if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
                throw new \Synergy\Exceptions\SendResponse($response);
            } elseif (is_array($response)) {
                $this->pageView->with($response);
            }

            // Load a page layout.
       
            $this->loadPageLayout();

            // Load a page view.

            if (
                $this->autoloadPageView
                && !$this->pageView->hasView()
            ) {
                $this->loadPageView();
            }

            // Load sublayout.

            if ($this->useSublayout) {
                $this->loadSublayout();
            }
            
            // Give the parent a chance to do some post-processing.
            
            $this->afterProcessing();
        } catch (\Synergy\Exceptions\SendResponse $send_response) {
            $this->afterProcessing(true);
            
            $this->response = $send_response->getResponse();

            $this->attachCookies(true);

            return $this->response;
        }

        // Share breadcrumbs
        
        $homepage = \Synergy\Modules\Pages\Models\Page::where('page_homepage', '=', 1)->first();
        
        if ($homepage) {
            $homeTitle = $homepage->page_title;
        } else {
            $homeTitle = 'Home';
        }
        
        array_unshift($this->breadcrumbs, array($homeTitle, '/'));
        
        \View::share('breadcrumbs', $this->breadcrumbs);
        
        // 

        if($this->layoutView instanceof \Illuminate\View\View) {
            //
            
            if (empty($this->layoutView->title)) {
                $this->layoutView->title = $this->pageTitle();
            }
            
            if (!empty($this->openGraphImage)) {
                $this->layoutView->og_image = $this->openGraphImage;
            }
            
            if (empty($this->layoutView->meta_description)) {
                $this->layoutView->meta_description = $this->metaDescription;
            }
            
            if (empty($this->layoutView->submenu)) {
                $this->layoutView->submenu = '';
            }
            
            if (empty($this->layoutView->messages)) {
                $this->layoutView->messages = '';
            }
            
            if (empty($this->layoutView->page_identifiers)) {
                $this->layoutView->page_identifiers = $this->pageIdentifiers();
            }
            
            // Pass page scripts & styles.
            
            $this->layoutView->page_scripts = $this->pageScripts;
            $this->layoutView->page_stylesheets = $this->pageStylesheets;

            // 
            
            $page = $this->layoutView;
            
            // 

            if ($this->pageView instanceof \Illuminate\View\View) {
                $page->body = $this->pageView;
            }

            // 
            
            if (empty($page->body)) {
                $page->body = '';
            }
        }

        // 

        if (!($this->response instanceof \Symfony\Component\HttpFoundation\Response)) {
            $this->response = \Response::make($this->layoutView, $this->responseCode);
        }
        
        // 

        $this->attachCookies(true);

        // 
        
        return $this->response;
    }
    
    /* */
    
    protected function pageTitle($title = null, $reset = false)
    {
        if (is_null($title)) {
            return implode($this->pageTitleSeparator, $this->pageTitle);
        }
        
        // 
        
        if ($reset) {
            $this->pageTitle = array($title);
        } else {
            $this->pageTitle[] = $title;
        }
        
        return $this;
    }
    
    /* */
    
    protected function metaDescription($description)
    {
        $this->metaDescription = $description;
        
        return $this;
    }
    
    /* */
    
    protected function loadPageLayout()
    {
        try {
            $this->setLayoutView("layouts/{$this->pageLayout}");
        } catch(\Exception $exception) {
            try {
                $this->setLayoutView("layouts/{$this->prefix}");
            } catch(\Exception $exception) {
                $this->setLayoutView("layouts/{$this->layout}");
            }
        }
    }
    
    protected function loadSublayout()
    {
        $sublayout = $this->sublayout
                        ? $this->sublayout
                        : $this->prefix;
        
        if ($sublayout) {
            try {
                $prefix_layout = clone $this->pageView;
                
                $this->setViewName($prefix_layout, "layouts/sublayouts/{$sublayout}");

                // 
                
                
                
                // 

                $prefix_layout->body = $this->pageView;

                $this->pageView = $prefix_layout;
            } catch (\Exception $e) {
                
            }
        }
    }
    
    /* */
    
    protected function callActionHandler($action = null)
    {
        $action_args = array_merge(array(&$this->pageView), $this->actionArguments);
        
        return call_user_func_array(
            array(
                $this,
                ($action) ? $action : $this->actionCallable
            ),
            $action_args
        );
    }
    
    /* */
    
    protected function setPageView($view, $page_prefix = true, $section_prefix = true)
    {
        if ($page_prefix) {
            $view = "{$this->pagePrefix}/{$view}";
        }
        
        $this->setViewName($this->pageView, $view, $section_prefix);
        
        return $this->pageView;
    }
    
    protected function setLayoutView($view, $section_prefix = true)
    {
        $this->setViewName($this->layoutView, $view, $section_prefix);
    }
    
    protected function setViewName(&$object, $view, $section_prefix = true)
    {
        if ($section_prefix) {
            $view = "{$this->section}/{$view}";
        }

        // 
        
        if ($object instanceof \Illuminate\View\View) {
            $object->setView($view);
        } else {
            $object = \View::make($view);
        }
    }
    
    /* */
    
    protected function setResponseCode($code)
    {
        $this->responseCode = $code;
    }
    
    /* */
    
    protected function pageIdentifiers()
    {
        $identifiers = array(
            'page-section-'.\Str::snake($this->section, '-'),
            'page-prefix-'.\Str::snake($this->prefix, '-'),
            'page-'.\Str::snake($this->prefix, '-').'-'.\Str::snake($this->action, '-')
        );
        
        return implode(' ', $identifiers);
    }
    
    /* */
    
    protected function setSublayoutEnabled($value = true)
    {
        $this->useSublayout = $value ? true : false;
    }
    
    protected function setAutoloadPageView($value = true)
    {
        $this->autoloadPageView = $value ? true : false;
    }
    
    //
    
    protected function setSublayout($name)
    {
        $this->sublayout = $name;
    }
    
    protected function setPageLayout($name)
    {
        $this->pageLayout = $name;
        
        return $this;
    }
    
    /* */
    
    protected function beforeProcessing()
    {
        
    }
    
    protected function afterProcessing()
    {
        
    }
    
    /* */
    
    protected function addPageScript($script)
    {
        $this->pageScripts[] = $script;
    }
    
    protected function addPageStylesheet($script)
    {
        $this->pageStylesheets[] = $script;
    }
    
    //
    
    protected function attachCookies()
    {
        foreach ($this->cookies as $cookie) {
            $this->response->withCookie($cookie);
        }
        
        return $this;
    }
    
    /* */
    
    protected function openGraph($key, $value)
    {
        switch (strtolower($key)) {
            case 'image':
                $this->openGraphImage = \URL::to(\Config::get('synergy.uploads.images.url').'resize/'.$value);
                break;
            case 'album_image':
                $this->openGraphImage = \URL::to(\Config::get('synergy.uploads.gallery.url').'m/'.$value);
                break;
        }
        
        return $this;
    }
    
    /* */
    
    protected function breadcrumb($title, $link = null)
    {
        $this->breadcrumbs[] = array($title, $link);
        
        return $this;
    }
}