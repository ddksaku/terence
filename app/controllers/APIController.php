<?php

namespace Controllers;

class APIController extends \Controllers\BaseController
{
    protected $ajaxPrefix = 'ajax';
    
    /* */
    
    protected function buildAjaxResponse($data, $convert = true)
    {
        if ($convert && is_array($data)) {
            $data = (object)$data;
        }
        
        if ($data instanceof \Illuminate\View\View) {
            $response = \Response::make($data);
        } else {
            $response = \Response::json($data);
        }

        return $response;
    }
    
    protected function buildEmptyResponse($code = 200, $content_type = 'text/html')
    {
        $response = \Response::make('', $code);
        
        $response->header('Content-Type', $content_type);
        
        return $response;
    }
    
    protected function handlePageRequest()
    {
        if (!$this->actionCallable) {
            $this->setResponseCode(404);
            
            return $this->buildAjaxResponse(
                array(
                    'error' => '404'
                )
            );
        } else {
            $result = $this->callActionHandler();

            if ($result !== false && !($result instanceof \Symfony\Component\HttpFoundation\Response)) {
                $result = \Response::json(array('success' => 'true'));
            }

            return $result;
        }
    }
    
    protected function loadAjaxView($view)
    {
        return \View::make("{$this->section}/{$this->ajaxPrefix}/{$this->prefix}/{$view}");
    }

    protected function loadPageView()
    {
    }
}