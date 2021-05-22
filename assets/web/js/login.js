$(document).ready(function () {
	$("#login-form").validate({
        rules: {
            phone_number: {
                digits: true,
                minlength: 10,
                required: true
            },
            user_password: {required: true}
        },
        messages: {
            phone_number: {required: "Please enter phone number", digits: "Number is invalid", minlength: "Number is invalid"},
            user_password: {required: "Please enter password"}
        },
        submitHandler: function(form) { 
            form.submit();
        },
        highlight: function (element) {
            
        },
        unhighlight: function (element) {
            
        }
    }); 

    $("#forgot-password-form").validate({
        rules: {
            phone_number: {
                digits: true,
                minlength: 10,
                required: true
            }
        },
        messages: {
            phone_number: {required: "Please enter phone number", digits: "Number is invalid", minlength: "Number is invalid"}
        },
        submitHandler: function(form) { 
            form.submit();
        },
        highlight: function (element) {
            
        },
        unhighlight: function (element) {
            
        }
    }); 
});