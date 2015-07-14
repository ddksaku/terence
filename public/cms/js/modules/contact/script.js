function synergyModuleContactRefresh()
{
    if ($('.page-contact-index').length) {
        ajaxLoadHTML('contact', 'map', '.map-load-script');
    }
}

$(document).ready(function()
{
    synergyModuleContactRefresh();
});