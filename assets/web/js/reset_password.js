
        jQuery(document).ready(function () {

            $.validator.addMethod("atLeastOneUppercaseLetter", function (value, element) {
                return this.optional(element) || /[A-Z]+/.test(value);
            }, "Password must have 1 uppercase letter.");

            $.validator.addMethod("atLeastOneSymbol", function (value, element) {
                return this.optional(element) || /[!@#$%^&*()]+/.test(value);
            }, "Password must have 1 special character.");

            $.validator.addMethod("atLeastOneDigit", function (value, element) {
                return this.optional(element) || /[0-9]+/.test(value);
            }, "Password must have 1 digit.");

            $('#reset-form').validate({
                errorElement: 'div',
                errorClass: 'help-block',
                focusInvalid: false,
                rules: {
                    "password": {
                        required: true,
                        minlength: 6,
                        maxlength: 12,
                        atLeastOneUppercaseLetter: true,
                        atLeastOneSymbol: true,
                        atLeastOneDigit: true
                    },
                    "confirmPassword": {
                        equalTo: "#password",
                    },
                },
                messages: {
                    "password": {
                        minlength: "Password must contain : atleast 6 characters",
                    },
                    "confirmPassword": {
                        equalTo: "Password and confirm password must be same.",
                    },
                },
                invalidHandler: function (event, validator) { //display error alert on form submit   
                    $('.alert-danger', $('.login-form')).show();
                },
                highlight: function (e) {
                    $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
                },
                success: function (e) {
                    $(e).closest('.form-group').removeClass('has-error').addClass('has-info');
                    $(e).remove();
                },
                errorPlacement: function (error, element) {
                    if (element.is(':checkbox') || element.is(':radio')) {
                        var controls = element.closest('div[class*="col-"]');
                        if (controls.find(':checkbox,:radio').length > 1)
                            controls.append(error);
                        else
                            error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
                    } else
                        jQuery(error).insertAfter(jQuery(element).parent());
                }
            });

            $.validator.addMethod("nowhitespace", function (value, element) {
                return this.optional(element) || /^\S+$/i.test(value);
            }, "No white space please");

            $.validator.addMethod("regex", function (value, element, regexpr) {
                if (value.trim() != '') {
                    return value.match(regexpr);
                } else {
                    return true;
                }
            }, "Invalid password request");

            $(".passwordShowHide").click(function() {
                if($(this).parent().find("input").attr("type") == 'text') {
                    $(this).parent().find("input").attr("type", "password");
                    $(this).find("i").removeClass("fa-eye").addClass("fa-eye-slash");
                } else if($(this).parent().find("input").attr("type") == 'password') {
                    $(this).parent().find("input").attr("type", "text");
                    $(this).find("i").removeClass("fa-eye-slash").addClass("fa-eye");
                }
            });
        });