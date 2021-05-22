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

jQuery("#custom_form").on('submit', function () {
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

    $.validator.addMethod("lettersonly", function (value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z\s]+$/);
    }, 'Please enter only alphabetical letters.');

    jQuery("#custom_form_add").validate({
        rules: {
            first_name: {
                required: true,
                minlength: 2,
                lettersonly: true
            },
            last_name: {
                required: true,
                minlength: 2,
                lettersonly: true
            },
            email: {
                required: true,
                email: true,
            },
            password: {
                required: true,
                minlength: 6,
                maxlength: 12,
            },
            cpassword: {
                required: true,
                equalTo: "#password",
            },
            photo: {
                accept: "image/jpg,image/jpeg,image/png,image/gif",
                filesize: 2000000,
            }
        },
        messages: {
            first_name: {
                required: 'First Name field cannot be blank!',
            },
            last_name: {
                required: 'Last Name field cannot be blank!',
            },
            email: {
                required: 'Email field cannot be blank!',
            },
            password: {
                required: 'Password field cannot be blank!',
                minlength: 'Password must contain minimum 6 characters',
                maxlength: 'Password must contain maximum 12 characters',
            },
            cpassword: {
                required: 'Confirm Password field cannot be blank!',
                equalTo: 'Password and confirm password must be same',
            },
            photo: {
                required: 'Please select image',
                accept: 'Please select only image',
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

    jQuery("#custom_form_edit").validate({
        rules: {
            first_name: {
                required: true,
                minlength: 2,
                lettersonly: true
            },
            last_name: {
                required: true,
                minlength: 2,
                lettersonly: true
            },
            email: {
                required: true,
                email: true,
            },
            password: {
                minlength: 6,
                maxlength: 12,
            },
            cpassword: {
                equalTo: "#password",
            },
            photo: {
                accept: "image/jpg,image/jpeg,image/png,image/gif",
                filesize: 2000000,
            }
        },
        messages: {
            first_name: {
                required: 'Please enter first name',
            },
            last_name: {
                required: 'Please enter last name',
            },
            email: {
                required: 'Please enter email',
            },
            password: {
                required: 'Please enter new password',
                minlength: 'Password must contain minimum 6 characters',
                maxlength: 'Password must contain maximum 12 characters',
            },
            cpassword: {
                required: 'Please enter confirm password',
                equalTo: 'Password and confirm password must be same',
            },
            photo: {
                accept: 'Please select only image',
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
