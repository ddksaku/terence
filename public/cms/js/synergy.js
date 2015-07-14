function synergyRefreshModulesList()
{
    ajaxLoadHTML('modules', 'index', '#tab1 .load_page');
}

function synergyRefreshSettingsList()
{
    ajaxLoadHTML('settings', 'index', '#tab1 .load_page');
}

function synergyUpdateMenu(modules)
{
    var template = $('.main-menu-template li');
    
    var new_menu_list = $('<div></div>');
    
    $('#main_menu li:not(.module-item)').each(function()
    {
        var item_clone = $(this).clone();
        
        new_menu_list.append(item_clone);
    });
    
    for (var index in modules) {
        var new_menu_item = template.clone();

        var module = modules[index];

        new_menu_item.find('.menu-item-url').attr('href', module.module_url);
        new_menu_item.find('.menu-item-name').text(module.module_name);
        
        if (module.module_icon) {
            new_menu_item.find('.menu-item-icon').addClass(module.module_icon);
        }

        new_menu_list.append(new_menu_item);
    }
    
    $('#main_menu').html(new_menu_list.html());
}

function synergyTagsAutocomplete(tag_input)
{
    $(tag_input).tagsInput({
        width: 'auto',
        'autocomplete_url': function(request, response) {
            $.ajax({
                url: 'cmsapi/tags',
                data: {
                    selected: $(tag_input).val(),
                    term: request.term
                },
                dataType: "json",
                success: function (data) {
                    response(data);
                },
                error: function () {
                    response([]);
                }
            });
        }
    });
}

$(document).ready(function()
{
    /* System-wide scroll for modals */
    
    $.extend($.fn.fancybox.defaults, {
        onStart: function() {
            $(window).scrollTop(0);
        }
    });
    
    /* System-wide disabling of caching for AJAX requests. */
    
    $.ajax({ cache: false });
    
    /* New Synergy lightbox API */
    
    $(document).on('click', '.synergy-modal', function()
    {
        var linkHref = $(this).attr('href');
        var requestMethod = $(this).data('method');
        var dataElements = $(this).data();
        
        var preAuth = ($(this).data('pre-auth') == 'yes') ? true : false;

        $.ajax({
            type: 'GET',
            url: 'loginapi/checkloggedin',
            success: function(data)
            {
                if (data.logged_in) {
                    var data_elements = dataElements;

                    var had_one = false;

                    var data_string = '';

                    for (var index in data_elements) {
                        if (had_one) {
                            data_string += '&';
                        }

                        data_string += index + '=' + data_elements[index];
                        
                        had_one = true;
                    }
                    
                    var preAuthPassed = false;

                    if (preAuth) {
                        $.ajax({
                            async: false,
                            type: (requestMethod == 'POST') ? 'POST' : 'GET',
                            url: linkHref,
                            data: data_string,
                            success: function(data) {
                                if (data.error) {
                                   alertMessage("error", data.message);
                                } else {
                                    preAuthPassed = true;
                                }
                            },
                            error: function(data) {
                                preAuthPassed = true;
                            },
                            dataType: 'json'
                        });
                    }
                    
                    if (!preAuth || preAuthPassed) {
                        $('<a href="#"></a>').attr('href', linkHref).fancybox({
                            ajax: {
                                type: (requestMethod == 'POST') ? 'POST' : 'GET',
                                data: data_string
                            },
                            showCloseButton: false
                        }).click().remove();
                    }
                } else {
                    window.location.replace('cms');
                }
            },
            dataType: 'json'
        });

        return false;
    });
    
    /* */
    
    if ($('.page-modules-index').length) {
        synergyRefreshModulesList();

        $('.page-modules-index').on('change', '.module-status input', function()
        {
            ajaxCallAPI(
                'modules',
                'post',
                'status',
                null,
                {
                    id: $(this).data('module-id'),
                    status: $(this).attr('checked') ? '1' : '0'
                },
                function(data)
                {
                    synergyUpdateMenu(data.modules);
                }
            );

            alertMessage("success", "Module status updated");
        });
    }
    
    /* */
    
    if ($('.page-settings-index').length) {
        synergyRefreshSettingsList();
    }
    
    /* */
});

if (typeof document.forms['formLogin'] != 'undefined'
    && typeof document.forms['formLogin'].elements['username'] != 'undefined') {
    document.forms['formLogin'].elements['username'].focus();
}

if (typeof document.forms['formLogin'] != 'undefined'
    && typeof document.forms['formLogin'].elements['user_email'] != 'undefined') {
    document.forms['formLogin'].elements['user_email'].focus();
}

if (typeof document.forms['formnewpass'] != 'undefined'
    && typeof document.forms['formnewpass'].elements['password'] != 'undefined') {
    document.forms['formnewpass'].elements['password'].focus();
}