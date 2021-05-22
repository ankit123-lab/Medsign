jQuery("#setting_add_form").validate({
        rules: {
            
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