jQuery("#file-0b").fileinput({
    showCaption: true,
    showUpload: false,
    previewFileType: "image",
    allowedFileExtensions: ["jpg", "png", "jpeg"],
    showRemove: true,    
    initialPreview: [
        "<img src='" + image_url + "' class='file-preview-image' height='100' width='100'>"
    ]
});


var flag = 1;
jQuery('#file-0b').on('fileerror', function (event, data, msg) {
    flag = 2;
});

jQuery('#file-0b').on('fileloaded', function (event, file, previewId, index, reader) {
    flag = 1;
});

jQuery("#edit_profile_form").on('submit', function () {

    if (flag == 2) {
        return false;
    } else {
        return true;
    }

});

jQuery.validator.addMethod('filesize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param)
}, 'File size must be less than 2 MB');

jQuery(document).ready(function () {

    jQuery("#edit_profile_form").validate({
        rules: {
            admin_name: {
                required: true,
            },
            admin_last_name: {
                required: true,
            },
            admin_email: {
                required: true,
                email: true,
            },
            photo: {
                accept: "image/jpg,image/jpeg,image/png,image/gif",
                filesize: 2000000,
            },
            totalImages: {
                required: true
            }
        },
        messages: {
            admin_name: {
                required: 'Please enter first name',
            },
            admin_last_name: {
                required: 'Please enter last name',
            },
            admin_email: {
                required: 'Please enter email',
                email: "Please enter a valid email address",
            },
            photo: {
                accept: 'Please select only image',
            },
            totalImages: {
                required: 'Please upload at least one image.'
            }
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
