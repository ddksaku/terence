function ajaxCallAPI(api, method, url, url_data, post_data, callback, require_login)
{
    if (typeof require_login == 'undefined') {
        var require_login = true;
    }
    
    var doCallApi = function()
    {
        if (url.indexOf('?') === -1) {
            url += '?';
        } else {
            url += '&';
        }

        url = api + 'api/' + url + 'cachekill=' + ((new Date).getTime());

        $.ajax({
            type: (method.toLowerCase() == 'post') ? 'POST' : 'GET',
            url: url,
            data: post_data,
            success: callback,
            dataType: 'json'
        });
    };

    if (require_login) {
        $.ajax({
            type: 'GET',
            url: 'loginapi/checkloggedin',
            success: function(data)
            {
                if (data.logged_in) {
                    doCallApi();
                } else {
                    window.location.replace('cms');
                }
            },
            dataType: 'json'
        });
    } else {
        doCallApi();
    }
}

function ajaxLoadHTML(api, url, target, callback, data)
{
    if (typeof data == 'undefined') {
        data = null;
    }
    
    loading('Loading', 0);

    // 
    
    ajaxCallAPI(
        api,
        'GET',
        url,
        null,
        data,
        function(data)
        {
            if ($(target).length) {
                $(target).html(data.html);
                $(target).fadeTo(0, 0);
                $(target).fadeTo(400, 1);
            }

            unloading();

            if (typeof callback == 'function') {
                callback(data);
            }
        }
    );
}