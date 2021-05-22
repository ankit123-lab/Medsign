autosize($('textarea.auto-growth'));

jQuery("#send_notification_form").validate({
    rules: {
        message: {
            required: true,
            minlength: 1,
            maxlength: 255
        }
    },
    messages: {
    },
    errorPlacement: function (error, element) {
        if (element.hasClass('chosen-select'))
            jQuery(error).insertAfter(jQuery(element).siblings(".chosen-container"));
        else
            jQuery(error).insertAfter(jQuery(element).parent());
    }
});