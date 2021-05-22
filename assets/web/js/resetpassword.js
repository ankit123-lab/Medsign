$(document).ready(function () {
	$("#reset-password-form").validate({
        rules: {
            user_password: {required: true, minlength : 6},
            c_user_password: {
                equalTo: "#user_password"
            }
        },
        messages: {
            user_password: {required: "Please Enter Password", minlength: "Enter password at least 6 characters"},
            c_user_password: " Enter Confirm Password Same as Password"
            
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