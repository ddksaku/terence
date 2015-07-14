function synergyModuleUsersRefresh()
{
    if ($('.page-users-index').length) {
        ajaxLoadHTML('users', 'index', '#tab1 .load_page');
    }
}

$(document).ready(function()
{
    synergyModuleUsersRefresh();
});