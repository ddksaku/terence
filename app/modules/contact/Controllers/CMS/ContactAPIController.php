<?php

namespace Synergy\Modules\Contact\Controllers\CMS;

class ContactAPIController extends \Controllers\CMSAPIController
{
    protected $ajaxPrefix = 'ajax/modules';
    
    /* */
    
    protected function actionAnyIndex()
    {
        $data = array();

        // 

        $view = $this->loadAjaxView('index');
        
        $view->with(
            'contact',
            \Synergy\Modules\Contact\Models\Contact::first()
        );

        $data['html'] = $view->render();

        // Return response.

        return $this->buildAjaxResponse($data);
    }

    //
    
    protected function actionPostEdit()
    {
        $data = array();
        
        // Attempt to find module registration in database.
        
        $contact = \Synergy\Modules\Contact\Models\Contact::where('contact_id', '=', $this->post->get('id'))
            ->first();
        
        if (!$contact) {
            $data['success'] = 0;
        } else {
            $contact->contact_email = $this->post->get('email');
            
            $contact->contact_email_status = ($this->post->get('emailstatusactive') == 1)
                                                ? 1
                                                : 0;
            
            $contact->contact_name = $this->post->get('name');
            $contact->contact_address = $this->post->get('address');
            $contact->contact_phone = $this->post->get('phone');
            $contact->contact_mobile = $this->post->get('mobile');
            $contact->contact_fax = $this->post->get('fax');

            $contact->contact_map_status = ($this->post->get('map_status') == 1)
                                                ? 1
                                                : 0;

            $contact->contact_type_value = ($map_type = $this->post->get('type_value'))
                                            ? $map_type
                                            : 'ROADMAP';
            $contact->contact_lon_value = $this->post->get('lon_value');
            $contact->contact_lat_value = $this->post->get('lat_value');
            $contact->contact_zoom_value = $this->post->get('zoom_value');

            $contact->save();

            /* Output data */
            
            $data['success'] = 1;
        }
        
        // Return response.

        return $this->buildAjaxResponse($data);
    }
}