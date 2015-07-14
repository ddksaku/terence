<?php

namespace Controllers\CMS;

class CMSAPIController extends \Controllers\CMSAPIController
{
    protected function actionGetTags()
    {
        $data = array();

        if ($this->input->has('selected')) {
            $selected = explode(',', $this->input->get('selected'));
        } else {
            $selected = null;
        }

        // Attempt to find user.
        
        $query = \Models\Site\Tag::where('tag_name', 'LIKE', "%{$this->input->get('term')}%");

        if (!empty($selected)) {
            $query = $query->whereNotIn('tag_name', $selected);
        }

        $tags = $query->orderBy('tag_name', 'ASC')
            ->get();
        
        foreach ($tags as $tag) {
            $data[] = array(
                'id' => $tag->tag_id,
                'value' => $tag->tag_name
            );
        }
        
        // Return response.

        return $this->buildAjaxResponse($data, false);
    }
}