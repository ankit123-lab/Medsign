<?php
include_once DOCROOT_PATH . 'application/views/reset_password_header.php';
?>
<tr>
    <td align="center" valign="top" style="font-family: 'Gotham Book';font-size: 28px;color: #212121;text-transform: uppercase;padding-top: 30px">
        RESET PASSWORD
    </td>
</tr>
<tr>
    <td align="center" valign="top" style="font-family: 'Gotham Book';font-size: 100%;color: #212121;padding: 30px">

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
            <form role="form" id="reset-form" class="form reset_password_form" method="post" action="<?= BASE_URL ?>reset_password">
                <div class="form-group">
                    <label for="email">New password:</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter new password">
                </div>
                <div class="form-group">
                    <label for="email">New confirm password:</label>
                    <input type="password" name="confirmPassword" class="form-control" id="cpassword" placeholder="Enter new confirm password">
                </div>
                <div class="clearfix"></div>
                <div class="form-group submit_reset_password_btn">
                    <input type="submit" style="font-family: 'Gotham';padding: 15px 40px;font-size: 16px;line-height: 20px;border-radius: 8px;text-transform: uppercase;color: #ffffff;background-color: #30aca5;outline: none !important;text-decoration: none;" value="Reset Password" content="Reset Password" />
                </div>
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_data">
                <input type="hidden" name="userId" value="<?php echo isset($userId) ? $userId : "" ?>" />
            </form> 
        <?PHP } ?>
    </td>
</tr>

<script type="text/javascript">
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
    });
</script>
<?php
include_once DOCROOT_PATH . 'application/views/reset_password_footer.php';
?>
