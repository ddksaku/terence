function synergyModulePortfolioRefresh()
{
    ajaxLoadHTML('portfolio', 'index', '#tab1 .load_page');
    ajaxLoadHTML('portfolio', 'categories', '#tab2 .load_page');
}

$(document).ready(function()
{
    synergyModulePortfolioRefresh();
});