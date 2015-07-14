<!-- Widget -->
<div class="widget span12 clearfix">

    <div class="widget-header">
        <span><span class="ico gray shadow <?php echo $module->module_icon; ?>"></span> <?php echo $module->getName(); ?></span>
    </div><!-- End widget-header -->	

    <div class="widget-content">

        <div id="UITab" style="position:relative;">
            <ul class="tabs">
                <li><a href="#tab1">All News</a></li>
                <li><a href="#tab2">Categories</a></li>
            </ul>
            <div class="tab_container">

                <div id="tab1" class="tab_content"> 
                    <!-- Table Reload -->
                    <div class="load_page"></div>
                </div>
                  
              <div id="tab2" class="tab_content"> 
                <!-- Categories Reload -->
                <div class="load_page"></div>
              </div>

            </div><!-- End tab_container -->
        </div><!-- End UITab -->
        
        <div class="clearfix"></div>

    </div><!--  end widget-content -->
</div><!-- widget  span12 clearfix-->
