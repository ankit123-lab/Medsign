
function nextTab(elem) {
    jQuery(elem).next().find('a[data-toggle="tab"]').click();
}

function prevTab(elem) {
    jQuery(elem).prev().find('a[data-toggle="tab"]').click();
}

function next_tab() {
    var $active = jQuery('.wizard .nav-tabs li.active');
    $active.addClass("done_step");
    var fordisablewiz = $active.attr('id');
    if (fordisablewiz == 'wizard_first' || fordisablewiz == 'wizard_second') {
        $active.addClass("disabled");
    }
    $active.next().removeClass('disabled');
    nextTab($active);
}

jQuery(document).ready(function () {
    var pad = jQuery(".navbar").outerHeight() + 50;
    jQuery(".login_reigstration").css('padding-top', pad + 'px');
    jQuery(".forgot_password_container").css('padding-top', pad + 'px');
    jQuery(".verify_otp_container").css('padding-top', pad + 'px');
    jQuery("#login_with_email_form").hide();
    jQuery(".verify_otp_container").hide();
    jQuery(".forgot_password_container").hide();

    jQuery('#login_form_link').click(function (e) {
        jQuery("#login_form .has-error").removeClass('has-error');
        jQuery("#login_form .help-block").remove();
        jQuery(".forgot_password_container").delay(100).fadeOut(100);
        jQuery("#login_form").delay(100).fadeIn(100);
        jQuery("#register_form").fadeOut(100);
        jQuery("#login_with_email_form").fadeOut(100);
        jQuery('#register_form_link').removeClass('active');
        jQuery(this).addClass('active');
        e.preventDefault();
    });

    jQuery('#register_form_link').click(function (e) {
        jQuery("#register_form .has-error").removeClass('has-error');
        jQuery("#register_form .help-block").remove();
        jQuery(".forgot_password_container").delay(100).fadeOut(100);
        jQuery("#register_form").delay(100).fadeIn(100);
        jQuery("#login_form").fadeOut(100);
        jQuery("#login_with_email_form").fadeOut(100);
        jQuery('#login_form_link').removeClass('active');
        jQuery(this).addClass('active');
        e.preventDefault();
    });

    jQuery('#login_with_email').click(function (e) {
        jQuery("#login_with_email_form .has-error").removeClass('has-error');
        jQuery("#login_with_email_form .help-block").remove();
        jQuery(".forgot_password_container").delay(100).fadeOut(100);
        jQuery("#register_form").delay(100).fadeOut(100);
        jQuery("#login_form").delay(100).fadeOut(100);
        jQuery("#login_form").hide();
        jQuery("#register_form").hide();
        jQuery("#login_with_email_form").fadeIn(100);
        jQuery('#login_form_link').addClass('active');
        jQuery(this).addClass('active');
        e.preventDefault();
    });

    jQuery('#login_with_mobile').click(function (e) {
        jQuery("#login_form .has-error").removeClass('has-error');
        jQuery("#login_form .help-block").remove();
        jQuery(".forgot_password_container").delay(100).fadeOut(100);
        jQuery("#register_form").delay(100).fadeOut(100);
        jQuery("#login_with_email_form").delay(100).fadeOut(100);
        jQuery("#login_form").hide();
        jQuery("#login_with_email_form").hide();
        jQuery("#login_form").fadeIn(100);
        jQuery('#login_form_link').addClass('active');
        jQuery(this).addClass('active');
        e.preventDefault();
    });

    jQuery('#forgot_password').click(function (e) {
        jQuery("#email_forgot_password .has-error").removeClass('has-error');
        jQuery("#email_forgot_password .help-block").remove();
        jQuery(".login_reigstration").hide();
        jQuery(".forgot_password_container").show();
    });

    jQuery('#login_again').click(function (e) {
        jQuery("#login_with_email_form .has-error").removeClass('has-error');
        jQuery("#login_with_email_form .help-block").remove();
        jQuery(".forgot_password_container").hide();
        jQuery(".login_reigstration").show();
        jQuery('#login_form_link').addClass('active');
        jQuery(this).addClass('active');
        e.preventDefault();
    });

    /*-------specialization---------*/
    var add_more_specialization_div_html = jQuery('.add_more_specialization_div div:first').html();
    jQuery(".add_more_specialization").click(function () {
        var html = '<div class="clearfix"></div><div class="extra_specialization">\
                                <div class="col-md-6  col-sm-6">\
                                <div class="form-group m_top_22">\
                                    <input type="text" name="specialization[]" id="username" tabindex="1" class="form-control login_input" placeholder="Write Specialization" value="">\
                                </div>\
                            </div>\
                            <div class="col-md-5 col-sm-5 col-xs-10 ">\
                                <div class="form-group m_top_22">\
                                    <select class="form-control select_input " id="sel1">\
                                        <option>Select Year of exp</option>\
                                        <option>2</option>\
                                        <option>3</option>\
                                        <option>4</option>\
                                    </select>\
                                </div>\
                            </div>\
                            <div class="col-md-1  col-sm-1 col-xs-2 remove_more">\
                                <div class="form-group m_top_22">\
                                    <img src="' + remove_button_png + '" class="minus_button remove_specialization">\
                                </div></div></div><div class="clearfix"></div>';
        jQuery(".add_more_specialization_div").append(html);
    });

    jQuery(document).on('click', '.remove_specialization', function () {
        jQuery(this).parent().parent().parent().remove();
    });
    /*-------specialization---------*/

    var add_more_edu_qualification_div_html = jQuery('.add_more_edu_qualification_div div:first').html();
    jQuery(".add_more_edu_qualification").click(function () {
        var html1 = '<div class="extra_edu_qualification"><div class="col-md-4 col-sm-4">\
                                <div class="form-group ">\
                                    <select class="form-control select_input m_top_22" id="sel1">\
                                        <option>Select Degree</option>\
                                        <option>2</option>\
                                        <option>3</option>\
                                        <option>4</option>\
                                    </select>\
                                </div>\
                            </div>\
                            <div class="col-md-4 col-sm-4">\
                                <div class="form-group ">\
                                    <select class="form-control select_input m_top_22" id="sel1">\
                                        <option>Select College/University</option>\
                                        <option>2</option>\
                                        <option>3</option>\
                                        <option>4</option>\
                                    </select>\
                                </div>\
                            </div>\
                            <div class="col-md-3 col-sm-3 col-xs-10">\
                                <div class="form-group ">\
                                    <select class="form-control select_input m_top_22" id="sel1">\
                                        <option>Year</option>\
                                        <option>2</option>\
                                        <option>3</option>\
                                        <option>4</option>\
                                    </select>\
                                </div>\
                            </div>\
                            <div class="col-md-1 col-sm-1 col-xs-2 remove_more">\
                                <div class="form-group m_top_22">\
                                    <img src="' + remove_button_png + '" class="minus_button remove_edu_qualification">\
                                </div>\
                            </div></div>';
        jQuery(".add_more_edu_qualification_div").append(html1);
    });
    jQuery(document).on('click', '.remove_edu_qualification', function () {
        jQuery(this).parent().parent().parent().remove();
    });
    /*------registration*-----*/



    jQuery(".add_more_registration_details").click(function () {
        var html2 = '<div class="extra_registration_details">\
                                <div class="col-md-4 col-sm-4">\
                                    <div class="form-group nolabel">\
                                    <div class="f_label"></div>\
                                        <input type="text" name="username" id="username" tabindex="1" class="form-control login_input" placeholder="Enter regsitration details" value="">\
                                    </div>\
                                </div>\
                                <div class="col-md-4 col-sm-4">\
                                    <div class="form-group nolabel">\
                                    <div class="f_label"></div>\
                                        <input type="text" name="username" id="username" tabindex="1" class="form-control login_input" placeholder="Enter regsitration counsel" value="">\
                                    </div>\
                                </div>\
                                <div class="col-md-3 col-sm-3 col-xs-10">\
                                    <div class="form-group nolabel">\
                                    <div class="f_label"></div>\
                                        <select class="form-control select_input" id="sel1">\
                                            <option>Year</option>\
                                            <option>2</option>\
                                            <option>3</option>\
                                            <option>4</option>\
                                        </select>\
                                    </div>\
                                </div>\
                                <div class="col-md-1 col-sm-1 col-xs-2 remove_more">\
                                    <div class="form-group nolabel">\
                                    <div class="f_label"></div>\
                                        <img src="' + remove_button_png + '" class="minus_button remove_registration_details">\
                                    </div>\
                                </div>\
                            </div>';
        jQuery(".add_more_registration_details_div").append(html2);
    });
    jQuery(document).on('click', '.remove_registration_details', function () {
        jQuery(this).parent().parent().parent().remove();
    });
    /*----session-of--clinic--*/
    jQuery(".add_more_session_time_of_clinic").click(function () {
        var html3 = '<div class="extra_session_time_of_clinic">\
                            <div class="col-md-6  col-sm-6">\
                            <div class="form-group nolabel">\
                            <div class="f_label"></div>\
                                <select class="form-control select_input" id="sel1">\
                                    <option>Select start time</option>\
                                    <option>2</option>\
                                    <option>3</option>\
                                    <option>4</option>\
                                </select>\
                            </div>\
                        </div>\
                        <div class="col-md-5  col-sm-5 col-xs-10">\
                            <div class="form-group nolabel">\
                            <div class="f_label"></div>\
                                <select class="form-control select_input" id="sel1">\
                                    <option>Select end time</option>\
                                    <option>2</option>\
                                    <option>3</option>\
                                    <option>4</option>\
                                </select>\
                            </div>\
                        </div>\
                        <div class="col-md-1  col-sm-1 col-xs-2 remove_more">\
                            <div class="form-group nolabel">\
                            <div class="f_label"></div>\
                                <img src="' + remove_button_png + '" class="minus_button remove_session_time_of_clinic">\
                            </div>\
                        </div><div class="clearfix"></div>\
                            </div>';
        jQuery(".add_more_session_time_of_clinic_div").append(html3);
    });
    jQuery(document).on('click', '.remove_session_time_of_clinic', function () {
        jQuery(this).parent().parent().parent().remove();
    });
    /*----session-of--clinic--*/
    jQuery(".add_more_clinic_services").click(function () {
        var html4 = '<div class="col-md-12 extra_clinic_services">\
                            <div class="col-md-5 col-sm-11 col-xs-10 padding_left_0">\
                                <div class="form-group nolabel">\
                                <div class="f_label"></div>\
                                    <input type="text" name="username" id="username" tabindex="1" class="form-control login_input" placeholder="Write clinic services" value="">\
                                </div>\
                            </div>\
                            <div class="col-md-1  col-sm-1 col-xs-2 remove_more">\
                                <div class="form-group nolabel">\
                                <div class="f_label"></div>\
                                    <img src="' + remove_button_png + '" class="minus_button remove_clinic_services">\
                                </div>\
                            </div>\
                            </div>';
//                jQuery(".add_more_clinic_services_div").append(html4);
        jQuery(html4).insertAfter(".add_more_clinic_services_div");
    });
    jQuery(document).on('click', '.remove_clinic_services', function () {
        jQuery(this).parent().parent().parent().remove();
    });
    jQuery(".gender_placeholder_male").click(function () {

        jQuery(this).css('background', 'url(' + icon_male_select + ') no-repeat 50% 50%');
        jQuery('.gender_placeholder_female').css('background', 'url(' + icon_female_unselect + ') no-repeat 50% 50%');
        jQuery('.gender_text_male').css('color', '#30aca5');
        jQuery('.gender_text_female').css('color', '#666666');
        jQuery('#p_gender').val('male');
    });
    jQuery(".gender_placeholder_female").click(function () {

        jQuery(this).css('background', 'url(' + icon_female_select + ') no-repeat 50% 50%');
        jQuery('.gender_placeholder_male').css('background', 'url(' + icon_male_unselect + ') no-repeat 50% 50%');
        jQuery('#p_gender').val('female');
        jQuery('.gender_text_male').css('color', '#666666');
        jQuery('.gender_text_female').css('color', '#30aca5');
    });

    var step = '';
    var percent = (parseInt(step) / 3) * 100;
    //Initialize tooltips

    //Wizard            
    jQuery('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        //update progress
        step = jQuery(jQuery(this).attr('href')).index();
        percent = (parseInt(step) / 3) * 100;
        var $target = jQuery(e.target);
        if ($target.parent().hasClass('disabled')) {
            return false;
        } else {
            jQuery('.progress-bar').css({width: percent + '%'});
            $target.parent().addClass('done_step');
        }
    });





    jQuery(".next-step").click(function (e) {
        var $active = jQuery('.wizard .nav-tabs li.active');
        $active.addClass("done_step");
        var fordisablewiz = $active.attr('id');
        if (fordisablewiz == 'wizard_first' || fordisablewiz == 'wizard_second') {
            $active.addClass("disabled");
        }
        $active.next().removeClass('disabled');
        nextTab($active);
    });

    jQuery(".prev-step").click(function (e) {
        var $active = jQuery('.wizard .nav-tabs li.active');
        prevTab($active);
    });

    //Add Inactive Class To All Accordion Headers
    jQuery('.accordion-header').toggleClass('inactive-header');
    //Set The Accordion Content Width
    var contentwidth = jQuery('.accordion-header').width();
    jQuery('.accordion-content').css({});
    //Open The First Accordion Section When Page Loads
    jQuery('.accordion-header').first().toggleClass('active-header').toggleClass('inactive-header');
    jQuery('.accordion-content').first().slideDown().toggleClass('open-content');
    // The Accordion Effect
    jQuery('.accordion-header').click(function () {
        if (jQuery(this).is('.inactive-header')) {
            jQuery('.active-header').toggleClass('active-header').toggleClass('inactive-header').next().slideToggle().toggleClass('open-content');
            jQuery(this).toggleClass('active-header').toggleClass('inactive-header');
            jQuery(this).next().slideToggle().toggleClass('open-content');
        } else {
            jQuery(this).toggleClass('active-header').toggleClass('inactive-header');
            jQuery(this).next().slideToggle().toggleClass('open-content');
        }
    });

    /*---------third step of registraiton-------------*/
    jQuery(".third_step").click(function (e) {

        jQuery("#login_form").validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: true,
            ignore: [],
            rules: {
                login_mobile_number: {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                    number: true
                },
            },
            messages: {
                login_mobile_number: {
                    required: please_enter_valid_no
                }
            },
            highlight: function (e) {
                $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
            },
            success: function (e) {
                $(e).closest('.form-group').removeClass('has-error').addClass('has-info');
                $(e).remove();
            },
            errorPlacement: function (error, element) {
                if (element.parent().hasClass('input-group')) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }

            }
        });




    });
    /*-----------Login-------*/


    jQuery("#login_form").submit(function (ev) {
        ev.preventDefault();
        if (jQuery("#login_form").valid()) {
            var login_mobile_number = jQuery("#login_mobile_number").val();
            $.ajax({
                url: base_url + 'login/login_with_mobile',
                type: "POST",
                dataType: 'json',
                data: {
                    login_mobile_number: login_mobile_number,
                },
                async: false,
                cache: false,
                success: function (response) {
                    console.log(response);
                    if (response.status == true) {
                        jQuery('#login_resend_mobile_number').val(login_mobile_number);
                        jQuery(".login_reigstration").delay(100).fadeOut(100);
                        jQuery("#register_form").delay(100).fadeOut(100);
                        jQuery("#login_with_email_form").delay(100).fadeOut(100);
                        jQuery(".verify_otp_container").delay(100).fadeIn(100);
                        jQuery('#login_form_link').addClass('active');
                        toastr.success(response.message);
                    } else {
                        if (response.status === 3) {
                            jQuery('#register_form_link').click();
                            jQuery('#create_account_mobile_number').val(login_mobile_number);
                        }
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    toastr.error(problem_in_action_to_send_sms);
                }
            });
        }
    });

    /*-----------Login otp verification-------*/
    jQuery("#enter_otp_mobile").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: true,
        ignore: [],
        rules: {
            mobile_login_otp: {
                required: true,
                minlength: 6,
                number: true
            },
        },
        messages: {
            mobile_login_otp: {
                required: please_enter_otp
            }
        },
        errorPlacement: function (error, element) {
            element.after(error);
        },
    });

    jQuery("#enter_otp_mobile").submit(function (ev) {
        ev.preventDefault();
        if (jQuery("#enter_otp_mobile").valid()) {
            var mobile_login_otp = jQuery("#mobile_login_otp").val();
            var login_resend_mobile_number = jQuery("#login_resend_mobile_number").val();
            $.ajax({
                url: base_url + 'login/verify_mobile_login_otp',
                type: "POST",
                dataType: 'json',
                data: {
                    mobile_login_otp: mobile_login_otp,
                    login_resend_mobile_number: login_resend_mobile_number,
                },
                async: false,
                cache: false,
                success: function (response) {
                    if (response.status == true) {
                        jQuery("#login_resend_mobile_number").val('');
                        window.location.href = dashboard_url;
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    toastr.error(problem_in_action_to_verify_otp);
                }
            });
        }
    });

    jQuery("#resend_login_otp").click(function () {
        var login_mobile_number = jQuery("#login_resend_mobile_number").val();
        $.ajax({
            url: base_url + 'login/resend_otp_to_mobile',
            type: "POST",
            dataType: 'json',
            data: {
                mobile_number: login_mobile_number,
            },
            async: false,
            cache: false,
            success: function (response) {
                console.log(response);
                if (response.status == true) {
                    jQuery("#login_resend_mobile_number").val('');
                    toastr.success(otp_resend_success);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function () {
                toastr.error(problem_in_action_to_send_sms);
            }
        });
    });


    /*-----------login with email passwrod-------*/

    jQuery("#login_with_email_form").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: true,
        ignore: [],
        rules: {
            enter_email_id: {
                required: true,
                email: true,
            },
            enter_password: {
                no_space_allow: true,
                required: true,
                minlength: 6,
            }
        },
        messages: {
            enter_email_id: {
                required: enter_valid_email_address
            },
            enter_password: {
                required: enter_valid_password
            }
        },
        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },
        success: function (e) {
            $(e).closest('.form-group').removeClass('has-error').addClass('has-info');
            $(e).remove();
        },
        errorPlacement: function (error, element) {
            if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });


    jQuery("#login_with_email_form").submit(function (ev) {
        ev.preventDefault();
        if (jQuery("#login_with_email_form").valid()) {
            var enter_email_id = jQuery("#enter_email_id").val();
            var enter_password = jQuery("#enter_password").val();
            $.ajax({
                url: base_url + 'login/login_with_email_password',
                type: "POST",
                dataType: 'json',
                data: {
                    enter_email_id: enter_email_id,
                    enter_password: enter_password,
                },
                async: false,
                cache: false,
                success: function (response) {
                    console.log(response);
                    if (response.status == true) {

                        if (response.status == true) {
                            jQuery("#enter_email_id").val('');
                            jQuery("#enter_password").val('');
                            window.location.href = dashboard_url;
                        } else {
                            toastr.error(response.message);
                        }
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    toastr.error(problem_in_action_to_verify_email);
                }
            });
        }
    });


    /*-----------reset password with email-------*/
    jQuery("#email_forgot_password").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: true,
        ignore: [],
        rules: {
            forgot_email_address: {
                required: true,
                email: true,
            },
        },
        messages: {
            forgot_email_address: {
                required: enter_valid_email_address
            },
        },
        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },
        success: function (e) {
            $(e).closest('.form-group').removeClass('has-error').addClass('has-info');
            $(e).remove();
        },
        errorPlacement: function (error, element) {
            if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }

        }
    });

    jQuery("#email_forgot_password").submit(function (ev) {
        ev.preventDefault();
        if (jQuery("#email_forgot_password").valid()) {
            var forgot_email_address = jQuery("#forgot_email_address").val();
            $.ajax({
                url: base_url + 'login/reset_password',
                type: "POST",
                dataType: 'json',
                data: {
                    forgot_email_address: forgot_email_address,
                },
                async: false,
                cache: false,
                success: function (response) {
                    console.log(response);
                    if (response.status == true) {

                        if (response.status == true) {
                            jQuery("#forgot_email_address").val('');
                            window.location.href = home_url;
                        } else {
                            toastr.error(response.message);
                        }
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    toastr.error(problem_in_action_to_verify_email);
                }
            });
        }
    });

    /*------------------------------------------------------------------------------*/


    /*-----------create account with mobile + password -------*/
    jQuery("#create_account").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: true,
        ignore: [],
        rules: {
            create_account_mobile_number: {
                required: true,
                minlength: 10,
                maxlength: 10,
                number: true
            },
            create_account_password: {
                no_space_allow: true,
                required: true,
            },
        },
        messages: {
            create_account_mobile_number: {
                required: please_enter_valid_no
            },
            create_account_password: {
                required: enter_valid_password
            }
        },
        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },
        success: function (e) {
            $(e).closest('.form-group').removeClass('has-error').addClass('has-info');
            $(e).remove();
        },
        errorPlacement: function (error, element) {
            if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }

        }
    });


    jQuery("#create_account").submit(function (ev) {
        ev.preventDefault();
        if (jQuery("#create_account").valid()) {
            var create_account_mobile_number = jQuery("#create_account_mobile_number").val();
            var create_account_password = jQuery("#create_account_password").val();
            create_account_password = jQuery.sha1(create_account_password);
            $.ajax({
                url: base_url + 'login/create_account',
                type: "POST",
                dataType: 'json',
                data: {
                    create_account_mobile_number: create_account_mobile_number,
                    create_account_password: create_account_password,
                },
                async: false,
                cache: false,
                success: function (response) {
                    console.log(response);
                    if (response.status == true) {
                        jQuery("#register_mobile_otp").val(create_account_mobile_number);
                        next_tab();
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    toastr.error(problem_in_action_to_send_sms);
                }
            });
        }
    });

    /*-----------create account OTP -------*/
    jQuery("#register_otp_form").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: true,
        ignore: [],
        rules: {
            register_otp: {
                required: true,
                minlength: 6,
                number: true
            },
        },
        messages: {
            register_otp: {
                required: please_enter_otp,
            }
        },
        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },
        success: function (e) {
            $(e).closest('.form-group').removeClass('has-error').addClass('has-info');
            $(e).remove();
        },
        errorPlacement: function (error, element) {
            if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

    jQuery("#register_otp_form").submit(function (ev) {
        ev.preventDefault();
        if (jQuery("#register_otp_form").valid()) {
            var register_otp = jQuery("#register_otp").val();
            var register_mobile_otp = jQuery("#register_mobile_otp").val();
            $.ajax({
                url: base_url + 'login/verify_register_otp',
                type: "POST",
                dataType: 'json',
                data: {
                    otp: register_otp,
                    mobile_number: register_mobile_otp,
                },
                async: false,
                cache: false,
                success: function (response) {
                    console.log(response);
                    if (response.status == true) {
                        next_tab();
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    toastr.error(problem_in_action_to_send_sms);
                }
            });
        }
    });

    /*--------resend registration otp----------*/

    jQuery("#otp_again").click(function () {
        var register_mobile_otp = jQuery("#register_mobile_otp").val();
        $.ajax({
            url: base_url + 'login/resend_otp_to_mobile',
            type: "POST",
            dataType: 'json',
            data: {
                mobile_number: register_mobile_otp,
            },
            async: false,
            cache: false,
            success: function (response) {
                console.log(response);
                if (response.status == true) {
                    toastr.success(otp_resend_success);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function () {
                toastr.error(problem_in_action_to_send_sms);
            }
        });
    });



    /*----------- Register provider details Form -------*/
    jQuery("#register_provider_form").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: true,
        ignore: [],
        rules: {
            register_otp: {
                required: true,
                minlength: 6,
                number: true
            },
        },
        messages: {
            register_otp: {
                required: please_enter_otp,
            }
        },
        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },
        success: function (e) {
            $(e).closest('.form-group').removeClass('has-error').addClass('has-info');
            $(e).remove();
        },
        errorPlacement: function (error, element) {
            if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

    jQuery("#register_provider_form").submit(function (ev) {
        ev.preventDefault();
        if (jQuery("#register_otp_form").valid()) {
            var register_otp = jQuery("#register_otp").val();
            var register_mobile_otp = jQuery("#register_mobile_otp").val();
            $.ajax({
                url: base_url + 'login/verify_register_otp',
                type: "POST",
                dataType: 'json',
                data: {
                    otp: register_otp,
                    mobile_number: register_mobile_otp,
                },
                async: false,
                cache: false,
                success: function (response) {
                    console.log(response);
                    if (response.status == true) {
                        next_tab();
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    toastr.error(problem_in_action_to_send_sms);
                }
            });
        }
    });
});