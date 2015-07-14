function synergyModulePagesRefresh()
{
    ajaxLoadHTML('pages', 'index', '#tab1 .load_page');
}

$(document).ready(function()
{
    synergyModulePagesRefresh();
});