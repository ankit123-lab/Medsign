<?php $this->load->view('web/header_layout'); ?>
<body data-spy="scroll" data-target=".header" data-offset="50">
    <div id="preloader"></div>
    <?php
    $this->load->view('web/header');
    ?>
    <!-- breadcrumb area start -->
    <section class="hero-area breadcrumb-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="hero-area-content">
                        <h1><?php echo $breadcrumbs; ?></h1>
                        <ul>
                            <li><a href="index.html">Home </a></li>
                            <li><?php echo '&nbsp' . $breadcrumbs; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- breadcrumb area end -->
    <!-- blog section start -->
    <section class="blog-detail" id="blog">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="blog-details">
                        <table border="0" cellpadding="8" cellspacing="0" id="emailContainer" bgcolor="#ffffff">
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
                                        <div class="form-group" style="position: relative;">
                                            <label for="email">New password:</label>
                                            <input type="password" name="password" class="form-control" id="password" placeholder="Enter new password">
                                            <span class="password-fa-icon passwordShowHide"><i class="fa fa-eye-slash"></i></span>
                                        </div>
                                        <div class="form-group" style="position: relative;">
                                            <label for="email">New confirm password:</label>
                                            <input type="password" name="confirmPassword" class="form-control" id="cpassword" placeholder="Enter new confirm password">
                                            <span class="password-fa-icon passwordShowHide"><i class="fa fa-eye-slash"></i></span>
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
                        </table>
                    </div>
                </div>
				<?php $this->load->view('web/right_side_sub_links'); ?>	
            </div>
        </div>
    </section>
    <?php $this->load->view('web/footer'); ?>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/resetpassword.css" />
    <?php $this->load->view('web/footer_layout'); ?>