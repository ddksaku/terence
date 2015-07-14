function synergyModuleUsersRefresh()
{
    ajaxLoadHTML('users', 'index', '#tab1 .load_page');
}

$(document).ready(function()
{
    synergyModuleUsersRefresh();
});