<!-- Widget -->
<div class="widget span12 clearfix">

    <div class="widget-header">
        <span><span class="ico gray shadow <?php echo $module->module_icon; ?>"></span> <?php echo $module->getName(); ?></span>

        <div id="buttom_top">
            <ul class="uibutton-group">
                <li><a class="uibutton icon add synergy-modal" href="galleryapi/editalbum">New Album</a></li>
                <li><a class="uibutton disable icon secure synergy-modal" href="galleryapi/upload" id="uploadAlbum">Upload Images</a><div id="uploadDisableBut"></div></li>
            </ul>
        </div>
    </div><!-- End widget-header -->	

    <div class="widget-content">

          <!-- Albums Load -->
          <div id="albumsLoad"></div>
          <!-- screen Load messages -->
          <div class="screen-msg"><span class="icon"><img src="images/icon/gray_18/pictures_folder.png" alt="Add Images"/></span>Click an album to add images.</div>
          <!-- Images Load -->
          <div id="imageLoad"></div>
              <div class="clearfix"></div>

    </div><!-- end widget-content -->
</div><!-- widget  span12 clearfix-->