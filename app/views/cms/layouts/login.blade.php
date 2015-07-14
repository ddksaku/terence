@extends('cms.layouts.default')

@section('body')

    {{ $body }}

    <!--Login div-->
    <div class="clear"></div>
    <div id="versionBar">
    <div class="copyright">Copyright &copy; <?php echo date("Y"); ?>. All Rights Reserved. <span class="tip"><a href="http://www.synergy.je" title="<?php echo \Config::get('synergy.footer.credits'); ?>"><?php echo \Config::get('synergy.footer.credits'); ?></a></span></div>
    <!-- // copyright-->
    </div>

@stop