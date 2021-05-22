
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />

        <title><?= APP_NAME ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />

        <link rel="stylesheet" href="<?= ASSETS_PATH ?>api/bootstrap/css/bootstrap.min.css" />


        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

        <!-- Custom Css -->
        <link href="<?= ADMIN_ASSETS_PATH ?>css/style.css" rel="stylesheet">


        <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
        <link href="<?= ADMIN_ASSETS_PATH ?>css/themes/all-themes.css" rel="stylesheet" />
        <link href="<?= ADMIN_ASSETS_PATH ?>css/custom.css" rel="stylesheet">

        <script type="text/javascript" src="<?= ASSETS_PATH ?>api/js/jquery-2.1.3.min.js"></script>
        <script type="text/javascript" src="<?= ASSETS_PATH ?>api/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?= ASSETS_PATH ?>api/js/jquery.validate.min.js"></script>
        <link rel="stylesheet" href="<?= ASSETS_PATH ?>api/css/style.theme.css" />
        <!-- Slimscroll Plugin Js -->
        <script src="<?= ADMIN_ASSETS_PATH ?>plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
        <!-- Waves Effect Plugin Js -->
        <script src="<?= ADMIN_ASSETS_PATH ?>plugins/node-waves/waves.js"></script>
        <!-- Morris Plugin Js -->
        <script src="<?= ADMIN_ASSETS_PATH ?>plugins/raphael/raphael.min.js"></script>
        <script src="<?= ADMIN_ASSETS_PATH ?>plugins/morrisjs/morris.js"></script>

        <!-- Custom Js -->
        <script src="<?= ADMIN_ASSETS_PATH ?>js/admin.js"></script>
        <script src="<?= ADMIN_ASSETS_PATH ?>js/jquery.validate.js"></script>
    </head>
    <body class="fp-page">
        <div class="fp-box">
            <div class="logo">
                <a href="javascript:void(0);"><img src="<?= ASSETS_PATH ?>images/logo.png"></a>                
            </div>
            <?php
            if (isset($success) && $success) {
                $successMessage = $message;
            } else if (isset($success)) {
                $errorMessage = $message;
            }
            if (!empty($errorMessage)) {
                echo '<div class="alert alert-danger text-center">' . $errorMessage . '</div>';
            }
            ?>
            <?php
            if (!empty($successMessage)) {
                echo '<div class="alert alert-success text-center">' . $successMessage . '</div>';
            }
            ?>
            <?php
            if (empty($errorMessage) && empty($successMessage)) {
                ?>
                <div class="card">
                    <div class="body">

                        <form role="form" id="reset-form" class="form" method="post" action="<?= BASE_URL ?>reset_password">
                            <div class="msg">
                                <input type="password" style="display: none">
                                Enter your new password.
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">email</i>
                                </span>
                                <div class="form-line">
                                    <input type="password" class="form-control input-lg" name="password" id="password" required="required" placeholder="New Password" autocomplete="off">                                
                                </div>
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">email</i>
                                </span>
                                <div class="form-line">
                                    <input type="password" class="form-control input-lg" name="confirmPassword" id="confirmPassword" required="required" placeholder="Confirm Password" autocomplete="off">
                                </div>
                            </div>

                            <input type="submit" class="btn btn-block btn-lg bg-pink waves-effect" value="RESET MY PASSWORD" content="RESET MY PASSWORD" />
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_data">
                            <input type="hidden" name="userId" value="<?php echo isset($userId) ? $userId : "" ?>" />
                        </form>

                    </div>
                </div>
            <?PHP } ?>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function() {


                $.validator.addMethod("atLeastOneUppercaseLetter", function(value, element) {
                    return this.optional(element) || /[A-Z]+/.test(value);
                }, "Password must have 1 uppercase letter.");

                $.validator.addMethod("atLeastOneSymbol", function(value, element) {
                    return this.optional(element) || /[!@#$%^&*()]+/.test(value);
                }, "Password must have 1 special character.");

                $.validator.addMethod("atLeastOneDigit", function(value, element) {
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
                    invalidHandler: function(event, validator) { //display error alert on form submit   
                        $('.alert-danger', $('.login-form')).show();
                    },
                    highlight: function(e) {
                        $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
                    },
                    success: function(e) {
                        $(e).closest('.form-group').removeClass('has-error').addClass('has-info');
                        $(e).remove();
                    },
                    errorPlacement: function(error, element) {
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

                $.validator.addMethod("nowhitespace", function(value, element) {
                    return this.optional(element) || /^\S+$/i.test(value);
                }, "No white space please");

                $.validator.addMethod("regex", function(value, element, regexpr) {
                    if (value.trim() != '') {
                        return value.match(regexpr);
                    } else {
                        return true;
                    }
                }, "Invalid password request");
            });
        </script>
</html>
