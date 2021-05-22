jQuery(document).ready(function () {

    $.validator.addMethod("lettersonly", function (value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z\s]+$/);
    }, 'Please enter only alphabetical letters.');

    jQuery("#custom_form_add").validate({
        rules: {
            name: {
                required: true,
                minlength: 2,
                lettersonly: true
            },
        },
        messages: {
        },
        errorPlacement: function (error, element) {
            var next = element;
            if (element.hasClass('file'))
                jQuery(error).insertAfter(jQuery(element).parents(".file-input"));
            else if (element.hasClass('chosen-select'))
                jQuery(error).insertAfter(jQuery(element).siblings(".chosen-container"));
            else
                jQuery(error).insertAfter(jQuery(element).parent());
        },
    });

    jQuery("#custom_form_edit").validate({
        rules: {
            name: {
                required: true,
                minlength: 2,
                lettersonly: true
            },
        },
        messages: {
        },
        errorPlacement: function (error, element) {
            var next = element;
            if (element.hasClass('file'))
                jQuery(error).insertAfter(jQuery(element).parents(".file-input"));
            else if (element.hasClass('chosen-select'))
                jQuery(error).insertAfter(jQuery(element).siblings(".chosen-container"));
            else
                jQuery(error).insertAfter(jQuery(element).parent());
        },
    });
});
