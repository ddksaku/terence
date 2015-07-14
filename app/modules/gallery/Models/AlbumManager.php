<?php

namespace Synergy\Modules\Gallery\Models;

class AlbumManager
{    
    /* */

    public function incrementAlbumOrder()
    {
        return \DB::table('synergy_albums')
            ->update(
                array('album_order' => \DB::raw('album_order + 1')
            )
        );
    }
    
    public function incrementPictureOrder($album_id)
    {
        return \DB::table('synergy_pictures')
            ->where('picture_album_id', '=', $album_id)
            ->update(
                array('picture_order' => \DB::raw('picture_order + 1')
            )
        );
    }
    
    public function setPictureOrder($picture_id, $position)
    {
        return \DB::table('synergy_pictures')
            ->where('picture_id', '=', $picture_id)
            ->update(
                array('picture_order' => $position
            )
        );
    }

    public function setAlbumOrder($album_id, $position)
    {
        return \DB::table('synergy_albums')
            ->where('album_id', '=', $album_id)
            ->update(
                array('album_order' => $position
            )
        );
    }

    public function deleteAlbum($album)
    {
        foreach ($album->pictures as $picture) {
            $picture->delete();
        }
        
        $album->delete();
    }
}