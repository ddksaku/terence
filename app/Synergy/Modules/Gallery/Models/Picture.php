<?php

namespace Synergy\Modules\Gallery\Models;

class Picture extends \Models\BaseModel
{
    protected $table = 'synergy_pictures';
    
    protected $primaryKey = 'picture_id';
    
    const CREATED_AT = 'picture_created';
    const UPDATED_AT = 'picture_updated';
    
    //
    
    protected function updateAlbumThumbnail($album)
    {
        $candidates = $album->firstTwo();

        if ($candidates->count() > 0) {
            $album->album_thumbnail_id = $candidates->first()->picture_id;
        } else {
            $album->album_thumbnail_id = 0;
        }

        $album->save();
    }
    
    /* */
    
    public function parent()
    {
        return $this->belongsTo('\\Synergy\\Modules\\Gallery\\Models\\Album', 'picture_album_id');
    }

    public function delete()
    {
        $picture_id = $this->picture_id;
        $album = $this->parent;
        
        // 
        
        $gallery_directory = \Config::get('synergy.uploads.gallery.directory');
        
        \File::delete("{$gallery_directory}{$this->picture_file}");
        \File::delete("{$gallery_directory}s/{$this->picture_file}");
        \File::delete("{$gallery_directory}m/{$this->picture_file}");

        $return =  parent::delete();
       
        // 
        
        if ($album->album_thumbnail_id == $picture_id) {
            $this->updateAlbumThumbnail($album);
        }
        
        // 
        
        return $return;
    }
    
    public function save(array $options = array())
    {
        $new_picture = !$this->exists;
        $original_album_id = $this->getOriginal('picture_album_id');

        $return = parent::save($options);
        
        //
        
        if ($this->picture_album_id != $original_album_id) {
            $original_album = \Synergy\Modules\Gallery\Models\Album::find($original_album_id);
            
            if ($original_album && $original_album->album_thumbnail_id == $this->picture_id) {
                $this->updateAlbumThumbnail($original_album);
            }
        }

        if (!$this->parent->thumbnail) {
            $this->parent->album_thumbnail_id = $this->picture_id;
            $this->parent->save();
        }

        //
        
        return $return;
    }
}