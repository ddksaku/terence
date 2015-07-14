<?php

namespace Synergy\Modules\Gallery\Models;

class Album extends \Models\BaseModel
{
    protected $table = 'synergy_albums';
    
    protected $primaryKey = 'album_id';
    
    const CREATED_AT = 'album_created';
    const UPDATED_AT = 'album_updated';
    
    /* */

    public function thumbnail()
    {
        return $this->belongsTo('\\Synergy\\Modules\\Gallery\\Models\\Picture', 'album_thumbnail_id');
    }
    
    public function pictures()
    {
        return $this->hasMany('\\Synergy\\Modules\\Gallery\\Models\\Picture', 'picture_album_id')
            ->orderBy('picture_order', 'asc');
    }
    
    public function firstTwo()
    {
        $query = $this->pictures();
        
        if (($thumbnail = $this->thumbnail)) {
            $query->where('picture_id', '!=', $thumbnail->picture_id);
        }

        return $query->orderBy('picture_order', 'asc')
                ->limit(2)
                ->get();
    }
    
    public function delete()
    {
        $return = parent::delete();
        
        // Reorder albums
        
        $albums = self::orderBy('album_order')->get();
        
        $position = 1;
        
        foreach ($albums as $album) {
            $album->album_order = $position++;
            $album->save();
        }

        // 
        
        return $return;
    }
}