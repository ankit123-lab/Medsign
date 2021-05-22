$(function () {
    $('.dateOfBirth').datepicker({
        autoclose: true,
        todayHighlight: true,
        endDate: '+0d',
        format: 'dd/mm/yyyy'
    });
});
$(document).ready(function () {
	$('.dateOfBirth').mask("00/00/0000", {placeholder: "DD/MM/YYYY"});
	$("#patient-register-form").validate({
        rules: {
            user_first_name: {
                required: true
            },
            user_last_name: {
                required: true
            },
            date_of_birth: {
                required: true
            },
            gender: {
                required: true
            },
            terms_conditions: {
                required: true
            },
            user_phone_number: {
                digits: true,
                minlength: 10,
                required: true
            },
            user_email: {
                email: true
            },
            user_password: {required: true, minlength : 6},
            c_user_password: {
                equalTo: "#user_password"
            }
        },
        messages: {
            user_first_name: "Please enter first name",
            user_last_name: "Please enter last name",
            date_of_birth: "Please enter date of birth",
            terms_conditions: "Accept terms & conditons",
            gender: "Please select gender",
            user_password: {required: "Please Enter Password", minlength: "Enter password at least 6 characters"},
            c_user_password: " Enter Confirm Password Same as Password",
            user_email: {email: "Your email address is invalid"},
            user_phone_number: {required: "Please enter phone number", digits: "Your phone number is invalid", minlength: "Your phone number is invalid"}
        },
        submitHandler: function() { 
            $("#register_server_side_error").hide();
            $("#reg-btn").hide();
            $(".loader-img").show();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: $("#patient-register-form").serialize(),
                url: site_url + "patient/patient_register",
                success: function (data) {
                    if (data.error) {
                        $("#register_server_side_error").html(data.error);
                        $("#register_server_side_error").addClass("alert alert-danger");
                        $("#register_server_side_error").show();
                        $(".loader-img").hide();
                        $("#reg-btn").show();
                        $('html, body').animate({
                            scrollTop: $("#register_server_side_error").offset().top - 100
                        }, 1000);
                    }
                    if(data.status == true) {
                        if($("#redirect_page").val() != '') {
                            window.location.href = site_url + "patient/" + $("#redirect_page").val();
                        } else {
                            window.location.href = site_url + "patient/profile/update";
                        }
                    }
                }
            });
        },
        highlight: function (element) {
            
        },
        unhighlight: function (element) {
            
        }
    }); 
});