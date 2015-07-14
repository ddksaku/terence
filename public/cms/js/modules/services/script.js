function synergyModuleServicesRefresh()
{
    ajaxLoadHTML('services', 'index', '#tab1 .load_page');
    ajaxLoadHTML('services', 'categories', '#tab2 .load_page');
}

$(document).ready(function()
{
    synergyModuleServicesRefresh();
});