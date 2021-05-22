/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function () {
    $('input').keypress(function( e ) {
        if(e.which === 32) 
            return false;
    });

    $("#change_password_form").validate({
        rules: {
            old_password: {
                required: true,
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
        },
        messages: {
            old_password: {
                required: 'Please enter current password',
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
        },
        errorPlacement: function (error, element) {

            if (element.hasClass('file'))
                $(error).insertAfter($(element).parents(".file-input"));
            else if (element.hasClass('chosen-select'))
                $(error).insertAfter($(element).siblings(".chosen-container"));
            else
                $(error).insertAfter(element);

        },
        errorClass: 'error',
    });
});

