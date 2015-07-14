function synergyModuleNewsRefresh()
{
    ajaxLoadHTML('news', 'index', '#tab1 .load_page');
    ajaxLoadHTML('news', 'categories', '#tab2 .load_page');
}

$(document).ready(function()
{
    synergyModuleNewsRefresh();
});