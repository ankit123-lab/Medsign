jQuery(document).ajaxSuccess(function (event, xhr, settings) {
    if (settings.url != home_url + "/get_csrf_value") {
        jQuery.ajax({
            url: home_url + "/get_csrf_value",
            type: 'POST',
            async: false,
            cache: false,
            success: function (data) {
                if (data.success) {
                    jQuery('input:hidden[name="' + get_csrf_token_name + '"]').val(data.value);
                } else {
                    window.location.reload();
                }
            },
            error: function () {
                window.location.reload();
            }
        });
    }
});

jQuery(document).ajaxError(function (event, xhr, settings) {
    if (settings.url != home_url + "/get_csrf_value") {
        jQuery.ajax({
            type: 'POST',
            async: false,
            url: home_url + "/get_csrf_value",
            success: function (data) {
                if (data.success) {
                    jQuery('input:hidden[name="' + get_csrf_token_name + '"]').val(data.value);
                } else {
                    window.location.reload();
                }
            },
            error: function () {
                window.location.reload();
            }
        });
    }
});

jQuery.ajaxSetup({
    beforeSend: function (xhr, data) {
        var token_val = jQuery('input:hidden[name="' + get_csrf_token_name + '"]').val();
        data.data += '&' + get_csrf_token_name + '=' + token_val;
        if (get_method != 'get_csrf_value') {
            $('#loading').show();
        }
    },
    complete: function () {
        $('#loading').hide();
    }
});

/*--------change lang-----
 jQuery(".change_language").change(function () {
 var language = jQuery(this).val();
 jQuery.ajax({
 type: 'POST',
 async: false,
 url: home_url + "/change_language",
 data: {"language": language},
 success: function (data) {
 if (data.success) {
 window.location.reload();
 } else {
 toastr.error(change_language_error_message, {timeOut: 5000});
 }
 },
 error: function () {
 toastr.error(internal_error_message, {timeOut: 5000});
 }
 });
 });
 
   ----*/