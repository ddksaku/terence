function synergyModuleGalleryRefresh()
{
    ajaxLoadHTML('gallery', 'index', '#tab1 .load_page');
}

function synergyModuleGalleryAlbumRefresh(callback)
{
    ajaxLoadHTML('gallery', 'albums', '#albumsLoad', callback);
}

function synergyModuleGalleryPicturesRefresh(album_id)
{
    ajaxLoadHTML(
        'gallery',
        'pictures',
        '#imageLoad',
        function ()
        {
            imgRow();
        },
        {
            album: album_id
        }
    );
}

$(document).ready(function()
{
    synergyModuleGalleryAlbumRefresh();
    
    // Call masonry plugin
    var $container = $('#container');
    $container.imagesLoaded( function(){
              $container.masonry({
                            itemSelector : '.boxs'
              });
    });
});