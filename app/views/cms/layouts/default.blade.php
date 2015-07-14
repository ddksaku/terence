<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Required to rewrite urls -->
        <base href="<?php echo \Request::root(); ?>/<?php echo \Request::segment(1); ?>/">

        <!-- Link shortcut icon-->
        <link rel="shortcut icon" type="image/ico" href="images/favicon.ico"/> 

        <!--[if lt IE 9]>
          <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- CSS Stylesheet-->
        <link type="text/css" rel="stylesheet" href="components/bootstrap/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="components/bootstrap/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="css/style.css"/>
        <link type="text/css" rel="stylesheet" href="css/icon.css"/>

        <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="components/flot/excanvas.min.js"></script><![endif]-->  

        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="components/ui/jquery.ui.min.js"></script> 
        <script type="text/javascript" src="components/bootstrap/bootstrap.min.js"></script>
        <script type="text/javascript" src="components/ui/timepicker.js"></script>
        <script type="text/javascript" src="components/colorpicker/js/colorpicker.js"></script>
        <script type="text/javascript" src="components/form/form.js"></script>
        <script type="text/javascript" src="components/elfinder/js/elfinder.full.js"></script>
        <script type="text/javascript" src="components/datatables/dataTables.min.js"></script>
        <script type="text/javascript" src="components/fancybox/jquery.fancybox.js"></script>
        <script type="text/javascript" src="components/jscrollpane/jscrollpane.min.js"></script>
        <script type="text/javascript" src="components/editor/jquery.cleditor.js"></script>
        <script type="text/javascript" src="components/chosen/chosen.js"></script>
        <script type="text/javascript" src="components/validationEngine/jquery.validationEngine.js"></script>
        <script type="text/javascript" src="components/validationEngine/jquery.validationEngine-en.js"></script>
        <script type="text/javascript" src="components/fullcalendar/fullcalendar.js"></script>
        <script type="text/javascript" src="components/flot/flot.js"></script>
        <script type="text/javascript" src="components/uploadify/uploadify.js"></script>       
        <script type="text/javascript" src="components/Jcrop/jquery.Jcrop.js"></script>
        <script type="text/javascript" src="components/smartWizard/jquery.smartWizard.min.js"></script>
        <script type="text/javascript" src="js/jquery.cookie.js"></script>
        
         <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
        <script src="components/blueimp/js/vendor/jquery.ui.widget.js"></script>
        <!-- The Templates plugin is included to render the upload/download listings -->

        <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
		<script src="js/load-image.min.js"></script>
        <!-- The Canvas to Blob plugin is included for image resizing functionality -->
        <script src="js/canvas-to-blob.min.js"></script>

        <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
        <script src="components/blueimp/js/jquery.iframe-transport.js"></script>

        <!-- The basic File Upload plugin -->
        <script src="components/blueimp/js/jquery.fileupload.js"></script>
        <!-- The File Upload processing plugin -->
        <script src="components/blueimp/js/jquery.fileupload-process.js"></script>
        <!-- The File Upload image resize plugin -->
        <script src="components/blueimp/js/jquery.fileupload-resize.js"></script>
        <!-- The File Upload validation plugin -->
        <script src="components/blueimp/js/jquery.fileupload-validate.js"></script>

        <script type="text/javascript" src="components/masonry/jquery.masonry.min.js"></script>
        
        
	<script src="components/msDropdown/js/msdropdown/jquery.dd.min.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="components/msDropdown/css/msdropdown/flags.css" />
        
        <?php
        
        foreach ($page_scripts as $script) {
            ?><script src="js/<?php echo $script; ?>" type="text/javascript"></script>
            <?php
        }
        
        foreach ($page_stylesheets as $stylesheet) {
            ?><link rel="stylesheet" type="text/css" href="<?php echo $stylesheet; ?>">
            <?php
        }
        
        ?>
        
    </head>
    <body class="<?php echo $page_identifiers; ?>">

        @section('body')

            <div id="header">
                <ul id="account_info" class="pull-right">
                    <li>
                        <span class="ntip">
                            <a href="usersapi/profile" class="synergy-modal" title="Edit Profile">
                                <i class="icon-user icon-large"></i>
                                <?php echo $user->getFullName(); ?>
                            </a> (<?php echo $user->getUserLevel(); ?>)
                        </span>
                    </li>
                    
                    <?php if($user->hasPermission('view_settings')): ?>
                        <a href="settingsapi/edit" class="synergy-modal">
                            <li
                                <?php if (\Request::segment(2) == 'settings'): ?>
                                class="select"
                                <?php endif; ?>
                                >
                                <i class="icon-cogs icon-large"></i>
                                Settings
                            </li>
                        </a>
                    <?php endif; ?>
                        
                    <?php if($user->hasPermission('view_modules')): ?>
                        <a href="modules/">
                            <li
                                <?php if (\Request::segment(2) == 'modules'): ?>
                                class="select"
                                <?php endif; ?>
                                >
                                <i class="icon-lock icon-large"></i>
                                Modules
                            </li>
                        </a>
                    <?php endif; ?>

                    <a href="logout">
                        <li class="logout">
                            <i class="icon-signout icon-large"></i>
                            Logout
                        </li>
                    </a>
                </ul>
            </div>

            <!-- left_menu -->

            <div id="left_menu">
                <ul id="main_menu" class="main_menu">
                    <li
                        <?php if (!\Request::segment(2) || \Request::segment(2) == 'index'): ?>
                        class="select"
                        <?php endif; ?>
                        >
                            <a href="">
                                <span class="ico gray home"></span>
                                <span>Dashboard</span>
                            </a>
                    </li>
                    
                    <?php

                    foreach ($modules as $module) {
                        $module_url = $module->getURL();
                        
                        ?>
                        <li class="module-item <?php if (\Request::segment(2) == $module_url): ?>select<?php endif; ?>">
                            <a href="<?php echo $module_url; ?>">
                                <span class="ico gray shadow <?php echo $module->module_icon; ?>"></span>
                                <span><?php echo $module->getName(); ?></span>
                            </a>
                        </li>
                        <?php
                    }

                    ?>
                </ul>
                
                <ul class="main-menu-template">
                    <li class="module-item">
                        <a href="" class="menu-item-url">
                            <span class="ico gray shadow menu-item-icon"></span>
                            <span class="menu-item-name"></span>
                        </a>
                    </li>
                </ul>
                
            </div>
			  <!-- End left_menu -->

            
              <div id="content" >
                <div class="inner">
                                    
                    <div class="row-fluid">
                          <div class="span12 clearfix">

                          </div>
                    </div>   
                    
                    <div class="row-fluid">
                        
                        {{ $body }}
                        
                    </div><!-- row-fluid -->
                    
                   <div id="footer">
                       Copyright &copy; <?php echo date("Y"); ?>.
                       All Rights Reserved.
                       <span class="tip"><a href="http://www.synergy.je" title="<?php echo \Config::get('synergy.footer.credits'); ?>"><?php echo \Config::get('synergy.footer.credits'); ?></a></span>
                   </div>
                    
                </div> <!--// End inner -->
              </div> <!--// End ID content -->

        @show

        <!-- Link JScript-->
        <script type="text/javascript" src="js/custom.js"></script>
        <script type="text/javascript" language="JavaScript">
            // document.forms['formLogin'].elements['username'].focus();
        </script>

        <script type="text/javascript" src="js/ajax_api.js"></script>
        <script type="text/javascript" src="js/synergy.js"></script>
    </body>
</html>